<?php

    namespace Kerwin\Core\Facades;

    use Kerwin\Core\Facades\Facade;
    use Kerwin\Core\Database as CoreDatabase;

    class Database extends Facade
    {
        protected static function getClass()
        {
            return new CoreDatabase();
        }
    }
    