<?php

namespace {

    require_once(dirname(__DIR__).'/vendor/autoload.php');

    //Uncomment these lines if you want to redirect user to https from http and / or with www. prefix
    /*$protocol = (@$_SERVER["HTTPS"] == "on")?"https://":"http://";
    if (isset($_SERVER['HTTP_HOST']) && substr($_SERVER['HTTP_HOST'], 0, 4) !== 'www.' && substr($_SERVER['HTTP_HOST'], 0, 4) !== 'cne.') {
        header('Location: ' . $protocol . 'www.' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI']);
        exit;
    }*/

    use Module\Users\Controller;
    use Symfony\Component\Dotenv\Dotenv;
    use Utils\Translations;
    use Utils\Util;

    if (!isset($_ENV["FOLDER_URL"])) {
        $dotenv = new Dotenv();
        $dotenv->load(dirname(__DIR__).'/.env');
    }

    if (empty($_ENV['APP_DIR'])) {
        $_ENV['APP_DIR'] = dirname(__FILE__, 2).'/';
    }

    //If site is currently under maintenance and user IP is not in the decoded json from allowedIps setting in DB tell user to come back later
    if ($_ENV['MAINTENANCE']) {
        $ok = false;
        $userIp = Util::getUserIP();
        $allowedIps = json_decode(Util::getSetting('allowedIps'), true);
        foreach ($allowedIps as $allowedIp) {
            if (strpos($userIp, $allowedIp) !== false) {
                $ok = true;
            }
        }
        if (!$ok) {
            header(($_SERVER['SERVER_PROTOCOL'] == 'HTTP/1.1' ? 'HTTP/1.1'
                    : 'HTTP/1.0').' 503 Service Unavailable',
                true, 503);
            header("Retry-After: 3600");
            echo '<h1 style="margin:100px auto;text-align:center;">'
                .__('This site is currently down for maintenance and should be back soon.')
                .'</h1>';
            exit;
        }
    }

    if ($_ENV['DEVELOPER_MODE']) {
        ini_set('display_errors', '1');
    }

    if (!isset($_ENV['CACHE_PREFIX'])) {
        $_ENV['CACHE_PREFIX'] = Util::getUrlFromString($_ENV['APP_NAME']);
    }

    if (isset($_SERVER['HTTP_COOKIE'])) {
        setcookie('PHPSESSID', '', time() - 1000);
        $_COOKIE["PHPSESSID"] = null;
        unset($_COOKIE["PHPSESSID"]);
    }
    //Start user session
    if (session_status() == PHP_SESSION_NONE) {
        $sessionName = Util::getUrlFromString($_ENV['APP_NAME']).'Session';
        if (session_name() != $sessionName) {
            session_name($sessionName);
        }
        session_start();
    }
    if (php_sapi_name() != "cli"
        && strpos($_SERVER['REQUEST_URI'], '/admin') === false
        && !arrayKeyExists('admin',
            $_SESSION)
        && isset($_COOKIE['rme'.$_ENV['CACHE_PREFIX']])
        && !arrayKeyExists('user', $_SESSION)
    ) {
        $cookie = $_COOKIE['rme'.$_ENV['CACHE_PREFIX']];
        list ($token, $mac) = explode(':', $cookie);
        if ($mac === hash_hmac('sha256', $token, $_ENV['HASH_KEY'])) {
            $userId = Controller::getUserFromToken($token);
            if ($userId) {
                $_SESSION['user'] = $userId;
            }
        }
    }
    if (!arrayKeyExists('userLanguage', $_SESSION)) {
        $userLanguage = Util::getUserLanguage();
        $_SESSION['userLanguage'] = $userLanguage;
    } elseif (arrayKeyExists('language', $_COOKIE)
        && $_SESSION['userLanguage'] != $_COOKIE['language']
    ) {
        $_SESSION['userLanguage'] = $_COOKIE['language'];
    }

