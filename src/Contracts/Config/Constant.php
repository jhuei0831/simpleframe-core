<?php

    namespace Kerwin\Core\Contracts\Config;

    interface Constant 
    {
        /**
         * 網站完整地址
         *
         * @return string
         */
        public function getAppAddress(): string;

        /**
         * csrf_token
         *
         * @return string
         */
        public function csrfToken(): string;

        /**
         * 是否為偵錯模式
         *
         * @return void
         */
        public function isDebug(): string;
    }