<?php

namespace Utils {

    use Controller\Mail;
    use finfo;
    use Model\Model;
    use Module\Users\Controller;
    use PHPMailer\PHPMailer\Exception;
    use function hash_equals;

    if (!defined("_FOLDER_URL_")) {
        require_once(dirname(__FILE__) . '/config.php');
    }
    //Start user session
    if (session_status() == PHP_SESSION_NONE) {
        $sessionName = Util::getUrlFromString(_APP_NAME_) . 'Session';
        if (session_name() != $sessionName) {
            session_name($sessionName);
        }
        session_start();
    }
    /**
     * Set to true if this is a development environment
     */
    define("DEVELOPER_MODE", true);
    /**
     * Set to true if site is currently under maintenance
     */
    define("MAINTENANCE", false);
    /**
     * DO NOT MODIFY Application working directory
     */
    define("_APP_DIR_", realpath(dirname(dirname(__FILE__))) . '/');
    /**
     * DO NOT MODIFY Application working directory
     */
    define("_LOCALE_DIR_", _APP_DIR_ . 'locale');
    /**
     * DO NOT MODIFY HTML Templates directory
     */
    define("_TEMPLATE_DIR_", _APP_DIR_ . 'templates/');

    define("FPDF_FONTPATH", _APP_DIR_ . 'assets/fonts/');

    /**
     * Class Util
     * @package Utils
     */
    class Util
    {
        /**
         * Autoload register classes
         * @param $class_name
         */
        public static function register($class_name)
        {
            $class_name = str_replace("\\", '/', $class_name);
            @include_once(_APP_DIR_ . $class_name . '.class.php' . '');
        }

        /**
         * Get user language
         * @param bool $user
         * @return mixed|string
         */
        public static function getUserLanguage($user = false)
        {
            if (arrayKeyExists('language', $_COOKIE)) {
                return $_COOKIE['language'];
            }
            if (!$user) {
                $user = Controller::getCurrentUser(false);
            }
            if ($user && property_exists($user, 'id')) {
                /** @noinspection PhpUndefinedFieldInspection */
                $accountSettings = json_decode($user->settings, true);
                if (arrayKeyExists('language', $accountSettings)) {
                    return $accountSettings['language'];
                }
            }
            return _DEFAULT_LANGUAGE_;
        }

        /**
         * Set language
         * @param string $language
         * @return bool
         */
        public static function setUserLanguage($language)
        {
            $_SESSION['userLanguage'] = $language;
            return setcookie('language', $language, time() + 60 * 60 * 24 * 30);
        }

        /**
         * Get the user IP
         * @return mixed
         */
        public static function getUserIP()
        {
            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                return $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                return $_SERVER['REMOTE_ADDR'];
            }
        }

        /**
         * Transform string to url
         * @param $string
         * @return string
         */
        public static function getUrlFromString($string)
        {
            setlocale(LC_ALL, "en_US.utf8");
            $characters = array(
                "-",
                "$",
                "+",
                "/",
                ":",
                ";",
                "=",
                "?",
                "@",
                " ",
                "'",
                "\"",
                "<",
                ">",
                ",",
                "[",
                "]",
                "!"
            );
            $url = str_replace($characters, "_", strtolower($string));
            $url = iconv('utf-8', 'ASCII//TRANSLIT', $url);
            $url = strtolower($url);
            $url = preg_replace('/[^_\w]+/', '_', $url);
            return strtolower($url);
        }

        /**
         * Generate CSRF Token and store it in session
         * @param $unique_form_name
         * @return string
         */
        public static function csrfguard_generate_token($unique_form_name)
        {
            if (function_exists("hash_algos") and in_array("sha512", hash_algos())) {
                $token = hash("sha512", mt_rand(0, mt_getrandmax()));
            } else {
                $token = ' ';
                for ($i = 0; $i < 128; ++$i) {
                    $r = mt_rand(0, 35);
                    $c = ($r < 26)?chr(ord('a') + $r):chr(ord('0') + $r - 26);
                    $token .= $c;
                }
            }
            $_SESSION[$unique_form_name] = $token;
            return $token;
        }

