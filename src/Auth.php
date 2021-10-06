<?php

    namespace Kerwin\Core;

    use Kerwin\Core\Database;
    use Kerwin\Core\Request;
    use Kerwin\Core\Session;
    use Kerwin\Core\Contracts\Auth\User;

    class Auth implements User
    {     
        /**
         * Database instance
         *
         * @var Kerwin\Core\Database
         */
        private $database;
        
        /**
         * Request instance
         *
         * @var Kerwin\Core\Request
         */
        private $request;

        /**
         * Session instance
         *
         * @var Kerwin\Core\Session
         */
        private $session;

        public function __construct() {
            $this->database = new Database();
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
    