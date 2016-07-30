<?php
/*** INDEX Programme complet - PROGRAMMATION - SM2***/
// 
//Cindy Chassot 19.02.2015 - 16.03.2015
//© Cinémathèque suisse
//


?>

<div class="left info_visible_screen">
<!--Liste des 5 dernier bulleins-->
	<h3>Bulletin</h3>
	
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
if($_REQUEST['zone']=='bulletin') {
	if($bu) {
		$sqlBulletinSelect = "SELECT * FROM pm_bulletin WHERE id_bulletin ='$bu'"; // nom de la table ! requette
		$resultBulletinSelect = mysqli_query($connexion, $sqlBulletinSelect ) or die(mysqli_error());
		$dataBulletinSelect = mysqli_fetch_array($resultBulletinSelect);
	}
	
	if($allday) {
		$sqlAlldaySelect = "SELECT * FROM pm_allday WHERE id ='$allday'"; // nom de la table ! requette
		$resultAlldaySelect = mysqli_query($connexion, $sqlAlldaySelect ) or die(mysqli_error());
		$dataAlldaySelect = mysqli_fetch_array($resultAlldaySelect);
	}
?>
<div class="right">
	<h4>Vacances / Indisponibilité de salle</h4>
		<?php if($_COOKIE['role']=='e') { ?>
	<div class="info_visible_screen">
			<form name="add_allDay" action="index.php?action=form&amp;zone=bulletin&amp;bu=<?php echo $bu; ?>" method="POST">
				<div>
				    <label for="titre">Titre</label>
				    <input id="titre" name="titre" type="text" placeholder="" required value="<?php echo utf8_encode($dataAlldaySelect['titre']); ?>">
				</div>
				<div class="input21">
				    <label for="start">Date de début</label>
				    <input name="start" type="text" placeholder="2015-01-01" required value="<?php echo utf8_encode($dataAlldaySelect['start']); ?>">
				</div>
				<div class="input22">
				    <label for="end">Date de fin</label>
				    <input name="end" type="text" placeholder="2015-01-01" required value="<?php echo utf8_encode($dataAlldaySelect['end']); ?>">
				</div>  
				<div class="input21">
				    <label for="categorie">Catégorie</label>
				    <select name="categorie">
					<option value="0"<?php if($dataAlldaySelect['categorie'] == 0) echo ' selected="selected"'; ?>>Vacances / festival</option>
					<option value="1"<?php if($dataAlldaySelect['categorie'] == 1) echo ' selected="selected"'; ?>>Indisponibilité</option>
				    </select>
				</div>
				<div class="input22">
				    <label for="id_salle">Salles</label>
				    <select name="id_salle">
					<option value="0">--</option>
<?php
//requète pour récupérer la liste des salles
$sql_salle = "SELECT * FROM pm_salle";
$result_salle = mysqli_query($connexion, $sql_salle) or die(mysqli_error());

//boucle pour récupérer toute les donneés	
while ($data_salle = mysqli_fetch_array($result_salle)) {
	echo '<option value="'.$data_salle['id_salle'].'"';
	if($dataAlldaySelect['id_salle'] == $data_salle['id_salle']) echo ' selected="selected"';
	echo '>'.utf8_encode($data_salle['titre']).'</option>';	
}
?>
				    </select>
				</div>
				<input type="hidden" name="bu" value="<?php echo $bu; ?>">
				<input type="hidden" name="allday" value="<?php echo $allday; ?>">
				<input name="pmAddAllday" class="btn" type="submit" value="Envoyer">
			</form>
</div>
<?php } ?>
	<table class="table">
	    <thead>
		<tr>
		    <th>Salle</th>
		    <th>Indisponibiltié</th>
		    <th>Début</th>
		    <th>Fin</th>
		    <?php if($_COOKIE['role']=='e') { echo '<th></th>'; } ?>
		</tr>
	    </thead>
	    <tbody>
			
<?php
//requête qui récupère les événements
$sql_allday = "SELECT * FROM pm_allday
		WHERE categorie = '1'
		AND actif = 'a'
		AND ((start BETWEEN '$dataBulletinSelect[start]' AND '$dataBulletinSelect[end]') OR (end BETWEEN '$dataBulletinSelect[start]' AND '$dataBulletinSelect[end]'))
		ORDER BY start";
$result_allday = mysqli_query($connexion, $sql_allday) or die(mysqli_error());

$tr_n = 1;
while ($data_allday = mysqli_fetch_array($result_allday)) {
		echo '<tr>';
		//salle
$sql_salle = "SELECT titre FROM pm_salle WHERE id_salle = '$data_allday[id_salle]'";
$result_salle = mysqli_query($connexion, $sql_salle) or die(mysqli_error());
$data_salle = mysqli_fetch_array($result_salle);
		echo '<td>'.substr(utf8_encode($data_salle['titre']), 0, 3).'</td>';
		
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;bu='.$bu.'&amp;allday='.$data_allday['id'].'">'.utf8_encode($data_allday['titre']).'</a></td>'; //renvoi l'id du cycle en "GET"
		
		//start
		echo '<td>'.$data_allday['start'].'</td>';
		//end
		echo '<td>'.$data_allday['end'].'</td>';
		
		if($_COOKIE['role']=='e') {
		echo '<td><nobr>';
			echo '<form name="suppr_news" action="index.php?action=form&amp;id_bulletin='.$bu.'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette inscription?\')">
				<input type="hidden" name="id" value="'.$data_allday['id'].'">
				<input type="hidden" name="bu" value="'.$bu.'">
				<input name="deleteAllDay" class="btn_suppr" type="submit" value="deleteAllDay">
			     </form>';
		echo '</nobr></td>';
		}
			    
		echo '</tr>'; //fermeture de la ligne
	    }
?>
	    </tbody>
	</table>
	<table class="table">
	    <thead>
		<tr>
		    <th>Vacances / Festivals</th>
		    <th>Début</th>
		    <th>Fin</th>
		    <?php if($_COOKIE['role']=='e') { echo '<th></th>'; } ?>
		</tr>
	    </thead>
	    <tbody>
			
<?php
//requête qui récupère les événements
//
$sql_allday = "SELECT * FROM pm_allday
		WHERE categorie = '0'
		AND actif = 'a'
		AND ((start BETWEEN '$dataBulletinSelect[start]' AND '$dataBulletinSelect[end]') OR (end BETWEEN '$dataBulletinSelect[start]' AND '$dataBulletinSelect[end]'))
		ORDER BY start";
$result_allday = mysqli_query($connexion, $sql_allday) or die(mysqli_error());

$tr_n = 1;
while ($data_allday = mysqli_fetch_array($result_allday)) {
		echo '<tr>'; 
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;bu='.$bu.'&amp;allday='.$data_allday['id'].'">'.utf8_encode($data_allday['titre']).'</a></td>'; //renvoi l'id du cycle en "GET"
		
		//start
		echo '<td>'.$data_allday['start'].'</td>';
		//end
		echo '<td>'.$data_allday['end'].'</td>';
		
		if($_COOKIE['role']=='e') {
			echo '<td><nobr>';
			echo '<form name="suppr_news" action="index.php?action=form&amp;id_bulletin='.$bu.'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette inscription?\')">
				<input type="hidden" name="id" value="'.$data_allday['id'].'">
				<input type="hidden" name="bu" value="'.$bu.'">
				<input name="deleteAllDay" class="btn_suppr" type="submit" value="deleteAllDay">
			     </form>';
		echo '</nobr></td>';
		}
			    
		echo '</tr>'; //fermeture de la ligne
	    }
?>
	    </tbody>
	</table>
</div>

<div class="middle">
	<form action="<?php $_SERVER['PHP_SELF']; ?>#cycle" method="post">
		<div>
		    <label for="numero">Numéro du bulletin</label>
		    <input id="numero" type="text" name="numero" value="<?php echo utf8_encode($dataBulletinSelect['numero']); ?>">
		</div>
		<div>
		    <label for="titre">Titre du bulletin</label>
		    <input id="titre" type="text" name="titre" value="<?php echo utf8_encode($dataBulletinSelect['titre']); ?>">
		</div>
		<div class="input21">
		    <label for="start">Date de début</label>
		    <input type="date" min="2015-01-01" placeholder="2015-01-01" name="start" value="<?php echo utf8_encode($dataBulletinSelect['start']); ?>">
		</div>
		<div class="input22">
		    <label for="end">Date de fin</label>
		    <input type="date" min="2015-01-01" placeholder="2015-03-01" name="end" value="<?php echo utf8_encode($dataBulletinSelect['end']); ?>">
		</div>
		<div class="info_visible_screen">
		    <label for="commentaire">Commentaire</label>
		    <textarea name="commentaire"><?php echo afficheHtml(utf8_encode($dataBulletinSelect['commentaire'])); ?></textarea>
		</div>
		<div class="info_visible_print">
		    <label for="commentaire">Commentaire</label>
		    <?php echo '<div class="div_textarea">'.afficheHtml(utf8_encode($dataBulletinSelect['commentaire'])).'&nbsp;</div>'; ?>
		</div>
		<input type="hidden" name="id_bulletin" value="<?php echo $bu; ?>">
		<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveBulletin" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>
<?php
	$date = '';
	$intro = '';
	$notule = '';
	$presence = '';
	$photo = '';
	$logo = '';
	$bat = '';
	$info = '';
	$commentaire = '';
	
//sélection du cycle et du cartouche
$sqlCycleAtt = "SELECT * FROM pm_bulletin_cycle
		JOIN pm_cycle
		ON pm_bulletin_cycle.id_cycle = pm_cycle.id_cycle
		JOIN pm_cartouche
		ON pm_cycle.id_cycle = pm_cartouche.id_cycle
		WHERE pm_bulletin_cycle.id_bulletin='$bu'
		AND pm_cycle.actif = 'a'
		ORDER BY pm_bulletin_cycle.ordre"; 
	$resultCycleAtt = mysqli_query($connexion, $sqlCycleAtt ) or die(mysqli_error());
	    
	while ($dataCycleAtt = mysqli_fetch_array($resultCycleAtt)) {
?>
<!--##########--><hr class="clear"><!--##############################################################################################################################-->

<div class="block" id="cycle<?php echo $dataCycleAtt['id_cycle']; ?>">
<div class="left demi">
	<select>
<?php
//liste des dernières modifications
$sqlTriggerCycle = "SELECT * FROM pm_trigger_cycle
		WHERE id_cycle= '$dataCycleAtt[id_cycle]'
		GROUP BY datetime
		ORDER BY datetime DESC"; 
	$resultTriggerCycle = mysqli_query($connexion, $sqlTriggerCycle ) or die(mysqli_error());
	    
	while ($dataTriggerCycle = mysqli_fetch_array($resultTriggerCycle)) {
		echo '<option>'.$dataTriggerCycle['edition'].' - '.date("d.m.Y G:i", strtotime($dataTriggerCycle['datetime']));
		if($dataTriggerCycle['date']) echo ' / Date';
		if($dataTriggerCycle['intro']) echo ' / Intro par';
		if($dataTriggerCycle['notule']) echo ' / Notules par';
		if($dataTriggerCycle['photo']) echo ' / Photos par';
		if($dataTriggerCycle['presence']) echo ' / En présence de';
		if($dataTriggerCycle['logo']) echo ' / Logos';
		if($dataTriggerCycle['bat']) echo ' / PDF bàt';
		if($dataTriggerCycle['commentaire']) echo ' / Infos';
		echo '</option>';
	}
?>
	</select>
<?php
if($_COOKIE['role']=='e') {
	if($dataCycleAtt['edition']) {
		echo '<p class="import">Ce cycle est en cour d\'édition par "'.$dataCycleAtt['edition'].'"</p>';
	} else {
?>
    <form class="action" action="<?php $_SERVER['PHP_SELF']; ?>#cycle<?php echo $dataCycleAtt['id_cycle']; ?>" method="post">
	    <input type="hidden" name="id_cycle" value="<?php echo $dataCycleAtt['id_cycle']; ?>">
	    <input type="hidden" name="id_cartouche" value="<?php echo $dataCycleAtt['id']; ?>">
	    <input type="hidden" name="edition" value="<?php echo $_COOKIE['geLogCon']; ?>">
	    <input type="submit" name="saveCycleCartouche" class="btn" value="Modifier">
    </form>
<?php } } ?>
    <form action="<?php $_SERVER['PHP_SELF']; ?>#cycle<?php echo $dataCycleAtt['id_cycle']; ?>" method="post">
	<table class="tableur">
		<thead>
			
		</thead>
		<tbody>
			<tr>
				<td width="99">Titre du cycle</td>
				<td><?php echo utf8_encode($dataCycleAtt['titre']); ?></td>
			</tr>
			<tr>
				<td>Titre simple</td>
				<td><?php echo utf8_encode($dataCycleAtt['titre_simple']); ?></td>
			</tr>
<?php
//récupération du titre de la catégorie
$sqlCategorie = "SELECT titre FROM pm_categorie WHERE id_categorie ='$dataCycleAtt[id_categorie]'"; 
$resultCategorie = mysqli_query($connexion, $sqlCategorie ) or die(mysqli_error());
$dataCategorie = mysqli_fetch_array($resultCategorie);
?>
			<tr>
				<td>Rubrique bulletin</td>
				<td><?php echo utf8_encode($dataCategorie['titre']); ?></td>
			</tr>
<?php
if($_COOKIE['role']=='e' && $dataCycleAtt['edition']==$_COOKIE['geLogCon']) {
?>
			<tr>
				<td><label for="date">Date</label></td>
				<td><textarea name="date"><?php echo utf8_encode($dataCycleAtt['date']); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="intro">Intro(s) par</label></td>
				<td><input name="intro" value="<?php echo utf8_encode($dataCycleAtt['intro']); ?>"></td>
			</tr>
			<tr>
				<td><label for="notule">Notules par</label></td>
				<td><input name="notule" value="<?php echo utf8_encode($dataCycleAtt['notule']); ?>"></td>
			</tr>
			<tr>
				<td><label for="photo">Photos par</label></td>
				<td><input name="photo" value="<?php echo utf8_encode($dataCycleAtt['photo']); ?>"></td>
			</tr>
			<tr>
				<td><label for="presence">En présence de</label></td>
				<td><input name="presence" value="<?php echo utf8_encode($dataCycleAtt['presence']); ?>"></td>
			</tr>
			<tr>
				<td><label for="logo">Logos</label></td>
				<td><textarea name="logo"><?php echo utf8_encode($dataCycleAtt['logo']); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="bat">PDF bàt</label></td>
				<td><textarea name="bat"><?php echo utf8_encode($dataCycleAtt['bat']); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="commentaire">Infos</label></td>
				<td><textarea name="commentaire"><?php echo utf8_encode($dataCycleAtt['commentaire']); ?></textarea></td>
			</tr>
		</tbody>
	</table>
	    <input type="hidden" name="id_bulletin" value="<?php echo $bu; ?>">
	    <input type="hidden" name="id_cycle" value="<?php echo $dataCycleAtt['id_cycle']; ?>">
	    <input type="hidden" name="id_cartouche" value="<?php echo $dataCycleAtt['id']; ?>">
	    <input type="hidden" name="edition" value="<?php echo NULL; ?>">
	    <input type="submit" name="saveCycleCartouche" class="btn" value="Enregistrer">
<?php
} else {
?>
			<tr>
				<td>Dates</td>
				<td><?php echo nl2br(utf8_encode($dataCycleAtt['date'])); ?></td>
			</tr>
			<tr>
				<td>Intro(s) par</td>
				<td><?php echo nl2br(utf8_encode($dataCycleAtt['intro'])); ?></td>
			</tr>
			<tr>
				<td>Notules par</td>
				<td><?php echo nl2br(utf8_encode($dataCycleAtt['notule'])); ?></td>
			</tr>
			<tr>
				<td>Photos par</td>
				<td><?php echo nl2br(utf8_encode($dataCycleAtt['photo'])); ?></td>
			</tr>
			<tr>
				<td>En présence de</td>
				<td><?php echo nl2br(utf8_encode($dataCycleAtt['presence'])); ?></td>
			</tr>
			<tr>
				<td>Logos</td>
				<td><?php echo nl2br(utf8_encode($dataCycleAtt['logo'])); ?></td>
			</tr>
			<tr>
				<td>PDF bàt</td>
				<td><?php echo nl2br(utf8_encode($dataCycleAtt['bat'])); ?></td>
			</tr>
			<tr>
				<td>Infos</td>
				<td><?php echo nl2br(utf8_encode($dataCycleAtt['commentaire'])); ?></td>
			</tr>
		</tbody>
	</table>
<?php } ?>
    </form>
</div>
<div class="right demi">
<?php
//variables pour le contenu
$contentTitre = array();
$content = '';
$num_ordre = 1;
	//récupérer tous les films du cycle
	$sql_film = "SELECT * FROM pm_film
		JOIN pm_cycle_film 
		ON pm_cycle_film.id_film = pm_film.id_film 
		WHERE pm_cycle_film.id_cycle='$dataCycleAtt[id_cycle]'
		AND pm_cycle_film.actif = 'a'
		AND pm_film.id_film > 1
		ORDER BY ordre, annee_prod";
	$result_film = mysqli_query($connexion, $sql_film) or die(mysqli_error());
	while ($data_film = mysqli_fetch_array($result_film)) {
		$id_film = $data_film['id_film'];
		
		/***********************************************************************************************************/
		/************************************ récupération si titre à la séance ************************************/
		/***********************************************************************************************************/
		$sqlTitre = "SELECT * FROM pm_seance, pm_film_seance WHERE pm_film_seance.id_film ='$id_film' AND pm_film_seance.id_seance = pm_seance.id_seance AND pm_film_seance.id_cycle ='$dataCycleAtt[id_cycle]'"; // nom de la table ! requette
		$resultTitre = mysqli_query($connexion, $sqlTitre ) or die(mysqli_error());
		$dataTitre = mysqli_fetch_array($resultTitre);
		if($dataTitre['titre']) {
			//$contentTitre[$dataTitre['titre']] = '<h4>'.utf8_encode($dataTitre['titre']).'</h4>';
			//Titre du film
			$contentTitre[$dataTitre['titre']].= '<h5 style="margin-left:-15px">';
			
		    if($data_film['ordre']== '0') {
			$contentTitre[$dataTitre['titre']].= $num_ordre;
			$num_ordre++;
		    } else {
			$contentTitre[$dataTitre['titre']].= $data_film['ordre'];
		    }
		    $contentTitre[$dataTitre['titre']].= '<i>';
			if($_COOKIE['role']=='e') $contentTitre[$dataTitre['titre']].= '<a href="'.$_SERVER['PHP_SELF'].'?internal=link&amp;sm=_sm1&amp;cat=e&amp;var[zone]=film&amp;var[bu]='.$bu.'&amp;var[cy]='.$dataCycleAtt['id_cycle'].'&amp;var[fi]='.$id_film.'&amp;var[]=#film">';
			if($data_film['prefix_titre_o']) {
				if($data_film['prefix_titre_o']=="L'") {
					$contentTitre[$dataTitre['titre']].= "L'";
				} else {
					$contentTitre[$dataTitre['titre']].= utf8_encode($data_film['prefix_titre_o']).' ';
				}
			}
			$contentTitre[$dataTitre['titre']].= utf8_encode($data_film['titre_o']);
			//titre fr
			if($data_film['titre_fr']) {
				$contentTitre[$dataTitre['titre']].= ' <span>(';
				if($data_film['prefix_titre_fr']) {
					if($data_film['prefix_titre_fr']=="L'") {
						$contentTitre[$dataTitre['titre']].=  "L'";
					} else {
						$contentTitre[$dataTitre['titre']].= utf8_encode($data_film['prefix_titre_fr']).' ';
					}
				}
				$contentTitre[$dataTitre['titre']].= utf8_encode($data_film['titre_fr']);
				$contentTitre[$dataTitre['titre']].= ')</span>';
			}
			if($_COOKIE['role']=='e') $contentTitre[$dataTitre['titre']].= '</a>';
			$contentTitre[$dataTitre['titre']].= '</i></h5>';
			
			//préfix réalisateur
			$prefix_realisateur = '';
			switch ($data_film['prefix_film_director']) {
				case 1:
				    $prefix_realisateur = 'De ';
				    break;
				case 2:
				    $prefix_realisateur = 'Documentaire de ';
				    break;
				case 3:
				    $prefix_realisateur = 'Film d\'animation de ';
				    break;
				case 4:
				    $prefix_realisateur = 'Film collectif de ';
				    break;
				case 5:
				    $prefix_realisateur = 'Court métrage de ';
				    break;
			    }
			
			//récupération des informations du film
			$contentTitre[$dataTitre['titre']].= '<p>';
			$contentTitre[$dataTitre['titre']].= '<span>'.utf8_encode($data_film['pays_prod']).' · '.$data_film['annee_prod'].'</span><br />';
			$contentTitre[$dataTitre['titre']].= '<span><b>'.$prefix_realisateur.'</b>'.utf8_encode($data_film['realisateur']).' / '.utf8_encode($data_film['ayants_droits']).'</span>';
			$contentTitre[$dataTitre['titre']].= '<span class="remarque">'.utf8_encode($data_film['remarque']).'</span>';
			if($data_film['distri']==1) {$contentTitre[$dataTitre['titre']].= 'Distribution Cinémathèque suisse';}
			/*if (empty($data_film['remarque']) && empty($data_film['ayants_droits']))*/ $contentTitre[$dataTitre['titre']].= '<br>';
			
			/* LISTE DES COPIES
			** Si une copie a été sélectionnée, nous affichons que celle-là. Sinon on affiche tout
			*/
			$sqlCopieStatut = "SELECT * FROM pm_copie WHERE id_film ='$id_film' AND id_film > 0 AND statut = 1"; // nom de la table ! requette
			$resultCopieStatut = mysqli_query($connexion, $sqlCopieStatut ) or die(mysqli_error());
			if(mysqli_num_rows($resultCopieStatut) > 0) {
				while ($dataCopieAtt = mysqli_fetch_array($resultCopieStatut)) {
					if($dataCopieAtt['statut']==1) $contentTitre[$dataTitre['titre']].= '<span class="select">';
					$contentTitre[$dataTitre['titre']].= '<span class="import">'.utf8_encode($dataCopieAtt['provenance']).':</span> ';
					$contentTitre[$dataTitre['titre']].= utf8_encode($dataCopieAtt['format']).' '.utf8_encode($dataCopieAtt['cryptage']).' | '.utf8_encode($dataCopieAtt['version']);
					if ($dataCopieAtt['soustitre']) $contentTitre[$dataTitre['titre']].= ' s-t '.utf8_encode($dataCopieAtt['soustitre']); 
					if ($dataCopieAtt['intertitre']) $contentTitre[$dataTitre['titre']].= ' i-t '.utf8_encode($dataCopieAtt['intertitre']);
					if($dataCopieAtt['duree'] == 0) {
						$contentTitre[$dataTitre['titre']].= ' | <span class="import">'.utf8_encode($dataCopieAtt['duree']).'\'</span>';
					} else {
						$contentTitre[$dataTitre['titre']].= ' | '.utf8_encode($dataCopieAtt['duree']).'\'';
					}
					if($dataCopieAtt['statut']==1) $contentTitre[$dataTitre['titre']].= '</span>';
					if ($dataCopieAtt['etat']) $contentTitre[$dataTitre['titre']].= ' | '.utf8_encode($dataCopieAtt['etat']);
					if($dataCopieAtt['id_nom']>0) {
						//requète pour récupérer la liste des salles
						$sql_nom = "SELECT nomenclature FROM pm_copie_nom WHERE id_nom = '$dataCopieAtt[id_nom]'";
						$result_nom = mysqli_query($connexion, $sql_nom) or die(mysqli_error());
						$data_nom = mysqli_fetch_array($result_nom);
						$contentTitre[$dataTitre['titre']].= ' | '.utf8_encode($data_nom['nomenclature']);
					}
					if (empty($dataCopieAtt['commentaire'])) { $contentTitre[$dataTitre['titre']].= '<br>';
					} else { $contentTitre[$dataTitre['titre']].= ' | '.utf8_encode($dataCopieAtt['commentaire']).'<br>'; }
				}
			
			} else {
				$sqlCopieAtt = "SELECT * FROM pm_copie WHERE id_film ='$id_film' AND id_film > 0 ORDER BY statut"; // nom de la table ! requette
				$resultCopieAtt = mysqli_query($connexion, $sqlCopieAtt ) or die(mysqli_error());
				while ($dataCopieAtt = mysqli_fetch_array($resultCopieAtt)) {
					$contentTitre[$dataTitre['titre']].= '<span class="import';
					if($dataCopieAtt['statut']==1) $contentTitre[$dataTitre['titre']].= ' select';
					$contentTitre[$dataTitre['titre']].= '">'.utf8_encode($dataCopieAtt['provenance']).':</span> ';
					$contentTitre[$dataTitre['titre']].= utf8_encode($dataCopieAtt['format']).' '.utf8_encode($dataCopieAtt['cryptage']).' | '.utf8_encode($dataCopieAtt['version']);
					if ($dataCopieAtt['soustitre']) $contentTitre[$dataTitre['titre']].= ' s-t '.utf8_encode($dataCopieAtt['soustitre']); 
					if ($dataCopieAtt['intertitre']) $contentTitre[$dataTitre['titre']].= ' i-t '.utf8_encode($dataCopieAtt['intertitre']);
					if($dataCopieAtt['duree'] == 0) {
						$contentTitre[$dataTitre['titre']].= ' | <span class="import">'.utf8_encode($dataCopieAtt['duree']).'\'</span>';
					} else {
						$contentTitre[$dataTitre['titre']].= ' | '.utf8_encode($dataCopieAtt['duree']).'\'';
					}
					if ($dataCopieAtt['etat']) $contentTitre[$dataTitre['titre']].= ' | '.utf8_encode($dataCopieAtt['etat']);
					if($dataCopieAtt['id_nom']>0) {
						//requète pour récupérer la liste des salles
						$sql_nom = "SELECT nomenclature FROM pm_copie_nom WHERE id_nom = '$dataCopieAtt[id_nom]'";
						$result_nom = mysqli_query($connexion, $sql_nom) or die(mysqli_error());
						$data_nom = mysqli_fetch_array($result_nom);
						$contentTitre[$dataTitre['titre']].= ' | '.utf8_encode($data_nom['nomenclature']);
					}
					if (empty($dataCopieAtt['commentaire'])) { $contentTitre[$dataTitre['titre']].= '<br>';
					} else { $contentTitre[$dataTitre['titre']].= ' | '.utf8_encode($dataCopieAtt['commentaire']).'<br>'; }
				}
			}
			
			$contentTitre[$dataTitre['titre']].= '<span class="import">';
			// Projection dans ce bulletin
			$sql_numb = "SELECT pm_seance.start, pm_salle.titre
				    FROM pm_seance
				    
				    JOIN pm_film_seance
				    ON pm_film_seance.id_seance = pm_seance.id_seance
				    
				    JOIN pm_bulletin_cycle
				    ON pm_bulletin_cycle.id_cycle = '$dataCycleAtt[id_cycle]'
				    JOIN pm_bulletin
				    ON pm_bulletin.id_bulletin = pm_bulletin_cycle.id_bulletin
				    
				    JOIN pm_salle
				    ON pm_salle.id_salle = pm_seance.id_salle
				    
				    WHERE pm_film_seance.id_film = '$id_film'
				    AND pm_seance.actif = 'a'
				    AND pm_seance.id_seance > '9'
				    
				    AND pm_seance.start > pm_bulletin.start
				    AND pm_seance.start <= pm_bulletin.end";
			$result_numb = mysqli_query($connexion, $sql_numb) or die(mysqli_error());
			//boucle pour récupérer toute les donneés	
			while ($data_numb = mysqli_fetch_array($result_numb)) {
				$dateProj = strtotime($data_numb['start']);
				$contentTitre[$dataTitre['titre']].= dateFrancais(date("D j M - G:i", $dateProj));
				//récupération de la salle
				$contentTitre[$dataTitre['titre']].= ' '.substr($data_numb['titre'], 0, 3);
				$contentTitre[$dataTitre['titre']].= ' | ';
			}
			$contentTitre[$dataTitre['titre']].= '<br>&nbsp;</span>';
			$contentTitre[$dataTitre['titre']].= '</p>';
			
		/***********************************************************************************************************/
		/************************************ récupération si pas de titre de séance ************************************/
		/***********************************************************************************************************/
		} else {
			//Titre du film		
			$content.= '<h5 style="margin-left:-15px;">';
			
		    if($data_film['ordre']== '0') {
			$content.= $num_ordre;
			$num_ordre++;
		    } else {
			$content.= $data_film['ordre'];
		    }
		    $content.= ' <i>';
			if($_COOKIE['role']=='e') $content.= '<a href="'.$_SERVER['PHP_SELF'].'?internal=link&amp;sm=_sm1&amp;cat=e&amp;var[zone]=film&amp;var[bu]='.$bu.'&amp;var[cy]='.$dataCycleAtt['id_cycle'].'&amp;var[fi]='.$id_film.'&amp;var[]=#film">';
			if($data_film['prefix_titre_o']) {
				if($data_film['prefix_titre_o']=="L'") {
					$content.= "L'";
				} else {
					$content.= utf8_encode($data_film['prefix_titre_o']).' ';
				}
			}
			$content.= utf8_encode($data_film['titre_o']);
			//titre fr
			if($data_film['titre_fr']) {
				$content.= ' <span>(';
				if($data_film['prefix_titre_fr']) {
					if($data_film['prefix_titre_fr']=="L'") {
						$content.=  "L'";
					} else {
						$content.= utf8_encode($data_film['prefix_titre_fr']).' ';
					}
				}
				$content.= utf8_encode($data_film['titre_fr']);
				$content.= ')</span>';
			}
			if($_COOKIE['role']=='e') $content.= '</a>';
			$content.= '</i></h5>';
			
			//récupération des informations du film
			$content.= '<p>';
			$content.= '<span>'.utf8_encode($data_film['pays_prod']).' · '.$data_film['annee_prod'].'</span><br />';
			$content.= '<span>'.utf8_encode($data_film['realisateur']).' / '.utf8_encode($data_film['ayants_droits']).'</span>';
			$content.= '<span class="remarque">'.utf8_encode($data_film['remarque']).'</span>';
			/*if (empty($data_film['remarque']) && empty($data_film['ayants_droits']))*/ $content.= '<br>';
			
			/* LISTE DES COPIES
			** Si une copie a été sélectionnée, nous affichons que celle-là. Sinon on affiche tout
			*/
			$sqlCopieStatut = "SELECT * FROM pm_copie WHERE id_film ='$id_film' AND id_film > 0 AND statut = 1"; // nom de la table ! requette
			$resultCopieStatut = mysqli_query($connexion, $sqlCopieStatut ) or die(mysqli_error());
			if(mysqli_num_rows($resultCopieStatut) > 0) {
				while ($dataCopieAtt = mysqli_fetch_array($resultCopieStatut)) {
					if($dataCopieAtt['statut']==1) $content.= '<span class="select">';
					$content.= '<span class="import">'.utf8_encode($dataCopieAtt['provenance']).':</span> ';
					$content.= utf8_encode($dataCopieAtt['format']).' '.utf8_encode($dataCopieAtt['cryptage']).' | '.utf8_encode($dataCopieAtt['version']);
					if ($dataCopieAtt['soustitre']) $content.= ' s-t '.utf8_encode($dataCopieAtt['soustitre']); 
					if ($dataCopieAtt['intertitre']) $content.= ' i-t '.utf8_encode($dataCopieAtt['intertitre']);
					if($dataCopieAtt['duree'] == 0) {
						$content.= ' | <span class="import">'.utf8_encode($dataCopieAtt['duree']).'\'</span>';
					} else {
						$content.= ' | '.utf8_encode($dataCopieAtt['duree']).'\'';
					}
					if($dataCopieAtt['statut']==1) $content.= '</span>';
					if ($dataCopieAtt['etat']) $content.= ' | '.utf8_encode($dataCopieAtt['etat']);
					if($dataCopieAtt['id_nom']>0) {
						//requète pour récupérer la liste des salles
						$sql_nom = "SELECT nomenclature FROM pm_copie_nom WHERE id_nom = '$dataCopieAtt[id_nom]'";
						$result_nom = mysqli_query($connexion, $sql_nom) or die(mysqli_error());
						$data_nom = mysqli_fetch_array($result_nom);
						$content.= ' | '.utf8_encode($data_nom['nomenclature']);
					}
					if (empty($dataCopieAtt['commentaire'])) { $content.= '<br>';
					} else { $content.= ' | '.utf8_encode($dataCopieAtt['commentaire']).'<br>'; }
				}
			
			} else {
				$sqlCopieAtt = "SELECT * FROM pm_copie WHERE id_film ='$id_film' AND id_film > 0 ORDER BY statut"; // nom de la table ! requette
				$resultCopieAtt = mysqli_query($connexion, $sqlCopieAtt ) or die(mysqli_error());
				while ($dataCopieAtt = mysqli_fetch_array($resultCopieAtt)) {
					$content.= '<span class="import';
					if($dataCopieAtt['statut']==1) $content.= ' select';
					$content.= '">'.utf8_encode($dataCopieAtt['provenance']).':</span> ';
					$content.= utf8_encode($dataCopieAtt['format']).' '.utf8_encode($dataCopieAtt['cryptage']).' | '.utf8_encode($dataCopieAtt['version']);
					if ($dataCopieAtt['soustitre']) $content.= ' s-t '.utf8_encode($dataCopieAtt['soustitre']); 
					if ($dataCopieAtt['intertitre']) $content.= ' i-t '.utf8_encode($dataCopieAtt['intertitre']);
					if($dataCopieAtt['duree'] == 0) {
						$content.= ' | <span class="import">'.utf8_encode($dataCopieAtt['duree']).'\'</span>';
					} else {
						$content.= ' | '.utf8_encode($dataCopieAtt['duree']).'\'';
					}
					if ($dataCopieAtt['etat']) $content.= ' | '.utf8_encode($dataCopieAtt['etat']);
					if($dataCopieAtt['id_nom']>0) {
						//requète pour récupérer la liste des salles
						$sql_nom = "SELECT nomenclature FROM pm_copie_nom WHERE id_nom = '$dataCopieAtt[id_nom]'";
						$result_nom = mysqli_query($connexion, $sql_nom) or die(mysqli_error());
						$data_nom = mysqli_fetch_array($result_nom);
						$content.= ' | '.utf8_encode($data_nom['nomenclature']);
					}
					if (empty($dataCopieAtt['commentaire'])) { $content.= '<br>';
					} else { $content.= ' | '.utf8_encode($dataCopieAtt['commentaire']).'<br>'; }
				}
			}
			
			// Projection dans ce bulletin
			$content.= '<span class="import">';
			$sql_numb = "SELECT pm_seance.start, pm_salle.titre
				    FROM pm_seance
				    
				    JOIN pm_film_seance
				    ON pm_film_seance.id_seance = pm_seance.id_seance
				    
				    JOIN pm_bulletin_cycle
				    ON pm_bulletin_cycle.id_cycle = '$dataCycleAtt[id_cycle]'
				    JOIN pm_bulletin
				    ON pm_bulletin.id_bulletin = pm_bulletin_cycle.id_bulletin
				    
				    JOIN pm_salle
				    ON pm_salle.id_salle = pm_seance.id_salle
				    
				    WHERE pm_film_seance.id_film = '$id_film'
				    AND pm_seance.actif = 'a'
				    AND pm_seance.id_seance > '9'
				    
				    AND pm_seance.start > pm_bulletin.start
				    AND pm_seance.start <= pm_bulletin.end";
			$result_numb = mysqli_query($connexion, $sql_numb) or die(mysqli_error());
			//boucle pour récupérer toute les donneés	
			while ($data_numb = mysqli_fetch_array($result_numb)) {
				$dateProj = strtotime($data_numb['start']);
				$content.= dateFrancais(date("D j M - G:i", $dateProj));
				//récupération de la salle
				$content.= ' '.substr($data_numb['titre'], 0, 3);
				$content.= ' | ';
			}
			if(mysqli_num_rows($result_numb) > 0) {$content.= '<br>&nbsp;';}
			$content.= '</span></p>';
		}
	}
	/*** AFFICHAGE DU CONTENU ***/
	foreach($contentTitre as $cle => $element) {
		echo '<h4>'.utf8_encode($cle).'</h4>';
		echo $element;
		echo '<hr>';
	}
	echo $content;
?>
</div>
</div>
<?php } ?>
<!--##########--><hr class="clear" id="merci" /><!--##############################################################################################################################-->

<?php 
/****************************************************************************************************************************************/
/********************************************************* merci **********************************************************************/
/****************************************************************************************************************************************/
?>
<div class="left demi">
	<h3>Remerciements</h3>

	<form class="clear" action="<?php $_SERVER['PHP_SELF']; ?>#merci" method="post">
		<div class="info_visible_screen">
		    <label for="merci">Remerciements</label>
		    <?php if($_COOKIE['role']=='e') { 
			    echo '<textarea name="merci">'.utf8_encode($dataBulletinSelect['merci']).'</textarea>';
		    } else {
			    echo '<div class="div_textarea">'.substr(utf8_encode($dataBulletinSelect['merci']),0,-4).'&nbsp;</div>';
		    } ?>
		</div>
		<div class="info_visible_print">
		    <label for="merci">Remerciements</label>
		    <?php echo '<div class="div_textarea">'.substr(utf8_encode($dataBulletinSelect['merci']),0,-4).'&nbsp;</div>'; ?>
		</div>
		<input type="hidden" name="id_bulletin" value="<?php echo $bu; ?>">
		<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveMerci" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>
<?php
/****************************************************************************************************************************************/
/********************************************************* JOURNAL **********************************************************************/
/****************************************************************************************************************************************/
?>
<!--##########--><hr class="clear" id="journal" /><!--##############################################################################################################################-->
<div class="left demi">
	<h3>Journal</h3>
<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>#journal" method="get">
    <input type="hidden" value="bulletin" name="zone">
    <input type="hidden" value="<?php echo $bu; ?>" name="bu">
    <input type="hidden" value="1" name="add">
    <input type="submit" class="btn" value="Ajouter">
  </form>
<?php } ?>
	<table class="table" id="listeJournal">
	    <thead>
		<tr>
		    <th>Titre</th>
		    <th>Rédacteur</th>
		    <th>Photo</th>
		    <?php if($_COOKIE['role']=='e') { echo '<th></th>'; } ?>
		</tr>
	    </thead>
	    <tbody class="content">
	   <tr style="background-color: #ded8d8;"><th colspan="4">Evénements</th></tr>
			
