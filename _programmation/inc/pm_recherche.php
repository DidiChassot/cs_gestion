<?php
/** Recherche dans les bases de données
 * 
 * Cindy Chassot 09.04.2015 - 29.04.2015
 * © Cinémathèque suisse
 */

foreach ($_POST as $key => $value) {
	$$key = $value;
}
 
?>

	<h4>Recherche</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>#film" method="post">
	    <div>
		<label for="baseselect">Base de données</label>
		<select name="baseselect">
<?php
			foreach ($base as $key => $value) {
			    echo '<option ';
			    if($_POST['baseselect']==$key) {
				echo 'selected="selected" ';
			    }
			    echo 'value="'.$key.'">';
			    echo $value;
			    echo '</option>';
			}
?>		</select>
	    </div>
	    <div>
		<label for="motif"></label>
		<select id="motif" name="motif" class="input31">
			<optgroup label="Rechercher par">
			<option <?php if($_POST['motif']=='titre') echo 'selected="selected"'; ?> value="titre">Titre</option>
			<option <?php if($_POST['motif']=='realisateur') echo 'selected="selected"'; ?> value="realisateur">Réalisateur</option>
		</select>
		<label for="recherche"></label>
		<input id="recherche" type="text" placeholder="Recherche" name="recherche" value="<?php echo $_POST['recherche']; ?>" class="input36">
	    </div>
	    <input type="submit" name="rechercheFilm" class="btn" value="Rechercher">
	</form>
<hr >

<?php 	
/*****************************************************************
 * Si le précédent formulaire a bien été envoyé
 *
 * $_POST['rechercheFilm'] => pour lancer la requête
 * $_POST['baseselect']=> définir la base de données de recherche
 * $_POST['motif']=> définir le motif de la recherche
 * $_POST['recherche']=> définir le contenu de la recherche
 * 
 *****************************************************************/
