<?php
/**
* RECHERCHER_FILM.PHP
*
* Conditions d'affichage / du formulaire de recherche d'une fiche film dans pm_film pour y récupérer les données
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 02.07.2015
*/


if($dataEventSelect['id_film']) {
?>
	<!--  table d'affichage	-->
	<table class="table">
	    <thead>
		<tr>
		    <th>Titre original</th>
		    <th>Réalisateur(s)</th>
		    <th>Année</th>
<?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
		</tr>
	    </thead>
	    <tbody>
<?php

	$sqlFilmBase = "SELECT id_film, prefix_titre_o, titre_o, annee_prod, realisateur FROM pm_film WHERE id_film = $dataEventSelect[id_film]";
	$resultFilmBase = mysqli_query($connexion, $sqlFilmBase ) or die(mysqli_error());
	while ($dataFilmBase = mysqli_fetch_array($resultFilmBase)) {
		// définir si le film a été sélectionné	
		echo '<tr>'; // pour l'altérnance des couleurs
		
		  $prefixSelect = '';
		  if($dataFilmBase['prefix_titre_o']) {
		      if($dataFilmBase['prefix_titre_o']=="L'") {
			  $prefixSelect.= "L'";
		      } else {
			  $prefixSelect.= utf8_encode($dataFilmBase['prefix_titre_o']).' ';
		      }
		  }
		  echo '<td>'.html_entity_decode($prefixSelect.''.utf8_encode($dataFilmBase['titre_o'])).'</td>'; //renvoi l'id du cycle en "GET"
		  
		  echo '<td>'.utf8_encode($dataFilmBase['realisateur']).'</td>';
		  
		  echo '<td>'.$dataFilmBase['annee_prod'].'</td>';
      
		  //Formulaire d'édition / suppression
		  if($_COOKIE['role']=='e') {	
		    echo '<td>';
		      echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=event&amp;event='.$event.'&amp;add=0" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé ce lien?\')">
			  <input type="hidden" value="'.$event.'" name="id_event">
			  <input name="deleteLink" class="btn_deletelink" type="submit" value="Supprimer">
		      </form>';
		    echo '</td>';
		  }
		  
		//fermeture de la ligne
		echo '</tr>';
	    }
?>
	    </tbody>
	</table>
<?php
}else {
?>
<!-----------------------------------Recherche--------------------------------------------------------->
  <h4>Recherche</h4>
    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<div>
	    <label for="motif"></label>
	    <select id="motif" name="motif" class="input31">
		    <optgroup label="Rechercher par">
		    <option <?php if($motif=='titre') echo 'selected="selected"'; ?> value="titre">Titre</option>
		    <option <?php if($motif=='realisateur') echo 'selected="selected"'; ?> value="realisateur">Réalisateur</option>
	    </select>
	    <label for="recherche"></label>
	    <input id="recherche" type="text" placeholder="Recherche" name="recherche" value="<?php echo $_POST['recherche']; ?>" class="input36">
	</div>
	<input type="submit" name="rechercheFilm" class="btn" value="Rechercher">
    </form>
  <hr >
<?php
if($_POST['rechercheFilm']) {
	
	//Traitement des variables
	if($_POST['motif'] == 'titre') {
		$searchTitreSound = clean($_POST['recherche']);
		$searchTitre = ($_POST['recherche']) ? '%'.clean($_POST['recherche']).'%' : '%';
		
	}elseif($_POST['motif'] == 'realisateur') {
		$searchRealisateur = ($_POST['recherche']) ? '%'.clean($_POST['recherche']).'%' : '%';	
	}
?>	
	<!--  table d'affichage	-->
	<table class="table">
	    <thead>
		<tr>
		    <th>Titre original</th>
		    <th>Réalisateur(s)</th>
		    <th>Année</th>
		</tr>
	    </thead>
	    <tbody>
		
<?php	//recherche dans la base / condition de recherche
	if($motif == 'titre') {
		$sqlFilmBase = "SELECT id_film, prefix_titre_o, titre_o, annee_prod, realisateur FROM pm_film
				WHERE titre_o LIKE '$searchTitre' OR SOUNDEX(titre_o) = SOUNDEX('$searchTitreSound')";
				
	} elseif($motif == 'realisateur') {
		$sqlFilmBase = "SELECT id_film, prefix_titre_o, titre_o, annee_prod, realisateur FROM pm_film
				WHERE realisateur LIKE '$searchRealisateur'
				ORDER BY annee_prod";
	}
	
	$resultFilmBase = mysqli_query($connexion, $sqlFilmBase ) or die(mysqli_error());
	while ($dataFilmBase = mysqli_fetch_array($resultFilmBase)) {
		// définir si le film a été sélectionné	
		echo '<tr>'; // pour l'altérnance des couleurs
		
		  $prefixSelect = '';
		  if($dataFilmBase['prefix_titre_o']) {
		      if($dataFilmBase['prefix_titre_o']=="L'") {
			  $prefixSelect.= "L'";
		      } else {
			  $prefixSelect.= utf8_encode($dataFilmBase['prefix_titre_o']).' ';
		      }
		  }
		  echo '<td><form action="'.$_SERVER['PHP_SELF'].'?zone=event&amp;event='.$event.'&amp;add=0" method="post">
			  <input type="hidden" value="'.$event.'" name="id_event">
			  <input type="hidden" value="'.$dataFilmBase['id_film'].'" name="id_film">
			  <input type="submit" name="addMovie" class="no_btn" value="'.html_entity_decode($prefixSelect.''.utf8_encode($dataFilmBase['titre_o'])).'">
		  </form></td>'; //renvoi l'id du cycle en "GET"
		  
		  echo '<td>'.utf8_encode($dataFilmBase['realisateur']).'</td>';
		  
		  echo '<td>'.$dataFilmBase['annee_prod'].'</td>';
		  
		//fermeture de la ligne
		echo '</tr>';
	    }
	echo '</tbody></table>';
      }
  
 } ?>  