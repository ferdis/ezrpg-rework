<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') || exit;

/*
Class: Module_Register
This module handles adding new players to the database.
*/
class Module_Register extends Base_Module {
    /*
    Function: start()
    Displays the registration form by default.
    
    See Also:
    - <render>
    - <register>
    */
    public function start() {
        if (LOGGED_IN) {
            header("Location: index.php");
            exit;
        } else {
            //If the form was submitted, process it in register().
            if (array_key_exists('register', $_POST))
                $this->register();
            else
                $this->render();
        }
    }
    
    /*
    Function: render
    Renders register.tpl.
    
    Also repopulates the form with submitted data if necessary.
    */
    private function render() {
        //Add form default values
        if (array_key_exists('username', $_GET))
            $this->tpl->assign('USERNAME', $_GET['username']);
        if (array_key_exists('email', $_GET))
            $this->tpl->assign('EMAIL', $_GET['email']);
        
        $this->tpl->display('register.tpl');
    }
    
    /*
    Function: register
    Processes the submitted player details.
    
    Checks if all the data is correct, and adds the player to the database.
    
    Otherwise, add an error message.
    
    At the end, use a *redirect* in order to be able to display a message through $_GET['msg'].
    */
    private function register() {
        $errors = array(
			'warn' => array(),
			'fail' => array()
		);
		
		// validate that this is not a bot.
		if ((strlen($_POST['username']) + strlen($_POST['email'])) !== intval($_POST['verify'])) {
			$this->setMessage('You\'re a bot, go away.', 'fail');					
			header('Location: index.php');
			return false;
		}
		
		// validate username length and character sequence. 
		if (empty($_POST['username'])) {
            $errors['warn'][] = 'You didn\'t enter your username!';
        } else if (!isUsername($_POST['username'])) { //If username is too short...
            $errors['fail'][] = 'Your username must be longer than a characters and may only contain alphanumerical characters!'; //Add to error message
        }
		
		// validate password length and character sequence. 
		if (empty($_POST['password'])) {
            $errors['warn'][] = 'You didn\'t enter a password!';
        } else if (!isPassword($_POST['password'])) { //If password is too short...
            $errors['fail'][] = 'Your password must be longer than 6 characters and must contain at least a lowercase, uppercase and symbol character!'; //Add to error message
        }
		
		// validate that an email has been supplied and that it's valid.
		if (empty($_POST['email'])) {
            $errors['warn'][] = 'You didn\'t enter your email!';
        } else if (!isEmail($_POST['email'])) {
            $errors['fail'][] = 'Your email format is wrong!';
		}
		
		//Check password against secondary password entered.
        if ($_POST['password2'] != $_POST['password']) 
            $errors['fail'][] = 'You didn\'t verify your password correctly!';
		
		// formulate a url to redirect to, in case of any errors.
		$url = 'index.php?mod=Register' . 
					(array_key_exists('username', $_POST) ? '&username=' . $_POST['username'] : '') .
					(array_key_exists('email', $_POST) ? '&email=' . $_POST['email'] : '');
		
		if (count($errors['warn']) > 0) {
			$msg = 'Sorry, there were some mistakes in your registration:<br />';
            $msg .= '<ul>';
            foreach ($errors['warn'] as $item) {
                $msg .= '<li>' . $item . '</li>';
            }
            $msg .= '</ul>';
			
			$this->setMessage($msg, 'warn');					
			header('Location: ' . $url);
            return false;
		}
		
				
		// check that the username hasn't been taken already.		
		$result = $this->db->fetchRow('SELECT COUNT(`id`) AS `count` FROM `<ezrpg>players` WHERE `username`=?', array(
            $_POST['username']
        ));
		
		if ($result->count > 0) 
            $errors['fail'][] = 'That username has already been used.';
        
        
        // Check that hte email isn't attached to another account.
        $result = $this->db->fetchRow('SELECT COUNT(`id`) AS `count` FROM `<ezrpg>players` WHERE `email`=?', array(
            $_POST['email']
        ));
		
		if ($result->count > 0) {
            $errors['fail'][] = 'That email has already been used. Please create only one account, creating more than one account will get all your accounts deleted!';
        }
		
		if (count($errors['fail']) > 0) {
			$msg = 'Sorry, there were some mistakes in your registration:<br />';
            $msg .= '<ul>';
            foreach ($errors['fail'] as $item) {
                $msg .= '<li>' . $item . '</li>';
            }
            $msg .= '</ul>';
			
			$this->setMessage($msg, 'fail');					
			header('Location: ' . $url);
			return false;
		}
        
		
		$insert               = array();
		//Add new user to database
		$insert['username']   = $insert['alias'] = $_POST['username'];
		$insert['email']      = $_POST['email'];
		$insert['secret_key'] = createKey(16);

		switch ($this->config['security']['hashing']) {

			// PBKDF2
			case 2:
				$insert['password'] = createPBKDF2($_POST['password'], $insert['secret_key']);
				break;

			// bcrypt
			case 4:
				$insert['password'] = createBcrypt($_POST['password'], $insert['secret_key']);
				break;

			// Oldschool
			case 0:
			default:
				$insert['password'] = sha1($insert['secret_key'] . $_POST['password'] . SECRET_KEY);
				break;
		}

		$insert['registered'] = time();

		global $hooks;
		//Run register hook
		$insert = $hooks->run_hooks('register', $insert);

		$new_player = $this->db->insert('<ezrpg>players', $insert);
		//Use $new_player to find their new ID number.

		$hooks->run_hooks('register_after', $new_player);

		$msg = 'Congratulations, you have registered! Please login now to play!';
		$this->setMessage($msg, 'good');
		
		header('Location: index.php');
		return true;
    }
}