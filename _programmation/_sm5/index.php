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
<h2>Recherche dans les archives</h2>
<div class="left">
  <h4>Par film</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="motif">Rechercher par</label>
		<select id="motif" name="motif" class="input31">
			<optgroup label="Rechercher par">
			<option <?php if($_POST['motif']=='titre') echo 'selected="selected"'; ?> value="titre">Titre</option>
			<option <?php if($_POST['motif']=='realisateur') echo 'selected="selected"'; ?> value="realisateur">Réalisateur</option>
		</select>
		<label for="recherche"></label>
		<input id="recherche" type="text" placeholder="Recherche" name="recherche" value="<?php if($rechercheFilm) echo $_POST['recherche']; ?>" class="input36">
	    </div>
	    <input type="submit" name="rechercheFilm" class="btn" value="Rechercher">
	</form>
</div>
<div class="right">
  <h4>Par période</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div class="input21">
		<label for="start">Date de début</label>
		<input type="date" placeholder="2015-01-26" name="start" value="<?php echo utf8_encode($_POST['start']); ?>">
	    </div>
	    <div class="input22">
		<label for="end">Date de fin</label>
		<input type="date" placeholder="2015-03-15" name="end" value="<?php echo utf8_encode($_POST['end']); ?>">
	    </div>
				<input type="hidden" name="liste" value="liste">
	    <input type="submit" name="recherchePeriode" class="btn" value="Rechercher">
	</form>
</div>
<div class="middle">
  <h4>Par cycle</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="recherche">Titre</label>
		<input id="recherche" type="text" name="recherche" value="<?php if($rechercheCycle) echo $_POST['recherche']; ?>">
	    </div>
				<input type="hidden" name="liste" value="liste">
	    <input type="submit" name="rechercheCycle" class="btn" value="Rechercher">
	</form>
</div>
<?php

if($_POST['rechercheFilm']) {
	echo '<hr class="clear">';
	
	//Traitement des variables
	if($_POST['motif'] == 'titre') {
		$searchTitreSound = clean($_POST['recherche']);
		$searchTitre = ($_POST['recherche']) ? '%'.clean($_POST['recherche']).'%' : '%';
		
	}elseif($_POST['motif'] == 'realisateur') {
		$searchRealisateur = ($_POST['recherche']) ? '%'.clean($_POST['recherche']).'%' : '%';	
	}
	?>
	<div class="left">
	<table class="table">
		    <thead>
			<tr>
			    <th>Titre original</th>
			    <th>Année</th>
			    <th>Réalisateur(s)</th>
			    <th>Copies</th>
			    <th>Synchro</th>
			    <th>Prog.</th>
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
				echo $_SERVER['PHP_SELF'].'?zone=film&amp;fi='.$dataFilmBase['id_film'].'#film';
			echo '" method="post">
				<input type="hidden" name="liste" value="liste">
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
			
			
			$sqlproj = "SELECT COUNT(DISTINCT pm_cycle_film.id_cycle) AS proj FROM `pm_cycle_film`
			JOIN pm_cycle
			ON pm_cycle.id_cycle = pm_cycle_film.id_cycle
			WHERE pm_cycle.actif = 'a'
			AND pm_cycle_film.id_film = '$dataFilmBase[id_film]'
			AND pm_cycle_film.actif = 'a'"; // nom de la table ! requette
			$resultproj = mysqli_query($connexion, $sqlproj ) or die(mysqli_error());
			$dataproj = mysqli_fetch_array($resultproj);
			//nombre de lien avec des cycles
			echo '<td>'.$dataproj['proj'].'</td>';
			
			//Ajout multiple
			//fermeture de la ligne
			echo '</tr>';
		    }
		echo '</table></div>';
}

