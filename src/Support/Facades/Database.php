<?php

    namespace Kerwin\Core\Support\Facades;

    use Kerwin\Core\Support\Facades\Facade;
    use Kerwin\Core\Database as CoreDatabase;

    class Database extends Facade
    {
        protected static function getClass()
        {
            return new CoreDatabase();
        }
    }
    