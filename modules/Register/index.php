<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
  Class: Module_Register
  This module handles adding new players to the database.
*/
class Module_Register extends Base_Module
{
    /*
      Function: start()
      Displays the registration form by default.
      
      See Also:
      - <render>
      - <register>
    */
    public function start()
    {
        if (LOGGED_IN)
        {
            header("Location: index.php");
            exit;
        }
        else
        {
            //If the form was submitted, process it in register().
            if (isset($_POST['register']))
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
    private function render()
    {
        //Add form default values
        if (!empty($_GET['username']))
            $this->tpl->assign('GET_USERNAME', $_GET['username']);
        if (!empty($_GET['email']))
            $this->tpl->assign('GET_EMAIL', $_GET['email']);
		
        if (array_key_exists('msg', $_GET)) 
            $this->tpl->assign('MSG', $_GET['msg']);
        
        $this->tpl->display('register.tpl');
    }
	
    /*
      Function: register
      Processes the submitted player details.
	
      Checks if all the data is correct, and adds the player to the database.
	
      Otherwise, add an error message.
	
      At the end, use a *redirect* in order to be able to display a message through $_GET['msg'].
    */
    private function register()
    {
        $error = 0;
        $errors = Array();
		
        //Check username
        $result = $this->db->fetchRow('SELECT COUNT(`id`) AS `count` FROM `<ezrpg>players` WHERE `username`=?', array($_POST['username']));
        if (empty($_POST['username']))
        {
            $errors[] = 'You didn\'t enter your username!';
            $error = 1;
        }
        else if (!isUsername($_POST['username']))
        { //If username is too short...
            $errors[] = 'Your username must be longer than 3 characters and may only contain alphanumerical characters!'; //Add to error message
            $error = 1; //Set error check
        }
        else if ($result->count > 0)
        {
            $errors[] = 'That username has already been used. Please create only one account!';
            $error = 1; //Set error check
        }
		
        //Check password
        if (empty($_POST['password']))
        {
            $errors[] = 'You didn\'t enter a password!';
            $error = 1;
        }
        else if (!isPassword($_POST['password']))
        { //If password is too short...
            $errors[] = 'Your password must be longer than 3 characters!'; //Add to error message
            $error = 1; //Set error check
        }
	
        if ($_POST['password2'] != $_POST['password'])
        {
            $errors[] = 'You didn\'t verify your password correctly!';
            $error = 1;
        }
	
        //Check email
        $result = $this->db->fetchRow('SELECT COUNT(`id`) AS `count` FROM `<ezrpg>players` WHERE `email`=?', array($_POST['email']));
        if (empty($_POST['email']))
        {
            $errors[] = 'You didn\'t enter your email!';
            $error = 1;
        }
        else if (!isEmail($_POST['email']))
        {
            $errors[] = 'Your email format is wrong!'; //Add to error message
            $error = 1; //Set error check
        }
        else if ($result->count > 0)
        {
            $errors[] = 'That email has already been used. Please create only one account, creating more than one account will get all your accounts deleted!';
            $error = 1; //Set error check
        }
	
		
        //verify_code must NOT be used again.
        session_unset();
        session_destroy();
		
		
        if ($error == 0)
        {
            unset($insert);
            $insert = Array();
            //Add new user to database
            $insert['username'] = $insert['alias'] = $_POST['username'];
            $insert['email'] = $_POST['email'];
            $insert['secret_key'] = createKey(16);
            
            switch ($this->config['security']['hashing']) {
                
                // PBKDF2
                case 2 :
                    $insert['password']= createPBKDF2($_POST['password'], $insert['secret_key']);
                    break;
                
                // bcrypt
               case 4 :
                   $insert['password'] = createBcrypt($_POST['password'], $insert['secret_key']);
                   break;
               
               // Oldschool
               case 0 :
               default :
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
            header('Location: index.php?msg=' . urlencode($msg));
            exit;
        }
        else
        {
            $msg = 'Sorry, there were some mistakes in your registration:<br />';
            $msg .= '<ul>';
            foreach($errors as $errmsg)
            {
                $msg .= '<li>' . $errmsg . '</li>';
            }
            $msg .= '</ul>';
            
            $url = 'index.php?mod=Register&msg=' . urlencode($msg)
                . '&username=' . urlencode($_POST['username'])
                . '&email=' . urlencode($_POST['email'])
                . '&email2=' . urlencode($_POST['email2']);
            header('Location: ' . $url);
            exit;
        }
    }
}
?>
