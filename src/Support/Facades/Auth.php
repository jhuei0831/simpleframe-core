<?php

    namespace Kerwin\Core\Support\Facades;

    use Kerwin\Core\Support\Facades\Facade;
    use Kerwin\Core\Auth as CoreAuth;

    class Auth extends Facade
    {
        protected static function getClass()
        {
            return new CoreAuth();
        }
    }
    