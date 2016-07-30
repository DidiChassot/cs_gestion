<?php
/**
* INDEX _Programmation / SM0 - gestion cycles en attente
*
* Préparation de cycles sans liens avec un bulletin
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 29.04.2015 - 08.10.2015
*/

/*************************************************************************************
 * ****************************** AFFICHAGE DES DONNEES ******************************
 * ***********************************************************************************/ ?>

<div class="left">
	<h3>Cycle</h3>
<?php if($_COOKIE['role']=='e') { ?>
	<form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
	    <input type="hidden" value="cycle" name="zone">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
<?php } ?>
	<form class="action" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
	    <input type="hidden" value="delete" name="liste">
	    <input type="submit" class="btn" value="Supprimés">
	</form>
	
	<table class="table">
	    <thead>
		<tr>
		    <th>Cycles en attente</th>
		    <th>Films</th>
<?php 		    if($_COOKIE['role']=='e') echo '<th></th>'; ?>
		</tr>
	    </thead>
	    <tbody>
<?php
/****************************************************************************************************************************
 * ****************************************** AFFICHAGE DES cycles en attente ***********************************************
 * **************************************************************************************************************************/
	    $sqlCycleAtt = "SELECT id_cycle, titre FROM pm_cycle WHERE bulletin='0' AND actif = 'a'"; // nom de la table ! requette
	    $resultCycleAtt = mysqli_query($connexion, $sqlCycleAtt ) or die(mysqli_error());
	    
	    while ($dataCycleAtt = mysqli_fetch_array($resultCycleAtt)) {
		// définir si le cycle a été sélectionné	
		if ($cy == $dataCycleAtt['id_cycle'] ) {
		    echo '<tr class="select">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr>'; // pour l'altérnance des couleurs
		}
		// lien qui renvoi l'id du cycle en "GET"
		echo '<td>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?zone=cycle&amp;cy='.$dataCycleAtt['id_cycle'].'">'.html_entity_decode(utf8_encode($dataCycleAtt['titre'])).'</a>';
		echo '</td>';
		
		//Nombre de Films séléctionnés
		$sqlListFilm = "SELECT COUNT(id) AS numb_film FROM pm_cycle_film WHERE id_cycle = $dataCycleAtt[id_cycle] AND actif='a' AND id_film > 1";
		$resultListFilm = mysqli_query($connexion, $sqlListFilm ) or die(mysqli_error());
		$dataListFilm = mysqli_fetch_array($resultListFilm);
		//Sur nombre de films listés
		$sqlListFilm2 = "SELECT COUNT(id) AS numb_film FROM pm_cycle_film WHERE id_cycle = $dataCycleAtt[id_cycle] AND id_film > 1";
		$resultListFilm2 = mysqli_query($connexion, $sqlListFilm2 ) or die(mysqli_error());
		$dataListFilm2 = mysqli_fetch_array($resultListFilm2);
		echo '<td>';
		echo $dataListFilm['numb_film'].' / '.$dataListFilm2['numb_film'];
		echo '</td>';
		
		//Formulaire d'édition / suppression
		if($_COOKIE['role']=='e') {	
		echo '<td>';
			echo '<a class="btn_visio" href="'.$_SERVER['PHP_SELF'].'?internal=link&amp;sm=_smi0&amp;cat=e&amp;var&#91;cy&#93;='.$dataCycleAtt['id_cycle'].'">V</a>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette inscription?\')">
				<input type="hidden" name="id_cycle" value="'.$dataCycleAtt['id_cycle'].'">
				<input name="deleteCycle" class="btn_suppr" type="submit" value="Supprimer">
			     </form>';
		echo '</td>';
		}
			    
		echo '</tr>'; //fermeture de la ligne
	    }
	    if($liste) {
		echo '<tr><td colspan="3">--------------------------------------------------------------------</td></tr>';
		/********************* Affichage des cycles supprimés *********************/
		$sqlCycleAtt = "SELECT id_cycle, titre FROM pm_cycle WHERE actif = 'i'"; // nom de la table ! requette
		$resultCycleAtt = mysqli_query($connexion, $sqlCycleAtt ) or die(mysqli_error());
		
		while ($dataCycleAtt = mysqli_fetch_array($resultCycleAtt)) {
		    // définir si le cycle a été sélectionné	
		    if ($cy == $dataCycleAtt['id_cycle'] ) {
			echo '<tr class="select">'; // pour l'altérnance des couleurs
		    } else {
			echo '<tr>'; // pour l'altérnance des couleurs
		    }
		    
		    echo '<td colspan="2">';
		    echo '<a href="'.$_SERVER['PHP_SELF'].'?zone=cycle&amp;cy='.$dataCycleAtt['id_cycle'].'&amp;liste=delete">'.html_entity_decode(utf8_encode($dataCycleAtt['titre'])).'</a></td>'; //renvoi l'id du cycle en "GET"
		    
		    
			//Formulaire d'édition / suppression
			if($_COOKIE['role']=='e') {	
				echo '<td>';
				echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette inscription?\')">
					<input type="hidden" name="id_cycle" value="'.$dataCycleAtt['id_cycle'].'">
					<input name="deleteFinalCycle" class="btn_poubelle" type="submit" value="SupprimerDef">
				     </form></td>';
			}
				
		    echo '</tr>'; //fermeture de la ligne
		}
	    }