        /**
         * Generate randon token
         * @param int $length
         * @param bool $strong
         * @return string
         */
        public static function GenerateRandomToken($length = 24, $strong = false)
        {
            if (function_exists('openssl_random_pseudo_bytes')) {
                $token = base64_encode(openssl_random_pseudo_bytes($length, $strong));
                if ($strong) {
                    return strtr(substr($token, 0, $length), '+/=', '-_,');
                }
            }
            $characters = '0123456789';
            $characters .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz/+';
            $charactersLength = strlen($characters) - 1;
            $token = '';
            for ($i = 0; $i < $length; $i++) {
                $token .= $characters[mt_rand(0, $charactersLength)];
            }

            return $token;
        }

        /**
         * Validate input field
         * @param $key
         * @param $value
         * @return bool
         */
        public static function checkFieldValue($key, $value)
        {
            if ($key == 'CSRFToken') {
                return self::csrfguard_validate_token($_REQUEST['CSRFName'], $value);
            }
            $rules = array(
                'firstname' => "/^([ \x{00c0}-\x{01ff}a-zA-Z\'\-]{2,20})+$/u",
                'lastname' => "/^([ \x{00c0}-\x{01ff}a-zA-Z\'\-]{2,20})+$/u",
                'email' => '/^(?:[\w\d-]+\.?)+\@(?:(?:[\w\d]\-?)+\.)+\w{2,4}$/',
                'password' => '/^(.){8,30}$/',
                'confirmPassword' => '/^(.){8,30}$/',
                'country' => '/^([0-9]{1,3})$/',
                'message' => '/^(.){10,1000}$/'
            );
            $v1 = mb_convert_encoding($value, "UTF-8", "auto");
            if (arrayKeyExists($key, $rules) && (!preg_match($rules[$key], $v1) || strip_tags($value) != $v1)) {
                return false;
            }
            return true;
        }

        /**
         * Validate CSRF Token
         * @param $unique_form_name
         * @param $token_value
         * @return bool
         */
        public static function csrfguard_validate_token($unique_form_name, $token_value)
        {
            if (!arrayKeyExists($unique_form_name, $_SESSION)) {
                return false;
            }
            $token = $_SESSION[$unique_form_name];
            if ($token === false) {
                return false;
            } elseif (hash_equals($token, $token_value)) {
                $result = true;
            } else {
                $result = false;
            }
            unset($_SESSION[$unique_form_name]);
            return $result;
        }

        /**
         * Send webmaster email
         * @param string $subject
         * @param string $message
         * @param bool $isHtml
         * @param null $from
         */
        public static function sendWebmasterEmail($subject = '', $message = '', $isHtml = false, $from = null)
        {
            self::send_email(_MAIL_ADDRESS_, 'webmaster', $subject, $message, $isHtml, $from);
        }

        /**
         * Send an email
         * @param string $email Email recipient
         * @param string $lastname lastname recipient
         * @param string $subject Subject message
         * @param string $message Body message
         * @param bool $isHtml It's a HTML message
         * @param null|array $from Sender
         * @param null|object $att Attachment
         * @param bool $bcc_self Self BCC
         * @return bool
         */
        public static function send_email(
            $email,
            $lastname,
            $subject = '',
            $message = '',
            $isHtml = false,
            $from = null,
            $att = null,
            $bcc_self = false
        ) {
            $path = _APP_DIR_ . 'assets/img/' . _LOGO_;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $html = file_get_contents(_TEMPLATE_DIR_ . 'email_template.html');
            $html = str_replace('{title}', $subject, $html);
            $html = str_replace('{LOGO_BASE64}', $base64, $html);
            $html = str_replace('{APP_NAME}', _APP_NAME_, $html);
            if (!$isHtml) {
                $html = str_replace('{content}', nl2br($message), $html);
            } else {
                $html = str_replace('{content}', $message, $html);
            }
            $mail = new Mail();
            try {
                $mail->addAddress($email, $lastname);
                $mail->setFrom(_MAIL_FROM_, _MAIL_NAME_);
            } catch (Exception $e) {
                debug($e->getMessage());
            }
            if (!is_null($from)) {
                $mail->addCustomHeader("Sender", "\"{$from['lastname']}\" <{$from['email']}>");
                try {
                    $mail->addReplyTo($from['email'], $from['lastname']);
                } catch (Exception $e) {
                    debug($e->getMessage());
                }
            }
            if ($bcc_self) {
                try {
                    $mail->addBCC(_MAIL_ADDRESS_, _MAIL_NAME_);
                } catch (Exception $e) {
                    debug($e->getMessage());
                }
            }
            $mail->Subject = '=?UTF-8?B?' . base64_encode(html_entity_decode($subject)) . '?=';
            $mail->msgHTML($html, dirname(__FILE__));
            if (!is_null($att)) {
                try {
                    $mail->addStringAttachment($att->body, $att->filename, 'base64', $att->mime);
                } catch (Exception $e) {
                    debug($e->getMessage());
                }
            }
            try {
                $send = $mail->send();
                if (!$send) {
                    debug($mail->ErrorInfo);
                }
                return $send;
            } catch (Exception $e) {
                debug($mail->ErrorInfo);
                debug($e->getMessage());
                return false;
            }
        }