if($liste) {
	if($_POST['rechercheFilm']) {
		$sqlRecherche = "SELECT pm_bulletin.numero, pm_bulletin.titre AS ttbulletin, pm_cycle.titre AS ttcycle, pm_categorie.titre_simple AS categorie, pm_cycle.id_cycle FROM pm_cycle_film
				JOIN pm_cycle
				ON pm_cycle.id_cycle = pm_cycle_film.id_cycle
				JOIN pm_bulletin_cycle
				ON pm_bulletin_cycle.id_cycle = pm_cycle.id_cycle
				JOIN pm_categorie
				ON pm_categorie.id_categorie = pm_bulletin_cycle.id_categorie
				JOIN pm_bulletin
				ON pm_bulletin.id_bulletin = pm_bulletin_cycle.id_bulletin
				WHERE pm_cycle_film.id_film = '$fi'
				AND pm_cycle_film.actif = 'a'
				AND pm_cycle.actif = 'a'";
		
	} elseif($_POST['rechercheCycle']) {
	echo '<hr class="clear">';
		$searchTitreSound = clean($_POST['recherche']);
		$searchTitre = ($_POST['recherche']) ? '%'.clean($_POST['recherche']).'%' : '%';
		
		$sqlRecherche = "SELECT pm_bulletin.numero, pm_bulletin.titre AS ttbulletin, pm_cycle.titre AS ttcycle, pm_categorie.titre_simple AS categorie, pm_cycle.id_cycle FROM pm_cycle
				JOIN pm_bulletin_cycle
				ON pm_bulletin_cycle.id_cycle = pm_cycle.id_cycle
				JOIN pm_categorie
				ON pm_categorie.id_categorie = pm_bulletin_cycle.id_categorie
				JOIN pm_bulletin
				ON pm_bulletin.id_bulletin = pm_bulletin_cycle.id_bulletin
				WHERE pm_cycle.titre LIKE '$searchTitre' OR SOUNDEX(pm_cycle.titre) = SOUNDEX('$searchTitreSound')
				AND pm_cycle.actif = 'a'
				ORDER BY pm_bulletin.numero DESC, pm_bulletin_cycle.ordre";
	
	} elseif($_POST['recherchePeriode']) {
	echo '<hr class="clear">';
		$start = $_POST['start'];
		$end = $_POST['end'];
		$sqlRecherche = "SELECT pm_bulletin.numero, pm_bulletin.titre AS ttbulletin, pm_cycle.titre AS ttcycle, pm_categorie.titre_simple AS categorie, pm_cycle.id_cycle FROM pm_bulletin
				JOIN pm_bulletin_cycle
				ON pm_bulletin.id_bulletin = pm_bulletin_cycle.id_bulletin
				JOIN pm_categorie
				ON pm_categorie.id_categorie = pm_bulletin_cycle.id_categorie
				JOIN pm_cycle
				ON pm_bulletin_cycle.id_cycle = pm_cycle.id_cycle
				WHERE pm_bulletin.start <= '$end'
				AND pm_bulletin.end >= '$start'
				ORDER BY pm_bulletin.numero DESC, pm_bulletin_cycle.ordre";
				
	} elseif($_POST['rechercheBulletin']) {
	echo '<hr class="clear">';
		$numero = $_POST['rechercheBulletin'];
		$sqlRecherche = "SELECT pm_bulletin.numero, pm_bulletin.titre AS ttbulletin, pm_cycle.titre AS ttcycle, pm_categorie.titre_simple AS categorie, pm_cycle.id_cycle FROM pm_bulletin
				JOIN pm_bulletin_cycle
				ON pm_bulletin.id_bulletin = pm_bulletin_cycle.id_bulletin
				JOIN pm_categorie
				ON pm_categorie.id_categorie = pm_bulletin_cycle.id_categorie
				JOIN pm_cycle
				ON pm_bulletin_cycle.id_cycle = pm_cycle.id_cycle
				WHERE pm_bulletin.numero = '$numero'
				ORDER BY pm_bulletin.numero DESC, pm_bulletin_cycle.ordre";
	}
	
if($_POST['rechercheFilm']) {
	echo '<div class="middle">';
} else {
	echo '<div class="left demi">';
}
	?>
	<table class="table">
		<thead>
			<tr>
			    <th>n°</th>
			    <th>Bulletin</th>
			    <th>Cycle</th>
			    <th>Catégorie</th>
			</tr>
		</thead>
		<tbody>
			<tr style="display:none;"><form></form></tr>
<?php
	$num = ''; //comparaison du numéro du bulletin
	//gestion du contenu
	$content = '';
	$content2 = '';
	$content3 = '';
		$resultRecherche = mysqli_query($connexion, $sqlRecherche ) or die(mysqli_error());
while ($dataRecherche = mysqli_fetch_array($resultRecherche)) {
	
	if($num == $dataRecherche['numero']) {
	
			// définir si la ligne a été sélectionné	
			if ($cy == $dataRecherche['id_cycle'] ) {
			    $content3 .= '<tr class="select">'; 
			} else {
			    $content3 .= '<tr>'; 
			}
				$count ++;
				
				//cycle
				$content3 .= '<td><form action="'.$_SERVER['PHP_SELF'].'" method="post">';
				if($rechercheFilm) {
					$content3 .= '<input type="hidden" value="liste" name="liste">
					<input type="hidden" value="'.$motif.'" name="motif">
					<input type="hidden" value="'.$recherche.'" name="recherche">
					<input type="hidden" value="'.$fi.'" name="fi">
					<input type="hidden" value="'.$dataRecherche['id_cycle'].'" name="cy">
					<input type="submit" name="rechercheFilm" class="no_btn" value="'.utf8_encode(afficheHtml($dataRecherche['ttcycle'])).'">';
					
				} elseif($rechercheCycle) {
					$content3 .= '<input type="hidden" value="liste" name="liste">
					<input type="hidden" value="'.$recherche.'" name="recherche">
					<input type="hidden" value="'.$dataRecherche['id_cycle'].'" name="cy">
					<input type="submit" name="rechercheCycle" class="no_btn" value="'.utf8_encode(afficheHtml($dataRecherche['ttcycle'])).'">';
					
				}elseif($recherchePeriode) {
					$content3 .= '<input type="hidden" value="liste" name="liste">
					<input type="hidden" value="'.$start.'" name="start">
					<input type="hidden" value="'.$end.'" name="end">
					<input type="hidden" value="'.$dataRecherche['id_cycle'].'" name="cy">
					<input type="submit" name="recherchePeriode" class="no_btn" value="'.utf8_encode(afficheHtml($dataRecherche['ttcycle'])).'">';
					
				}elseif($rechercheBulletin) {
					$content3 .= '<input type="hidden" value="liste" name="liste">
					<input type="hidden" value="'.$dataRecherche['id_cycle'].'" name="cy">
					<input type="hidden" value="'.$dataRecherche['numero'].'" name="rechercheBulletin">
					<input type="submit" name="recherche" class="no_btn" value="'.utf8_encode(afficheHtml($dataRecherche['ttcycle'])).'">';
				}
				$content3 .= '</form></td>'; //renvoi l'id du cycle en "GET"
				
				//catégorie
				$content3 .= '<td>'.$dataRecherche['categorie'].'</td>';
			$content3 .= '</tr>';
		
	} else {
		echo $content.''.$count.''.$content2.''.$count.''.$content3;
		$content = '';
		$content2 = '';
		$content3 = '';
		$count = '1';
		$num = $dataRecherche['numero'];
		
			// définir si la ligne a été sélectionné	
			if ($cy == $dataRecherche['id_cycle'] ) {
			    $content .= '<tr class="select">'; 
			} else {
			    $content .= '<tr>'; 
			}
				//n°bulletin
				$content .= '<td rowspan="';
				$content2 .='"><form action="'.$_SERVER['PHP_SELF'].'" method="post">
					<input type="hidden" value="liste" name="liste">
					<input type="submit" name="rechercheBulletin" class="no_btn" value="'.$dataRecherche['numero'].'">
				</form></td>'; //renvoi l'id du cycle en "GET"
				
				//titre bulletin
				$content2 .= '<td rowspan="';
				$content3 .= '">'.utf8_encode($dataRecherche['ttbulletin']).'</td>';
				
				//cycle
				$content3 .= '<td><form action="'.$_SERVER['PHP_SELF'].'" method="post">';
				if($rechercheFilm) {
					$content3 .= '<input type="hidden" value="liste" name="liste">
					<input type="hidden" value="'.$motif.'" name="motif">
					<input type="hidden" value="'.$recherche.'" name="recherche">
					<input type="hidden" value="'.$fi.'" name="fi">
					<input type="hidden" value="'.$dataRecherche['id_cycle'].'" name="cy">
					<input type="submit" name="rechercheFilm" class="no_btn" value="'.utf8_encode(afficheHtml($dataRecherche['ttcycle'])).'">';
					
				} elseif($rechercheCycle) {
					$content3 .= '<input type="hidden" value="liste" name="liste">
					<input type="hidden" value="'.$recherche.'" name="recherche">
					<input type="hidden" value="'.$dataRecherche['id_cycle'].'" name="cy">
					<input type="submit" name="rechercheCycle" class="no_btn" value="'.utf8_encode(afficheHtml($dataRecherche['ttcycle'])).'">';
					
				}elseif($recherchePeriode) {
					$content3 .= '<input type="hidden" value="liste" name="liste">
					<input type="hidden" value="'.$start.'" name="start">
					<input type="hidden" value="'.$end.'" name="end">
					<input type="hidden" value="'.$dataRecherche['id_cycle'].'" name="cy">
					<input type="submit" name="recherchePeriode" class="no_btn" value="'.utf8_encode(afficheHtml($dataRecherche['ttcycle'])).'">';
					
				}elseif($rechercheBulletin) {
					$content3 .= '<input type="hidden" value="liste" name="liste">
					<input type="hidden" value="'.$dataRecherche['id_cycle'].'" name="cy">
					<input type="hidden" value="'.$dataRecherche['numero'].'" name="rechercheBulletin">
					<input type="submit" name="recherche" class="no_btn" value="'.utf8_encode(afficheHtml($dataRecherche['ttcycle'])).'">';
				}
				$content3 .= '</form></td>'; //renvoi l'id du cycle en "GET"
				
				//catégorie
				$content3 .= '<td>'.$dataRecherche['categorie'].'</td>';
			$content3 .= '</tr>';
		
	}
				
}
echo $content.''.$count.''.$content2.''.$count.''.$content3;
?>
		</tbody>
	</table>
</div>
<?php }

