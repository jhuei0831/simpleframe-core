<?php

    namespace Kerwin\Core\Support\Facades;

    use Kerwin\Core\Support\Facades\Facade;
    use Kerwin\Core\Session as CoreSession;

    class Session extends Facade
    {
        protected static function getClass()
        {
            return new CoreSession();
        }
    }
    