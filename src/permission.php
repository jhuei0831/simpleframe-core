<?php
    namespace Kerwin\Core;

    use kerwin\core\Database;

    class Permission
    {        
        /**
         * 建立權限
         *
         * @param  string|array $data
         * @return void
         */
        public static function create($data)
        {
            $data = (array) $data;
            return Database::table('permissions')->insert($data);
        }
        
        /**
         * 取得符合權限的所有角色
         *
         * @param  string $permission_name
         * @return array
         */
        public static function permission_belong_roles($permission_name)
        {
            $roles = Database::table('roles')
                ->select('roles.id', 'roles.name')
                ->join('role_has_permissions', 'role_has_permissions.role_id = roles.id')
                ->join('permissions', 'permissions.id = role_has_permissions.permission_id')
                ->where("permissions.name = '".$permission_name."'")
                ->get();
                
            return $roles;
        }
        
        /**
         * 是否有這個權限
         *
         * @param  string $permission
         * @return void
         */
        public static function can($permission)
        {
            $roles = self::permission_belong_roles($permission);
            if (empty($roles) || empty($_SESSION['USER_ID'])) {
                return false;
            }
            $role_list = array_column($roles, 'id');
            
            $check = Database::table($_ENV['AUTH_TABLE'])->where('id = "'.$_SESSION['USER_ID'].'" and role in('.join(', ', $role_list).')')->count();

            return $check > 0 ? true : false;
        }
    }
    