    if (!function_exists('hash_equals')) {
        function hash_equals($str1, $str2): bool
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
        if (isset($_GET) && is_array($_GET) && arrayKeyExists('logout', $_GET)
            || $page_url == 'logout'
        ) {
            Controller::logout();
        }
    } else {
        global $argv;
        /** @noinspection PhpUnusedLocalVariableInspection */
        $page_url = dirname(realpath($argv[0]));
    }
    function debug($message)
    {
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        file_put_contents($_ENV['APP_DIR'].'debug.log',
            '['.date('Y-m-d H:i:s').'] '.$message.PHP_EOL,
            FILE_APPEND);
    }

    /**
     * Translate a string
     */
    function __(string $string): string
    {
        $translations = Translations::getInstance();
        $numargs = func_num_args();
        if ($numargs > 1) {
            return $translations->ngettext($string, func_get_arg(1),
                intval(func_get_arg(2)));
        }
        return $translations->translate($string);
    }

    function arrayKeyExists(string $key, $array): bool
    {
        return (isset($array) && is_array($array)
            && array_key_exists($key, $array));
    }
}

namespace Utils {

    use Controller\Mail;
    use Exception;
    use finfo;
    use Model\Settings;
    use Module\Users\Controller;

    use function hash_equals;

    /**
     * Class Util
     *
     * @package Utils
     */
    class Util
    {
        /**
         * Get user language
         *
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
            if ($user) {
                $accountSettings = json_decode($user->settings, true);
                if (arrayKeyExists('language', $accountSettings)) {
                    return $accountSettings['language'];
                }
            }
            return $_ENV['DEFAULT_LANGUAGE'];
        }

        /**
         * Set language
         */
        public static function setUserLanguage(string $language): bool
        {
            $_SESSION['userLanguage'] = $language;
            return setcookie('language', $language, time() + 60 * 60 * 24 * 30);
        }

