<?php 

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     png_image
 * Date:     4-aug-2003
 * Version:  1.0
 * Author:   Bart Bons <bartbons at debster.nl>
 * Changes:  Reynier P�rez Mira <reynierpm at gmail dot com>
 * Purpose:  output an PNG image with Alpha Transparency
 *           If browser is IE then we use a special trick with the AlphaImageLoader FILTER style
 *					 For all other browser we don't do anything special because they display PNG's correctly
 * 
 * Input:    src       =  source of the image
 *           height    =  the height of the image; if 0 then height wil be determined automatically
 *           width     =  the width of the image; if 0 then width wil be determined automatically
 *           alt       =  Alternative text if image not found or loaded
 *           extra     =  For extra atrributes like "onclick=..." or "class='black'" 
 *
 * Examples: {png_image src="computer.png" height="48" width="48" alt="Computer" extra="class=''"}
 *           {png_image src="somepic.png" height="100" width="100"}
 *           {png_image src="somepic.png" height="0" width="0"} // slower because of extra 'getimagesize'
 * I added a new functionality for display image as a valid XHTML 1.0 tag
 * -------------------------------------------------------------
 */
 
function smarty_function_png_image($params, $smarty, $template)
{
  extract($params);
	  
  if (empty($src)) {
    $smarty->trigger_error("assign_array: missing 'src' parameter");
    return;
  }
  if (empty($height))
    $height = 0;
		
  if (empty($width))
    $width = 0;

	if (($height == 0) or ($width == 0)) {
          $currentimagesize = getimagesize($src);
          $width = $currentimagesize[0];
          $height= $currentimagesize[1];
	}

	$PNGcompliantAgent = !(stristr( $_SERVER['HTTP_USER_AGENT'], 'MSIE'));
	//if ($PNGcompliantAgent)
          // $html = "<img src='$src' height='$height' width='$width' alt='$alt' $extra />"; 
		   $html = "<img src='$src' class='png' height='$height' width='$width' alt='$alt' $extra />";
	//else
	  // $html = "<SPAN STYLE='position:relative;height:".$height."px;width:".$width."px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\"$src\",sizingMethod=\"scale\");' $extra></SPAN>";

  return $html;
}

?>