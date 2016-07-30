<?php
/*** HEADER ***/
// 
//Cindy Chassot 21.15.2015 - 26.01.2015
//© Cinémathèque suisse
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
<?php
//Si le titre est indiqué, on l'affiche entre les balises <title>
echo (!empty($titreApp))?'<title>'.$titreApp.'</title>':'<title>Gestion des applications</title>';
?>
    <!--<link href='css/global.css' rel='stylesheet' />-->
    <link href='css/full_compress.css' rel='stylesheet' />
    <link rel="stylesheet" type="text/css" href="css/print.css" media="print" />
<?php 
//inclusion link des sous-menu sélectionné
$filename = $_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/header.php";
if (file_exists($filename)) {
    include($filename);
} 
?>
<!--Pour l'éditeur de texte-->
    
</head>
<body>
    <div id="global">