<?php
//requête qui récupère les événements
$sqlJournalEvt = "SELECT * FROM pm_journal
		WHERE categorie = '0'
		AND id_bulletin = '$bu'
		ORDER BY ordre";
$resultJournalEvt = mysqli_query($connexion, $sqlJournalEvt) or die(mysqli_error());
while ($dataJournalEvt = mysqli_fetch_array($resultJournalEvt)) {	
		if ($journal == $dataJournalEvt['id_journal'] ) {
		    echo '<tr class="select" id="pos_'.$dataJournalEvt['id_journal'].'">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr id="pos_'.$dataJournalEvt['id_journal'].'">'; // pour l'altérnance des couleurs
		}
		
		//titre
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;add=1&amp;bu='.$bu.'&amp;journal='.$dataJournalEvt['id_journal'].'#journal">';
		if($dataJournalEvt['statut'] == '1') {
			echo '<s>'.utf8_encode($dataJournalEvt['titre']).'</s>';
		} else echo utf8_encode($dataJournalEvt['titre']);
		echo '</a></td>'; //renvoi l'id du cycle en "GET"
		
		//Rédacteur
		echo '<td>'.utf8_encode($dataJournalEvt['redacteur']).'</td>';
		
		//photo
		echo '<td>'.utf8_encode($dataJournalEvt['photo']).'</td>';
		
		if($_COOKIE['role']=='e') {
		echo '<td><nobr>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;bu='.$bu.'#journal" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette inscription?\')">
				<input type="hidden" name="id_journal" value="'.$dataJournalEvt['id_journal'].'">
				<input name="deleteJournal" class="btn_suppr" type="submit" value="deleteJournal">
			     </form>';
		echo '</nobr></td>';
		}
			    
		echo '</tr>'; //fermeture de la ligne
	}
