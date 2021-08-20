<?php
    namespace Kerwin\Core;

    use Kerwin\Core\Config;
    use Kerwin\Core\Database as DB;
    use Kerwin\Core\Message as MG;

    class Auth
    {     
        private static $config;

        public function __construct() {
            $this->config = new Config();
        }

        /**
         * 已登入的使用者ID
         *
         * @return object
         */
        public static function id()
        {
            $user = DB::table($_ENV['AUTH_TABLE'])->select('id')->where("id = '{$_SESSION['USER_ID']}'")->first();
            return isset($user->id) ? $user->id : false;
        }
        
        /**
         * 密碼重設規範
         *
         * @param  mixed $root 路徑
         * @return void
         */
        public static function password_reset($root='../../')
        {
            // 禁止已登入或連結錯誤訪問
            if (!is_null($_SESSION['USER_ID']) && empty($_GET['auth']) && empty($_GET['id'])) {
                include_once($root.'_error/404.php');
                exit;
            }

            // 確認連結資料正確性
            $password_resets = DB::table('password_resets')->where("id='{$_GET['id']}' and email_token='{$_GET['auth']}'")->first();
            
            if (empty($password_resets)) {
                MG::flash('連結有問題，請確認或重新申請密碼重設信件，謝謝', 'warning');
                MG::redirect(self::$config->app_address().'auth/password/password_forgot.php');
            }
            elseif (strtotime('now') > strtotime($password_resets->token_updated_at.' +30 minutes')) {
                MG::flash('密碼重設信已逾期，請重新獲取，謝謝。', 'warning');
                MG::redirect(self::$config->app_address().'auth/password/password_forgot.php');
            }
            elseif (strtotime('now') < strtotime($password_resets->password_updated_at.' +1 days')) {
                MG::flash('密碼更新時間小於一天，'.date('Y-m-d H:i:s', strtotime($password_resets->password_updated_at.' +1 days')).'後才可以再次更改。', 'warning');
                MG::redirect(self::$config->app_address());
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
            $user = DB::table($_ENV['AUTH_TABLE'])->select('id', 'name', 'email', 'auth_code', 'email_varified_at', 'role', 'updated_at')->where("id = '{$_SESSION['USER_ID']}'")->first();
            return isset($user) ? $user : false;
         }   
    }
    