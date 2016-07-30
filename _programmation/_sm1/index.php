<?php
/**
* INDEX _Programmation / SM1 - gestion des bulletins
*
* Préparation de cycles sans liens avec un bulletin
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 12.02.2015 - 08.10.2015
*/

/*************************************************************************************
 * ****************************** AFFICHAGE DES DONNEES ******************************
 * ***********************************************************************************/ ?>

<div class="left">
<!--Liste des 5 dernier bulleins-->
	<h3>Bulletin</h3>
<?php if($_COOKIE['role']=='e') { ?>
	<form class="action" action="<?php $_SERVER['PHP_SELF']; ?>#bulletin" method="get">
	    <input type="hidden" value="bulletin" name="zone">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
<?php } ?>
	
	<table class="table">
	    <thead>
		<tr>
		    <th>Bulletin</th>
		</tr>
	    </thead>
	    <tbody>
<?php /********************* Affichage des Bulletins Limité au 10 derniers *********************/
	    $sqlBulletinAtt = "SELECT id_bulletin, numero, titre FROM pm_bulletin ORDER BY numero DESC LIMIT 5"; 
	    $resultBulletinAtt = mysqli_query($connexion, $sqlBulletinAtt ) or die(mysqli_error());
	    
	while ($dataBulletinAtt = mysqli_fetch_array($resultBulletinAtt)) {
		
		// définir si le cycle a été sélectionné	
		if ($bu == $dataBulletinAtt['id_bulletin'] ) {
		    echo '<tr class="select">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr>'; // pour l'altérnance des couleurs
		}
		
		echo '<td>';
		echo '<a href="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;bu='.$dataBulletinAtt['id_bulletin'].'">'.$dataBulletinAtt['numero'].' '.utf8_encode($dataBulletinAtt['titre']).'</a></td>'; //renvoi l'id du cycle en "GET"	    
		echo '</tr>'; //fermeture de la ligne
	    }
?>
	    </tbody>
	</table>
<!-- LISTE DES BULLETINS-->
<?php
//requète pour récupérer les information
$sql = "SELECT id_bulletin, numero FROM pm_bulletin ORDER BY numero DESC";
$result = mysqli_query($connexion, $sql) or die(mysqli_error());
?>
	<form name="select_bulletin" action="index.php" method="GET">
		<select name="bu">
			<option value="">Sélectionnez le bulletin</option>
<?php
//boucle pour récupérer toute les donneés	
while ($data_bulletin = mysqli_fetch_array($result)) {
   echo '<option value="'.$data_bulletin['id_bulletin'].'">Bulletin n°'.$data_bulletin['numero'].'</option>';
}
?>
		</select>
		<input type="hidden" name="zone" value="bulletin">
		<input class="btn" type="submit" value="Sélectionner">
	</form>
<!-- /LISTE DES BULLETINS-->
</div>

