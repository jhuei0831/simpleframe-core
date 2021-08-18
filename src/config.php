<?php

    namespace Kerwin\Core;

    use Exception;

    class Config
    {        
        /**
         * 網站完整地址
         *
         * @return string
         */
        public function app_address(): string
        {
            $address = $this->app_portocol()."://".$this->app_domain()."/".$this->app_folder().$this->app_name()."/";
            return $address;
        }
        
        /**
         * 網站網域
         *
         * @return string
         */
        protected function app_domain(): string
        {
            $domain = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "localhost";
            return $domain;
        }
        
        /**
         * 網站子資料夾
         *
         * @return string
         */
        protected function app_folder(): string
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
        protected function app_name(): string
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
        protected function app_portocol(): string
        {
            $portocol = isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : "http";
            return $portocol;
        }
        
        /**
         * csrf_token
         *
         * @return string
         */
        public function csrf_token(): string
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
        public function is_debug(): string
        {
            if (!isset($_ENV['IS_DEBUG'])) {
                throw new Exception("Please defined IS_DEBUG in .env", 1);
            }
            $debug = isset($_ENV["IS_DEBUG"]) ? $_ENV["IS_DEBUG"] : "FALSE";
            return $debug;
        }
    }
    