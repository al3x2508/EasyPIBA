<?php
namespace Utils;

use PHPHtmlParser\Dom;
use Utils\SabberwormCSS\Property\Selector;
use Utils\SabberwormCSS\RuleSet\DeclarationBlock;

class BuildInPageCSS {
	private $selectors;

	public function __construct($md5url) {
		$rediscache = \Utils\Redis::getInstance();
		if($rediscache) {
			$redisKey = _APP_NAME_ . 'output|' . $md5url;
			$buffer = $rediscache->get($redisKey);
			$cache = (extension_loaded('Memcached')) ? Memcached::getInstance() : false;
			$dom = new \DomDocument();
			$dom->loadHTML($buffer);
			$xpath = new \DomXPath($dom);
			$children = $dom->childNodes;
			$elementsWithIds = $xpath->query("//*[@id]");
			$elementsWithClasses = $xpath->query("//*[@class]");
			foreach($children AS $child) $this->setNodeNames($child);
			/** @var \DOMElement $element */
			foreach($elementsWithIds as $element) {
				$selector = '#' . $element->getAttribute('id');
				if(!in_array($selector, $this->selectors)) $this->selectors[] = $selector;
			}
			foreach($elementsWithClasses as $element) {
				$classes = explode(" ", $element->getAttribute('class'));
				foreach($classes AS $class) {
					$selector = '.' . $class;
					if(!in_array($selector, $this->selectors)) $this->selectors[] = $selector;
				}
			}
			sort($this->selectors);
			$md5buffer = md5(json_encode($this->selectors));
			$inPageFile = _APP_DIR_ . 'css/' . $md5buffer . '.inpage';
			$cacheKey = _APP_NAME_ . 'inpage' . $md5buffer;
			if(!file_exists($inPageFile)) {
				if(!$cache || !($inpageCss = $cache->get($cacheKey))) {
					require_once _APP_DIR_ . 'Utils/PHPHtmlParser/Autoloader.php';
					require_once _APP_DIR_ . 'Utils/stringEncode/Autoloader.php';
					$dom = new Dom();
					$dom->loadStr($buffer, []);
					$css_contents = file_get_contents(_APP_DIR_ . 'css/main.css');
					$inpageCss = '';
					$oSettings = SabberwormCSS\Settings::create()->withMultibyteSupport(false);
					$oCssParser = new SabberwormCSS\Parser($css_contents, $oSettings);
					$cssParsed = $oCssParser->parse();
					foreach($cssParsed->getContents() as $oItem) {
						if($oItem instanceof SabberwormCSS\CSSList\KeyFrame) continue;
						if($oItem instanceof SabberwormCSS\RuleSet\AtRuleSet) continue;
						if($oItem instanceof SabberwormCSS\RuleSet\DeclarationBlock) {
							$oBlock = $oItem;
							$selectors = array();
							/** @var Selector $oSelector */
							foreach($oBlock->getSelectors() as $oSelector) $selectors[] = $oSelector->getSelector();
							if(count($dom->find(implode(",", $selectors))) > 0) $inpageCss .= $oBlock->render(SabberwormCSS\OutputFormat::createCompact());
						}
						if($oItem instanceof SabberwormCSS\CSSList\AtRuleBlockList) {
							/** @var DeclarationBlock $oBlock */
							foreach($oItem->getContents() as $oBlock) {
								$selectors = array();
								foreach($oBlock->getSelectors() as $oSelector) $selectors[] = $oSelector->getSelector();
								if(count($dom->find(implode(",", $selectors))) > 0) {
									$inpageCss .= $oItem->render(SabberwormCSS\OutputFormat::createCompact());
									break;
								}
							}
						}
					}
					file_put_contents($inPageFile, $inpageCss);
					if($cache) $cache->set($cacheKey, $inpageCss);
				}
			}
			else {
				$inpageCss = file_get_contents($inPageFile);
				if($cache) $cache->set($cacheKey, $inpageCss);
			}
			if($cache) $cache->set(_APP_NAME_ . 'inpageurl' . $md5url, $cacheKey);
		}
	}

	/**
	 * @param \DOMNode $node
	 */
	private function setNodeNames($node) {
		$selector = $node->nodeName;
		if(!empty($selector)) {
			if(!in_array($selector, $this->selectors)) $this->selectors[] = $selector;
			if($node->hasChildNodes()) foreach($node->childNodes as $child) $this->setNodeNames($child);
		}
	}
}
