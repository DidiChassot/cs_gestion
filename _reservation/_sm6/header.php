<?php
/**
* HEADER SM6
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 29.04.2015
*/
?>
<script src='<?php echo $_COOKIE['indexApp']."/"; ?>js/jquery.min.js'></script>
<script src='<?php echo $_COOKIE['indexApp']."/"; ?>js/jquery-ui.custom.min.js'></script>

<?php

include($_COOKIE['indexApp']."/inc/re_functions.php"); 
if($_COOKIE['role']=='e') { 
    include($_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/inc/re_js.php"); //paramêtres js pour les effets ajax
}
?>
