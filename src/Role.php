<?php

    namespace Kerwin\Core;

    use Exception;
    use Kerwin\Core\Database;
    use Kerwin\Core\Request;
    use Kerwin\Core\Session;
    use Kerwin\Core\Contracts\Auth\Role as roleGuard;
    
    class Role implements roleGuard
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
         * 建立角色
         *
         * @param  array $data
         * @return void
         */
        public function create(array $data)
        {
            $data = (array) $data;
            return $this->database->table('roles')->insert($data);
        }
        
        /**
         * 取得角色ID
         *
         * @param  string $role
         * @return string
         */
        private function getRoleID(string $role)
        {
            $role = $this->database->table('roles')->select('id')->where("name ='{$role}'")->first();

            return $role->id ?? false;
        }

                
        /**
         * 使用者是否符合角色身分
         *
         * @param  string $role
         * @return bool
         */
        public function has(string $role): bool
        {
            $roleId = $this->getRoleID($role);
            if ($roleId === false) {
                return false;
            }
            if (!isset($_ENV['AUTH_TABLE'])) {
                throw new Exception("Please defined AUTH_TABLE in .env", 1);
            }
            $check = $this->database->table($this->request->server->get('AUTH_TABLE'))->where('id ="'.$this->session->get('USER_ID').'" and role ="'.$roleId.'"')->count();
            return $check > 0 ? true : false;
        }
    }
    