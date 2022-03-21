<?php
    namespace Kerwin\Core;
    
    use Kerwin\Core\Session;
    use Kerwin\Core\Contracts\Message\Message as MessageInterface;
    
    class Message implements MessageInterface
    {        
        /**
         * Session instance
         *
         * @var Kerwin\Core\Session
         */
        private $session;

        public function __construct() {
            $this->session = new Session();
        }

        /**
         * 將訊息存在session中
         *
         * @param string $msg
         * @param string $type
         * @return void
         */
        public function flash(string $msg, string $type): object
        {
            $this->session->getFlashBag()->add($type, $msg);
            
            return $this;
        }

        /**
         * JS前往指定URL
         *
         * @param  string $url
         * @return void
         */
        public function redirect(string $url): void
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
        public function showConsole(string $msg): void
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>console.log("'.$msg.'");</script>';
        }

        /**
         * 跳出session flash訊息框
         *
         * @return void
         */
        public function showFlash(): void
        {
            foreach ($this->session->getFlashBag()->all() as $type => $messages) {
                foreach ($messages as $message) {
                    $this->showSwal($message, $type);
                }
            }
        }
     
        /**
         * 跳出JS對話框
         *
         * @param  string $msg
         * @return void
         */
        public function showMessage(string $msg): void
        {
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            echo '<script>alert("'.$msg.'");</script>';
        }
        
        /**
         * 跳出sweetalert2訊息框
         *
         * @param  string $msg
         * @param  string $type
         * @return void
         */
        public function showSwal(string $msg, string $type = null): void
        {
            $type = is_null($type) ? 'success' : $type;
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
            echo "<script>
                window.onload = function(){
                    Swal.fire({
                        toast: true,
                        position: 'top',
                        icon: '{$type}',
                        title: '{$msg}',
                        showConfirmButton: false,
                        showCloseButton: true,
                        heightAuto: false,
                        background: '#EBF7EE',
                        timer: 5000,
                        customClass: {
                            popup: 'alert-{$type}'
                        }
                    });
                };
                </script>";
        }
    }
    
