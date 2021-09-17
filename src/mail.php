<?php
    namespace Kerwin\Core;

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    class Mail
    {        
        /**
         * 發送信件
         *
         * @param  string $subject
         * @param  string $message
         * @param  string $email
         * @param  string $name
         * @return void
         */
        public static function send($subject, $message, $email, $name)
        {
            try {
                $mail= new PHPMailer();
                $mail->IsSMTP();
                $mail->SMTPAuth = true;
                $mail->Host = $_ENV['MAIL_HOST'];
                $mail->Port = $_ENV['MAIL_PORT'];
                $mail->CharSet = "utf-8";
                $mail->Username = $_ENV['MAIL_USERNAME']; //設定驗證帳號
                $mail->Password = $_ENV['MAIL_PASSWORD']; //設定驗證密碼
                $mail->From = $_ENV['MAIL_FROM_ADDRESS'];
                $mail->FromName = $_ENV['MAIL_FROM_NAME'];
                $mail->Subject = $subject;
                $mail->WordWrap  = 50;
                $mail->Body = $message;
                $mail->IsHTML(true);
                $mail->AddAddress($email, $name);
                return $mail->Send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$e->getMessage()}";
            }
            
        }
    }
    