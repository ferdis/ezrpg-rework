<?php

//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/**
 * Top Players
 * 
 * Displays top 10 players, and can be ordered by money and level. 
 * @author Aventoro
 * @date 10 Mar 2013
 */
class Module_TopPlayers extends Base_Module {
    public function start() {

        //Require login
        requireLogin();
 
        switch ($_GET['order']) {
            case 'money':
                $order = 'money';
            	break;
            case 'level':
		// falls through

	    default:
                $order = 'level';
        }
         
        $query = $this->db->execute('SELECT `username`, `level`, `money` FROM `<ezrpg>players` ORDER BY ' . $order . ' DESC LIMIT 10');
        $members = $this->db->fetchAll($query);
 
        $this->tpl->assign('members', $members);
        $this->tpl->display('topplayers.tpl');
    }
}
