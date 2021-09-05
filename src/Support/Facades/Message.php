<?php

    namespace Kerwin\Core\Facades;

    use Kerwin\Core\Facades\Facade;
    use Kerwin\Core\Facades\Message as CoreMessage;

    class Message extends Facade
    {
        protected static function getClass()
        {
            return new CoreMessage();
        }
    }
    