        /**
         * @param $arr
         * @return string
         */
        public static function arrayToOptions($arr, $removeEmpty = false)
        {
            if ($removeEmpty) {
                if (arrayKeyExists(-1, $arr)) {
                    unset($arr[-1]);
                }
                if ($removeEmpty !== '-1') {
                    if (arrayKeyExists(0, $arr)) {
                        unset($arr[0]);
                    }
                }
            }
            $html = '';
            foreach ($arr as $key => $value) {
                $additionalAtts = '';
                if (is_array($value)) {
                    $text = $value[0];
                    if (arrayKeyExists(1, $value)) {
                        if ($value[1] == 'selected') {
                            $additionalAtts = ' selected';
                        } else {
                            $additionalAtts = ' ' . $value[1];
                        }
                    }
                } else {
                    $text = $value;
                }
                /** @var string $additionalAtts */
                $html .= "<option value=\"{$key}\"{$additionalAtts}>{$text}</option>" . PHP_EOL;
            }
            return $html;
        }

        /**
         * Get application setting
         * @param $key
         * @return bool|float|int|null|string
         */
        public static function getSetting($key)
        {
            $setting = new Model('settings');
            $setting = $setting->getOneResult('setting', $key);
            return ($setting && property_exists($setting, 'value'))?$setting->value:false;
        }

        /**
         * Set application setting
         * @param $key
         * @param $value
         * @return bool|float|int|null|string
         */
        public static function setSetting($key, $value)
        {
            $setting = new Model('settings');
            $setting = $setting->getOneResult('setting', $key);
            if ($setting) {
                if ($setting->value != $value) {
                    $setting = new Model('settings');
                    $paramType = 'ss';
                    $data = array(&$paramType, $value, $key);
                    $setting->runQuery('UPDATE settings SET value = ? WHERE setting = ?', $data, false);
                }
                return true;
            } else {
                $setting = new Model('settings');
                $setting->setting = $key;
                $setting->value = $value;
                $create = $setting->create();
                return is_a($create, 'Model\Model');
            }
        }


        /**
         * Check image / pdf uploaded
         * @param $name
         * @return array
         */
        public static function checkUploaded($name)
        {
            $allowedTypes = array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'pdf' => 'application/pdf'
            );
            if (!isset($_FILES[$name]['error']) || is_array($_FILES[$name]['error'])) {
                return array('ok' => 0, 'error' => 'File has errors');
            }
            switch ($_FILES[$name]['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    return array('ok' => 0, 'error' => 'No file uploaded');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    return array('ok' => 0, 'error' => 'File is too big (max 2MB)');
                default:
                    return array('ok' => 0, 'error' => 'File has errors');
            }
            if ($_FILES[$name]['size'] > 16777200) {
                return array('ok' => 0, 'error' => 'File is too big (max 2MB)');
            }
            $file_info = new finfo(FILEINFO_MIME_TYPE);
            if (false === $ext = array_search($file_info->file($_FILES[$name]['tmp_name']), $allowedTypes, true)) {
                return array(
                    'ok' => 0,
                    'error' => 'File not supported (supported extensions: ' . implode(', ',
                            array_keys($allowedTypes)) . ')'
                );
            }
            $filename = sprintf('%s.%s', sha1_file($_FILES[$name]['tmp_name']) . '-' . $name, $ext);
            $contents = file_get_contents($_FILES[$name]['tmp_name']);
            if (empty($contents)) {
                return array('ok' => 0, 'error' => 'File without content');
            }

            return array('ok' => 1, 'filename' => $filename, 'contents' => $contents);
        }

