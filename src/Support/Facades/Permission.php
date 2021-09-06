<?php

    namespace Kerwin\Core\Support\Facades;

    use Kerwin\Core\Support\Facades\Facade;
    use Kerwin\Core\Permission as CorePermission;

    class Permission extends Facade
    {
        protected static function getClass()
        {
            return new CorePermission();
        }
    }
    