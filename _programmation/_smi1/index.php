<?php
/*** affichage 2 mois ***
 *
 * Cindy Chassot 17.06.2015
 * © Cinémathèque suisse
 */

	
include($_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/inc/pm_js_simple.php"); //paramêtres js pour les effets ajax sans modifications



// remplacement de l'apostrophe
$caract = array(utf8_decode("'"));
$new_caract = array(utf8_encode("&acute;"));
?>

<div id='wrap'>
<!--affichage du calendrier-->
		<div id='calendar'></div>
		<div id='calendar2'></div>
		<div class="clear"></div>
</div>
	
	
