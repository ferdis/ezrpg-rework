<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
  Class: Module_Legal
  This is a skeleton module, which can be used as the starting point for coding new modules.
  See:
  <Base_Module>
*/
class Module_Legal extends Base_Module
{
    /*
      Function: start
    */
    public function start() {
		if (array_key_exists('act', $_GET)) 
			$this->tpl->display('privacy.tpl');
		else 
			$this->tpl->display('terms.tpl');
			
	}
}
?>