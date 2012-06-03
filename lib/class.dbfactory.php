<?php

//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
  Class: DbFactory
  Factory class for database drivers.

  See Also:
  - <DbException>
 */

class DbFactory {
    /*
      Function: factory
      A static function to return the correct database object according to the database type.

      Parameters:
      $config - Configuration variales.

      Returns:
      A new instance of the database driver class.

      Throws a <DbException> on failure.

      Example Usage:
      > try
      > {
      >     $db = DbFactory::factory($config);
      > }
      > catch (DbException $e)
      > {
      >     $e->__toString();
      > }

      See Also:
      - <DbException>
     */

    public static function factory($config) {
        try {
            include_once(LIB_DIR . '/db.' . $config['driver'] . '.php');
            
            $config['driver'] .= '_adapter';
            return call_user_func(
                    array(
                        new ReflectionClass($config['driver']),
                        'newInstance'
                    ), 
                    $config);
        }
        catch(Exception $e) {
            throw new DbException($config['driver'], DRIVER_ERROR);
        }
    }

}

?>