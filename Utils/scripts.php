<?PHP

use Utils\Util;

ini_set("zlib.output_compression", 4096);
if (!isset($_ENV['APP_NAME'])) {
    require_once(dirname(__FILE__) . '/functions.php');
}
function loadJs($js, $fromCache = true, $return = true)
{
    $cache = Util::getCache();
    $scripts = explode(',', $js);
    $md5Value = md5($js);
    $cacheFile = $_ENV['APP_DIR'] . 'cache/js/' . $md5Value . '.js';
    $buffer = '';
    /** @var bool|Memcached $cache */
    if (!$cache || !($buffer = $cache->get($_ENV['CACHE_PREFIX'] . 'javaScript' . $md5Value))) {
        if (!$cache || empty($buffer)) {
            $buffer = "";
            if ($fromCache && file_exists($cacheFile)) {
                return ($return) ? file_get_contents($cacheFile) : true;
            }
            if (count($scripts) > 0) {
                foreach ($scripts as $script) {
                    switch ($script) {
                        case 'jquery.min.js':
                            $fileName = $_ENV['APP_DIR'] . 'node_modules/jquery/dist/' . $script;
                            break;
                        case 'jquery-ui.min.js':
                            $fileName = $_ENV['APP_DIR'] . 'node_modules/jqueryui/' . $script;
                            break;
                        case 'bootstrap.bundle.min.js':
                            $fileName = $_ENV['APP_DIR'] . 'node_modules/bootstrap/dist/js/' . $script;
                            break;
                        case 'bootstrap-slider.min.js':
                            $fileName = $_ENV['APP_DIR'] . 'node_modules/bootstrap-slider/dist/' . $script;
                            break;
                        default:
                            if (strpos($script, 'Module/') !== 0) {
                                $fileName = $_ENV['APP_DIR'] . 'assets/js/' . $script;
                            } else {
                                $fileName = $_ENV['APP_DIR'] . $script;
                            }
                            break;
                    }
                    if (file_exists($fileName)) {
                        $buffer .= file_get_contents($fileName) . PHP_EOL;
                    }
                }
            }
            try {
                $buffer = \JShrink\Minifier::minify($buffer, array('flaggedComments' => false));
            } catch (\Exception $e) {
                debug($e->getMessage());
            }
            if ($cache) {
                $cache->set($_ENV['CACHE_PREFIX'] . 'javaScript' . $md5Value, $buffer);
            }
        }
    }
    if ($fromCache) {
        if (!file_exists($_ENV['APP_DIR'] . 'cache/')) {
            mkdir($_ENV['APP_DIR'] . 'cache/', 0775, true);
        }
        if (!file_exists($_ENV['APP_DIR'] . 'cache/js/')) {
            mkdir($_ENV['APP_DIR'] . 'cache/js/', 0775, true);
        }
        if (file_exists($cacheFile)) {
            return ($return) ? file_get_contents($cacheFile) : true;
        } else {
            file_put_contents($cacheFile, $buffer);
        }
    }
    return ($return) ? $buffer : true;
}

function loadCss($css, $fromCache = true, $return = true, $saveFileName = '')
{
    $cache = Util::getCache();
    $scripts = explode(',', $css);
    $md5Value = (empty($saveFileName)) ? md5($css) : $saveFileName;
    $buffer = '';
    $cacheFile = $_ENV['APP_DIR'] . 'cache/css/' . $md5Value . '.css';
    if (!$cache || !($buffer = $cache->get($_ENV['CACHE_PREFIX'] . 'css' . $md5Value))) {
        if (!$cache || empty($buffer)) {
            $buffer = "";
            if ($fromCache && file_exists($cacheFile)) {
                return ($return) ? file_get_contents($cacheFile) : true;
            }
            if (count($scripts) > 0) {
                foreach ($scripts as $script) {
                    if (strpos($script, 'Module/') !== 0) {
                        $fileName = $_ENV['APP_DIR'] . 'assets/css/' . $script;
                    } else {
                        $fileName = $_ENV['APP_DIR'] . $script;
                    }
                    if (file_exists($fileName)) {
                        $buffer .= remove_utf8_bom(file_get_contents($fileName)) . PHP_EOL;
                    }
                }
            }
            // Remove comments
            $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
            // Remove space after colons
            $buffer = str_replace(': ', ':', $buffer);
            // Remove whitespace
            $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
            preg_match('/@charset ([^;]*);/', $buffer, $output_array);
            if (count($output_array) > 1) {
                $charset = $output_array[1];
                $buffer = preg_replace('/(@charset [^;]*);/', '', $buffer);
                $buffer = '@charset ' . $charset . ';' . $buffer;
            }
            // Enable GZip encoding.
            if ($cache) {
                $cache->set($_ENV['CACHE_PREFIX'] . 'css' . $md5Value, $buffer);
            }
        }
    }
    if ($fromCache) {
        if (!file_exists($_ENV['APP_DIR'] . 'cache/')) {
            mkdir($_ENV['APP_DIR'] . 'cache/', 0775, true);
        }
        if (!file_exists($_ENV['APP_DIR'] . 'cache/css/')) {
            mkdir($_ENV['APP_DIR'] . 'cache/css/', 0775, true);
        }
        if (file_exists($cacheFile)) {
            return ($return) ? file_get_contents($cacheFile) : true;
        } else {
            file_put_contents($cacheFile, $buffer);
        }
    }
    return ($return) ? $buffer : true;
}

if (arrayKeyExists('js', $_GET)) {
    header("content-type: text/javascript");
    header('Cache-Control: public');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
    $buffer = loadJs($_GET['js']);
    echo $buffer;
    exit;
} elseif (arrayKeyExists('css', $_GET)) {
    header("content-type: text/css");
    header('Cache-Control: public');
    header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
    $buffer = loadCss($_GET['css']);
    echo $buffer;
    exit;
} elseif (isset($page_url)) {
    if ($page_url == 'main.css') {
        header("content-type: text/css");
        header('Cache-Control: public');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
        $buffer = loadCss('app.css', true, true, 'main');
        echo $buffer;
        exit;
    }
}

function remove_utf8_bom($text)
{
    $bom = pack('H*','EFBBBF');
    $text = preg_replace("/^$bom/", '', $text);
    return $text;
}