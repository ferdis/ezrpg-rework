<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
  Class: Module_AccountSettings
  Lets the user change their password.
*/
class Module_AccountSettings extends Base_Module
{
    /*
      Function: start
      Begins the account settings page/
    */
    public function start()
    {
        //Require login
        requireLogin();
		
		if(!array_key_exists('form', $_POST)) {
			if (array_key_exists('msg', $_GET))
						$this->tpl->assign('MSG', $_GET['msg']);
				
			$this->tpl->display('account_settings.tpl');
			return true;
		}
		
		switch ($_POST['form']) {
			case 'password' :
				$this->changePassword();
				break;
			
			case 'avatar' : 
				if (array_key_exists('remove_avatar', $_POST))
					$this->removeAvatar();
				else
					$this->changeAvatar();
				break;
			
			default :
				$this->tpl->display('account_settings.tpl');
		}
		
		return true;
    }

    private function changePassword()
    {
        $msg = '';
        if (empty($_POST['current_password']) || empty($_POST['new_password']) || empty($_POST['new_password2']))
        {
            $msg = 'You forgot to fill in something!';
        }
        else
        {
            $check = sha1($this->player->secret_key . $_POST['current_password'] . SECRET_KEY);
            if ($check != $this->player->password)
            {
                $msg = 'The password you entered does not match this account\'s password.';
            }
            else if (!isPassword($_POST['new_password']))
            {
                $msg = 'Your password must be longer than 3 characters!';
            }
            else if ($_POST['new_password'] != $_POST['new_password2'])
            {
                $msg = 'You didn\'t confirm your new password correctly!';
            }
            else
            {
                $new_password = sha1($this->player->secret_key . $_POST['new_password2'] . SECRET_KEY);
                $this->db->execute('UPDATE `<ezrpg>players` SET `password`=? WHERE `id`=?', array($new_password, $this->player->id));
                $msg = 'You have changed your password.';
            }
        }
        
        header('Location: index.php?mod=AccountSettings&msg=' . urlencode($msg));
    }
	
	private function changeAvatar() {
		
		// Check that something is present for us to handle.
		if (!array_key_exists('avatar', $_FILES)) {
			$msg = 'You forgot to select an image!';
			header('Location: index.php?mod=AccountSettings&msg=' . urlencode($msg));
			return false;
		}
		
		// check that error equal 0, or else something went wrong.
		if ($_FILES['avatar']['error'] !== 0) {
			$msg = 'Something went wrong, please try again.';
			header('Location: index.php?mod=AccountSettings&msg=' . urlencode($msg));
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
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$tmp_mime = finfo_file($finfo, $_FILES['avatar']['tmp_name']);
			finfo_close($finfo);
		} else {
			// Trust the user as a last resort.
			$tmp_mime = $_FILES['avatar']['type'];
		}
		
		// validate according to it.
		if (in_array($tmp_mime, $allowed_types) === false) {
			$msg = 'We only allow certain image files to be uploaded.';
			header('Location: index.php?mod=AccountSettings&msg=' . urlencode($msg));
			return false;			
		}
		
		// check that the file isn't larger than something we can handle. 
		$max_size = 35 * 1024; // in kB
		if ($_FILES['avatar']['size'] > $max_size) {
			$msg = 'The image you uploaded is too large, we allow images of up to ' . round($max_size / 1024, 2) . ' KiB';
			$msg .= '.<br />Your\'s was ' . round($_FILES['avatar']['size'] / 1024, 2) . ' KiB';
			header('Location: index.php?mod=AccountSettings&msg=' . urlencode($msg));
			return false;
		}
		
		
		// grab the file contents.
		$content = file_get_contents($_FILES['avatar']['tmp_name']);
		
		// convert the contents to base64, and formulate a url we can use.
		$url = sprintf('data:%s;base64,%s', $tmp_mime, base64_encode($content));
		unset($content);
		
		// update our player's record.
		$sql = 'UPDATE `<ezrpg>players` SET `avatar`=? WHERE `id`=?';
		$this->db->execute($sql, array($url, $this->player->id));	
		
		// keep the file, PHP will delete it later in any case.
		// redirect the player.
		$msg = 'Your avatar has been updated!';
		header('Location: index.php?mod=AccountSettings&msg=' . urlencode($msg));
		return false;
	}
	
	private function removeAvatar() {
		$sql = 'UPDATE `<ezrpg>players` SET `avatar`=NULL WHERE `id`=?';
		$this->db->execute($sql, array($this->player->id));	
		
		// redirect the player.
		$msg = 'Your avatar has been removed!';
		header('Location: index.php?mod=AccountSettings&msg=' . urlencode($msg));
		return false;
	}
}
?>