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
            $this->session->set('flash_message', $msg);
            $this->session->set('flash_message_type', $type);
            
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
            if ($this->session->has('flash_message') && $this->session->get('flash_message') != '') {
                $this->showSwal($this->session->get('flash_message'), $this->session->get('flash_message_type'));
                $this->session->remove('flash_message');
                $this->session->remove('flash_message_type');
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
                        customClass: {
                            popup: 'alert-{$type}'
                        }
                    });
                };
                </script>";
        }
    }
    