<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
  Class: Module_Login
  This module handles user authentication.
*/
class Module_Login extends Base_Module
{
    /*
      Function: start
      Checks player details to login the player.
	
      If successful, a new session is generated and the user is redirected to the game.
	
      On failure, session data is cleared and the user is redirected back to the login page.
    */
    public function start() {
        $fail = '';
		$warn = '';

        if (empty($_POST['username']) || empty($_POST['password'])) {
            $warn = 'Please enter your username and password!';
		} else {
			$player = $this->validate();
			if ($player === false)
				$fail = 'Please check your username/password!';
		}
		
        if (empty($fail) && empty($warn)) {
            global $hooks;
            
            //Run login hook
            $player = $hooks->run_hooks('login', $player);
            
            $query = $this->db->execute('UPDATE `<ezrpg>players` SET `last_login`=? WHERE `id`=?', array(time(), $player->id));
            
            $_SESSION['userid'] = $player->id;
            $_SESSION['hash'] = generateSignature();
            $_SESSION['last_active'] = time();
			
            $hooks->run_hooks('login_after', $player);
            
            header('Location: index.php');
            exit;
        } else {
            session_unset();
			
			if (!empty($warn)) {
				$this->setMessage($warn, 'WARN');
			} else {
				$msg = 'Sorry, you could not be logged in:<br />' . $fail;
				$this->setMessage($msg, 'FAIL');
			}
			
            header('Location: index.php');
            exit;
        }
    }
	
	private function validate() {
		$query = $this->db->execute('SELECT `id`, `username`, `password`, `secret_key` FROM `<ezrpg>players` WHERE `username`=?', array($_POST['username']));
        
		if ($this->db->numRows($query) == 0)
			return false;
		
        else {
            $player = $this->db->fetch($query);
            
            // We have different authentication methods at our disposal.
            // Currently, the hashing algorithm is defined in the config file.
            switch ($this->config['security']['hashing']) {
                
                // PBKDF2
                case 2 :
                    $check = comparePBKDF2(array($_POST['password'], $player->secret_key), $player->password);
                    break;
                
                // bcrypt
               case 4 :
                   $check = compareBcrypt(array($_POST['password'], $player->secret_key), $player->password);
                   break;
               
               // Oldschool
               case 0 :
               default :
                    $check = $player->password === sha1($player->secret_key . $_POST['password'] . SECRET_KEY);
                    break;
            }			
            
            if ($check !== true) 
                return false;
			
			return $player;
        }
	}
}
?>
