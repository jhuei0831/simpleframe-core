<?php

    namespace Kerwin\Core\Support;

    use Exception;

    class Config
    {        
        /**
         * 網站完整地址
         *
         * @return string
         */
        public static function getAppAddress(): string
        {
            $address = self::getAppPortocol()."://".self::getAppDomain()."/".self::getAppFolder()."/";
            return $address;
        }
        
        /**
         * 網站網域
         *
         * @return string
         */
        protected static function getAppDomain(): string
        {
            $domain = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "localhost";
            return $domain;
        }
        
        /**
         * 網站子資料夾
         *
         * @return string
         */
        protected static function getAppFolder(): string
        {
            if (!isset($_ENV['APP_FOLDER'])) {
                throw new Exception("Please defined APP_FOLDER in .env", 1);
            }
            $folder = isset($_ENV['APP_FOLDER']) ? $_ENV['APP_FOLDER'] : '';
            return $folder;
        }
        
        /**
         * 網站名稱
         *
         * @return string
         */
        protected static function getAppName(): string
        {
            if (!isset($_ENV['APP_NAME'])) {
                throw new Exception("Please defined APP_NAME in .env", 1);
            }
            $name = isset($_ENV['APP_NAME']) ? $_ENV['APP_NAME'] : '';
            return $name;
        }
        
        /**
         * 網站協定
         *
         * @return string
         */
        protected static function getAppPortocol(): string
        {
            $portocol = isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : "http";
            return $portocol;
        }
        
        /**
         * csrf_token
         *
         * @return string
         */
        public static function csrfToken(): string
        {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!isset($_SESSION['token'])) {
                throw new Exception("Please defined SESSION token", 1);
            }
            $token = $_SESSION['token'];
            return $token;
        }
        
        /**
         * 是否為偵錯模式
         *
         * @return void
         */
        public static function isDebug(): string
        {
            if (!isset($_ENV['APP_DEBUG'])) {
                throw new Exception("Please defined APP_DEBUG in .env", 1);
            }
            $debug = isset($_ENV["APP_DEBUG"]) ? $_ENV["APP_DEBUG"] : "FALSE";
            return $debug;
        }
    }
    