?>
	   <tr style="background-color: #ded8d8;"><th colspan="4">Autre</th></tr>
			
<?php
//requête qui récupère les événements
$sqlJournalEvt = "SELECT * FROM pm_journal
		WHERE categorie = '1'
		AND id_bulletin = '$bu'
		ORDER BY ordre";
$resultJournalEvt = mysqli_query($connexion, $sqlJournalEvt) or die(mysqli_error());
while ($dataJournalEvt = mysqli_fetch_array($resultJournalEvt)) {	
		if ($journal == $dataJournalEvt['id_journal'] ) {
		    echo '<tr class="select" id="pos_'.$dataJournalEvt['id_journal'].'">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr id="pos_'.$dataJournalEvt['id_journal'].'">'; // pour l'altérnance des couleurs
		}
		
		//titre
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;add=1&amp;bu='.$bu.'&amp;journal='.$dataJournalEvt['id_journal'].'#journal">';
		if($dataJournalEvt['statut'] == '1') {
			echo '<s>'.utf8_encode($dataJournalEvt['titre']).'</s>';
		} else echo utf8_encode($dataJournalEvt['titre']);
		echo '</a></td>'; //renvoi l'id du cycle en "GET"
		
		//Rédacteur
		echo '<td>'.utf8_encode($dataJournalEvt['redacteur']).'</td>';
		
		//photo
		echo '<td>'.utf8_encode($dataJournalEvt['photo']).'</td>';
		
		if($_COOKIE['role']=='e') {
		echo '<td><nobr>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;bu='.$bu.'#journal" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette inscription?\')">
				<input type="hidden" name="id_journal" value="'.$dataJournalEvt['id_journal'].'">
				<input name="deleteJournal" class="btn_suppr" type="submit" value="deleteJournal">
			     </form>';
		echo '</nobr></td>';
		}
			    
		echo '</tr>'; //fermeture de la ligne
	}
