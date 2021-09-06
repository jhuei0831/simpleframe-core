<?php

    namespace Kerwin\Core\Contracts\Auth;

    interface User 
    {
        /**
        * 已登入的使用者ID
        *
        * @return object
        */
        public function id();

        /**
        * 已登入的使用者
        *
        * @return object
        */
        public function user();
    }