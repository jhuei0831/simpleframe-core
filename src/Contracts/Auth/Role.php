<?php

    namespace Kerwin\Core\Contracts\Auth;

    interface Role 
    {
        /**
        * 使用者是否符合角色身分
        *
        * @param string $role
        * @return bool
        */
        public function has($role);
    }