<?php

if (!defined('IN_EZRPG'))
    exit;

/*
  Title: Security functions
  This file contains functions used for security pruposes.
 */

/*
  Function: hashPBKDF2
  Creates a PBKDF2 has from the arguments passed to it.

  Paramaters:
  $password - The string/password to apply PBKDF2 to.
  $salt - A string to be used as a salt.
  $global_salt - A string to be used a global salt.
  $count - Possitive integer for how many times the hash should be iterated.
  $length - The derived key length.
  $algorithm - Algorithm to apply on hash.

  Returns:
  String - The derived key.
 */

function createPBKDF2($password, $salt, $global_salt = SECRET_KEY, 
                        $count = 1005, $length = 32, $algorithm = 'sha256') {
    // Introduce custom salt
    $password = implode($salt, $password);
    
    $kb = $start + $length;                           // Key blocks to compute 
    $derived = '';                                    // Derived key 
    
    // Create key 
    for ($block = 1; $block <= $kb; $block++) {
        // Initial hash for this block 
        $hash = hash_hmac($algorithm, $global_salt . pack('N', $block), $password, true);
        $initial_block = $hash;
        
        // Perform block iterations 
        for ($i = 1; $i < $count; $i++) {
            // XOR each iterate 
            $initial_block ^= ($h = hash_hmac($algorithm, $hash, $password, true));
        }
        
        // Append iterated block 
        $derived .= $initial_block; 
    }

    // Return derived key of correct length 
    return substr($derived, 0, $length);
}

/*
  Function: comparePBKDF2
  Compares two PBKDF2 passwords.

  Paramaters:
  $origin - An array, double cell when introducing a salt.
  $comparison - A PBKDF2 hash representation of a password.
  $algorithm - Algorithm to apply on hash.

  Returns:
  Boolean - true or false.
 */
function comparePBKDF2($origin, $comparison) {
    if (count($origin) == 2) 
        return (createPBKDF2 ($origin[0], $origin[1]) === $comparison);
    else
        return (createPBKDF2($origin[0]) === $comparison);
}

?>