        /**
         * Get the user IP
         *
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
         */
        public static function getUrlFromString(
            string $string,
            ?string $filename = null
        ): string {
            setlocale(LC_CTYPE, "en_US.utf8");
            $characters = array(
                "_",
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
            $url = str_replace($characters, "-", strtolower($string));
            $url = iconv('utf-8', 'us-ascii//TRANSLIT', $url);
            $url = strtolower($url);
            $url = preg_replace(!$filename ? '/[^\-\_\w]+/' : '/[^\-\.\_\w]+/',
                '-', $url);
            return strtolower($url);
        }

        /**
         * Generate CSRF Token and store it in session
         */
        public static function csrfguard_generate_token(string $unique_form_name
        ): string {
            if (function_exists("hash_algos") and in_array("sha512",
                    hash_algos())
            ) {
                $token = hash("sha512", mt_rand(0, mt_getrandmax()));
            } else {
                $token = ' ';
                for ($i = 0; $i < 128; ++$i) {
                    $r = mt_rand(0, 35);
                    $c = ($r < 26) ? chr(ord('a') + $r)
                        : chr(ord('0') + $r - 26);
                    $token .= $c;
                }
            }
            $_SESSION[$unique_form_name] = $token;
            return $token;
        }

        /**
         * Generate randon token
         */
        public static function GenerateRandomToken(
            int $length = 24,
            bool $strong = false
        ): string {
            if (function_exists('openssl_random_pseudo_bytes')) {
                $token = base64_encode(openssl_random_pseudo_bytes($length,
                    $strong));
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
         */
        public static function checkFieldValue(string $key, string $value): bool
        {
            if ($key == 'CSRFToken') {
                return self::csrfguard_validate_token($_REQUEST['CSRFName'],
                    $value);
            }
            $rules = array(
                'firstname'       => "/^([ \x{00c0}-\x{01ff}a-zA-Z'\-]{2,20})+$/u",
                'lastname'        => "/^([ \x{00c0}-\x{01ff}a-zA-Z'\-]{2,20})+$/u",
                'email'           => '/^(?:[\w\d-]+\.?)+@(?:(?:[\w\d]-?)+\.)+\w{2,4}$/',
                'password'        => '/^(.){8,30}$/',
                'confirmPassword' => '/^(.){8,30}$/',
                'country'         => '/^([0-9]{1,3})$/',
                'message'         => '/^(.){10,1000}$/'
            );
            $v1 = mb_convert_encoding($value, "UTF-8", "auto");
            if (arrayKeyExists($key, $rules)
                && (!preg_match($rules[$key], $v1)
                    || strip_tags($value) != $v1)
            ) {
                return false;
            }
            return true;
        }

        /**
         * Validate CSRF Token
         */
        public static function csrfguard_validate_token(
            string $unique_form_name,
            string $token_value
        ): bool {
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
         */
        public static function sendWebmasterEmail(
            string $subject = '',
            string $message = '',
            bool $isHtml = false,
            ?array $from = null
        ) {
            self::send_email($_ENV['MAILADDRESS'], 'webmaster', $subject,
                $message, $isHtml, $from);
        }

        /**
         * Send an email
         */
        public static function send_email(
            string $email,
            string $lastname,
            string $subject = '',
            string $message = '',
            bool $isHtml = false,
            array $from = null,
            ?object $att = null,
            bool $bcc_self = false
        ): bool {
            $path = $_ENV['APP_DIR'].'assets/img/'.$_ENV['LOGO'];
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
            $html = file_get_contents($_ENV['APP_DIR'].$_ENV['TEMPLATE_DIR']
                .'email_template.html');
            $html = str_replace('{title}', $subject, $html);
            $html = str_replace('{LOGO_BASE64}', $base64, $html);
            $html = str_replace('{APP_NAME}', $_ENV['APP_NAME'], $html);
            if (!$isHtml) {
                $html = str_replace('{content}', nl2br($message), $html);
            } else {
                $html = str_replace('{content}', $message, $html);
            }
            try {
                $mail = new Mail();
                $mail->addAddress($email, $lastname);
                $mail->setFrom($_ENV['MAIL_FROM'], $_ENV['MAIL_NAME']);
                if (!is_null($from)) {
                    $mail->addCustomHeader("Sender",
                        "\"{$from['lastname']}\" <{$from['email']}>");
                    $mail->addReplyTo($from['email'], $from['lastname']);
                }
                if ($bcc_self) {
                    $mail->addBCC($_ENV['MAILADDRESS'], $_ENV['MAIL_NAME']);
                }
                $mail->Subject = '=?UTF-8?B?'
                    .base64_encode(html_entity_decode($subject)).'?=';
                $mail->msgHTML($html, dirname(__FILE__));
                if (!is_null($att)) {
                    $mail->addStringAttachment($att->body, $att->filename,
                        'base64', $att->mime);
                }
                return $mail->send();
            } catch (Exception $e) {
                return false;
            }
        }

        public static function arrayToOptions($arr): string
        {
            $html = '';
            foreach ($arr as $key => $value) {
                $html .= "<option value=\"$key\">$value</option>".PHP_EOL;
            }
            return $html;
        }

        /**
         * Get application setting
         *
         * @param $key
         *
         * @return bool|float|int|null|string
         */
        public static function getSetting($key)
        {
            $setting = new Settings();
            $setting = $setting->getOneResult('setting', $key);
            return (property_exists($setting, 'value')) ? $setting->value
                : false;
        }

        /**
         * Check image / pdf uploaded
         */
        public static function checkUploaded(string $name): array
        {
            $allowedTypes = array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'pdf' => 'application/pdf'
            );
            if (!isset($_FILES[$name]['error'])
                || is_array($_FILES[$name]['error'])
            ) {
                return array('ok' => 0, 'error' => 'File has errors');
            }
            switch ($_FILES[$name]['error']) {
                case UPLOAD_ERR_OK:
                    break;
                case UPLOAD_ERR_NO_FILE:
                    return array('ok' => 0, 'error' => 'No file uploaded');
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    return array(
                        'ok'    => 0,
                        'error' => 'File is too big (max 2MB)'
                    );
                default:
                    return array('ok' => 0, 'error' => 'File has errors');
            }
            if ($_FILES[$name]['size'] > 16777200) {
                return array('ok' => 0, 'error' => 'File is too big (max 2MB)');
            }
            if (false === $ext
                    = array_search((new finfo(FILEINFO_MIME_TYPE))->file($_FILES[$name]['tmp_name']),
                    $allowedTypes, true)
            ) {
                return array(
                    'ok'    => 0,
                    'error' => 'File not supported (supported extensions: '
                        .implode(', ',
                            array_keys($allowedTypes)).')'
                );
            }
            $filename = sprintf('%s.%s',
                sha1_file($_FILES[$name]['tmp_name']).'-'.$name, $ext);
            $contents = file_get_contents($_FILES[$name]['tmp_name']);
            if (empty($contents)) {
                return array('ok' => 0, 'error' => 'File without content');
            }

            return array(
                'ok'       => 1,
                'filename' => $filename,
                'contents' => $contents
            );
        }

        public static function ucname(
            string $string,
            array $delimiters = [' -_']
        ): string {
            $string = ucwords(strtolower($string),
                " \t\r\n\f\v".join("", $delimiters));
            foreach ($delimiters as $delimiter) {
                if (strpos($string, $delimiter) !== false) {
                    $string = implode($delimiter,
                        array_map('ucfirst', explode($delimiter, $string)));
                }
            }
            return $string;
        }

        public static function getCurrentUrl(): string
        {
            $query_position = ($_SERVER['QUERY_STRING'] != '')
                ? strpos($_SERVER['REQUEST_URI'],
                    $_SERVER['QUERY_STRING']) : false;
            $page_url = ($query_position !== false)
                ? substr($_SERVER['REQUEST_URI'], 0,
                    $query_position - 1) : $_SERVER['REQUEST_URI'];
            $page_url = preg_replace('/^('.str_replace('/', '\/',
                    $_ENV['FOLDER_URL']).')(.*)/', '$2', $page_url);

            preg_match('/^(((?!amp)[a-z]{2,3})((?=\/)\/(.*))?)$/', $page_url,
                $matches);
            if (count($matches)) {
                preg_match('/^(((?!amp|css|js|img)[a-z]{2,3})((?=\/)\/(.*))?)$/',
                    $page_url, $matches2);
                if (count($matches2)
                    && file_exists($_ENV['LOCALE_DIR'].'/'
                        .$matches2[1].'/LC_MESSAGES/i18n.mo')
                ) {
                    $_COOKIE['language'] = $matches2[1];
                    $_SESSION['userLanguage'] = $matches2[1];
                }
                $page_url = count($matches) > 3 ? trim($matches[4], '/')
                    : $matches[1];
            }

            return $page_url;
        }

        public static function getPageUrl(): string
        {
            return self::getCurrentUrl();
        }

        public static function pageNotFound(): void
        {
            header('HTTP/1.0 404 Not Found');
            $currentUrl = self::getCurrentUrl();
            preg_match('/^(((?!amp|css|js|img)[a-z]{2,3})((?=\/)\/(.*))?)$/',
                $currentUrl, $matches2);
            if (count($matches2) > 1
                && $matches2[2] == $_SESSION['userLanguage']
            ) {
                unset($_COOKIE['language']);
                setcookie('language', null, -1, '/');
                unset($_SESSION['userLanguage']);
                Translations::setLanguage($_ENV['default_language']);
            }
        }

        public static function getImageSize($img)
        {
            $img = ltrim($img, $_ENV['FOLDER_URL']);
            $cache = self::getCache();
            $md5img = md5($img);
            if ($cache
                &&
                $buffer = $cache->get($_ENV['CACHE_PREFIX'].'imgsize'.$md5img)
                    && !empty($buffer)
            ) {
                return json_decode($buffer, true);
            } else {
                if (file_exists($_ENV['APP_DIR'].'assets/'.$img)) {
                    $size = @getimagesize($_ENV['APP_DIR'].'assets/'.$img);
                    if ($size && $cache) {
                        $cache->set($_ENV['CACHE_PREFIX'].'imgsize'.$md5img,
                            json_encode($size));
                    }
                    return $size;
                }
                return false;
            }
        }

        public static function getCache()
        {
            return Redis::getInstance();
        }
    }
}
