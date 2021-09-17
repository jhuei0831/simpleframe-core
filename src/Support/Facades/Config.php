<?php

    namespace Kerwin\Core\Support\Facades;

    use Kerwin\Core\Support\Facades\Facade;
    use Kerwin\Core\Config as CoreConfig;

    class Config extends Facade
    {
        protected static function getClass()
        {
            return new CoreConfig();
        }
    }
    