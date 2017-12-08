<?PHP
ini_set("zlib.output_compression", 4096);
if(!defined("_APP_NAME_")) require_once(dirname(__FILE__) . '/functions.php');
function loadJs($js, $fromCache = true, $return = true) {
	$cache = (extension_loaded('Memcached'))?\Utils\Memcached::getInstance():false;
	$scripts=explode(',', $js);
	$md5Value = md5($js);
	$buffer = '';
	/** @var bool|Memcached $cache */
	if(!$cache || !($buffer = $cache->get(_CACHE_PREFIX_ . 'javaScript' . $md5Value))) {
		if(!$cache || $cache->getResultCode() == Memcached::RES_NOTFOUND) {
			$buffer = "";
			if(count($scripts) > 0) {
				foreach($scripts as $script) {
					if(file_exists(dirname(dirname(__FILE__)) . '/js/' . $script)) $buffer .= file_get_contents(dirname(dirname(__FILE__)) . '/js/' . $script) . PHP_EOL;
				}
			}
			require_once(dirname(dirname(__FILE__)) . '/Utils/JShrink/Minifier.class.php');
			$buffer = \Utils\JShrink\Minifier::minify($buffer, array('flaggedComments' => false));
			if($cache) $cache->set(_CACHE_PREFIX_ . 'javaScript' . $md5Value, $buffer);
		}
	}
	if($fromCache) {
		if(file_exists(dirname(dirname(__FILE__)) . '/js/' . $md5Value . '.js')) return ($return)?file_get_contents(dirname(dirname(__FILE__)) . '/js/' . $md5Value . '.js'):true;
		else file_put_contents(dirname(dirname(__FILE__)) . '/js/' . $md5Value . '.js', $buffer);
	}
	return ($return)?$buffer:true;
}
function loadCss($css, $fromCache = true, $return = true, $filename = '') {
	$cache = (extension_loaded('Memcached'))?\Utils\Memcached::getInstance():false;
	$scripts=explode(',', $css);
	$md5Value = (empty($filename))?md5($css):$filename;
	$buffer = '';
	if(!$cache || !($buffer = $cache->get(_CACHE_PREFIX_ . 'css' . $md5Value))) {
		if(!$cache || $cache->getResultCode() == Memcached::RES_NOTFOUND) {
			$buffer = "";
			if(count($scripts) > 0) {
				foreach($scripts as $script) {
					if(file_exists(dirname(dirname(__FILE__)) . '/css/' . $script)) $buffer .= file_get_contents(dirname(dirname(__FILE__)) . '/css/' . $script) . PHP_EOL;
				}
			}
			// Remove comments
			$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
			// Remove space after colons
			$buffer = str_replace(': ', ':', $buffer);
			// Remove whitespace
			$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
			// Enable GZip encoding.
			if($cache) $cache->set(_CACHE_PREFIX_ . 'css' . $md5Value, $buffer);
		}
	}
	if($fromCache) {
		if(file_exists(dirname(dirname(__FILE__)) . '/css/' . $md5Value . '.css')) return ($return)?file_get_contents(dirname(dirname(__FILE__)) . '/css/' . $md5Value . '.css'):true;
		else file_put_contents(dirname(dirname(__FILE__)) . '/css/' . $md5Value . '.css', $buffer);
	}
	return ($return)?$buffer:true;
}
if(array_key_exists('js', $_GET)) {
	header("content-type: text/javascript");
	header('Cache-Control: public');
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
	$buffer = loadJs($_GET['js']);
	echo $buffer;
	exit;
}
elseif(array_key_exists('css', $_GET)) {
	header("content-type: text/css");
	header('Cache-Control: public');
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
	$buffer = loadCss($_GET['css']);
	echo $buffer;
	exit;
}
elseif(isset($page_url) && $page_url == 'main.css') {
	header("content-type: text/css");
	header('Cache-Control: public');
	header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000));
	$buffer = loadCss('bootstrap.css,font-montserrat.css,font-awesome.css,_main.css', true, true, 'main');
	echo $buffer;
	exit;
}