<?php

    namespace Kerwin\Core;

    use Kerwin\Core\Database;
    use Kerwin\Core\Request;
    use Kerwin\Core\Session;
    use Kerwin\Core\Contracts\Auth\User;
    use Kerwin\Core\Support\Config;
    use Kerwin\Core\Support\Facades\Message;

    class Auth implements User
    {     

        private $config;
        private $database;
        private $request;
        private $session;

        public function __construct() {
            $this->database = new Database();
            $this->config = new Config();
            $this->request = Request::createFromGlobals();
            $this->session = new Session();
        }

        /**
         * 已登入的使用者ID
         *
         * @return object
         */
        public function id()
        {
            $user = $this->database->table($this->request->server->get('AUTH_TABLE'))->select('id')->where("id= '{$this->session->get('USER_ID')}'")->first();
            return isset($user->id) ? $user->id : false;
        }
        
        /**
         * 密碼重設規範
         *
         * @param  mixed $root 路徑
         * @return void
         */
        public function passwordReset($root='../../')
        {
            // 禁止已登入或連結錯誤訪問
            if (!is_null($this->session->get('USER_ID')) && empty($_GET['auth']) && empty($_GET['id'])) {
                include_once($root.'_error/404.php');
                exit;
            }

            // 確認連結資料正確性
            $passwordResets = $this->database->table('password_resets')->where("id='{$_GET['id']}' and email_token='{$_GET['auth']}'")->first();
            
            if (empty($passwordResets)) {
                Message::flash('連結有問題，請確認或重新申請密碼重設信件，謝謝', 'warning');
                Message::redirect($this->config->getAppAddress().'auth/password/password_forgot.php');
            }
            elseif (strtotime('now') > strtotime($passwordResets->token_updated_at.' +30 minutes')) {
                Message::flash('密碼重設信已逾期，請重新獲取，謝謝。', 'warning');
                Message::redirect($this->config->getAppAddress().'auth/password/password_forgot.php');
            }
            elseif (strtotime('now') < strtotime($passwordResets->password_updated_at.' +1 days')) {
                Message::flash('密碼更新時間小於一天，'.date('Y-m-d H:i:s', strtotime($passwordResets->password_updated_at.' +1 days')).'後才可以再次更改。', 'warning');
                Message::redirect($this->config->getAppAddress());
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
         public function user()
         {
            $user = $this->database->table($this->request->server->get('AUTH_TABLE'))->select('id', 'name', 'email', 'auth_code', 'email_varified_at', 'role', 'updated_at')->where("id = '{$this->session->get('USER_ID')}'")->first();
            return isset($user) ? $user : false;
         }   
    }
    