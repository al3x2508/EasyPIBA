<?php
namespace Utils;
use Gettext\Translation;

use FileReader;
use gettext_reader;

require_once(_APP_DIR_ . 'locale/gettext.php');
require_once(_APP_DIR_ . 'locale/streams.php');

class Translations extends gettext_reader
{
    private static ?Translations $instance = null;

    public function __construct()
    {
        if(file_exists(_LOCALE_DIR_ . '/' . $_SESSION['userLanguage'] . '/LC_MESSAGES/' . $_SESSION['userLanguage'] . '.mo')) {
            $streamer = new FileReader(_LOCALE_DIR_ . '/' . $_SESSION['userLanguage'] . '/LC_MESSAGES/' . $_SESSION['userLanguage'] . '.mo');
            parent::gettext_reader($streamer);
        } else {
            parent::gettext_reader(false);
        }
    }

    public static function getInstance(): Translations
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}