// affichage de la liste des film d'un cycle
if($cy) { ?>
<div class="right">
	<table class="table">
		    <thead>
			<tr>
			    <th>Titre original</th>
			    <th>Année</th>
			    <th>Réalisateur(s)</th>
			    <th>Copies</th>
			    <th>Synchro</th>
			    <th>Prog.</th>
			</tr>
		    </thead>
		    <tbody>
			<tr style="display:none;"><form></form></tr>
		<?php
			$sqlFilmList = "SELECT pm_film.id_film, prefix_titre_o, titre_o, annee_prod, realisateur FROM pm_cycle_film
					JOIN pm_film
					ON pm_film.id_film = pm_cycle_film.id_film
					WHERE id_cycle = '$cy'
					AND actif = 'a'
					ORDER BY ordre, annee_prod";
		
		
		$resultFilmList = mysqli_query($connexion, $sqlFilmList ) or die(mysqli_error());
		while ($dataFilmList = mysqli_fetch_array($resultFilmList)) {
			// définir si le film a été sélectionné	
		    if ($fi == $dataFilmList['id_film'] ) {
			echo '<tr class="select">'; 
		    } else {
			echo '<tr>'; 
		    }
			$prefixSelect = '';
			if($dataFilmList['prefix_titre_o']) {
			    if($dataFilmList['prefix_titre_o']=="L'") {
				$prefixSelect.= "L'";
			    } else {
				$prefixSelect.= utf8_encode($dataFilmList['prefix_titre_o']).' ';
			    }
			}
			echo '<td><form action="';
				echo $_SERVER['PHP_SELF'].'?zone=film&amp;fi='.$dataFilmList['id_film'].'#film';
			echo '" method="post">
				<input type="hidden" name="liste" value="liste">
				<input type="hidden" value="titre" name="motif">
				<input type="hidden" value="'.html_entity_decode($prefixSelect.''.utf8_encode($dataFilmList['titre_o'])).'" name="recherche">
				<input type="hidden" value="'.$dataFilmList['id_film'].'" name="fi">
				<input type="submit" name="rechercheFilm" class="no_btn" value="'.html_entity_decode($prefixSelect.''.utf8_encode($dataFilmList['titre_o'])).'">
			</form></td>'; //renvoi l'id du cycle en "GET"
			echo '<td>'.$dataFilmList['annee_prod'].'</td>';
			echo '<td>'.utf8_encode($dataFilmList['realisateur']).'</td>';
			
			//Nombre de copies / vert si copie(s) sélectionnée(s)
			$sqlListCopie2 = "SELECT COUNT(id_copie) AS numb_copie,
				      CASE statut
					WHEN 1 THEN '1'
					ELSE '0'
				      END AS statut
				      FROM pm_copie WHERE id_film = $dataFilmList[id_film]";
			$resultListCopie2 = mysqli_query($connexion, $sqlListCopie2 ) or die(mysqli_error());
			$dataListCopie2 = mysqli_fetch_array($resultListCopie2);
			if($dataListCopie2['statut'] == '1') {
				echo '<td class="select">';
			} else echo '<td>';
			echo $dataListCopie2['numb_copie'].'</td>';
			
			// Liens avec les autres bases de données
			$link2 = ' ';
			$n = '0';
			$sqllink2 = "SELECT base FROM pm_film_film
				      WHERE id_film = '$dataFilmList[id_film]'
				      ORDER BY base"; // nom de la table ! requette
			$resultlink2 = mysqli_query($connexion, $sqllink2 ) or die(mysqli_error());
			while ($datalink2 = mysqli_fetch_array($resultlink2)) {
				if($n==0) {$link2.='';} else {$link2.=' | ';}
				$link2.= $base[$datalink2['base']].'';
				$n++;
			}
			echo '<td>'.$link2.'</td>';
			
			//nombre de lien avec des cycles
			$sqlproj2 = "SELECT COUNT(DISTINCT pm_cycle_film.id_cycle) AS proj FROM `pm_cycle_film`
				JOIN pm_cycle
				ON pm_cycle.id_cycle = pm_cycle_film.id_cycle
				WHERE pm_cycle.actif = 'a'
				AND pm_cycle_film.id_film = '$dataFilmList[id_film]'
				AND pm_cycle_film.actif = 'a'"; 
			$resultproj2 = mysqli_query($connexion, $sqlproj2 ) or die(mysqli_error());
			$dataproj2 = mysqli_fetch_array($resultproj2);
			echo '<td>'.$dataproj2['proj'].'</td>';
			
			echo '</tr>';
		    }
		echo '</table></div>';
}
?>


<hr class="clear">
