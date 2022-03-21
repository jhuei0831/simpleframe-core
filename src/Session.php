<?php

    namespace Kerwin\Core;

    use Symfony\Component\HttpFoundation\Session\Session as SymfonySession;

    class Session extends SymfonySession
    {
        public function __construct() {
            if (isset($_ENV['APP_FOLDER'])) {
                $attribute = new \Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag($_ENV['APP_FOLDER'].'_attributes');
                $flash = new \Symfony\Component\HttpFoundation\Session\Flash\FlashBag($_ENV['APP_FOLDER'].'_flashes');
                $meta = new \Symfony\Component\HttpFoundation\Session\Storage\MetadataBag($_ENV['APP_FOLDER'].'_meta');
                $session = new \Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage([], null, $meta);
                parent::__construct($session, $attribute, $flash);
            }
            else{
                parent::__construct();
            }
        }
    }