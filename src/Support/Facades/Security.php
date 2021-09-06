<?php

    namespace Kerwin\Core\Support\Facades;

    use Kerwin\Core\Support\Facades\Facade;
    use Kerwin\Core\Security as CoreSecurity;

    class Security extends Facade
    {
        protected static function getClass()
        {
            return new CoreSecurity();
        }
    }
    