<?php
/********************* Formulaire du Bulletin *********************/
if($zone==TRUE) {
	if($bu) {
		$sqlBulletinSelect = "SELECT numero, titre, start, end, commentaire FROM pm_bulletin WHERE id_bulletin ='$bu'"; // nom de la table ! requette
		$resultBulletinSelect = mysqli_query($connexion, $sqlBulletinSelect ) or die(mysqli_error());
		$dataBulletinSelect = mysqli_fetch_array($resultBulletinSelect);
	}
?>
<div class="right">
  <form action="_programmation/inc/export_synchro_typo.php" method="get">
    <input type="hidden" name="zone" value="bulletin">
    <input type="hidden" name="bu" value="<?php echo $bu; ?>">
    <input type="submit" class="btn" value="Export non-Typo">
  </form>
  <form action="_programmation/inc/export_synchro_oracle.php" method="get">
    <input type="hidden" name="zone" value="bulletin">
    <input type="hidden" name="bu" value="<?php echo $bu; ?>">
    <input type="submit" class="btn" value="Export Oracle">
  </form>
</div>

<div class="middle">
	<h4>Ajout</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>#bulletin" method="post">
	    <div>
		<label for="numero">Numéro du bulletin</label>
		<input type="text" name="numero" id="numero" value="<?php echo utf8_encode($dataBulletinSelect['numero']); ?>">
	    </div>
	    <div>
		<label for="titre">Titre du bulletin</label>
		<input type="text" name="titre" id="titre" value="<?php echo utf8_encode($dataBulletinSelect['titre']); ?>">
	    </div>
	    <div class="input21">
		<label for="start">Date de début</label>
		<input type="date" min="2015-01-01" placeholder="2015-01-01" name="start" id="start" value="<?php echo utf8_encode($dataBulletinSelect['start']); ?>">
	    </div>
	    <div class="input22">
		<label for="end">Date de fin</label>
		<input type="date" min="2015-01-01" placeholder="2015-03-01" name="end" id="end" value="<?php echo utf8_encode($dataBulletinSelect['end']); ?>">
	    </div>
	    <div>
		<label for="commentaire">Commentaire</label>
		<textarea name="commentaire" id="commentaire"><?php echo afficheHtml(utf8_encode($dataBulletinSelect['commentaire'])); ?></textarea>
	    </div>
	    <input type="hidden" name="id_bulletin" value="<?php echo $bu; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveBulletin" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>

<?php if($bu) { ?>
<!--##########--><hr class="clear" id="cycle"><!--##############################################################################################################################-->

<h2><?php echo utf8_encode($dataBulletinSelect['numero']); ?> <?php echo utf8_encode($dataBulletinSelect['titre']); ?></h2>
<div class="left">

	<h3>Cycle</h3>
<?php if($_COOKIE['role']=='e') { ?>
	<form class="action" action="<?php $_SERVER['PHP_SELF']; ?>#cycle" method="get">
	    <input type="hidden" value="cycle" name="zone">
	    <input type="hidden" value="<?php echo $bu; ?>" name="bu">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
<?php } ?>
  <table class="table" id="listeCycle"> 
    <thead>
     <tr>
      <th>N°</th>
      <th>Cycles</th>
      <th>F.</th>
      <th>Cat. </th>
<?php 		    if($_COOKIE['role']=='e') echo '<th style="width:35px;">&nbsp; </th>'; ?>
     </tr>
    </thead >
    <tbody class=content>
<?php /********************* Affichage des cycles en attente *********************/
	    $sqlCycleAtt = "SELECT pm_cycle.id_cycle, pm_cycle.titre, pm_bulletin_cycle.id_categorie, pm_bulletin_cycle.ordre, pm_cycle.couleur FROM pm_cycle
			JOIN pm_bulletin_cycle
			ON pm_bulletin_cycle.id_cycle = pm_cycle.id_cycle
			WHERE pm_bulletin_cycle.id_bulletin='$bu'
			AND pm_cycle.actif = 'a'
			ORDER BY pm_bulletin_cycle.ordre"; 
	    $resultCycleAtt = mysqli_query($connexion, $sqlCycleAtt ) or die(mysqli_error());
	    
	while ($dataCycleAtt = mysqli_fetch_array($resultCycleAtt)) {
		//ordre des cycles	
		if ($cy == $dataCycleAtt['id_cycle'] ) {// définir si le cycle a été sélectionné
		    echo '<tr class="select" id="pos_'.$dataCycleAtt['id_cycle'].'">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr id="pos_'.$dataCycleAtt['id_cycle'].'">'; // pour l'altérnance des couleurs
		}
		echo '<td>'.$dataCycleAtt['ordre'].'</td>';
		
		//titre
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=cycle&amp;bu='.$bu.'&amp;cy='.$dataCycleAtt['id_cycle'].'#cycle">'.html_entity_decode(utf8_encode($dataCycleAtt['titre'])).'</a></td>'; //renvoi l'id du cycle en "GET"
		
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
		
		//Catégorie - couleur
		$idCategorie = $dataCycleAtt['id_categorie'];
		$sqlCategorie = "SELECT titre_simple FROM pm_categorie WHERE id_categorie ='$idCategorie'"; // nom de la table ! requette
		$resultCategorie = mysqli_query($connexion, $sqlCategorie ) or die(mysqli_error());
		$dataCategorie = mysqli_fetch_array($resultCategorie);
		echo '<td style="background-color:'.$dataCycleAtt['couleur'].'">'.$dataCategorie['titre_simple'].'</td>';
		
		//Formulaire d'édition / suppression
		if($_COOKIE['role']=='e') {	
		echo '<td>';
			echo '<a class="btn_visio" href="'.$_SERVER['PHP_SELF'].'?internal=link&amp;sm=_smi0&amp;cat=e&amp;var[cy]='.$dataCycleAtt['id_cycle'].'&amp;var[bu]='.$bu.'">V</a>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;bu='.$bu.'" method="post" onclick="return confirm(\'voulez-vous vraiment duppliquer ce lien?\')">
				<input type="hidden" name="id_cycle" value="'.$dataCycleAtt['id_cycle'].'">
				<input name="dobbleCycle" class="btn_dobble" type="submit" value="Duppliquer">
			     </form>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;bu='.$bu.'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé ce lien?\')">
				<input type="hidden" name="id_bulletin" value="'.$bu.'">
				<input type="hidden" name="id_cycle" value="'.$dataCycleAtt['id_cycle'].'">
				<input name="deleteLinkBulletinCycle" class="btn_deletelink" type="submit" value="Supprimer">
			     </form>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;bu='.$bu.'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé ce cycle?\')">
				<input type="hidden" name="id_bulletin" value="'.$bu.'">
				<input type="hidden" name="id_cycle" value="'.$dataCycleAtt['id_cycle'].'">
				<input name="deleteCycle" class="btn_suppr" type="submit" value="Supprimer">
			     </form>';
		echo '</td>';
		}
		
		echo '</tr>'; //fermeture de la ligne
	    }
?> 
    </tbody>
  </table>
</div>

<?php
/********************* Formulaire du Cycle *********************/
if($_REQUEST['zone']=='cycle' || $_REQUEST['zone']=='film' || $_REQUEST['zone']=='copie') {
	if($cy) {
		$sqlCycleSelect = "SELECT * FROM pm_cycle
				JOIN pm_bulletin_cycle
				ON pm_cycle.id_cycle = pm_bulletin_cycle.id_cycle
				WHERE pm_cycle.id_cycle ='$cy'
				AND pm_bulletin_cycle.id_bulletin ='$bu'"; // nom de la table ! requette
		$resultCycleSelect = mysqli_query($connexion, $sqlCycleSelect ) or die(mysqli_error());
		$dataCycleSelect = mysqli_fetch_array($resultCycleSelect);
	}
?>
<div class="right"></div>

<div class="middle">
<?php
    if($co) {
	echo '<h4>Modification</h4>';
    }else {
	echo '<h4>Ajout</h4>';
    }
?>
	<form action="<?php $_SERVER['PHP_SELF']; ?>#cycle" method="post">
	    <div>
		<label for="titre">Titre</label>
		<input id="titre" type="text" name="titre" value="<?php echo afficheHtml(utf8_encode($dataCycleSelect['titre'])); ?>">
	    </div>
	    <div>
		<label for="titre_simple">Titre simplifié</label>
		<input type="text" name="titre_simple" id="titre_simple" value="<?php echo utf8_encode($dataCycleSelect['titre_simple']); ?>">
	    </div>
	    <div>
		<label for="date">Dates</label>
		<textarea name="date" id="date"><?php echo nl2br(utf8_encode($dataCycleSelect['date'])); ?></textarea>
	    </div>
	    <div>
		<label for="commentaire">Infos</label>
		<textarea name="commentaire" id="commentaire"><?php echo afficheHtml(utf8_encode($dataCycleSelect['commentaire'])); ?></textarea>
	    </div>
	    <div>
		<label for="id_categorie">Catégorie</label>
		<select name="id_categorie">
			<option value="0">--Catégorie--</option>
<?php
			$sqlCategorie = "SELECT id_categorie, titre FROM pm_categorie"; 
			$resultCategorie = mysqli_query($connexion, $sqlCategorie ) or die(mysqli_error());
			while ($dataCategorie = mysqli_fetch_array($resultCategorie)) {
				echo '<option';
				if($dataCycleSelect['id_categorie']==$dataCategorie['id_categorie']) { echo ' selected';}
				echo ' value="'.$dataCategorie['id_categorie'].'">'.utf8_encode($dataCategorie['titre']);
				echo '</option>';
			}
?>
		</select>
	    </div>
	    <div>
		<label for="couleur"></label>
		<input type="color" value="<?php echo $dataCycleSelect['couleur']; ?>" name="couleur">
	    </div>
	    <input type="hidden" name="id_bulletin" value="<?php echo $bu; ?>">
	    <input type="hidden" name="numero" value="<?php echo $dataBulletinSelect['numero']; ?>">
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
	    <input type="hidden" value="<?php echo $bu; ?>" name="bu">
	    <input type="hidden" value="<?php echo $cy; ?>" name="cy">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
	<form class="" action="<?php echo $_SERVER['PHP_SELF']; ?>#film" method="get">
	    <input type="hidden" value="film" name="zone">
	    <input type="hidden" value="<?php echo $bu; ?>" name="bu">
	    <input type="hidden" value="<?php echo $cy; ?>" name="cy">
	    <input type="submit" name="listeMulti" class="btn" value="Liste multiple">
	</form>
<?php } ?>
	<form class="action" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
	    <input type="hidden" value="film" name="zone">
	    <input type="hidden" value="<?php echo $bu; ?>" name="bu">
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
	    <tbody class=content>
<?php /********************* Affichage des films liés au cycle *********************/
	    $sqlFilmAtt = "SELECT pm_film.id_film, prefix_titre_o, titre_o, annee_prod FROM pm_cycle_film
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
		//ordre
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
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy.'&amp;fi='.$dataFilmAtt['id_film'].'#film">'.html_entity_decode(utf8_encode($prefixSelect.''.$dataFilmAtt['titre_o'])).'</a></td>'; //renvoi l'id du cycle en "GET"
		
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
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy.'#film" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé ce lien?\')">
				<input type="hidden" name="id_film" value="'.$dataFilmAtt['id_film'].'">
				<input type="hidden" name="id_cycle" value="'.$cy.'">
				<input title="supprimer le lien" name="deleteLinkCycleFilm" class="btn_deletelink" type="submit" value="Supprimer">
			     </form>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy.'#film" method="post" onclick="return confirm(\'voulez-vous déselectionner ce film?\')">
				<input type="hidden" name="id_film" value="'.$dataFilmAtt['id_film'].'">
				<input type="hidden" name="id_cycle" value="'.$cy.'">
				<input title="déselectionner le film" name="deselectFilm" class="btn_deselect" type="submit" value="Deselect">
			     </form>';
			if($basefilm == '1') {	
				echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=copie&amp;bu='.$bu.'&amp;cy='.$cy.'&amp;fi='.$fi.'#film" method="post">
					<input type="hidden" name="id_copie" value="'.$dataCopieAtt['id_copie'].'">
					<input type="hidden" name="id_film" value="'.$dataFilmAtt['id_film'].'">
					<input type="hidden" name="id_cycle" value="'.$cy.'">
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
			// ordre
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
			echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=film&amp;deselect=deselect&amp;bu='.$bu.'&amp;cy='.$cy.'&amp;fi='.$dataFilmAtt['id_film'].'#film">'.html_entity_decode(utf8_encode($prefixSelect.''.$dataFilmAtt['titre_o'])).'</a></td>'; //renvoi l'id du cycle en "GET"
				
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
				echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy.'#film" method="post">
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
// L'
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
	    <input type="hidden" value="<?php echo $bu; ?>" name="bu">
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
	<h4>Ajout</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>#copie" method="post">
	      
<?php include($_COOKIE['indexApp']."/inc/pm_fiche_copie.php"); ?>

	    <input type="hidden" name="id_film" value="<?php echo $fi; ?>">
	    <input type="hidden" name="id_copie" value="<?php echo $co; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveCopie" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>
<?php
} //if copie
}} //if film
}} //if cycle
}} //if bulletin
?>
<hr class="clear">

