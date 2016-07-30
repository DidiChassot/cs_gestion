<?php
/*** HEADER attente cycle - PROGRAMMATION - SM0***/
// 
//Cindy Chassot 29.01.2015 - 01.04.2015
//© Cinémathèque suisse
?>
<script src='<?php echo $_COOKIE['indexApp']."/"; ?>js/jquery.min.js'></script>
<script src='<?php echo $_COOKIE['indexApp']."/"; ?>js/jquery-ui.custom.min.js'></script>

<?php

include($_COOKIE['indexApp']."/inc/pm_functions.php"); //paramêtres js pour les effets ajax
if($_COOKIE['role']=='e') { 
    include($_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/inc/pm_js.php"); //paramêtres js pour les effets ajax
}
?>
