<?php

/**
 * @property string h1
 * @property string title
 * @property array js
 * @property array css
 * @property string APP_NAME
 * Class Template
 */
class Template {
	/**
	 * @var
	 */
	protected $filename;
	/**
	 * @var array
	 */
	protected $values = array();
	/**
	 * @var string
	 */
	private $template = '';
	/**
	 * @var string
	 */
	private $header = '';
	/**
	 * @var string
	 */
	public $content = '';
	/**
	 * @var string
	 */
	private $footer = '';
	/**
	 * @var string
	 */
	private $adminName = '';

	/**
	 * Template constructor.
	 * @param $filename
	 * @param string $adminName
	 */
	public function __construct($filename, $adminName = '') {
		$this->filename = $filename;
		$this->adminName = $adminName;
	}

	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}

	/**
	 *
	 */
	public function loadTemplate() {
		if(!file_exists($this->filename) || is_dir($this->filename)) die("Error loading template ({$this->filename}).");
		$this->template = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . $this->filename);
		$this->APP_NAME = _APP_NAME_;
		$this->logout = __('Logout');
		foreach(get_object_vars($this) AS $key => $value) {
			if(!is_object($value) && !is_array($value)) {
				$change = "{" . $key . "}";
				$this->template = str_replace($change, $value, $this->template);
			}
		}
		if(property_exists($this, 'css') && count($this->css)) {
			$replacement = '';
			foreach($this->css AS $script) $replacement .= '		<link rel="stylesheet" href="' . $script . '" />' . PHP_EOL;
			$pos = strripos($this->template, "\t</body>");
			$this->template = substr_replace($this->template, $replacement, $pos, 0);
		}
		if(property_exists($this, 'js') && count($this->js)) {
			$replacement = '';
			foreach($this->js AS $script) $replacement .= '		<script type="text/javascript" src="' . $script . '"></script>' . PHP_EOL;
			$pos = strripos($this->template, "\t</body>");
			$this->template = substr_replace($this->template, $replacement, $pos, 0);
		}
		$adminMenu = new AdminMenu();
		$replace = '<nav id="mainMenu">';
		$replacement = '<nav id="mainMenu">' . $adminMenu->getLinks();
		$this->template = str_replace($replace, $replacement, $this->template);
		$this->header = substr($this->template, 0, strripos($this->template, "\t</head>") + 10);
		$this->content = substr($this->template, strlen($this->header), strlen($this->template) - strlen($this->header));
		$this->footer = substr($this->content, strripos($this->content, "\t\t</article>"), strlen($this->content) - strripos($this->content, "\t\t</article>"));
		$this->content = substr($this->content, 0, $this->content - strlen($this->footer));
	}

	/**
	 *
	 */
	public function output() {
		$this->loadTemplate();
		echo $this->header;
		echo $this->content;
		echo $this->footer;
	}
}