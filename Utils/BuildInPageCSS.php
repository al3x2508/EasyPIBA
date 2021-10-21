<?php

use PHPHtmlParser\Dom;
use Sabberworm\CSS\CSSList\AtRuleBlockList;
use Sabberworm\CSS\CSSList\KeyFrame;
use Sabberworm\CSS\OutputFormat;
use Sabberworm\CSS\Parser;
use Sabberworm\CSS\Parsing\OutputException;
use Sabberworm\CSS\Property\Selector;
use Sabberworm\CSS\RuleSet\AtRuleSet;
use Sabberworm\CSS\RuleSet\DeclarationBlock;
use Sabberworm\CSS\Settings;

class BuildInPageCSS
{
    private $selectors;

    public function __construct($md5url)
    {
        $cache = Util::getCache();
        if ($cache) {
            $cacheKey = _CACHE_PREFIX_ . 'output|' . $md5url;
            $buffer = $cache->get($cacheKey);
            $dom = new DomDocument();
            $dom->loadHTML($buffer);
            $xpath = new DomXPath($dom);
            $children = $dom->childNodes;
            $elementsWithIds = $xpath->query("//*[@id]");
            $elementsWithClasses = $xpath->query("//*[@class]");
            foreach ($children as $child) {
                $this->setNodeNames($child);
            }
            /** @var DOMElement $element */
            foreach ($elementsWithIds as $element) {
                $selector = '#' . $element->getAttribute('id');
                if (!in_array($selector, $this->selectors)) {
                    $this->selectors[] = $selector;
                }
            }
            foreach ($elementsWithClasses as $element) {
                $classes = explode(" ", $element->getAttribute('class'));
                foreach ($classes as $class) {
                    $selector = '.' . $class;
                    if (!in_array($selector, $this->selectors)) {
                        $this->selectors[] = $selector;
                    }
                }
            }
            sort($this->selectors);
            $md5buffer = md5(json_encode($this->selectors));
            $inPageFile = _APP_DIR_ . 'css/' . $md5buffer . '.inpage';
            $cacheKey = _CACHE_PREFIX_ . 'inpage' . $md5buffer;
            if (!file_exists($inPageFile)) {
                if (!$cache || !($inpageCss = $cache->get($cacheKey))) {
                    $dom = new Dom();
                    $dom->loadStr($buffer, []);
                    $css_contents = file_get_contents(_APP_DIR_ . 'cache/css/main.css');
                    $inpageCss = '';
                    $oSettings = Settings::create()->withMultibyteSupport(false);
                    $oCssParser = new Parser($css_contents, $oSettings);
                    $cssParsed = $oCssParser->parse();
                    foreach ($cssParsed->getContents() as $oItem) {
                        if ($oItem instanceof KeyFrame) {
                            continue;
                        }
                        if ($oItem instanceof AtRuleSet) {
                            continue;
                        }
                        if ($oItem instanceof DeclarationBlock) {
                            $oBlock = $oItem;
                            $selectors = array();
                            /** @var Selector $oSelector */
                            foreach ($oBlock->getSelectors() as $oSelector) {
                                $selectors[] = $oSelector->getSelector();
                            }
                            if (count($dom->find(implode(",", $selectors))) > 0) {
                                try {
                                    $inpageCss .= $oBlock->render(OutputFormat::createCompact());
                                } catch (OutputException $e) {
                                }
                            }
                        }
                        if ($oItem instanceof AtRuleBlockList) {
                            /** @var DeclarationBlock $oBlock */
                            foreach ($oItem->getContents() as $oBlock) {
                                $selectors = array();
                                foreach ($oBlock->getSelectors() as $oSelector) {
                                    $selectors[] = $oSelector->getSelector();
                                }
                                if (count($dom->find(implode(",", $selectors))) > 0) {
                                    try {
                                        $inpageCss .= $oItem->render(OutputFormat::createCompact());
                                    } catch (OutputException $e) {
                                    }
                                    break;
                                }
                            }
                        }
                    }
                    file_put_contents($inPageFile, $inpageCss);
                    if ($cache) {
                        $cache->set($cacheKey, $inpageCss);
                    }
                }
            } else {
                $inpageCss = file_get_contents($inPageFile);
                if ($cache) {
                    $cache->set($cacheKey, $inpageCss);
                }
            }
            if ($cache) {
                $cache->set(_CACHE_PREFIX_ . 'inpageurl|' . $md5url, $cacheKey);
            }
        }
    }

    /**
     * @param DOMNode $node
     */
    private function setNodeNames($node)
    {
        $selector = $node->nodeName;
        if (!empty($selector)) {
            if (!in_array($selector, $this->selectors)) {
                $this->selectors[] = $selector;
            }
            if ($node->hasChildNodes()) {
                foreach ($node->childNodes as $child) {
                    $this->setNodeNames($child);
                }
            }
        }
    }
}
