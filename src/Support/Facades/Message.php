<?php

    namespace Kerwin\Core\Support\Facades;

    use Kerwin\Core\Support\Facades\Facade;
    use Kerwin\Core\Message as CoreMessage;

    class Message extends Facade
    {
        protected static function getClass()
        {
            return new CoreMessage();
        }
    }
    