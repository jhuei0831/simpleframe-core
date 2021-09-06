<?php

    namespace Kerwin\Core\Support\Facades;

    use Kerwin\Core\Support\Facades\Facade;
    use Kerwin\Core\Role as CoreRole;

    class Role extends Facade
    {
        protected static function getClass()
        {
            return new CoreRole();
        }
    }
    