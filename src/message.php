<?php
    namespace Kerwin\Core;
    
    class Message
    {
                
        /**
         * 將訊息存在session中
         *
         * @param  string $msg
         * @param string $type
         * @return void
         */
        public function flash($msg, $type)
        {
            $_SESSION['flash_message'] = $msg;
            $_SESSION['flash_message_type'] = $type;
            
            return $this;
        }

        /**
         * JS前往指定URL
         *
         * @param  string $url
         * @return void
         */
        public function redirect($url)
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
        public function showConsole($msg)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>console.log("'.$msg.'");</script>';
        }

        /**
         * 跳出session flash訊息框
         *
         * @return void
         */
        public function showFlash()
        {
            if (isset($_SESSION['flash_message']) && $_SESSION['flash_message'] != '') {
                $this->showSwal($_SESSION['flash_message'], $_SESSION['flash_message_type']);
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
        public function showMessage($msg)
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>alert("'.$msg.'");</script>';
        }
        
        /**
         * 跳出sweetalert2訊息框
         *
         * @param  string $msg
         * @param  string $title
         * @param  string $type
         * @return void
         */
        public function showSwal($msg, $type = 'success')
        {
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
    