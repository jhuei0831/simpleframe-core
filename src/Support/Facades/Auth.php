<?php

    namespace Kerwin\Core\Facades;

    use Kerwin\Core\Facades\Facade;
    use Kerwin\Core\Auth as CoreAuth;

    class Auth extends Facade
    {
        protected static function getClass()
        {
            return new CoreAuth();
        }
    }
    