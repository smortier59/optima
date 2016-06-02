<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     number_format
 * Purpose:  format numbers 
 * -------------------------------------------------------------
 */
function smarty_modifier_number_format($string, $format, $decimal=",", $millier=" ")
{
    return number_format($string,$format,$decimal,$millier);
}

/* vim: set expandtab: */

?>
