<?php

    namespace Kerwin\Core\Contracts\Security;

    interface Filter 
    {
        /**
        * defendFilter 用 addslashes防SQL Injection、filter_var防XSS
        *
        * @param array|string $data
        * @return array|string|object
        */
        public function defendFilter($data);
    }