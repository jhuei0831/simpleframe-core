<?php
    namespace Kerwin\Core;

    use Kerwin\Core\Config;
    use Kerwin\Core\Database;
    use Kerwin\Core\Message;

    class Auth
    {     
        private static $config;

        /**
         * 已登入的使用者ID
         *
         * @return object
         */
        public static function id()
        {
            $user = Database::table($_ENV['AUTH_TABLE'])->select('id')->where("id = '{$_SESSION['USER_ID']}'")->first();
            return isset($user->id) ? $user->id : false;
        }
        
        /**
         * 密碼重設規範
         *
         * @param  mixed $root 路徑
         * @return void
         */
        public static function passwordReset($root='../../')
        {
            self::$config = new Config();

            // 禁止已登入或連結錯誤訪問
            if (!is_null($_SESSION['USER_ID']) && empty($_GET['auth']) && empty($_GET['id'])) {
                include_once($root.'_error/404.php');
                exit;
            }

            // 確認連結資料正確性
            $password_resets = Database::table('password_resets')->where("id='{$_GET['id']}' and email_token='{$_GET['auth']}'")->first();
            
            if (empty($password_resets)) {
                Message::flash('連結有問題，請確認或重新申請密碼重設信件，謝謝', 'warning');
                Message::redirect(self::$config->getAppAddress().'auth/password/password_forgot.php');
            }
            elseif (strtotime('now') > strtotime($password_resets->token_updated_at.' +30 minutes')) {
                Message::flash('密碼重設信已逾期，請重新獲取，謝謝。', 'warning');
                Message::redirect(self::$config->getAppAddress().'auth/password/password_forgot.php');
            }
            elseif (strtotime('now') < strtotime($password_resets->password_updated_at.' +1 days')) {
                Message::flash('密碼更新時間小於一天，'.date('Y-m-d H:i:s', strtotime($password_resets->password_updated_at.' +1 days')).'後才可以再次更改。', 'warning');
                Message::redirect(self::$config->getAppAddress());
            }
            else {
                return true;
            }
        }

        /**
         * 已登入的使用者
         *
         * @return object
         */
         public static function user()
         {
            $user = Database::table($_ENV['AUTH_TABLE'])->select('id', 'name', 'email', 'auth_code', 'email_varified_at', 'role', 'updated_at')->where("id = '{$_SESSION['USER_ID']}'")->first();
            return isset($user) ? $user : false;
         }   
    }
    