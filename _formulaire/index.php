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
$num_dep = substr($_COOKIE['sousMenu'], -1); // pour récupérer les bons formulaires par département
$id_form = '';
$id_demande = '';

$zone = '';
$add = '';
$mod = '';

$inscrip = '';
$traduction = '';

//Récupération des variables $_GET envoyées
foreach ($_GET as $key => $value) {
	$$key = $value;
}

/**************************************************************************************/
$saveForm = '';
$deleteForm = '';
$saveTrad = '';
$saveChamp = '';
$deleteChamp = '';
$saveChampTrad = '';
$addInscForm = '';
$modInsc = '';
$deleteInsc = '';

//Récupération des variables $_POST envoyées
foreach ($_POST as $key => $value) {
	$$key = $value;
}
 
/**************************************************************************************
 * ****************************** TRAITEMENT DES DEMANDES ****************************
 * ***********************************************************************************/
if($saveForm) saveForm();
if($deleteForm) deleteForm();

if($saveTrad) saveTrad();

if($saveChamp) saveChamp();
if($deleteChamp) deleteChamp();

if($saveChampTrad) saveChampTrad();

if($addInscForm) addInscForm();

if($modInsc) modInsc();
if($deleteInsc) deleteInsc();

/**************************************************************************************
 * ****************************** APPLICATION *****************************************
 * ***********************************************************************************/ 
//inclusion des constantes de l'app
include($_COOKIE['indexApp']."/inc/constants.php");

//inclusion du sous-menu
include($_COOKIE['indexApp']."/menu.php");

//inclusion de l'index du sous-menu sélectionné
if ($_COOKIE['sousMenu']) include("_formulaire/".$_COOKIE['sousMenu']."/index.php");

?>