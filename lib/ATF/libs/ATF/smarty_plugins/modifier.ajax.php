<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     ajax
 * Purpose:  Brackets javascript
 * -------------------------------------------------------------
 */
function smarty_modifier_ajax($json, $func)
{
    return "__ajax_".$func."({".$json."});";
}

/* vim: set expandtab: */

?>