?>
	    </tbody>
	</table>
	
</div>
<?php if($add == '1') { 
    $sqlJournalSelect = "SELECT * FROM pm_journal WHERE id_journal ='$journal'"; // nom de la table ! requette
    $resultJournalSelect = mysqli_query($connexion, $sqlJournalSelect ) or die(mysqli_error());
    $dataJournalSelect = mysqli_fetch_array($resultJournalSelect);
?>
<div class="right">
	
	<h4>Ajout / Modification</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="titre">Titre</label>
		<input type="text" id="titre" name="titre" value="<?php echo utf8_encode($dataJournalSelect['titre']); ?>">
	    </div>
	    <div>
		<label for="categorie">Catégorie</label>
		<select name="categorie" id="categorie">
			<option <?php if($dataJournalSelect['categorie']==0) echo 'selected'; ?> value="0">événement</option>
			<option <?php if($dataJournalSelect['categorie']==1) echo 'selected'; ?> value="1">autre</option>
		</select>
	    </div>
	    <div>
		<label for="redacteur">Rédacteur</label>
		<input type="text" id="redacteur" name="redacteur" value="<?php echo utf8_encode($dataJournalSelect['redacteur']); ?>">
	    </div>
	    <div>
		<label for="photo">Photo</label>
		<input type="text" id="photo" name="photo" value="<?php echo utf8_encode($dataJournalSelect['photo']); ?>">
	    </div>
	    <div>
		<label for="statut">Statut</label>
		<select name="statut" id="statut">
			<option <?php if($dataJournalSelect['statut']==0) echo 'selected'; ?> value="0">Accepté</option>
			<option <?php if($dataJournalSelect['statut']==1) echo 'selected'; ?> value="1">Refusé</option>
		</select>
	    </div>
	    <input type="hidden" name="id_journal" value="<?php echo $journal; ?>">
	    <input type="hidden" name="id_bulletin" value="<?php echo $bu; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveJournal" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>
<?php } ?>
<!--##########--><hr class="clear" id="pub" /><!--##############################################################################################################################-->

