<?php
/*** HEADER Programme complet - PROGRAMMATION - SM2***/
// 
//Cindy Chassot 19.02.2015
//© Cinémathèque suisse
//
?>
<!--css print pour programmation-->
<link rel="stylesheet" type="text/css" href="<?php echo $_COOKIE['indexApp']; ?>/css/pm_print.css" media="print" />
<!--css print pour SMI0-->
<link rel="stylesheet" type="text/css" href="<?php echo $_COOKIE['indexApp']; ?>/<?php echo $_COOKIE['sousMenu']; ?>/css/pm_print.css" media="print" />
        

<?php

include($_COOKIE['indexApp']."/inc/pm_functions.php"); //paramêtres js global programmation 
include($_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/inc/pm_js.php"); //paramêtres js pour les effets ajax
?>
