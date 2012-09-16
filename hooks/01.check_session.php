<?php

defined('IN_EZRPG') or exit(1);

//Add a player object hook - check the user session, priority 0
$hooks->add_hook('player', 'check_session', 0);

//Player hook to check the session and get player data
function hook_check_session($db, $config, &$tpl, $player, $args = 0) {
    
    // we follow a "guilty" until proven otherwise approach.
	$authenticated = false;
	
    if (array_key_exists('userid', $_SESSION) && array_key_exists('hash', $_SESSION)) {
        
        // The cliemt has prompted that they have authorization details.
        // Validate they they are indeed valid: this will be in the for 

        if (compareSignature($_SESSION['hash'])) {
            //Select player details
            $player = $db->fetchRow('SELECT * FROM `<ezrpg>players` WHERE `id`=?', array($_SESSION['userid']));
			
            $tpl->assign('player', $player);
            
            // Set logged-in flag
            $authenticated = true;
			
			// check the last time the user was active.
			// if they weren't active for a certain time period, prompt for password again.
			if ($_SESSION['last_active'] < (time() - $config['security']['session_timeout'])) {
				if (!in_array($_GET['mod'], array('SessionExpired', 'Logout'))) {
					$_SESSION['last_page'] = $_SERVER['REQUEST_URI'];
					header('location: index.php?mod=SessionExpired');
					exit;
				}
			} else {
				$_SESSION['last_active'] = time();
			}
			
        } else {
            session_destroy();           
        }
    }
    
	define('LOGGED_IN', $authenticated);
    $tpl->assign('LOGGED_IN', (LOGGED_IN === true) ? 'TRUE' : 'FALSE');
    
    return $player;
}
?>
