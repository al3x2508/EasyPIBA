<?php
namespace Utils;

use \PHPHtmlParser\Dom;
use \Sabberworm\CSS\Property\Selector;
use \Sabberworm\CSS\RuleSet\DeclarationBlock;

class BuildInPageCSS {
	private $selectors;

	public function __construct($md5url) {
		$cache = Util::getCache();
		if($cache) {
			$cacheKey = _CACHE_PREFIX_ . 'output|' . $md5url;
			$buffer = $cache->get($cacheKey);
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
			$cacheKey = _CACHE_PREFIX_ . 'inpage' . $md5buffer;
			if(!file_exists($inPageFile)) {
				if(!$cache || !($inpageCss = $cache->get($cacheKey))) {
					$dom = new Dom();
					$dom->loadStr($buffer, []);
					$css_contents = file_get_contents(_APP_DIR_ . 'cache/css/main.css');
					$inpageCss = '';
					$oSettings = \Sabberworm\CSS\Settings::create()->withMultibyteSupport(false);
					$oCssParser = new \Sabberworm\CSS\Parser($css_contents, $oSettings);
					$cssParsed = $oCssParser->parse();
					foreach($cssParsed->getContents() as $oItem) {
						if($oItem instanceof \Sabberworm\CSS\CSSList\KeyFrame) continue;
						if($oItem instanceof \Sabberworm\CSS\RuleSet\AtRuleSet) continue;
						if($oItem instanceof \Sabberworm\CSS\RuleSet\DeclarationBlock) {
							$oBlock = $oItem;
							$selectors = array();
							/** @var Selector $oSelector */
							foreach($oBlock->getSelectors() as $oSelector) $selectors[] = $oSelector->getSelector();
							if(count($dom->find(implode(",", $selectors))) > 0) $inpageCss .= $oBlock->render(\Sabberworm\CSS\OutputFormat::createCompact());
						}
						if($oItem instanceof \Sabberworm\CSS\CSSList\AtRuleBlockList) {
							/** @var DeclarationBlock $oBlock */
							foreach($oItem->getContents() as $oBlock) {
								$selectors = array();
								foreach($oBlock->getSelectors() as $oSelector) $selectors[] = $oSelector->getSelector();
								if(count($dom->find(implode(",", $selectors))) > 0) {
									$inpageCss .= $oItem->render(\Sabberworm\CSS\OutputFormat::createCompact());
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
			if($cache) $cache->set(_CACHE_PREFIX_ . 'inpageurl|' . $md5url, $cacheKey);
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
