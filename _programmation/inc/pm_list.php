<?php
/**
* PM_LIST
*
* Ajout d'une liste de titres (ex depuis IMDB)
*
* @copyright	Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author	Cindy Chassot
* @version	0.1 - 22.06.2015
* @variable	$_GET['listeMulti'] / $_GET['zone']=film / $_GET['cy']=$cy / ($_GET['bu']=$bu) / ($_GET['fi']=$fi)
*/
foreach ($_POST as $key => $value) {
	$$key = $value;
}
?>

	<h4>Ajout d'une liste</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>#film" method="post">
	    <div>
		<label for="realisateur">Réalisateur</label>
		<input id="realisateur" type="text" name="realisateur" value="<?php echo $realisateur; ?>">
	    </div>
	    <div>
		<label for="list">Liste</label>
		<textarea id="list" name="list"><?php echo $list; ?></textarea>
	    </div>
	    <input type="submit" name="addList" class="btn" value="Comparer">
	</form>
<hr >

<?php 	
/*****************************************************************
 * Si le précédent formulaire a bien été envoyé
 *
 * $_POST['addList'] => pour lancer la requête
 * $_POST['realisateur']=> Nom du réalisateur de ces films
 * $_POST['list']=> liste des films à ajouter dans la table provisoire pour la comparaison
 * 
 *****************************************************************/
