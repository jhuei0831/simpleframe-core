<?php

    namespace Kerwin\Core\Contracts\Message;

    interface Message 
    {
        /**
         * 將訊息存在session中
         *
         * @param  string $msg
         * @param string $type
         * @return void
         */
        public function flash(string $msg, string $type): object;

        /**
         * JS前往指定URL
         *
         * @param  string $url
         * @return void
         */
        public function redirect(string $url): void;

        /**
         * 跳出JS console log  
         *
         * @param  string $msg
         * @return void
         */
        public function showConsole(string $msg): void;

        /**
         * 跳出session flash訊息框
         *
         * @return void
         */
        public function showFlash(): void;

        /**
         * 跳出JS對話框
         *
         * @param  string $msg
         * @return void
         */
        public function showMessage(string $msg): void;

        /**
         * 跳出sweetalert2訊息框
         *
         * @param  string $msg
         * @param  string $type
         * @return void
         */
        public function showSwal(string $msg, string $type = null): void;
    }