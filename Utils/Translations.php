<?php

namespace Utils;

require_once($_ENV['APP_DIR'].$_ENV['LOCALE_DIR'].'/gettext.php');
require_once($_ENV['APP_DIR'].$_ENV['LOCALE_DIR'].'/streams.php');

class Translations extends \gettext_reader
{
    private static $instance = null;
    private static $language = false;

    public function __construct()
    {
        self::$language = $_SESSION['userLanguage'];
        $streamer = new \FileReader($_ENV['LOCALE_DIR'].'/'
            .self::$language.'/LC_MESSAGES/i18n.mo');
        parent::__construct($streamer);
    }

    public static function getInstance(): ?Translations
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function setLanguage($language)
    {
        self::$language = $language;
    }
}