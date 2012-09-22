<?php

if (!defined('IN_EZRPG'))
    exit;

/*
  Title: Validation functions
  This file contains functions you can use to validate player data: username, password, email, etc.
 */

/*
  Function: isUsername
  Checks the length and format of the username.

  Parameters:
  $username - The value to check if it's a username.

  Returns:
  Boolean - true or false
 */

function isUsername($username) {
    if (strlen($username) < 4)
        return false;
    if (!preg_match("/^[_a-zA-Z0-9]+$/", $username))
        return false;
    //Everything's fine, return true
    return true;
}

/*
  Function: isPassword
  Checks if the length of the password is long enough.

  Parameters:
  $password - The value to check

  Returns:
  Boolean - true or false
 */

function isPassword($password) {
    if (strlen($password) < 6)
        return false;
	if (!preg_match("/[a-zA-Z0-9\W]+/", $password))
        return false;
    return true;
}

/*
  Function: isEmail
  Checks if the email is valid

  Parameters:
  $email - The value to check

  Returns:
  Boolean - true or false
 */

function isEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}