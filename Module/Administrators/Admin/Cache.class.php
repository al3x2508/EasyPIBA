<?php
namespace Module\Administrators\Admin;
use Utils\Util;

class Cache {
	/**
	 * @param $key
	 * @param $value
	 */
	public function __set($key, $value) {
		$this->$key = $value;
	}
	public function __construct() {
		$this->title = __('Cache');
		$this->h1 = __('Cache');
		$this->js = array();
		$this->css = array();
		$this->content = '';
		if(arrayKeyExists('reread', $_REQUEST)) {
			require_once _APP_DIR_ . 'admin/modules.php';
			reread();

			$this->content .= '<div class="alert alert-success" role="alert"><strong>' . __('Modules reread') . '.</strong></div>';


            $files = glob(_APP_DIR_. '/cache/css/*'); // get all file names
            foreach($files as $file){ // iterate files
                unlink($file); // delete file

            }
            $files = glob(_APP_DIR_. '/cache/js/*'); // get all file names
            foreach($files as $file){ // iterate files
                unlink($file); // delete file
            }
            $this->content .= '<div class="alert alert-success" role="alert"><strong>' . __('Remove JS and CSS from cache dir') . '.</strong></div>';

            //TODO: add apc !!!!
//            apc_clear_cache();
//            $this->content .= '<div class="alert alert-success" role="alert"><strong>' . __('Clear APC cache ') . '.</strong></div>';

            $cache = Util::getCache();
            if($cache) {
                $cache->flush();
            }
            $this->content .= '<div class="alert alert-success" role="alert"><strong>' . __('Clear all cache data') . '.</strong></div>';

        }
		$this->content .= '<form action="#" method="post"><input type="hidden" name="reread" value="1" /><input type="submit" class="btn btn-outline-primary" value="' . __('Reread modules') . '" /></form>';
	}
}