<?php
/**
* INDEX _RESERVATIONS
*
* Index de base de l'application
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 23.07.2015
*/

/**************************************************************************************
 * ****************************** DEFINITION DES VARIABLES ****************************
 * ***********************************************************************************/
$zone = '';
$add = '';
$event = '';
$reserv = '';

$inscrip = '';

$mod = '';
//Récupération des variables $_GET envoyées
foreach ($_GET as $key => $value) {
	$$key = $value;
}

/**************************************************************************************/
$saveEvent = '';
$deleteEvent = '';
$addMovie = '';
$deleteLink = '';
$saveReserv = '';
$deleteReserv = '';

$addInsc = '';
$addInscGestion = '';
$deleteInsc = '';

$saveForm = '';
$deleteForm = '';
$addInscForm = '';

//Récupération des variables $_POST envoyées
foreach ($_POST as $key => $value) {
	$$key = $value;
}
 
/**************************************************************************************
 * ****************************** TRAITEMENT DES DEMANDES ****************************
 * ***********************************************************************************/
if($saveEvent) saveEvent();
if($deleteEvent) deleteEvent();
if($addMovie) addMovie();
if($deleteLink) deleteLink();
if($saveReserv) saveReserv();
if($deleteReserv) deleteReserv();

if($addInsc) addInsc();
if($addInscGestion) addInscGestion();
if($deleteInsc) deleteInsc();

if($saveForm) saveForm();
if($deleteForm) deleteForm();
if($addInscForm) addInscForm();

/**************************************************************************************
 * ****************************** APPLICATION *****************************************
 * ***********************************************************************************/ 
//inclusion des constantes de l'app
include($_COOKIE['indexApp']."/inc/constants.php");

//inclusion du sous-menu
include($_COOKIE['indexApp']."/menu.php");

//inclusion de l'index du sous-menu sélectionné
if ($_COOKIE['sousMenu']) include("_scolaire/".$_COOKIE['sousMenu']."/index.php");

?>