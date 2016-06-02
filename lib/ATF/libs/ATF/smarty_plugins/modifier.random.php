<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     number_format
 * Purpose:  format numbers 
 * -------------------------------------------------------------
 */
function smarty_modifier_random($str,$min,$max)
{
    return mt_rand($min,$max);
}

/* vim: set expandtab: */

?>
