<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
  Class: Base_Module
  The base class for modules. Every module must extend this class.
*/
abstract class Base_Module {
    /*
      Variable: $db
      Contains the database object.
    */
    protected $db;
    
    /*
      Variable: $tpl
      The smarty template object.
    */
    protected $tpl;
    
    /*
      Variable: $player
      The currently logged in player. Value is 0 if no user is logged in.
    */
    protected $player;
    
    /*
     Variable: $config
     Configuration relevant to the application.
    */
    protected $config;
	
	protected $messages = array(
		'INFO'		=> '',
		'WARN'		=> '',
		'FAIL'		=> '',
		'GOOD'		=> ''
	);
    
    /*
      Function: __construct
      The constructor the every module. Saves the database, template and player variables as class variables.
      
      Parameters:
      The parameters are passed by reference so that all modules and other code use the same objects.
      
      $db - An instance of the database class.
      $tpl - A smarty object.
      $player - A player result set from the database, or 0 if not logged in.
    */
    public function __construct(&$db, &$tpl, &$config, &$player = 0)
    {
        $this->db = $db;
        $this->tpl = $tpl;
        $this->player = $player;
        $this->config = $config;
    }
	
	/**
	 * Sets a status message for use later on.
	 * 
	 * Levels:
	 *	INFO
	 *	WARN
	 *	FAIL
	 *	GOOD
	 * 
	 * @param string $message
	 * @param integer $level
	 * @return boolean 
	 */
	public function setMessage($message, $level='info') {
		$level = strtoupper($level);
		
		// for better practices.
		if (array_key_exists($level, $this->messages) === false) {
			throw new Exception('Message level "' . $level . '" does not exists.');
			return false;
		}
		
		$this->messages[$level] .= $message;
		return true;
	}
	
	public function __destruct() {
		$_SESSION['status_messages'] = array();
		
		foreach($this->messages as $key => $message) {
			 $_SESSION['status_messages'][$key] = $message;
		}
		
		return true;
	}
}
?>