?>
	    </tbody>
	</table>
</div>

<?php
/*************************************************************** Formulaire du Cycle ***************************************************************/
if($_REQUEST['zone']=='cycle' || $_REQUEST['zone']=='film' || $_REQUEST['zone']=='copie') {
	if($cy && $cy != 1) {
		$sqlCycleSelect = "SELECT titre, commentaire FROM pm_cycle WHERE id_cycle ='$cy'"; // nom de la table ! requette
		$resultCycleSelect = mysqli_query($connexion, $sqlCycleSelect ) or die(mysqli_error());
		$dataCycleSelect = mysqli_fetch_array($resultCycleSelect);
	}
?>
<div class="right"></div>

<div class="middle">
	<h4>Ajout</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="titre">Titre</label>
		<input id="titre" type="text" name="titre" value="<?php echo utf8_encode($dataCycleSelect['titre']); ?>">
	    </div>
	    <div>
		<label for="commentaire">Infos</label>
		<textarea id="commentaire" name="commentaire"><?php echo afficheHtml(utf8_encode($dataCycleSelect['commentaire'])); ?></textarea>
	    </div>
	    <div>
		<label for="bulletin"></label>
		<input id="bulletin" type="text" name="bulletin" placeholder="Bulletin n° -> Validation">
	    </div>
	    <input type="hidden" name="id_cycle" value="<?php echo $cy; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveCycle" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>
<?php if($cy) { ?>

<!--##########--><hr class="clear" id="film"><!--##############################################################################################################################-->

<h2><?php echo utf8_encode($dataCycleSelect['titre']); ?></h2>

<div class="left">
	<h3>Film</h3>
<?php if($_COOKIE['role']=='e') { ?>
	<form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>#film" method="get">
	    <input type="hidden" value="film" name="zone">
	    <input type="hidden" value="<?php echo $cy; ?>" name="cy">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
	<form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>#film" method="get">
	    <input type="hidden" value="film" name="zone">
	    <input type="hidden" value="<?php echo $cy; ?>" name="cy">
	    <input type="submit" name="listeMulti" class="btn" value="Liste multiple">
	</form>
<?php } ?>
	<form class="action" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
	    <input type="hidden" value="film" name="zone">
	    <input type="hidden" value="<?php echo $cy; ?>" name="cy">
	    <input type="hidden" value="deselect" name="deselect">
	    <input type="submit" class="btn" value="Déselectionnés">
	</form>
	
	<table class="table" id="listeFilm">
	    <thead>
		<tr>
		    <th></th>
		    <th></th>
		    <th>Films</th>
		    <th>Copies</th>
		    <th>Liens</th>
<?php 		    if($_COOKIE['role']=='e') echo '<th style="width:45px;"></th>'; ?>
		</tr>
	    </thead>
	    <tbody class="content">
<?php 
/****************************************************************************************************************************
 * ********************************************** AFFICHAGE DES FILMS *******************************************************
 * **************************************************************************************************************************/
	    $sqlFilmAtt = "SELECT pm_film.id_film, prefix_titre_o, titre_o, annee_prod, ordre FROM pm_cycle_film
			   JOIN pm_film
			   ON pm_cycle_film.id_film = pm_film.id_film
			   WHERE pm_cycle_film.id_cycle='$cy'
			   AND pm_cycle_film.actif = 'a'
			   ORDER BY ordre, annee_prod ASC, titre_o"; // nom de la table ! requette
	    $resultFilmAtt = mysqli_query($connexion, $sqlFilmAtt ) or die(mysqli_error());
	$ordre = 1;   
	while ($dataFilmAtt = mysqli_fetch_array($resultFilmAtt)) {
		// définir si le cycle a été sélectionné	
		if ($fi == $dataFilmAtt['id_film'] ) {
		    echo '<tr class="select" id="pos_'.$dataFilmAtt['id_film'].'">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr id="pos_'.$dataFilmAtt['id_film'].'">'; // pour l'altérnance des couleurs
		}
		
		//Ordre
		//$ordre++;
		echo '<td>'.$ordre++.'</td>';
		
		//Année de production
		echo '<td>'.$dataFilmAtt['annee_prod'].'</td>';
		
		// Titre
		$prefixSelect = '';
		if($dataFilmAtt['prefix_titre_o']) {
			if($dataFilmAtt['prefix_titre_o']=="L'") {
				$prefixSelect.= "L'";
			} else {
				$prefixSelect.= utf8_encode($dataFilmAtt['prefix_titre_o']).' ';
			}
		}
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=film&amp;cy='.$cy.'&amp;fi='.$dataFilmAtt['id_film'].'#film">'.html_entity_decode(utf8_encode($prefixSelect.''.$dataFilmAtt['titre_o'])).'</a></td>'; //renvoi l'id du cycle en "GET"
		
		//Nombre de copies / vert si copie(s) sélectionnée(s)
		$sqlListCopie = "SELECT COUNT(id_copie) AS numb_copie
				      FROM pm_copie WHERE id_film = $dataFilmAtt[id_film]";
		$resultListCopie = mysqli_query($connexion, $sqlListCopie ) or die(mysqli_error());
		$dataListCopie = mysqli_fetch_array($resultListCopie);
		$sqlListCopie2 = "SELECT statut
				      FROM pm_copie WHERE id_film = $dataFilmAtt[id_film] and statut = 1 GROUP BY statut";
		$resultListCopie2 = mysqli_query($connexion, $sqlListCopie2 ) or die(mysqli_error());
		$dataListCopie2 = mysqli_fetch_array($resultListCopie2);
		if($dataListCopie2['statut'] == '1') {
			echo '<td class="select">';
		} else echo '<td>';
		echo $dataListCopie['numb_copie'].'</td>';
	
		// Liens avec les autres bases de données
		$link = ' ';
		$n = '0';
		$basefilm ='0';
		$sqllink = "SELECT base FROM pm_film_film
			      WHERE id_film = '$dataFilmAtt[id_film]'
			      ORDER BY base"; // nom de la table ! requette
		$resultlink = mysqli_query($connexion, $sqllink ) or die(mysqli_error());
		while ($datalink = mysqli_fetch_array($resultlink)) {
			if($n==0) {$link.='';} else {$link.=' | ';}
			$link.= $base[$datalink[base]].'';
			if($datalink['base']=='1') {$basefilm = '1';}
			$n++;
		}		
		echo '<td>'.$link.'</td>';
		
		//Formulaire d'édition / suppression
		if($_COOKIE['role']=='e') {	
		echo '<td>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=film&amp;cy='.$cy.'#film" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé ce lien?\')">
				<input type="hidden" name="id_film" value="'.$dataFilmAtt['id_film'].'">
				<input type="hidden" name="id_cycle" value="'.$cy.'">
				<input title="supprimer le lien" name="deleteLinkCycleFilm" class="btn_deletelink" type="submit" value="Supprimer">
			     </form>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=film&amp;cy='.$cy.'#film" method="post" onclick="return confirm(\'voulez-vous déselectionner ce film?\')">
				<input type="hidden" name="id_film" value="'.$dataFilmAtt['id_film'].'">
				<input type="hidden" name="id_cycle" value="'.$cy.'">
				<input title="déselectionner le film" name="deselectFilm" class="btn_deselect" type="submit" value="Deselect">
			     </form>';
			if($basefilm == '1') {	
				echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=copie&amp;cy='.$cy.'&amp;fi='.$fi.'#film" method="post">
					<input type="hidden" name="id_film" value="'.$dataFilmAtt['id_film'].'">
					<input title="actualiser" name="actuCopie" class="btn_actu" type="submit" value="Actu">
				     </form>';
			}
			echo '</td>';
		}
			    
		echo '</tr>'; //fermeture de la ligne
	    }
	    
	    if($deselect) {
		echo '<tr><td colspan="6">--------------------------------------------------------------------</td></tr>';
		/********************* Affichage des films déselectionnés *********************/
		$sqlFilmAtt = "SELECT pm_film.id_film, prefix_titre_o, titre_o, annee_prod FROM pm_cycle_film
			   JOIN pm_film
			   ON pm_cycle_film.id_film = pm_film.id_film
			   WHERE pm_cycle_film.id_cycle='$cy'
			   AND pm_cycle_film.actif = 'i'
			   ORDER BY annee_prod ASC, titre_o"; // nom de la table ! requette
		$resultFilmAtt = mysqli_query($connexion, $sqlFilmAtt ) or die(mysqli_error());
	    
		while ($dataFilmAtt = mysqli_fetch_array($resultFilmAtt)) {
			// définir si le cycle a été sélectionné	
			if ($fi == $dataFilmAtt['id_film'] ) {
			    echo '<tr class="select">'; // pour l'altérnance des couleurs
			} else {
			    echo '<tr>'; // pour l'altérnance des couleurs
			}
			//ordre
			echo '<td></td>';
			//Année de production
			echo '<td>'.$dataFilmAtt['annee_prod'].'</td>';
			
			// Titre
			$prefixSelect = '';
			if($dataFilmAtt['prefix_titre_o']) {
				if($dataFilmAtt['prefix_titre_o']=="L'") {
					$prefixSelect.= "L'";
				} else {
					$prefixSelect.= utf8_encode($dataFilmAtt['prefix_titre_o']).' ';
				}
			}
			echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=film&amp;deselect=deselect&amp;cy='.$cy.'&amp;fi='.$dataFilmAtt['id_film'].'#film">'.html_entity_decode(utf8_encode($prefixSelect.''.$dataFilmAtt['titre_o'])).'</a></td>'; //renvoi l'id du cycle en "GET"
			
			//Nombre de copies / vert si copie(s) sélectionnée(s)
			$sqlListCopie = "SELECT COUNT(id_copie) AS numb_copie
					      FROM pm_copie WHERE id_film = $dataFilmAtt[id_film]";
			$resultListCopie = mysqli_query($connexion, $sqlListCopie ) or die(mysqli_error());
			$dataListCopie = mysqli_fetch_array($resultListCopie);
			$sqlListCopie2 = "SELECT statut
					      FROM pm_copie WHERE id_film = $dataFilmAtt[id_film] and statut = 1 GROUP BY statut";
			$resultListCopie2 = mysqli_query($connexion, $sqlListCopie2 ) or die(mysqli_error());
			$dataListCopie2 = mysqli_fetch_array($resultListCopie2);
			if($dataListCopie2['statut'] == '1') {
				echo '<td class="select">';
			} else echo '<td>';
			echo $dataListCopie['numb_copie'].'</td>';
		
			// Liens avec les autres bases de données
			$link = ' ';
			$n = '0';
			$basefilm ='0';
			$sqllink = "SELECT base FROM pm_film_film
				      WHERE id_film = '$dataFilmAtt[id_film]'
				      ORDER BY base"; // nom de la table ! requette
			$resultlink = mysqli_query($connexion, $sqllink ) or die(mysqli_error());
			while ($datalink = mysqli_fetch_array($resultlink)) {
				if($n==0) {$link.='';} else {$link.=' | ';}
				$link.= $base[$datalink[base]].'';
				if($datalink['base']=='1') {$basefilm = '1';}
				$n++;
			}		
			echo '<td>'.$link.'</td>';
			
			//Formulaire d'édition / suppression
			if($_COOKIE['role']=='e') {	
			echo '<td>';
				echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=film&amp;cy='.$cy.'#film" method="post">
					<input type="hidden" name="id_film" value="'.$dataFilmAtt['id_film'].'">
					<input type="hidden" name="id_cycle" value="'.$cy.'">
					<input type="hidden" value="deselect" name="deselect">
					<input title="sélectionner le film" name="selectFilm" class="btn_select" type="submit" value="Select">
				     </form>';
				echo '</td>';
			}
				    
			echo '</tr>'; //fermeture de la ligne
		    }
	    }
?>
	    </tbody>
	</table>
</div>

<?php
$prefix_realisateur = '99';
$prefix_acteur = '99';
/*************************************************************** Formulaire du Film ***************************************************************/
if($_REQUEST['zone']=='film' || $_REQUEST['zone']=='copie') {
	if($fi && $fi != 1) {
		$sqlFilmSelect = "SELECT * FROM pm_film WHERE id_film ='$fi'"; // nom de la table ! requette
		$resultFilmSelect = mysqli_query($connexion, $sqlFilmSelect ) or die(mysqli_error());
		$dataFilmSelect = mysqli_fetch_array($resultFilmSelect);
		$prefix_realisateur = $dataFilmSelect['prefix_film_director'];
		$prefix_acteur = $dataFilmSelect['prefix_film_actor'];
	}
?>
<div class="right">
	<h4>Ajout</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>#film" method="post">
	
<?php include($_COOKIE['indexApp']."/inc/pm_fiche_film.php"); ?>

	    <input type="hidden" name="id_film" value="<?php echo $fi; ?>">
	    <input type="hidden" name="id_cycle" value="<?php echo $cy; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveFilm" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>

<div class="middle">
<?php
if($listeMulti) {
	include($_COOKIE['indexApp']."/inc/pm_list.php");
}else {
	include($_COOKIE['indexApp']."/inc/pm_recherche.php");
}?>
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
	    <input type="hidden" value="<?php echo $cy; ?>" name="cy">
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
		    <th>Sous-titres</th>
		    <th>Durée</th>
		    <th>Statut</th>
		    <th>Cote</th>
<?php 		    if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
		</tr>
	    </thead>
	    <tbody>
<?php 
/****************************************************************************************************************************
 * ********************************************** AFFICHAGE DES COPIES ******************************************************
 * **************************************************************************************************************************/
	$sqlCopieAtt = "SELECT id_copie, provenance, format, version, soustitre, duree, statut, cote FROM pm_copie WHERE id_film ='$fi' AND id_film > 0 ORDER BY statut"; // nom de la table ! requette
	$resultCopieAtt = mysqli_query($connexion, $sqlCopieAtt ) or die(mysqli_error());
	    
	include($_COOKIE['indexApp']."/inc/pm_table_copie.php"); 
?>
	    </tbody>
	</table>
</div>

<?php
/****************************************** Formulaire de Copie ***************************************************************/
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
}} 
}}?>

<hr class="clear">

