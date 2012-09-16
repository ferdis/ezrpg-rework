<?php
//This file cannot be viewed, it must be included
defined('IN_EZRPG') or exit;

/*
  Class: Module_PasswordTest
  A testing module for passwords.
*/
class Module_PasswordTest extends Base_Module
{
    /*
      Function: start
      Renders  tests the duration of password comparison and generating.
    */
    public function start()
    {
		
		$pbk = createPBKDF2('admin', 't/u@31c}K.(K#@k{');
		echo 'HASH: ' . $pbk . "<Br />\n";
		$hex = array();
		for($i=0;$i<=strlen($pbk)-1;$i++) {
			echo $pbk[$i] . ':' . dechex(ord($pbk[$i])) . "\n";
			$hex[] = dechex(ord($pbk[$i]));
		}
		
		$lhex= implode($hex);
		$mysql_hex = 'dbd7bdce8dcee81a65f6a9929aa43d565ce35a411eb5fc396f90c88fb3abd90';
		echo "\n..Mine: $lhex\nTheirs: $mysql_hex";
		
		
		echo 'Result: '  . "\n";
		
		if ($r['hex'] === $mysql_hex)
			echo 'valid';
		else
			echo 'false';
        
        $times = (int) $_GET['times'] < 1 ? 1 : (int) $_GET['times'];
        
        // test hashes for speed
        
        $s = microtime();
        $e = 0;
        for($i=0;$i<=$times;$i++) {
            $pbk = createPBKDF2('admin', '&OR}E(Ew7)7r[7T%');
            $e += microtime();
        }
        
        // Average
        $e = $e / $times;
        $e = $e - $s;
        $pbk_time = round($e, 6);
        $pbk_bool = comparePBKDF2(array('admin', '&OR}E(Ew7)7r[7T%'), $pbk);
        
        $s = microtime();
        $e = 0;
        for($i=0;$i<=$times;$i++) {
            $bcr = createBcrypt('ezRPG');
            $e += microtime();
        }
        
        // Average
        $e = $e / $times;
        $e = $e - $s;
        $bcr_time = round($e, 6);
        
        // Comparison
        $bcr_bool = compareBcrypt(array('ezRPG'), $bcr);
        
        $hash = array(
            'pbk_origin' => $pbk,
            'pbk_time'  => $pbk_time,
            'pbk_bool'  => (($pbk_bool) ? 'true' : 'false'),
            'bcr_origin'=> $bcr,
            'bcr_time'  => $bcr_time,
            'bcr_bool'  => (($bcr_bool) ? 'true' : 'false'),
            'times'     => $times
        );
        
        $this->tpl->assign('hash', $hash);
        $this->tpl->display('password_test.tpl');
    }
}
?>