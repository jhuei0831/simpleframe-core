<?php


    namespace Kerwin\Core\Support\Facades;

    use RuntimeException;
    
    abstract class Facade
    {
        public static function __callStatic($method, $args)
        {
            $instance = static::getClass();

            if (!$instance) {
                throw new RuntimeException('尚未設定facade');
            }

            return $instance->$method(...$args);
        }
    }