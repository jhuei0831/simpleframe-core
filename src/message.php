<?php
    namespace Kerwin\Core;
    
    class Message
    {
                
        /**
         * 將訊息存在session中
         *
         * @param  mixed $msg
         * @param  mixed $type
         * @return void
         */
        public static function flash($msg, $type)
        {
            $_SESSION['flash_message'] = $msg;
            $_SESSION['flash_message_type'] = $type;
        }

        /**
         * JS前往指定URL
         *
         * @param  string $url
         * @return void
         */
        public static function redirect($url)
        {
            echo '<script>window.location="'.$url.'";</script>';
            exit(0);
        }
   
        /**
         * 跳出JS console log  
         *
         * @param  string $msg
         * @return void
         */
        public static function show_console($msg)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>console.log("'.$msg.'");</script>';
        }

        /**
         * 跳出session flash訊息框
         *
         * @return void
         */
        public static function show_flash()
        {
            if (isset($_SESSION['flash_message']) && $_SESSION['flash_message'] != '') {
                self::show_swal($_SESSION['flash_message'], $_SESSION['flash_message_type']);
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_message_type']);
            }
        }
     
        /**
         * 跳出JS對話框
         *
         * @param  string $msg
         * @return void
         */
        public static function show_message($msg)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>alert("'.$msg.'");</script>';
        }
        
        /**
         * 跳出sweetalert2訊息框
         *
         * @param  mixed $msg
         * @param  mixed $title
         * @param  mixed $type
         * @return void
         */
        public static function show_swal($msg, $type = 'success')
        {
            // echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>';
            echo "<script>
                window.onload = function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: '{$type}',
                        title: '{$msg}',
                        showConfirmButton: false,
                        confirmButtonColor: 'LightSeaGreen',
                        background: '#fcf8eb',
                        timer: 5000,
                        timerProgressBar: true,
                    });
                };
                </script>";
        }
    }
    