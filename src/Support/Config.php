<?php

    namespace Kerwin\Core\Support;

    use Exception;
    use Kerwin\Core\Request;
    use Kerwin\Core\Session;

    class Config
    {        
        private $session;
        private $request;

        public function __construct() {
            $this->request = Request::createFromGlobals();
            $this->session = new Session();
        }

        /**
         * 網站完整地址
         *
         * @return string
         */
        public function getAppAddress(): string
        {
            $address = $this->getAppPortocol()."://".$this->getAppDomain()."/".$this->getAppFolder()."/";
            return $address;
        }
        
        /**
         * 網站網域
         *
         * @return string
         */
        protected function getAppDomain(): string
        {
            $domain = $this->request->server->has('HTTP_HOST') ? $this->request->server->get('HTTP_HOST') : "localhost";
            return $domain;
        }
        
        /**
         * 網站子資料夾
         *
         * @return string
         */
        protected function getAppFolder(): string
        {
            if (!$this->request->server->has('APP_FOLDER')) {
                throw new Exception("Please defined APP_FOLDER in .env", 1);
            }
            $folder = $this->request->server->has('APP_FOLDER') ? $this->request->server->get('APP_FOLDER') : '';
            return $folder;
        }
        
        /**
         * 網站名稱
         *
         * @return string
         */
        protected function getAppName(): string
        {
            if (!$this->request->server->has('APP_NAME')) {
                throw new Exception("Please defined APP_NAME in .env", 1);
            }
            $name = $this->request->server->has('APP_NAME') ? $this->request->server->get('APP_NAME') : '';
            return $name;
        }
        
        /**
         * 網站協定
         *
         * @return string
         */
        protected function getAppPortocol(): string
        {
            $portocol = $this->request->server->has('REQUEST_SCHEME') ? $this->request->server->get('REQUEST_SCHEME') : 'http';;
            return $portocol;
        }
        
        /**
         * csrf_token
         *
         * @return string
         */
        public function csrfToken(): string
        {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            if (!$this->session->has('token')) {
                throw new Exception("Please defined SESSION token", 1);
            }
            $token = $this->session->get('token');
            return $token;
        }
        
        /**
         * 是否為偵錯模式
         *
         * @return void
         */
        public function isDebug(): string
        {
            if (!$this->request->server->has('APP_DEBUG')) {
                throw new Exception("Please defined APP_DEBUG in .env", 1);
            }
            $debug = $this->request->server->has("APP_DEBUG") ? $this->request->server->get("APP_DEBUG") : "FALSE";
            return $debug;
        }
    }
    