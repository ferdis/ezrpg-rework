<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
Class: Module_AccountSettings
Lets the user change their password.
*/
class Module_AccountSettings extends Base_Module {
	/*
	Function: start
	Begins the account settings page
	*/
	public function start() {
		//Require login
		requireLogin();
		
		if (!array_key_exists('form', $_POST)) {
			$this->tpl->display('account_settings.tpl');
			return true;
		}
		
		switch ($_POST['form']) {
			case 'password':
				$this->changePassword();
				break;
			
			case 'avatar':
				if (array_key_exists('remove_avatar', $_POST))
					$this->removeAvatar();
				else
					$this->changeAvatar();
				break;
			
			default:
				$this->tpl->display('account_settings.tpl');
		}
		
		return true;
	}
	
	private function changePassword() {
		$errors = array(
			'warn' => '',
			'fail' => '',
			'good' => ''
		);
		
		if (empty($_POST['current_password']) || empty($_POST['new_password']) || empty($_POST['new_password2'])) {
			$errors['warn'] = 'You forgot to fill in something!';
		} else {
			
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
			
			if ($check !== true) {
				$errors['fail'] = 'The password you entered does not match this account\'s password.';
			} else if (!isPassword($_POST['new_password'])) {
				$errors['warn'] = 'Your password must be longer than 3 characters!';
			} else if ($_POST['new_password'] != $_POST['new_password2']) {
				$errors['warn'] = 'You didn\'t confirm your new password correctly!';
			} else {
				// password type switch
				switch ($this->config['security']['hashing']) {
					// PBKDF2
					case 2:
						$new_password = createPBKDF2($_POST['password'], $insert['secret_key']);
						break;
					
					// bcrypt
					case 4:
						$new_password = createBcrypt($_POST['password'], $insert['secret_key']);
						break;
					
					// Oldschool
					case 0:
					default:
						$new_password = sha1($insert['secret_key'] . $_POST['password'] . SECRET_KEY);
						break;
				}
				
				$this->db->execute('UPDATE `<ezrpg>players` SET `password`=? WHERE `id`=?', array(
					$new_password,
					$this->player->id
				));
				$errors['good'] = 'You have changed your password.';
			}
		}
		
		foreach ($errors as $err_type => $message)
			$this->setMessage($message, $err_type);
		
		header('Location: index.php?mod=AccountSettings');
	}
	
	private function changeAvatar() {
		// Check that something is present for us to handle.
		if (!array_key_exists('avatar', $_FILES)) {
			$this->setMessage('You forgot to select an image!', 'info');
			header('Location: index.php?mod=AccountSettings');
			return false;
		}
		
		// check that error equal 0, or else something went wrong.
		if ($_FILES['avatar']['error'] !== 0) {
			$this->setMessage('Something went wrong, please try again.', 'fail');
			header('Location: index.php?mod=AccountSettings');
			return false;
		}
		
		// here is a list of all the file types we are going to allow.
		$allowed_types = array(
			'image/png',
			'image/jpeg',
			'image/gif'
		);
		
		// validate that the image is what it says it is.
		if (function_exists('mime_content_type'))
			$tmp_mime = mime_content_type($_FILES['avatar']['tmp_name']);
		else if (function_exists('finfo_open')) {
			$finfo    = finfo_open(FILEINFO_MIME_TYPE);
			$tmp_mime = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
			finfo_close($finfo);
		} else {
			// Trust the user as a last resort.
			$tmp_mime = $_FILES['avatar']['type'];
		}
		
		// validate according to it.
		if (in_array($tmp_mime, $allowed_types) === false) {
			$this->setMessage('We only allow certain image files to be uploaded.', 'warn');
			header('Location: index.php?mod=AccountSettings');
			return false;
		}
		
		// check that the file isn't larger than something we can handle. 
		$max_size = 35 * 1024; // in kB
		if ($_FILES['avatar']['size'] > $max_size) {
			$msg = 'The image you uploaded is too large, we allow images of up to ' . round($max_size / 1024, 2) . ' KiB';
			$msg .= '.<br />Your\'s was ' . round($_FILES['avatar']['size'] / 1024, 2) . ' KiB';
			$this->setMessage($msg, 'warn');
			header('Location: index.php?mod=AccountSettings');
			return false;
		}
		
		
		// grab the file contents.
		$content = file_get_contents($_FILES['avatar']['tmp_name']);
		
		// convert the contents to base64, and formulate a url we can use.
		$url = sprintf('data:%s;base64,%s', $tmp_mime, base64_encode($content));
		unset($content);
		
		// update our player's record.
		$sql = 'UPDATE `<ezrpg>players` SET `avatar`=? WHERE `id`=?';
		$this->db->execute($sql, array(
			$url,
			$this->player->id
		));
		
		// keep the file, PHP will delete it later in any case.
		// redirect the player.
		$this->setMessage('Your avatar has been updated!', 'good');
		header('Location: index.php?mod=AccountSettings');
		
		return false;
	}
	
	private function removeAvatar() {
		$sql = 'UPDATE `<ezrpg>players` SET `avatar`=NULL WHERE `id`=?';
		$this->db->execute($sql, array(
			$this->player->id
		));
		
		// redirect the player.
		$this->setMessage('Your avatar has been removed!', 'good');
		header('Location: index.php?mod=AccountSettings');
		
		return false;
	}
}