if($_POST['addList']) {
	
//création de la table provisoire
$sqlTableTemp = "CREATE TEMPORARY TABLE pm_film_add LIKE pm_film;";

//traitement des valeurs envoyées $list
$listTitre = cleanTextarea($list);
$listTitre = str_replace(",", "', '", $listTitre);
$listTitre = str_replace("% ", "%", $listTitre);
$tableauTitre = explode('%', $listTitre);

//boucle sur chaque ligne du textarea -> insertion des données dans la table provisoire
foreach($tableauTitre as $entree){
	$sqlTableTemp .= "INSERT INTO pm_film_add (annee_prod, titre_o, realisateur) VALUES ('".$entree."', '".$realisateur."');";
}
//Exécution d'une requête multiple
if (mysqli_multi_query($connexion, $sqlTableTemp)) {
    do {
        //Stockage du premier résultat
        if ($result = mysqli_store_result($connexion)) {
            while ($row = mysqli_fetch_row($result)) {
                printf("%s\n", $row[0]);
            }
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($connexion));
}

//Définition/mise à zero des variables
$link = '';
$noLink = '';

//titre du 1er tableau juste pour une question de mise en page 
echo '<h3>Référence dans la base</h3>';

//form pour l'ajout des titres
echo '<form style="margin-top:55px;" action="';
	if($_COOKIE['sousMenu'] == '_sm0') {
		echo $_SERVER['PHP_SELF'].'?zone=film&amp;cy='.$cy;
	} else {
		echo $_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy;
	}
echo '" method="post">';

//sélection des entrées dans la table provisoire
$sqlFilmList = "SELECT * FROM pm_film_add ORDER BY annee_prod";
$resultFilmList = mysqli_query($connexion, $sqlFilmList ) or die(mysqli_error());
while ($dataFilmList = mysqli_fetch_array($resultFilmList)) {
	
	//comparaison des entrées provisoire avec la table pm_film
	$sqlReference = "SELECT id_film, annee_prod, titre_o, realisateur FROM pm_film
			WHERE annee_prod = '".$dataFilmList[annee_prod]."'
			AND realisateur LIKE '%".$realisateur."%'";
	$resultReference = mysqli_query($connexion, $sqlReference ) or die(mysqli_error());
	
	//si correspondance -> affichage 1er tableau
	if(mysqli_num_rows($resultReference) > 0) {
		$dataReference = mysqli_fetch_array($resultReference);
		    if ($fi == $dataReference['id_film'] ) {
			$link .= '<tr class="select">'; // pour l'altérnance des couleurs
		    } else {
			$link .= '<tr>'; // pour l'altérnance des couleurs
		    }
		//Année de production
		$link .= '<td>'.$dataReference['annee_prod'].'</td>';
		
		//Titre principal
		$link .= '<td><form action="';
		if($_COOKIE['sousMenu'] == '_sm0') {
			$link .= $_SERVER['PHP_SELF'].'?zone=film&amp;cy='.$cy.'&amp;fi='.$dataReference['id_film'].'&amp;listeMulti=Multi#film';
		} else {
			$link .= $_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy.'&amp;fi='.$dataReference['id_film'].'&amp;listeMulti=Multi#film';
		}
		$link .= '" method="post">
				<input type="hidden" value="'.$realisateur.'" name="realisateur">
				<input type="hidden" value="'.$list.'" name="list">
				<input type="hidden" value="comparer" name="addList">
				<input type="hidden" value="'.$dataReference['id_film'].'" name="fi">
				<input type="submit" name="titre_o" class="no_btn" value="'.html_entity_decode($dataReference['titre_o']).'">
			</form></td>';
		
		//Réalisateur
		$link .= '<td>'.$dataReference['realisateur'].'</td>';
		
		//Nombre de copies / vert si copie(s) sélectionnée(s)
		$sqlListCopieR = "SELECT COUNT(id_copie) AS numb_copie,
				      CASE statut
					WHEN 1 THEN '1'
					ELSE '0'
				      END AS statut
				      FROM pm_copie WHERE id_film = '$dataReference[id_film]'";
		$resultListCopieR = mysqli_query($connexion, $sqlListCopieR ) or die(mysqli_error());
		$dataListCopieR = mysqli_fetch_array($resultListCopieR);
		if($dataListCopieR['statut'] == '1') {
			$link .= '<td class="select">';
		} else $link .= '<td>';
		$link .= $dataListCopieR['numb_copie'].'</td>';
	
		//Synchro / Liens avec les autres bases de données
		$linkBase = ' ';
		$n = '0';
		$basefilm ='0';
		$sqllinkR = "SELECT base FROM pm_film_film
			      WHERE id_film = '$dataReference[id_film]'
			      ORDER BY base"; // nom de la table ! requette
		$resultlinkR = mysqli_query($connexion, $sqllinkR ) or die(mysqli_error());
		while ($datalinkR = mysqli_fetch_array($resultlinkR)) {
			if($n==0) {$linkBase.='';} else {$linkBase.=' | ';}
			$linkBase.= $base[$datalinkR[base]].'';
			if($datalinkR['base']=='1') {$basefilm = '1';}
			$n++;
		}		
		$link .= '<td>'.$linkBase.'</td>';
	
		//Nombre de fois programmé -> dans table prog
		$sqlproj = "SELECT COUNT(DISTINCT pm_cycle_film.id_cycle) AS proj FROM `pm_cycle_film`
		JOIN pm_cycle
		ON pm_cycle.id_cycle = pm_cycle_film.id_cycle
		WHERE pm_cycle.actif = 'a'
		AND pm_cycle_film.id_film = '$dataReference[id_film]'
		AND pm_cycle_film.actif = 'a'";
		$resultproj = mysqli_query($connexion, $sqlproj ) or die(mysqli_error());
		$dataproj = mysqli_fetch_array($resultproj);
		$link .= '<td>'.$dataproj['proj'].'</td>';
		
		//Add-> checkbox pour le formulaire principal
		$link .= '<td><input type="checkbox" value="'.$dataReference['id_film'].'" name="link[]"></td>';
		
		$link .= '</tr>';
		
	//sinon affichage dans le 2ème tableau
	} else {
		$noLink .= '<tr>';
		
		//id
		$noLink .= '<td>'.$dataFilmList['id_film'].'</td>';
		
		//Année de production
		$noLink .= '<td>'.$dataFilmList['annee_prod'].'</td>';
		
		//Titre principal
		$noLink .= '<td>'.utf8_encode($dataFilmList['titre_o']).'</td>';
		
		//Réalisateur
		$noLink .= '<td>'.$dataFilmList['realisateur'].'</td>';
		
		//Add
		$noLink .= '<td><input type="checkbox" checked value="'.$dataFilmList['id_film'].'" name="newFilm[]"></td>';
		
		$noLink .= '</tr>';
	}
}

?>
<!--Premier tableau-->
<table class="table">
    <thead>
        <tr>
	    <th>Année</th>
	    <th>Titre principal</th>
	    <th>Réalisateur(s)</th>
	    <th>Copies</th>
	    <th>Synchro</th>
	    <th>Prog.</th>
	    <th>Add</th>
        </tr>
    </thead>
    <tbody>
       <tr style="display:none;"><form></form></tr> <!-- ligne supplémentaire pour contrer le formulaire global -->
<?php echo $link; ?>
    </tbody>
</table>

<!--2ème tableau-->
<h3>Aucune référence</h3>
<table class="table">
    <thead>
        <tr>
	    <th>id</th>
	    <th>Année</th>
	    <th>Titre principal</th>
	    <th>Réalisateur(s)</th>
	    <th>Add</th>
        </tr>
    </thead>
    <tbody>
       <tr style="display:none;"><form></form></tr> <!-- ligne supplémentaire pour contrer le formulaire global -->
<?php echo $noLink; ?>
    </tbody>
</table>		

<?php

	echo '<input type="hidden" name="id_cycle" value="'.$cy.'">';
	echo '<input type="hidden" name="sqlTableTemp" value="'.$sqlTableTemp.'">';
	echo '<input type="submit" class="btn" name="linkMultiNewFilm" value="Ajout Multiple">';
echo '</form>';
	
} // fin de la condition de liste
?>