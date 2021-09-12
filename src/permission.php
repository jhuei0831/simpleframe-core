<?php
    namespace Kerwin\Core;

    use Kerwin\Core\Database;
    use Kerwin\Core\Request;
    use Kerwin\Core\Session;
    use Kerwin\Core\Contracts\Auth\Permission as permissionGuard;

    class Permission implements permissionGuard
    {        
        private $database;
        private $request;
        private $session;

        public function __construct() {
            $this->database = new Database();
            $this->request = Request::createFromGlobals();
            $this->session = new Session();
        }
        
        /**
         * 建立權限
         *
         * @param  string|array $data
         * @return void
         */
        public function create($data)
        {
            $data = (array) $data;
            return $this->database->table('permissions')->insert($data);
        }
        
        /**
         * 取得符合權限的所有角色
         *
         * @param  string $permissionName
         * @return array
         */
        private function permissionBelongRoles($permissionName)
        {
            $roles = $this->database->table('roles')
                ->select('roles.id', 'roles.name')
                ->join('role_has_permissions', 'role_has_permissions.role_id = roles.id')
                ->join('permissions', 'permissions.id = role_has_permissions.permission_id')
                ->where("permissions.name = '".$permissionName."'")
                ->get();
                
            return $roles;
        }
        
        /**
         * 是否有這個權限
         *
         * @param  string $permission
         * @return bool
         */
        public function can($permission)
        {
            $roles = $this->permissionBelongRoles($permission);
            if (empty($roles) || empty($this->session->get('USER_ID'))) {
                return false;
            }
            $roleList = array_column($roles, 'id');
            
            $check = $this->database->table($this->request->server->get('AUTH_TABLE'))->where('id = "'.$this->session->get('USER_ID').'" and role in('.join(', ', $roleList).')')->count();

            return $check > 0 ? true : false;
        }
    }
    