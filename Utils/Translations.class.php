<?php
namespace Utils;

require_once(_APP_DIR_ . 'locale/gettext.php');
require_once(_APP_DIR_ . 'locale/streams.php');

class Translations extends \gettext_reader
{
    private static $instance = null;

    public function __construct()
    {
        $streamer = new \FileReader(_LOCALE_DIR_ . '/' . $_SESSION['userLanguage'] . '/LC_MESSAGES/' . $_SESSION['userLanguage'] . '.mo');
        parent::gettext_reader($streamer);
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}