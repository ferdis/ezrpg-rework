<?php
defined('IN_EZRPG') or exit;

if (defined('IN_ADMIN'))
	$hooks->add_hook('admin_header', 'online_players');
else
	$hooks->add_hook('header', 'online_players');

function hook_online_players(&$db, $config, &$tpl, &$player, $args = 0) {
	$query = $db->fetchRow('SELECT COUNT(`id`) AS `count` FROM `<ezrpg>players` WHERE `last_active`>?', array(
		time() - (60 * 5)
	));
	$tpl->assign('ONLINE', $query->count);
	
	return $args;
}
?>