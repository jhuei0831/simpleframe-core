<?php

    namespace Kerwin\Core;

    use Exception;
    use Kerwin\Core\Database;
    use Kerwin\Core\Contracts\Auth\Role as roleGuard;
    
    class Role implements roleGuard
    {        
        /**
         * 建立角色
         *
         * @param  mixed $data
         * @return void
         */
        public function create($data)
        {
            $data = (array) $data;
            return Database::table('roles')->insert($data);
        }
        
        /**
         * 取得角色ID
         *
         * @param  string $role
         * @return string
         */
        private function getRoleID($role)
        {
            $role = Database::table('roles')->select('id')->where("name ='{$role}'")->first();

            return $role->id ?? false;
        }

                
        /**
         * 使用者是否符合角色身分
         *
         * @param  string $role
         * @return bool
         */
        public function has($role)
        {
            $roleId = $this->getRoleID($role);
            if ($roleId === false) {
                return false;
            }
            if (!isset($_ENV['AUTH_TABLE'])) {
                throw new Exception("Please defined AUTH_TABLE in .env", 1);
            }
            $check = Database::table($_ENV['AUTH_TABLE'])->where('id ="'.$_SESSION['USER_ID'].'" and role ="'.$roleId.'"')->count();
            return $check > 0 ? true : false;
        }
    }
    