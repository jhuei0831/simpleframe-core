<?php
    namespace Kerwin\Core;

    use Exception;
    use Kerwin\Core\Config;

    class Security
    {        
        private static $config;

        public function __construct() {
            $this->config = new Config();
        }

        /**
         * check_csrf 防止跨站請求偽造 (Cross-site request forgery)
         *
         * @param  array $data
         * @return boolean
         */
        public static function check_csrf($data)
        {
            if (empty($data['token'])) {
                if (self::$config->is_debug() === 'TRUE') {
                    throw new Exception('請進行CSRF驗證');
                }
                return false;
            }
            elseif($data['token'] != self::$config->csrf_token())
            {
                if (self::$config->is_debug() === 'TRUE') {
                    throw new Exception('禁止跨域請求');
                }
                return false;
            }
            else{
                return true;
            }    
        }
                
        /**
         * defend_filter 用 addslashes防SQL Injection、filter_var防XSS
         *
         * @param  array|string $data
         * @return array|string|object
         */
        public static function defend_filter($data)
        {
            if (is_array($data) || is_object($data)) {
                $origin_type = gettype($data);
                $data = (array) $data;
                foreach ($data as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $data[$key] = self::defend_filter($value);
                    }
                    else {
                        $data[$key]  = filter_var(addslashes($value), FILTER_SANITIZE_STRING);
                    }
                }   
                return $origin_type == 'array' ? $data : (object) $data;
            }
            else{
                $data  = addslashes($data);
                return filter_var($data, FILTER_SANITIZE_STRING);
            }
        }
    }
    