<?php
    namespace Kerwin\Core;

    use Exception;
    use Kerwin\Core\Support\Config;
    use Kerwin\Core\Contracts\Security\Filter;
    use Kerwin\Core\Contracts\Security\Verify;

    class Security implements Filter, Verify
    {        
        private $config;

        public function __construct() {
            $this->config = new Config();
        }

        /**
         * checkCSRF 防止跨站請求偽造 (Cross-site request forgery)
         *
         * @param  array $data
         * @return boolean
         */
        public function checkCSRF($data)
        {
            if (empty($data['token'])) {
                if ($this->config->isDebug() === 'TRUE') {
                    throw new Exception('請進行CSRF驗證');
                }
                return false;
            }
            elseif($data['token'] != $this->config->csrfToken())
            {
                if ($this->config->isDebug() === 'TRUE') {
                    throw new Exception('禁止跨域請求');
                }
                return false;
            }
            else{
                return true;
            }    
        }
                
        /**
         * defendFilter 用 addslashes防SQL Injection、filter_var防XSS
         *
         * @param  array|string $data
         * @return array|string|object
         */
        public function defendFilter($data)
        {
            if (is_array($data) || is_object($data)) {
                $originType = gettype($data);
                $data = (array) $data;
                foreach ($data as $key => $value) {
                    if (is_array($value) || is_object($value)) {
                        $data[$key] = $this->defendFilter($value);
                    }
                    else {
                        $data[$key]  = filter_var(addslashes($value), FILTER_SANITIZE_STRING);
                    }
                }   
                return $originType == 'array' ? $data : (object) $data;
            }
            else{
                $data  = addslashes($data);
                return filter_var($data, FILTER_SANITIZE_STRING);
            }
        }
    }
    