<?php
/**
* INDEX _PROGRAMMATION
*
* Application pour la création de la programmation des séances de la CS.
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 22.15.2015 - 20.07.2015
*/

/**************************************************************************************
 * ****************************** DEFINITION DES VARIABLES ****************************
 * ***********************************************************************************/ 
$zone  = '';
$liste = '';
$bu = '';
$cy = '';
$fi = '';
$co = '';
$allday = '';

//Récupération des variables $_GET envoyées
foreach ($_GET as $key => $value) {
	$$key = $value;
}

$saveBulletin = '';
$saveCycle = '';
$deleteCycle = '';
$deleteFinalCycle = '';
$deleteLinkBulletinCycle = '';
$dobbleCycle = '';
$saveCycleCartouche = '';
$saveFilm = '';
$deleteFilm = '';
$linkMultiCycleFilm = '';
$linkMultiNewFilm = '';
$linkSynchroMultiFilm = '';
$deleteLinkCycleFilm = '';
$deselectFilm = '';
$selectFilm = '';
$actuCopie = '';
$saveCopie = '';
$deleteCopie = '';
$synchroMovie = '';

$baseselect = false;
$motif = false;
$recherche = false;

$pmAddAllday = '';
$deleteAllDay = '';
$saveMerci = '';
$saveJournal = '';
$deleteJournal = '';
$savePub = '';
$deletePub = '';

//Récupération des variables $_POST envoyées
foreach ($_POST as $key => $value) {
	$$key = $value;
}

/**************************************************************************************
 * ****************************** TRAITEMENT DES DEMANDES ****************************
 * ***********************************************************************************/
if($saveBulletin) saveBulletin();
if($saveCycle) saveCycle();
if($deleteCycle) deleteCycle();
if($deleteFinalCycle) deleteFinalCycle();
if($deleteLinkBulletinCycle) deleteLinkBulletinCycle();
if($dobbleCycle) dobbleCycle();
if($saveCycleCartouche) saveCycleCartouche();
if($saveFilm) saveFilm();
if($deleteFilm) deleteFilm();
if($linkMultiCycleFilm) linkMultiCycleFilm();
if($linkMultiNewFilm) linkMultiNewFilm();
if($linkSynchroMultiFilm) linkSynchroMultiFilm();
if($deleteLinkCycleFilm) deleteLinkCycleFilm();
if($deselectFilm) deselectFilm();
if($selectFilm) selectFilm();
if($actuCopie) actuCopie();
if($saveCopie) saveCopie();
if($deleteCopie) deleteCopie();
if($synchroMovie) synchroMovie();

//_sm2
if($pmAddAllday) pmAddAllday();
if($deleteAllDay) pmDeleteAllday();
if($saveMerci) saveMerci();
if($saveJournal) saveJournal();
if($deleteJournal) deleteJournal();
if($savePub) savePub();
if($deletePub) deletePub();

/**************************************************************************************
 * ****************************** APPLICATION *****************************************
 * ***********************************************************************************/ 
//inclusion des constantes de l'app
include($_COOKIE['indexApp']."/inc/constants.php");

//inclusion du sous-menu
include($_COOKIE['indexApp']."/menu.php");
?>
<link rel="stylesheet" type="text/css" href="<?php echo $_COOKIE['indexApp']; ?>/css/pm_screen.css" media="screen" />
<?php

//inclusion de l'index du sous-menu sélectionné
if ($_COOKIE['sousMenu']) {
    if(file_exists("_programmation/".$_COOKIE['sousMenu']."/index.php")){
        include("_programmation/".$_COOKIE['sousMenu']."/index.php");
    }    
}

?>