<?php
defined('IN_EZRPG') or exit;

$hooks->add_hook('player', 'check_stats', 2);

function hook_check_stats($db, &$tpl, $player, $args = 0)
{
    if ($args === 0 || LOGGED_IN == false)
        return $args;
    
    $changed = false;
    //Check if player's stats are above the limit
    if ($args->hp > $args->max_hp)
    {
        $args->hp = $args->max_hp;
        $changed = true;
    }

    if ($args->energy > $args->max_energy)
    {
        $args->energy = $args->max_energy;
        $changed = true;
    }

    if ($changed === true)
    {
        $db->execute('UPDATE `<ezrpg>players` SET `energy`=?, `hp`=? WHERE `id`=?', array($args->energy, $args->hp, $args->id));
    }

    $args->hp_percentage = ($args->hp <= 0) ? 
                                0 : 
                                floor(($args->max_hp / $args->hp) * 100);
    
    // Do not display EXP as 100% if percentage is ~ 99.6%
    $args->exp_percentage = ($args->exp <= 0) ?
                                0 :
                                ceil(($args->max_exp / $args->exp) * 100);
    
    $args->energy_percentage = ($args->energy <= 0) ? 
                                0 :
                                floor(($args->max_energy / $args->energy) * 100);

    return $args;
}
?>
