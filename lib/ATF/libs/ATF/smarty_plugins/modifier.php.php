<?php

function smarty_modifier_php($var1,$function)
{
	$args = func_get_args();
	array_splice($args,1,1);
	return call_user_func_array( $function, $args );
}

/* vim: set expandtab: */

?>
