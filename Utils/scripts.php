<?PHP

use JShrink\Minifier;
use Utils\Util;

ini_set("zlib.output_compression", 4096);
if (!defined("_APP_NAME_")) {
	require_once(dirname(__FILE__) . '/functions.php');
}
function loadJs($js, $fromCache = true, $return = true) {
    $js = preg_replace('/\s/', '', $js);
	$cache = Util::getCache();
	$scripts = explode(',', $js);
	$md5Value = md5($js);
	$cacheFile = _APP_DIR_ . 'cache/js/' . $md5Value . '.js';
	$buffer = '';
	/** @var bool|Memcached $cache */
	if (!$cache || !($buffer = $cache->get(_CACHE_PREFIX_ . 'javaScript' . $md5Value))) {
		if (!$cache || empty($buffer)) {
			$buffer = "";
			if ($fromCache && file_exists($cacheFile)) {
			    $buffer = file_get_contents($cacheFile);
			    if($cache) $cache->set(_CACHE_PREFIX_ . 'javaScript' . $md5Value, $buffer);
				return ($return) ? $buffer : $md5Value;
			}
			if (count($scripts) > 0) {
				foreach ($scripts as $script) {
					switch ($script) {
						case 'jquery.min.js':
							$fileName = _APP_DIR_ . 'vendor/components/jquery/' . $script;
							break;
						case 'jquery-ui.min.js':
							$fileName = _APP_DIR_ . 'vendor/components/jqueryui/' . $script;
							break;
						case 'bootstrap.min.js':
							$fileName = _APP_DIR_ . 'vendor/twbs/bootstrap/dist/js/' . $script;
							break;
						default:
							if (strpos($script, 'Module/') !== 0) {
								$fileName = _APP_DIR_ . 'assets/js/' . $script;
							}
							else {
								$fileName = _APP_DIR_ . $script;
							}
							break;
					}
					if (file_exists($fileName)) {
						$buffer .= file_get_contents($fileName) . PHP_EOL;
					}
                    else debug('File not found: ' . $fileName);
				}
			}
			try {
				$buffer = Minifier::minify($buffer, array('flaggedComments' => false));
			}
			catch (Exception $e) {
			}
			if ($cache) {
				$cache->set(_CACHE_PREFIX_ . 'javaScript' . $md5Value, $buffer);
			}
		}
	}
	if ($fromCache) {
		if (!file_exists(_APP_DIR_ . 'cache/')) {
			mkdir(_APP_DIR_ . 'cache/', 0775, true);
		}
		if (!file_exists(_APP_DIR_ . 'cache/js/')) {
			mkdir(_APP_DIR_ . 'cache/js/', 0775, true);
		}
		if (file_exists($cacheFile)) {
			return ($return) ? file_get_contents($cacheFile) : $md5Value;
		}
		else {
			file_put_contents($cacheFile, $buffer);
		}
	}
	return ($return) ? $buffer : $md5Value;
}

function loadCss($css, $fromCache = true, $return = true, $saveFileName = '') {
    $css = preg_replace('/\s/', '', $css);
	$cache = Util::getCache();
	$scripts = explode(',', $css);
	$md5Value = (empty($saveFileName)) ? md5($css) : $saveFileName;
	$buffer = '';
	$cacheFile = _APP_DIR_ . 'cache/css/' . $md5Value . '.css';
	if (!$cache || !($buffer = $cache->get(_CACHE_PREFIX_ . 'css' . $md5Value))) {
		if (!$cache || empty($buffer)) {
			$buffer = "";
			if ($fromCache && file_exists($cacheFile)) {
				return ($return) ? file_get_contents($cacheFile) : true;
			}
			if (count($scripts) > 0) {
				foreach ($scripts as $script) {
					switch ($script) {
						case 'bootstrap.css':
							$fileName = _APP_DIR_ . 'vendor/twbs/bootstrap/dist/css/' . $script;
							break;
						case 'font-awesome.css':
							$fileName = _APP_DIR_ . 'vendor/components/font-awesome/css/all.min.css';
							break;
						default:
							if (strpos($script, 'Module/') !== 0) {
								$fileName = _APP_DIR_ . 'assets/css/' . $script;
							}
							else {
								$fileName = _APP_DIR_ . $script;
							}
							break;
					}
					if (file_exists($fileName)) {
						$buffer .= file_get_contents($fileName) . PHP_EOL;
					}
                    else debug('File not found: ' . $fileName);
				}
			}
			// Remove comments
			$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
			// Remove space after colons
			$buffer = str_replace(': ', ':', $buffer);
			// Remove whitespace
			$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
			// Enable GZip encoding.
			if ($cache) {
				$cache->set(_CACHE_PREFIX_ . 'css' . $md5Value, $buffer);
			}
		}
	}
	if ($fromCache) {
		if (!file_exists(_APP_DIR_ . 'cache/')) {
			mkdir(_APP_DIR_ . 'cache/', 0775, true);
		}
		if (!file_exists(_APP_DIR_ . 'cache/css/')) {
			mkdir(_APP_DIR_ . 'cache/css/', 0775, true);
		}
		if (!file_exists($cacheFile)) {
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
}
elseif (arrayKeyExists('css', $_GET)) {
	header("content-type: text/css");
	header('Cache-Control: public');
	header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
	$buffer = loadCss($_GET['css']);
	echo $buffer;
	exit;
}
elseif (isset($page_url)) {
	if ($page_url == 'main.css') {
		header("content-type: text/css");
		header('Cache-Control: public');
		header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
		$buffer = loadCss('main.css,
            fontawesome.css,
            font-montserrat.css,
            style.css', true, true, 'main');
		echo $buffer;
		exit;
	}
}