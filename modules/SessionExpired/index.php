<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
  Class: SessionExpired
  This is a skeleton module, which can be used as the starting point for coding new modules.
  See:
  <Base_Module>
*/
class Module_SessionExpired extends Base_Module
{
    /*
      Function: start
      This is the function that is called to display the module to the player.
      This is where most of your player-facing code will go.
      
      Since this module extens Module_Base, you can use the following class variables:
      $db - An instance of the database class.
      $tpl - A template smarty object.
      $player - A player result set from the database, or 0 if not logged in.
    */
    public function start()
    {
        // You may call the requireLogin() function if this module is only available to players who are logged in.
        requireLogin();
		
		if (array_key_exists('password', $_POST)) {
			$this->checkPassword();
		}
		
		$this->tpl->display('session_exired.tpl');
    }
	
	private function checkPassword() {
		
		switch ($this->config['security']['hashing']) {

			// PBKDF2
			case 2 :
				$check = comparePBKDF2(array($_POST['password'], $this->player->secret_key), $this->player->password);
				break;

			// bcrypt
			case 4 :
				$check = compareBcrypt(array($_POST['password'], $this->player->secret_key), $this->player->password);
				break;

			// Oldschool
			case 0 :
			default :
				$check = $this->player->password === sha1($this->player->secret_key . $_POST['password'] . SECRET_KEY);
				break;
		}		

		if ($check !== false) {
			$_SESSION['last_active'] = time();
			header('location: ' . $_SESSION['last_page']);
			exit;
		} else {
			if (!array_key_exists('pwd_enter_attempts', $_SESSION)) {
				$_SESSION['pwd_enter_attempts'] = 0;
			}
			
			$_SESSION['pwd_enter_attempts']++;
			
			if ($_SESSION['pwd_enter_attempts'] >= 3) {
				header('location: index.php?mod=Logout');
				exit;
			}
			
			$this->tpl->assign('MSG_WARN', 'Password entered is invalid!<br />You have ' . (3 - $_SESSION['pwd_enter_attempts']) . ' attempts left before you are automatically logged out.');			
		}
	}
}
?>