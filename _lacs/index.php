<?php
/**
* INDEX _FORMULAIRE
*
* Index de base de l'application
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 07.09.2015
*/

/**************************************************************************************
 * ****************************** DEFINITION DES VARIABLES ****************************
 * ***********************************************************************************/
$zone = '';
$add = '';
$mod = '';


//Récupération des variables $_GET envoyées
foreach ($_GET as $key => $value) {
	$$key = $value;
}

/**************************************************************************************/

//Récupération des variables $_POST envoyées
foreach ($_POST as $key => $value) {
	$$key = $value;
}
 
/**************************************************************************************
 * ****************************** TRAITEMENT DES DEMANDES ****************************
 * ***********************************************************************************/

/**************************************************************************************
 * ****************************** APPLICATION *****************************************
 * ***********************************************************************************/ 
//inclusion des constantes de l'app
include($_COOKIE['indexApp']."/inc/constants.php");

//inclusion du sous-menu
include($_COOKIE['indexApp']."/menu.php");

//inclusion de l'index du sous-menu sélectionné
if ($_COOKIE['sousMenu']) include("_lacs/".$_COOKIE['sousMenu']."/index.php");

?>