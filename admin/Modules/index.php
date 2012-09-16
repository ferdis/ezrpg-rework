<?php
defined('IN_EZRPG') or exit;

/*
  Class: Admin_Members
  Admin page for managing members
*/
class Admin_Modules extends Base_Module
{
    /*
      Function: start
      Displays the list of members or a member edit form.
    */
    public function start() {
        switch ($_GET['act']) {
            case 'view' :
                $this->view_module();
                break;
            default :
                $this->list_modules();
        }
    }
    
    private function list_modules() {
        $dir = scandir(MOD_DIR);
        $modules = array();
        foreach($dir as $item) 
            if (!preg_match('/^(\.\.|\.)/', $item)) 
                $modules[] = $item;
        
        asort($modules);
        
        // determine folder size
        $modules_full = array();
        foreach($modules as $module) {
            // Yes, I'm dev'ing on Win.
            try {
                $com_obj = new COM('scripting.filesystemobject');
                $modules_full[$module] = $com_obj->getFolder(MOD_DIR . '\\' . $module)->size;
            } catch(Exception $e) {
                // most probably invalid dir, or Linux.
                continue;
            }
        }
        
        
        foreach($modules_full as $module => $size) {
            echo $module . ' is ' . round($size / 1024, 2) . 'kB<br />';
            $class_b = get_declared_classes(); 
            require_once(MOD_DIR . '/' . $module . '/index.php');
            $class_a = array_diff($class_b, get_declared_classes());
            var_dump($class_a);
            echo '<br />';
        }
    }
    
}