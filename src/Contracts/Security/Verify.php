<?php

    namespace Kerwin\Core\Contracts\Security;

    interface Verify 
    {
        /**
         * checkCSRF 防止跨站請求偽造 (Cross-site request forgery)
         *
         * @param  array $data
         * @return bool
         */
        public function checkCSRF(array $data): bool;
    }