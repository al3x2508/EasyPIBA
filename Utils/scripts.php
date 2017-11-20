<?PHP
ini_set("zlib.output_compression", 4096);
if(!defined("_APP_NAME_")) require_once(dirname(__FILE__) . '/functions.php');
function loadJs($js, $fromCache = true, $return = true) {
	$cache = Utils\getCache();
	$scripts=explode(',', $js);
	$md5Value = md5($js);
	if($fromCache && file_exists(dirname(dirname(__FILE__)) . '/js/' . $md5Value . '.js')) return ($return)?file_get_contents(dirname(dirname(__FILE__)) . '/js/' . $md5Value . '.js'):true;
	$buffer = '';
	/** @var bool|Memcached $cache */
	if(!$cache || !($buffer = $cache->get('javaScript' . $md5Value))) {
		if(!$cache || $cache->getResultCode() == Memcached::RES_NOTFOUND) {
			$buffer = "";
			if(count($scripts) > 0) {
				foreach($scripts as $script) {
					if(file_exists(dirname(dirname(__FILE__)) . '/js/' . $script)) $buffer .= file_get_contents(dirname(dirname(__FILE__)) . '/js/' . $script) . PHP_EOL;
				}
			}
			require_once(dirname(dirname(__FILE__)) . '/Utils/JShrink/Minifier.class.php');
			$buffer = \Utils\JShrink\Minifier::minify($buffer, array('flaggedComments' => false));
			if($cache) $cache->set('javaScript' . $md5Value, $buffer);
		}
	}
	if($fromCache || !file_exists(dirname(dirname(__FILE__)) . '/js/' . $md5Value . '.js')) file_put_contents(dirname(dirname(__FILE__)) . '/js/' . $md5Value . '.js', $buffer);
	return ($return)?$buffer:true;
}
function loadCss($css, $fromCache = true, $return = true, $filename = '') {
	$cache = Utils\getCache();
	$scripts=explode(',', $css);
	$md5Value = (empty($filename))?md5($css):$filename;
	if($fromCache && file_exists(dirname(dirname(__FILE__)) . '/css/' . $md5Value . '.css')) return ($return)?file_get_contents(dirname(dirname(__FILE__)) . '/css/' . $md5Value . '.css'):true;
	$buffer = '';
	if(!$cache || !($buffer = $cache->get('css' . $md5Value))) {
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
			if($cache) $cache->set('css' . $md5Value, $buffer);
		}
	}
	if($fromCache || !file_exists(dirname(dirname(__FILE__)) . '/css/' . $md5Value . '.css')) file_put_contents(dirname(dirname(__FILE__)) . '/css/' . $md5Value . '.css', $buffer);
	return ($return)?$buffer:true;
}
if(array_key_exists('js', $_GET)) {
	header("content-type: text/javascript");
	header('Cache-Control: public');
	$buffer = loadJs($_GET['js']);
	echo $buffer;
	exit;
}
elseif(array_key_exists('css', $_GET)) {
	header("content-type: text/css");
	header('Cache-Control: public');
	$buffer = loadCss($_GET['css']);
	echo $buffer;
	exit;
}
elseif(isset($page_url) && $page_url == 'css/main.css') {
	header("content-type: text/css");
	header('Cache-Control: public');
	$buffer = loadCss('font-montserrat.css,font-awesome.css,bootstrap.css,_main.css', true, true, 'main.css');
	echo $buffer;
	exit;
}