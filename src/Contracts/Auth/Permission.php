<?php

    namespace Kerwin\Core\Contracts\Auth;

    interface Permission 
    {
        /**
         * 是否有這個權限
         *
         * @param  string $permission
         * @return bool
         */
        public function can(string $permission): bool;
    }