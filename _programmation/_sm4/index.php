<?php
/**
* INDEX _Programmation / SM4 - gestion des films
*
* Interface permettant la gestion des roles des applications ainsi que les utilisateurs
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 29.04.2015 - 16.07.2015
*/
/*************************************************************************************
 * ****************************** AFFICHAGE DES DONNEES ******************************
 * ***********************************************************************************/ 
?>
<h2>Liste des films</h2>
<div class="left demi">
<?php if($_COOKIE['role']=='e') { ?>
	<form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>#film" method="get">
	    <input type="hidden" value="film" name="zone">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
<?php } ?>
  <?php include($_COOKIE['indexApp']."/inc/pm_recherche.php"); ?>
</div>

<?php
$prefix_realisateur = '99';
$prefix_acteur = '99';
/********************* Formulaire du Film *********************/
if($_REQUEST['zone']=='film' || $_REQUEST['zone']=='copie') {
	if($fi && $fi != 1) {
		$sqlFilmSelect = "SELECT * FROM pm_film WHERE id_film ='$fi'"; // nom de la table ! requette
		$resultFilmSelect = mysqli_query($connexion, $sqlFilmSelect ) or die(mysqli_error());
		$dataFilmSelect = mysqli_fetch_array($resultFilmSelect);
		$prefix_realisateur = $dataFilmSelect['prefix_film_director'];
		$prefix_acteur = $dataFilmSelect['prefix_film_actor'];
	}
?>
<div class="right demi">
	<h4>Base prog : <?php echo utf8_encode($dataFilmSelect['titre_o']); ?></h4>
	
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	
<?php include($_COOKIE['indexApp']."/inc/pm_fiche_film.php"); ?>

	    <input type="hidden" name="id_film" value="<?php echo $dataFilmSelect['id_film']; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveFilm" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>


<?php if($fi) { ?>
<!--###########--><hr class="clear" id="copie"><!--############################################################################################################################-->
<?php
$prefixSelect = '';
if($dataFilmSelect['prefix_titre_o']) {
	if($dataFilmSelect['prefix_titre_o']=="L'") {
		$prefixSelect.= "L'";
	} else {
		$prefixSelect.= utf8_encode($dataFilmSelect['prefix_titre_o']).' ';
	}
}
?>
<h2><i><?php echo utf8_encode($prefixSelect.''.$dataFilmSelect['titre_o']); ?></i></h2>
<div class="left demi">
	<h3>Copie</h3>
<?php if($_COOKIE['role']=='e') { ?>
	<form class="action" action="<?php echo $_SERVER['PHP_SELF']; ?>#copie" method="get">
	    <input type="hidden" value="copie" name="zone">
	    <input type="hidden" value="<?php echo $fi; ?>" name="fi">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
<?php } ?>
	
	
	
	<table class="table">
	    <thead>
		<tr>
		    <th>Provenance</th>
		    <th>Format</th>
		    <th>Version</th>
		    <th>Sous-titre</th>
		    <th>Durée</th>
		    <th>Cote</th>
		    <th>Statut</th>
<?php 		    if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
		</tr>
	    </thead>
	    <tbody>
<?php /********************* Affichage des copies du film *********************/
	$sqlCopieAtt = "SELECT id_copie, provenance, format, version, soustitre, duree, statut, cote FROM pm_copie WHERE id_film ='$fi' AND id_film > 0 ORDER BY statut"; // nom de la table ! requette
	$resultCopieAtt = mysqli_query($connexion, $sqlCopieAtt ) or die(mysqli_error());
	    
	include($_COOKIE['indexApp']."/inc/pm_table_copie.php"); 
?>
	    </tbody>
	</table>
</div>

<?php
/********************* Formulaire de Copie *********************/
if($_REQUEST['zone']=='copie') {
	if($co && $co != 1) {
		$sqlCopieSelect = "SELECT * FROM pm_copie WHERE id_copie ='$co'"; // nom de la table ! requette
		$resultCopieSelect = mysqli_query($connexion, $sqlCopieSelect ) or die(mysqli_error());
		$dataCopieSelect = mysqli_fetch_array($resultCopieSelect);
	}
?>

<div class="right">
<?php
    if($co) {
	echo '<h4>Modification</h4>';
    }else {
	echo '<h4>Ajout</h4>';
    }
?>
	<form action="<?php $_SERVER['PHP_SELF']; ?>#copie" method="post">
	    
<?php include($_COOKIE['indexApp']."/inc/pm_fiche_copie.php"); ?>
	    
	    <input type="hidden" name="id_film" value="<?php echo $fi; ?>">
	    <input type="hidden" name="id_copie" value="<?php echo $co; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveCopie" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>
<?php
}
}} ?>

<hr class="clear">

