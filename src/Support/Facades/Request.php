<?php

    namespace Kerwin\Core\Support\Facades;

    use Kerwin\Core\Support\Facades\Facade;
    use Kerwin\Core\Request as CoreRequest;

    class Request extends Facade
    {
        protected static function getClass()
        {
            return new CoreRequest();
        }
    }
    