if($_POST['rechercheFilm']) {
	
	//Traitement des variables
	if($_POST['motif'] == 'titre') {
		$searchTitreSound = clean($_POST['recherche']);
		$searchTitre = ($_POST['recherche']) ? '%'.clean($_POST['recherche']).'%' : '%';
		
	}elseif($_POST['motif'] == 'realisateur') {
		$searchRealisateur = ($_POST['recherche']) ? '%'.clean($_POST['recherche']).'%' : '%';	
	}
	
/**************************************** Traitement pour la base de programmation ****************************************/	
    if($_POST['baseselect']=='0'){
	if($_COOKIE['sousMenu'] != '_sm4') {?>
		<form action="<?php $_SERVER['PHP_SELF']; ?>#film" method="post" style="margin-top:65px;">
	<?php } ?>
		<!--  table d'affichage	-->
		<table class="table">
		    <thead>
			<tr>
			    <th style="min-width:105px"><nobr>Titre original</nobr></th>
			    <th>Année</th>
			    <th>Réal.</th>
			    <th>Cop.</th>
			    <th>Synchro</th>
			    <!--<th>Prog.</th>-->
			    <?php if($_COOKIE['sousMenu'] != '_sm4') {echo '<th>Add</th>';} ?>
			</tr>
		    </thead>
		    <tbody>
			<tr style="display:none;"><form></form></tr>
			
	<?php	//recherche dans la base / condition de recherche
		if($_POST['motif'] == 'titre') {
			$sqlFilmBase = "SELECT id_film, prefix_titre_o, titre_o, annee_prod, realisateur FROM pm_film
					WHERE titre_o LIKE '$searchTitre' OR SOUNDEX(titre_o) = SOUNDEX('$searchTitreSound')";
					
		} elseif($_POST['motif'] == 'realisateur') {
			$sqlFilmBase = "SELECT id_film, prefix_titre_o, titre_o, annee_prod, realisateur FROM pm_film
					WHERE realisateur LIKE '$searchRealisateur'
					ORDER BY annee_prod";
		}
		
		$resultFilmBase = mysqli_query($connexion, $sqlFilmBase ) or die(mysqli_error());
		while ($dataFilmBase = mysqli_fetch_array($resultFilmBase)) {
			// définir si le film a été sélectionné	
		    if ($fi == $dataFilmBase['id_film'] ) {
			echo '<tr class="select">'; // pour l'altérnance des couleurs
		    } else {
			echo '<tr>'; // pour l'altérnance des couleurs
		    }
			$prefixSelect = '';
			if($dataFilmBase['prefix_titre_o']) {
			    if($dataFilmBase['prefix_titre_o']=="L'") {
				$prefixSelect.= "L'";
			    } else {
				$prefixSelect.= utf8_encode($dataFilmBase['prefix_titre_o']).' ';
			    }
			}
			echo '<td><form action="';
			if($_COOKIE['sousMenu'] == '_sm4') {
				echo $_SERVER['PHP_SELF'].'?zone=film&amp;fi='.$dataFilmBase['id_film'].'#film';
			} elseif($_COOKIE['sousMenu'] == '_sm0') {
				echo $_SERVER['PHP_SELF'].'?zone=film&amp;cy='.$cy.'&amp;fi='.$dataFilmBase['id_film'].'#film';
			} else {
				echo $_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy.'&amp;fi='.$dataFilmBase['id_film'].'#film';
			}
			echo '" method="post">
			
				<input type="hidden" name="baseselect" value="'.$baseselect.'">
				<input type="hidden" value="'.$motif.'" name="motif">
				<input type="hidden" value="'.$recherche.'" name="recherche">
				<input type="hidden" value="'.$dataFilmBase['id_film'].'" name="fi">
				<input type="submit" name="rechercheFilm" class="no_btn" value="'.html_entity_decode($prefixSelect.''.utf8_encode($dataFilmBase['titre_o'])).'">
			</form></td>'; //renvoi l'id du cycle en "GET"
			echo '<td>'.$dataFilmBase['annee_prod'].'</td>';
			echo '<td>'.utf8_encode($dataFilmBase['realisateur']).'</td>';
			
			//Nombre de copies / vert si copie(s) sélectionnée(s)
			$sqlListCopie = "SELECT COUNT(id_copie) AS numb_copie,
				      CASE statut
					WHEN 1 THEN '1'
					ELSE '0'
				      END AS statut
				      FROM pm_copie WHERE id_film = $dataFilmBase[id_film]";
			$resultListCopie = mysqli_query($connexion, $sqlListCopie ) or die(mysqli_error());
			$dataListCopie = mysqli_fetch_array($resultListCopie);
			if($dataListCopie['statut'] == '1') {
				echo '<td class="select">';
			} else echo '<td>';
			echo $dataListCopie['numb_copie'].'</td>';
			
			// Liens avec les autres bases de données
			$link = ' ';
			$n = '0';
			$sqllink = "SELECT base FROM pm_film_film
				      WHERE id_film = '$dataFilmBase[id_film]'
				      ORDER BY base"; // nom de la table ! requette
			$resultlink = mysqli_query($connexion, $sqllink ) or die(mysqli_error());
			while ($datalink = mysqli_fetch_array($resultlink)) {
				if($n==0) {$link.='';} else {$link.=' | ';}
				$link.= $base[$datalink['base']].'';
				$n++;
			}
			echo '<td>'.$link.'</td>';
			
			//nombre de lien avec des cycles
			/*$sqlproj = "SELECT COUNT(DISTINCT pm_cycle_film.id_cycle) AS proj FROM `pm_cycle_film`
			JOIN pm_cycle
			ON pm_cycle.id_cycle = pm_cycle_film.id_cycle
			WHERE pm_cycle.actif = 'a'
			AND pm_cycle_film.id_film = '$dataFilmBase[id_film]'
			AND pm_cycle_film.actif = 'a'"; // nom de la table ! requette
			$resultproj = mysqli_query($connexion, $sqlproj ) or die(mysqli_error());
			$dataproj = mysqli_fetch_array($resultproj);
			echo '<td>'.$dataproj['proj'].'</td>';*/
			
			//Ajout multiple
			if($_COOKIE['sousMenu'] != '_sm4') {echo '<td><input type="checkbox" value="'.$dataFilmBase['id_film'].'" name="link[]"></td>';}
			//fermeture de la ligne
			echo '</tr>';
		    }
		echo '</table>';
	    if($_COOKIE['sousMenu'] != '_sm4') {	    
		echo '<input type="hidden" name="id_cycle" value="'.$cy.'">';
		echo '<input type="submit" class="btn" name="linkMultiCycleFilm" value="Ajout Multiple">';
	    }
	echo '</form>';
	    
/**************************************** Traitement pour la base FILM (Oracle) ****************************************/	    
    } elseif($_POST['baseselect']=='1'){ //si base oracle
	
	//form pour l'ajout multiple
	if($_COOKIE['sousMenu'] != '_sm4') {?>
		<form action="<?php $_SERVER['PHP_SELF']; ?>#film" method="post" style="margin-top:65px;">
	<?php } ?>
	
	<!--  table d'affichage	Oracle-->
	<table class="table">
	    <thead>
		<tr>
		    <th>Titre principal</th>
		    <th>Année</th>
		    <th>Réalisateur(s)</th>
		    <th>Copie</th>
		    <th>ID Syn.</th>
		    <?php if($_COOKIE['sousMenu'] != '_sm4') {echo '<th>Add</th>';} ?>
		</tr>
	    </thead>
	    <tbody>
<?php
	if($_POST['motif'] == 'titre') {
		$sqlFilmBase = "SELECT id_film, prefix_titre_o, titre_o, annee_prod, realisateur FROM or_film
				WHERE titre_o LIKE '$searchTitre' OR SOUNDEX(titre_o) = SOUNDEX('$searchTitreSound')
				ORDER BY annee_prod";
				
	} elseif($_POST['motif'] == 'realisateur') {
		$sqlFilmBase = "SELECT id_film, prefix_titre_o, titre_o, annee_prod, realisateur FROM or_film
				WHERE realisateur LIKE '$searchRealisateur'
				ORDER BY annee_prod";
	}
	
	$resultFilmBase = mysqli_query($connexion, $sqlFilmBase ) or die(mysqli_error());
	while ($dataFilmBase = mysqli_fetch_array($resultFilmBase)) {
		// définir si le film a été sélectionné	
		if ($id_search == $dataFilmBase['id_film'] ) {
		    echo '<tr class="select">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr>'; // pour l'altérnance des couleurs
		}
		$prefixSelect = '';
		if($dataFilmBase['prefix_titre_o']) {
			if($dataFilmBase['prefix_titre_o']=="L'") {
				$prefixSelect.= "L'";
			} else {
				$prefixSelect.= utf8_encode($dataFilmBase['prefix_titre_o']).' ';
			}
		}
		
		echo '<td><form action="';
		if($_COOKIE['sousMenu'] == '_sm4') {
			echo $_SERVER['PHP_SELF'].'?zone=film&amp;fi='.$fi.'#film';
		} else {
			echo $_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy.'&amp;fi='.$fi.'#film';
		}
		echo '" method="post">
			<input type="hidden" name="baseselect" value="'.$baseselect.'">
			<input type="hidden" value="'.$motif.'" name="motif">
			<input type="hidden" value="'.$recherche.'" name="recherche">
			<input type="hidden" name="id_search" value="'.$dataFilmBase['id_film'].'">
			<input type="submit" name="rechercheFilm" class="no_btn" value="'.html_entity_decode($prefixSelect.''.utf8_encode($dataFilmBase['titre_o'])).'">
		</form></td>'; //renvoi l'id du cycle en "GET"
		
		//année de production du film
		echo '<td>'.$dataFilmBase['annee_prod'].'</td>';
		
		//réalisateur
		echo '<td>'.utf8_encode($dataFilmBase['realisateur']).'</td>';
		
		//nombre de copies
			$sqlListCopie = "SELECT COUNT(id_copie) AS numb_copie FROM or_copie WHERE id_film = $dataFilmBase[id_film]";
			$resultListCopie = mysqli_query($connexion, $sqlListCopie ) or die(mysqli_error());
			$dataListCopie = mysqli_fetch_array($resultListCopie);
			echo '<td>'.$dataListCopie['numb_copie'].'</td>';
		
		//synchronisation avec programmation
		$sqlSynchro = "SELECT id_film FROM pm_film_film
			      WHERE id_foreign ='$dataFilmBase[id_film]' AND base = '$baseselect'"; // nom de la table ! requette
		$resultSynchro = mysqli_query($connexion, $sqlSynchro ) or die(mysqli_error());
		$dataSynchro = mysqli_fetch_array($resultSynchro);
		if($_COOKIE['sousMenu'] == '_sm4') {
		    echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=film&amp;fi='.$dataSynchro['id_film'].'#film">'.$dataSynchro['id_film'].'</a></td>';
		} else {
		    echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=film&amp;cy='.$cy.'&amp;fi='.$dataSynchro['id_film'].'#film">'.$dataSynchro['id_film'].'</a></td>';
		}
			
		//Ajout multiple
		if($_COOKIE['sousMenu'] != '_sm4') {echo '<td><input type="checkbox" value="'.$dataFilmBase['id_film'].'" name="link[]"></td>';}

		//fermeture de la ligne
		echo '</tr>'; 
	    

	}
?>
	    </tbody>
	</table>
<?php
	    if($_COOKIE['sousMenu'] != '_sm4') {	    
		echo '<input type="hidden" name="id_cycle" value="'.$cy.'">
		      <input type="hidden" name="baseselect" value="'.$baseselect.'">
		      <input type="submit" class="btn" name="linkSynchroMultiFilm" value="Synchro Multi">';
	    }
	echo '</form>';
?>
<!--  fin de la table d'affichage typo3-->
<!--  Formulaire de synchronisation typo3-->
<?php if($id_search>0) {
	$sqlFilmOracle = "SELECT * FROM or_film
			WHERE id_film = '$id_search'";
	$resultFilmOracle = mysqli_query($connexion, $sqlFilmOracle ) or die(mysqli_error());
	$dataFilmOracle = mysqli_fetch_array($resultFilmOracle);
?>
	<form action="<?php $_SERVER['PHP_SELF']; ?>#film" method="post" class="synchro">
	    <div>
		<div class="input31">
			<label for="prefix_titre_o">Préf. titre principal</label>
			<input type="text" value="<?php echo $dataFilmOracle['prefix_titre_o']; ?>" name="prefix_titre_o" placeholder="">
			<input type="checkbox" value="x" name="s_prefix_titre_o" checked>
		</div>
		<div class="input36">
			<label for="titre_o">Titre principal</label>
			<input type="text" value="<?php echo utf8_encode($dataFilmOracle['titre_o']); ?>"  name="titre_o" placeholder="">
			<input type="checkbox" value="x" name="s_titre_o" checked>
		</div>
	    </div>
	    <div>
		<div class="input31">
			<label for="prefix_titre_fr">Préf. titre secondaire</label>
			<input type="text" value="<?php echo $dataFilmOracle['prefix_titre_fr']; ?>" name="prefix_titre_fr" placeholder="">
			<input type="checkbox" value="x" name="s_prefix_titre_fr" checked>
		</div>
		<div class="input36">
			<label for="titre_fr">Titre secondaire</label>
			<input type="text" value="<?php echo utf8_encode($dataFilmOracle['titre_fr']); ?>" name="titre_fr" placeholder="">
			<input type="checkbox" value="x" name="s_titre_fr" checked>
		</div>
	    </div>
	    <div>
		<div class="input31">
			<label for="annee_prod">Année de production</label>
			<input type="text" value="<?php echo $dataFilmOracle['annee_prod']; ?>" name="annee_prod" placeholder="">
			<input type="checkbox" value="x" name="s_annee_prod" checked>
		</div>
		<div class="input36">
			<label for="pays_prod">Pays de production</label>
			<input type="text" value="<?php echo utf8_encode($dataFilmOracle['pays_prod']); ?>" name="pays_prod" placeholder="">
			<input type="checkbox" value="x" name="s_pays_prod" checked>
		</div>
	    </div>
		<div>
			<label for="realisateur">Réalisateur(s)</label>
			<input type="text" value="<?php echo utf8_encode($dataFilmOracle['realisateur']); ?>" name="realisateur" placeholder="">
			<input type="checkbox" value="x" name="s_realisateur" checked>
		</div>
	    <input type="hidden" name="id_cycle" value="<?php echo $cy; ?>">
	    <input type="hidden" name="id_film" value="<?php echo $fi; ?>">
	    <input type="hidden" name="baseselect" value="<?php echo $baseselect; ?>">
	    <input type="hidden" name="motif" value="<?php echo $motif; ?>">
	    <input type="hidden" name="recherche" value="<?php echo $recherche; ?>">
	    <input type="hidden" name="rechercheFilm" value="<?php echo $rechercheFilm; ?>">
	    <input type="hidden" name="id_search" value="<?php echo $id_search; ?>">
	    <input type="submit" class="btn" name="synchroMovie" value="Synchroniser">
	    <div class="clear"></div>
	</form>
		
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
	$sqlCopieAtt = "SELECT id_copie, format, version, soustitre, duree, cote FROM or_copie WHERE id_film = $id_search"; // nom de la table ! requette
	$resultCopieAtt = mysqli_query($connexion, $sqlCopieAtt ) or die(mysqli_error());
	$provenance = 'CS';
	include($_COOKIE['indexApp']."/inc/pm_table_copie.php"); 
?>
	    </tbody>
	</table>
<?php } ?>
	
<?php   /**************************************** Traitement pour la base BULLETIN (Typo3) ****************************************/	
   } elseif($_POST['baseselect']=='2'){ //si base Typo3
?>

	<!--  table d'affichage	typo3-->
	<table class="table">
	    <thead>
		<tr>
		    <th>Titre original</th>
		    <th>Année</th>
		    <th>Réalisateur(s)</th>
		    <th>ID Syn.</th>
		    <th>Prog.</th>
		</tr>
	    </thead>
	    <tbody>
<?php
	//Recherche dans typo3 / Condition de recherche
	if($_POST['motif'] == 'titre') {
		$sqlFilmBase = "SELECT film.uid AS id_film, prefix_original_title AS prefix_titre_o, original_title AS titre_o, production_year AS annee_prod, GROUP_CONCAT(CONCAT(person_dir.first_name, ' ', person_dir.last_name)) AS film_directors 
				FROM tx_bulletin_domain_model_film AS film

				LEFT JOIN tx_bulletin_film_directors_person_mm AS directorslink
				ON directorslink.uid_local = film.uid 
				LEFT JOIN tx_bulletin_domain_model_person AS person_dir
				ON directorslink.uid_foreign = person_dir.uid
				
				WHERE film.deleted = 0
				AND (original_title LIKE '$searchTitre' OR SOUNDEX(original_title) = SOUNDEX('$searchTitreSound'))
				
				GROUP BY film.uid";
				
	} elseif($_POST['motif'] == 'realisateur') {
		$sqlFilmBase = "SELECT film.uid AS id_film, prefix_original_title AS prefix_titre_o, original_title AS titre_o, production_year AS annee_prod, GROUP_CONCAT(CONCAT(person_dir.first_name, ' ', person_dir.last_name)) AS film_directors 
				FROM tx_bulletin_domain_model_film AS film

				LEFT JOIN tx_bulletin_film_directors_person_mm AS directorslink
				ON directorslink.uid_local = film.uid 
				LEFT JOIN tx_bulletin_domain_model_person AS person_dir
				ON directorslink.uid_foreign = person_dir.uid
				
				WHERE film.deleted = 0
				AND (CONCAT(person_dir.first_name, ' ', person_dir.last_name) LIKE '$searchRealisateur')
				
				GROUP BY film.uid
				ORDER BY production_year";
	}
	
	    $resultFilmBase = mysqli_query($connexTypo3, $sqlFilmBase ) or die(mysqli_error());
	    while ($dataFilmBase = mysqli_fetch_array($resultFilmBase)) {
		// définir si le film a été sélectionné	
		if ($id_search == $dataFilmBase['id_film'] ) {
		    echo '<tr class="select">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr>'; // pour l'altérnance des couleurs
		}
		$prefixSelect = '';
		if($dataFilmBase['prefix_titre_o']) {
			if($dataFilmBase['prefix_titre_o']=="L'") {
				$prefixSelect.= "L'";
			} else {
				$prefixSelect.= utf8_encode($dataFilmBase['prefix_titre_o']).' ';
			}
		}
		//récupération du nombre de programmation déjà effectuée pour ce film old
		$sqlCountMovieOld = "SELECT COUNT(uid_local) AS oldprog FROM tx_bulletin_cycle_film_mm  WHERE uid_foreign = '$dataFilmBase[id_film]'";
		$resultCountMovieOld = mysqli_query($connexTypo3, $sqlCountMovieOld ) or die(mysqli_error());
		$dataCountMovieOld = mysqli_fetch_array($resultCountMovieOld);
		//récupération du nombre de programmation déjà effectuée pour ce film new
		$sqlCountMovieNew = "SELECT COUNT(cycle_id) AS newprog FROM tx_bulletin_cycle_has_film_relations  WHERE film_id = '$dataFilmBase[id_film]'";
		$resultCountMovieNew = mysqli_query($connexTypo3, $sqlCountMovieNew ) or die(mysqli_error());
		$dataCountMovieNew = mysqli_fetch_array($resultCountMovieNew);
		//addition
		$total = $dataCountMovieNew['newprog'] + $dataCountMovieOld['oldprog'];
		
		
		echo '<td><form action="';
		if($_COOKIE['sousMenu'] == '_sm4') {
			echo $_SERVER['PHP_SELF'].'?zone=film&amp;fi='.$fi.'#film';
		} else {
			echo $_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy.'&amp;fi='.$fi.'#film';
		}
		echo '" method="post">
			<input type="hidden" name="baseselect" value="'.$baseselect.'">
			<input type="hidden" value="'.$motif.'" name="motif">
			<input type="hidden" value="'.$recherche.'" name="recherche">
			<input type="hidden" name="id_search" value="'.$dataFilmBase['id_film'].'">
			<input type="submit" name="rechercheFilm" class="no_btn" value="'.html_entity_decode($prefixSelect.''.utf8_encode($dataFilmBase['titre_o'])).'">
		</form></td>'; //renvoi l'id du cycle en "GET"
		echo '<td>'.$dataFilmBase['annee_prod'].'</td>';
		echo '<td>'.utf8_encode($dataFilmBase['film_directors']).'</td>';
		
		$sqlSynchro = "SELECT id_film FROM pm_film_film
			      WHERE id_foreign ='$dataFilmBase[id_film]' AND base = '$baseselect'"; // nom de la table ! requette
		$resultSynchro = mysqli_query($connexion, $sqlSynchro ) or die(mysqli_error());
		$dataSynchro = mysqli_fetch_array($resultSynchro);
		if($_COOKIE['sousMenu'] == '_sm4') {
		    echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=film&amp;fi='.$dataSynchro['id_film'].'#film">'.$dataSynchro['id_film'].'</a></td>';
		} else {
		    echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=film&amp;cy='.$cy.'&amp;fi='.$dataSynchro['id_film'].'#film">'.$dataSynchro['id_film'].'</a></td>';
		}
		echo '<td>'.$total.'</td>';
		//fermeture de la ligne
		echo '</tr>'; 
	    

	}
?>
	    </tbody>
	</table>
<!--  fin de la table d'affichage typo3-->
<!--  Formulaire de synchronisation typo3-->
<?php if($id_search>0) {
	$sqlFilmTypo3 = "SELECT film.uid, original_title, prefix_original_title, french_title, prefix_french_title, production_year, age_legal, age_suggested, family, prefix_film_director, prefix_actor, actors, countries_of_production, 
			GROUP_CONCAT(CONCAT(person_dir.first_name, ' ', person_dir.last_name)) AS film_directors
			FROM tx_bulletin_domain_model_film AS film
			LEFT JOIN tx_bulletin_film_directors_person_mm AS directorslink
			ON directorslink.uid_local = film.uid 
			LEFT JOIN tx_bulletin_domain_model_person AS person_dir
			ON directorslink.uid_foreign = person_dir.uid 
			WHERE film.uid = '$id_search' 
			GROUP BY film.uid";
	$resultFilmTypo3 = mysqli_query($connexTypo3, $sqlFilmTypo3 ) or die(mysqli_error());
	$dataFilmTypo3 = mysqli_fetch_array($resultFilmTypo3);
?>
	<form action="<?php $_SERVER['PHP_SELF']; ?>#film" method="post" class="synchro">
	    <div>
		<div class="input31">
			<label for="prefix_titre_o">Préf. titre principal</label>
			<input type="text" value="<?php echo $dataFilmTypo3['prefix_original_title']; ?>" name="prefix_titre_o" placeholder="">
			<input type="checkbox" value="x" name="s_prefix_titre_o" checked>
		</div>
		<div class="input36">
			<label for="titre_o">Titre principal</label>
			<input type="text" value="<?php echo utf8_encode($dataFilmTypo3['original_title']); ?>"  name="titre_o" placeholder="">
			<input type="checkbox" value="x" name="s_titre_o" checked>
		</div>
	    </div>
	    <div>
		<div class="input31">
			<label for="prefix_titre_fr">Préf. titre secondaire</label>
			<input type="text" value="<?php echo $dataFilmTypo3['prefix_french_title']; ?>" name="prefix_titre_fr" placeholder="">
			<input type="checkbox" value="x" name="s_prefix_titre_fr" checked>
		</div>
		<div class="input36">
			<label for="titre_fr">Titre secondaire</label>
			<input type="text" value="<?php echo utf8_encode($dataFilmTypo3['french_title']); ?>" name="titre_fr" placeholder="">
			<input type="checkbox" value="x" name="s_titre_fr" checked>
		</div>
	    </div>
	    <div>
		<div class="input31">
			<label for="annee_prod">Année de production</label>
			<input type="text" value="<?php echo $dataFilmTypo3['production_year']; ?>" name="annee_prod" placeholder="">
			<input type="checkbox" value="x" name="s_annee_prod" checked>
		</div>
<?php
$lieu_prod = '';
if($dataFilmTypo3['countries_of_production']) {
	//$lieu_prod = $id_prod; // test de l'ordre des id de pays !!
	$array_prod = explode(",", $dataFilmTypo3['countries_of_production']); // construire l'array sur les lieu de production

	$test_sous = 0; // mise à zéro du témoin
	foreach ($array_prod as $lieu_a){ // foreach qui permet de reprendre chaque valeur du tableau pour la mettre dans la boucle
		$sql10 = "SELECT cn_short_fr FROM tx_rpmoviespecdb_countries WHERE uid = '$lieu_a'";
		$result10 = mysqli_query($connexTypo3, $sql10 ) or die(mysqli_error());
		while ($data10 = mysqli_fetch_array($result10)) {
		    if($test_sous == 0) { // condition pour la virgule si +de 1pays...
			$lieu_prod .= $data10["cn_short_fr"];
		    } else {
			$lieu_prod .= ', '.$data10["cn_short_fr"];
		    }
		    $test_sous +=1; // incrémentation du témoin si +de 1pays
		}
	}
}
?>
		<div class="input36">
			<label for="pays_prod">Pays de production</label>
			<input type="text" value="<?php echo utf8_encode($lieu_prod); ?>" name="pays_prod" placeholder="">
			<input type="checkbox" value="x" name="s_pays_prod" checked>
		</div>
	    </div>
	    <div>
<?php
$prefix_realisateur = $dataFilmTypo3['prefix_film_director'];
	
?>
		<div class="input31">
			<label for="prefix_realisateur"></label>
			<select class="input31" name="prefix_realisateur">
				<option <?php if($prefix_realisateur==0) echo 'selected="selected"'; ?> value="0">---</option>
				<option <?php if($prefix_realisateur==1) echo 'selected="selected"'; ?> value="1">De</option>
				<option <?php if($prefix_realisateur==2) echo 'selected="selected"'; ?> value="2">Documentaire de</option>
				<option <?php if($prefix_realisateur==3) echo 'selected="selected"'; ?> value="3">Film d'animation de</option>
				<option <?php if($prefix_realisateur==4) echo 'selected="selected"'; ?> value="4">Film collectif de</option>
				<option <?php if($prefix_realisateur==5) echo 'selected="selected"'; ?> value="5">Court métrage de</option>
			</select>
			<input type="checkbox" value="x" name="s_prefix_realisateur" checked>
		</div>
		<div class="input36">
			<label for="realisateur"></label>
			<input type="text" value="<?php echo utf8_encode($dataFilmTypo3['film_directors']); ?>" name="realisateur" placeholder="Réalisateur(s)">
			<input type="checkbox" value="x" name="s_realisateur" checked>
		</div>
	    </div>
	    <div>
<?php
$prefix_acteur = $dataFilmTypo3['prefix_actor'];
?>
		<div class="input31">
			<label for="prefix_acteur"></label>
			<select class="input31" name="prefix_acteur">
				<option <?php if($prefix_acteur==0) echo 'selected="selected"'; ?> value="0">---</option>
				<option <?php if($prefix_acteur==1 || $prefix_acteur==99) echo 'selected="selected"'; ?> value="1">Avec</option>
				<option <?php if($prefix_acteur==2) echo 'selected="selected"'; ?> value="2">Avec les voix de</option>
			</select>
			<input type="checkbox" value="x" name="s_prefix_acteur" checked>
		</div>
<?php
if($dataFilmTypo3['actors']>0) {
	$sqlActeurs = "SELECT GROUP_CONCAT(CONCAT(person_act.first_name, ' ', person_act.last_name)) AS actors
			FROM tx_bulletin_film_actors_person_mm AS actorslink
			LEFT JOIN tx_bulletin_domain_model_person AS person_act
			ON actorslink.uid_foreign = person_act.uid 
			WHERE actorslink.uid_local = '$id_search' 
			GROUP BY actorslink.uid_local";
	$resultActeurs = mysqli_query($connexTypo3, $sqlActeurs ) or die(mysqli_error());
	$dataActeurs = mysqli_fetch_array($resultActeurs);
	
}
?>
		<div class="input36">
			<label for="acteur"></label>
			<input type="text" value="<?php echo utf8_encode($dataActeurs['actors']); ?>" name="acteur" placeholder="Acteur(s)">
			<input type="checkbox" value="x" name="s_acteur" checked>
		</div>
	    </div>
	    <div>
		<div class="input31">
			<label for="age_legal">Age légal</label>
			<input type="text" value="<?php echo $dataFilmTypo3['age_legal']; ?>" name="age_legal" placeholder="">
			<input type="checkbox" value="x" name="s_age_legal" checked>
		</div>
		<div class="input32">
			<label for="age_sugg">Age suggéré</label>
			<input type="text" value="<?php echo $dataFilmTypo3['age_suggested']; ?>" name="age_sugg" placeholder="">
			<input type="checkbox" value="x" name="s_age_sugg" checked>
		</div>
		<div class="input32">
			<label for="film_famille">film_famille</label>
			<input type="text" value="<?php if($dataFilmTypo3['family'])echo 'x'; ?>" placeholder="Film Famille" name="film_famille">
			<input type="checkbox" value="1" name="s_film_famille">
		</div>
	    </div>
	    <input type="hidden" name="id_cycle" value="<?php echo $cy; ?>">
	    <input type="hidden" name="id_film" value="<?php echo $fi; ?>">
	    <input type="hidden" name="baseselect" value="<?php echo $baseselect; ?>">
	    <input type="hidden" name="motif" value="<?php echo $motif; ?>">
	    <input type="hidden" name="recherche" value="<?php echo $recherche; ?>">
	    <input type="hidden" name="rechercheFilm" value="<?php echo $rechercheFilm; ?>">
	    <input type="hidden" name="id_search" value="<?php echo $id_search; ?>">
	    <input type="submit" class="btn" name="synchroMovie" value="Synchroniser">
	    <div class="clear"></div>
	</form>
<?php } } ?>
<!--  Fin du formulaire de synchronisaion typo3-->

<?php } // fin de la condition de la recherche ?>