        public static function ucname($string, $delimiters = array(' -'))
        {
            $string = ucwords(strtolower($string));
            foreach ($delimiters as $delimiter) {
                if (strpos($string, $delimiter) !== false) {
                    $string = implode($delimiter, array_map('ucfirst', explode($delimiter, $string)));
                }
            }
            return $string;
        }

        /**
         * @return string
         */
        public static function getCurrentUrl()
        {
            $query_position = ($_SERVER['QUERY_STRING'] != '')?strpos($_SERVER['REQUEST_URI'],
                $_SERVER['QUERY_STRING']):false;
            $page_url = ($query_position !== false)?substr($_SERVER['REQUEST_URI'], 0,
                $query_position - 1):$_SERVER['REQUEST_URI'];
            $page_url = preg_replace('/^(' . str_replace('/', '\/', _FOLDER_URL_) . ')(.*)/', '$2', $page_url);

            preg_match('/^((?!amp)[a-z]{2,3})$/', $page_url, $matches);
            if (count($matches)) {
                if(!in_array($matches[1], array('js', 'css'))) {
                    $_COOKIE['language'] = $matches[1];
                    $_SESSION['userLanguage'] = $matches[1];
                }
                $page_url = '';
            }
            preg_match('/^((?!amp)[a-z]{2,3})(?=\/)\/(.*)$/', $page_url, $matches);
            if (count($matches)) {
                if(!in_array($matches[1], array('js', 'css'))) {
                    $_COOKIE['language'] = $matches[1];
                    $_SESSION['userLanguage'] = $matches[1];
                }
                $page_url = trim($matches[2], '/');
            }

            return $page_url;
        }

        public static function getImageSize($img)
        {
            $img = ltrim($img, _FOLDER_URL_);
            $cache = self::getCache();
            $md5img = md5($img);
            if ($cache && $buffer = $cache->get(_CACHE_PREFIX_ . 'imgsize' . $md5img) && !empty($buffer)) {
                return json_decode($buffer, true);
            } else {
                $size = file_exists(_APP_DIR_ . $img)?getimagesize(_APP_DIR_ . $img):false;
                if ($size && $cache) {
                    $cache->set(_CACHE_PREFIX_ . 'imgsize' . $md5img, json_encode($size));
                }
                return $size;
            }
        }

        /**
         * @return false|Memcached|Redis
         */
        public static function getCache()
        {
            return false;
			$cache = Redis::getInstance();
            /*$cache = Memcached::getInstance();
            if (!$cache || ($cache && !$cache->isConnected())) {
                $cache = Redis::getInstance();
            }*/
            return $cache;
        }
    }
}

namespace {

    require dirname(__DIR__) . '/vendor/autoload.php';
    //Uncomment these lines if you want to redirect user to https from http and / or with www. prefix
    /*$protocol = (@$_SERVER["HTTPS"] == "on")?"https://":"http://";
    if (isset($_SERVER['HTTP_HOST']) && substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.' && substr($_SERVER['HTTP_HOST'], 0, 4) !== 'cne.') {
        header('Location: ' . $protocol . 'www.' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']);
        exit;
    }*/

    use Gettext\Loader\MoLoader;
    use Module\Users\Controller;
    use Utils\Translations;
    use Utils\Util;

