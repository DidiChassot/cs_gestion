<?php
/**
* IHEADER Programme complet - PROGRAMMATION - SM2
*
* Gestion du programme complet avec les événement extérieur, le journal et la publicié
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 19.02.2015 - 15.07.2015
*/
?>
<script src='<?php echo $_COOKIE['indexApp']."/"; ?>js/jquery.min.js'></script>
<script src='<?php echo $_COOKIE['indexApp']."/"; ?>js/jquery-ui.custom.min.js'></script>
        

<?php

include($_COOKIE['indexApp']."/inc/pm_functions.php"); //paramêtres js global programmation

if($_COOKIE['role']=='e') { 
    include($_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/inc/pm_js.php"); //paramêtres js pour les effets ajax
}
?>

<!--css print pour programmation-->
<link rel="stylesheet" type="text/css" href="<?php echo $_COOKIE['indexApp']; ?>/css/pm_print.css" media="print" />