<?php 
/****************************************************************************************************************************************/
/********************************************************* PUB **********************************************************************/
/****************************************************************************************************************************************/
?>
<div class="left demi">
	<h3>Publicités</h3>
<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>#pub" method="get">
    <input type="hidden" value="bulletin" name="zone">
    <input type="hidden" value="<?php echo $bu; ?>" name="bu">
    <input type="hidden" value="2" name="add">
    <input type="submit" class="btn" value="Ajouter">
  </form>
<?php } ?>
	<table class="table" id="listePub">
	    <thead>
		<tr>
		    <th>Titre</th>
		    <th>Commentaires</th>
		    <?php if($_COOKIE['role']=='e') { echo '<th></th>'; } ?>
		</tr>
	    </thead>
	    <tbody class="content">
			
<?php
//requête qui récupère les événements
$sqlPubEvt = "SELECT * FROM pm_pub
		WHERE id_bulletin = '$bu'
		ORDER BY ordre";
$resultPubEvt = mysqli_query($connexion, $sqlPubEvt) or die(mysqli_error());
while ($dataPubEvt = mysqli_fetch_array($resultPubEvt)) {	
		if ($pub == $dataPubEvt['id_pub'] ) {
		    echo '<tr class="select" id="pos_'.$dataPubEvt['id_pub'].'">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr id="pos_'.$dataPubEvt['id_pub'].'">'; // pour l'altérnance des couleurs
		}
		
		//titre
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;add=2&amp;bu='.$bu.'&amp;pub='.$dataPubEvt['id_pub'].'#journal">'.utf8_encode($dataPubEvt['titre']).'</a></td>'; //renvoi l'id du cycle en "GET"
		
		//Commentaire
		echo '<td>'.utf8_encode($dataPubEvt['commentaire']).'</td>';
		
		if($_COOKIE['role']=='e') {
		echo '<td><nobr>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=bulletin&amp;bu='.$bu.'#pub" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette inscription?\')">
				<input type="hidden" name="id_pub" value="'.$dataPubEvt['id_pub'].'">
				<input name="deletePub" class="btn_suppr" type="submit" value="deletePub">
			     </form>';
		echo '</nobr></td>';
		}
			    
		echo '</tr>'; //fermeture de la ligne
	}
?>
	    </tbody>
	</table>
	
</div>
<?php if($add == '2') { 
    $sqlPubSelect = "SELECT * FROM pm_pub WHERE id_pub ='$pub'"; // nom de la table ! requette
    $resultPubSelect = mysqli_query($connexion, $sqlPubSelect ) or die(mysqli_error());
    $dataPubSelect = mysqli_fetch_array($resultPubSelect);
?>
<div class="right">
	
	<h4>Ajout / Modification</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="titre">Titre</label>
		<input type="text" id="titre" name="titre" value="<?php echo utf8_encode($dataPubSelect['titre']); ?>">
	    </div>
	    <div>
		<label for="commentaire">Titre</label>
		<textarea name="commentaire"><?php echo utf8_encode($dataPubSelect['commentaire']); ?></textarea>
	    </div>
	    <input type="hidden" name="id_pub" value="<?php echo $pub; ?>">
	    <input type="hidden" name="id_bulletin" value="<?php echo $bu; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="savePub" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>
<?php } ?>

<?php } ?>
<!--##########--><hr class="clear" /><!--##############################################################################################################################-->