    //If site is currently under maintenance and user IP is not in the decoded json from allowedIps setting in DB tell user to come back later
    if (MAINTENANCE) {
        $ok = false;
        $userIp = Util::getUserIP();
        $allowedIps = json_decode(Util::getSetting('allowedIps'), true);
        foreach ($allowedIps as $allowedIp) {
            if (strpos($userIp, $allowedIp) !== false) {
                $ok = true;
            }
        }
        if (!$ok) {
            header(($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1'?'HTTP/1.1':'HTTP/1.0') . ' 503 Service Unavailable', true,
                503);
            header("Retry-After: 3600");
            echo '<h1 style="margin:100px auto;text-align:center;">' . __('This site is currently down for maintenance and should be back soon.') . '</h1>';
            exit;
        }
    }

    if (DEVELOPER_MODE) {
        ini_set('display_errors', '1');
    }

    if (!defined('_CACHE_PREFIX_')) {
        define("_CACHE_PREFIX_", Util::getUrlFromString(_APP_NAME_));
    }

    //Autoload classes (PSR-0)
    if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
        spl_autoload_register('\Utils\Util::register', true, true);
    } else {
        spl_autoload_register('\Utils\Util::register');
    }
    if (isset($_SERVER['HTTP_COOKIE'])) {
        setcookie('PHPSESSID', '', time() - 1000);
        $_COOKIE["PHPSESSID"] = null;
        unset($_COOKIE["PHPSESSID"]);
    }
    if (php_sapi_name() != "cli" && strpos($_SERVER['REQUEST_URI'], '/admin') === false && !arrayKeyExists('admin',
            $_SESSION) && isset($_COOKIE['rme' . _CACHE_PREFIX_]) && !arrayKeyExists('user', $_SESSION)
    ) {
        $cookie = $_COOKIE['rme' . _CACHE_PREFIX_];
        list ($token, $mac) = explode(':', $cookie);
        if ($mac === hash_hmac('sha256', $token, _HASH_KEY_)) {
            $userId = Controller::getUserFromToken($token);
            if ($userId) {
                $_SESSION['user'] = $userId;
            }
        }
    }
    if (!arrayKeyExists('userLanguage', $_SESSION)) {
        $userLanguage = Util::getUserLanguage();
        $_SESSION['userLanguage'] = $userLanguage;
    } else {
        if (arrayKeyExists('language', $_COOKIE) && $_SESSION['userLanguage'] != $_COOKIE['language']) {
            $_SESSION['userLanguage'] = $_COOKIE['language'];
        }
    }

    if (!function_exists('hash_equals')) {
        function hash_equals($str1, $str2)
        {
            if (strlen($str1) != strlen($str2)) {
                return false;
            } else {
                $res = $str1 ^ $str2;
                $ret = 0;
                for ($i = strlen($res) - 1; $i >= 0; $i--) {
                    $ret |= ord($res[$i]);
                }
                return !$ret;
            }
        }
    }
    if (php_sapi_name() != "cli") {
        $page_url = Util::getCurrentUrl();
        if (isset($_GET) && is_array($_GET) && arrayKeyExists('logout', $_GET) || $page_url == 'logout') {
            Controller::logout();
        }
    } else {
        global $argv;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $page_url = dirname(realpath($argv[0]));
    }

    /**
     * Translate a string
     * @param $string
     * @return mixed
     */
    function __($string)
    {
        /** @var MoLoader $translations */
        /*$translations = Translations::getInstance();
        return $translations->loadString($string);*/
        $translations = Translations::getInstance();
        $numargs = func_num_args();
        if ($numargs > 1) {
            return $translations->ngettext(func_get_arg(0), func_get_arg(1), intval(func_get_arg(2)));
        }
        return $translations->translate($string);
    }

    function arrayKeyExists($key, $array)
    {
        return (isset($array) && is_array($array) && array_key_exists($key, $array));
    }

    function debug($message)
    {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        file_put_contents(_APP_DIR_ . 'debug.log', $message . PHP_EOL, FILE_APPEND);
    }

    $enqScripts = array();
    $enqStyles = array();
    function enqueue_script($script)
    {
        global $enqScripts;
        $enqScripts[] = $script;
    }

    function enqueue_style($script)
    {
        global $enqStyles;
        $enqStyles[] = $script;
    }
}