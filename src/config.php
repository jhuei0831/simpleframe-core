<?php

    namespace Kerwin\Core;

    use Exception;

    class Config
    {        
        /**
         * 網站完整地址
         *
         * @return void
         */
        public function app_address()
        {
            $address = $this->app_portocol()."://".$this->app_domain()."/".$this->app_folder().$this->app_name()."/";
            return $address;
        }
        
        /**
         * 網站網域
         *
         * @return void
         */
        protected function app_domain()
        {
            $domain = isset($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"] : "localhost";
            return $domain;
        }
        
        /**
         * 網站子資料夾
         *
         * @return void
         */
        protected function app_folder()
        {
            $folder = isset($_ENV['APP_FOLDER']) ? $_ENV['APP_FOLDER'] : '';
            return $folder;
        }
        
        /**
         * 網站名稱
         *
         * @return void
         */
        protected function app_name()
        {
            $name = isset($_ENV['APP_NAME']) ? $_ENV['APP_NAME'] : '';
            return $name;
        }
        
        /**
         * 網站協定
         *
         * @return void
         */
        protected function app_portocol()
        {
            $portocol = isset($_SERVER["REQUEST_SCHEME"]) ? $_SERVER["REQUEST_SCHEME"] : "http";
            return $portocol;
        }

        public function csrf_token()
        {
            try {
                $token = $_SESSION['token'];
                return $token;
            } catch (Exception $e) {
                throw new Exception("Please defined SESSION token", 1);
            }
        }
        
        /**
         * 是否為偵錯模式
         *
         * @return void
         */
        public function is_debug()
        {
            $debug = isset($_SERVER["IS_DEBUG"]) ? $_SERVER["IS_DEBUG"] : "FALSE";
            return $debug;
        }
    }
    