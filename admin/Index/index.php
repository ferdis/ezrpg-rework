<?php
defined('IN_EZRPG') or exit;

/*
  Class: Admin_Index
  Home page for the admin panel.
*/
class Admin_Index extends Base_Module
{
    /*
      Function: start
      Displays admin/index.tpl
    */
    public function start() {
		$modules = glob( ADMIN_DIR .'/*', GLOB_ONLYDIR);
		
		foreach($modules as $k => $v) {
			if (strstr($v, 'Index'))
					unset($modules[$k]);
			else
				$modules[$k] = basename($v); 
		}
		
		$dirs = '<ul>';
		foreach($modules as $dir) {
			$dirs .= '<li><a href="index.php?mod='. $dir .'">' . $dir . '</a></li>';
		}
		
		$dirs .= '</ul>';
		$this->tpl->assign('DIR', $dirs);
		
        $this->tpl->display('admin/index.tpl');
    }
}
?>