<?php
/**
* INDEX Détail d'un cycle
*
* sous menu caché _smi0
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 01.04.2015 - 13.07.2015
*/

//sélection du cycle et du cartouche
if($bu){
	$sqlCycleAtt = "SELECT * FROM pm_cycle
		JOIN pm_cartouche
		ON pm_cycle.id_cycle = pm_cartouche.id_cycle
		JOIN pm_bulletin_cycle
		ON pm_bulletin_cycle.id_cycle = pm_cycle.id_cycle
		WHERE pm_cycle.id_cycle='$cy'"; 
	$resultCycleAtt = mysqli_query($connexion, $sqlCycleAtt ) or die(mysqli_error());
	$dataCycleAtt = mysqli_fetch_array($resultCycleAtt);
}else {
	$sqlCycleAtt = "SELECT * FROM pm_cycle
		JOIN pm_cartouche
		ON pm_cycle.id_cycle = pm_cartouche.id_cycle
		WHERE pm_cycle.id_cycle='$cy'"; 
	$resultCycleAtt = mysqli_query($connexion, $sqlCycleAtt ) or die(mysqli_error());
	$dataCycleAtt = mysqli_fetch_array($resultCycleAtt);
}
?>
<div id="cycle<?php echo $dataCycleAtt['id_cycle']; ?>">
<div class="left demi">
	<select>
<?php
//liste des dernières modifications
	$sqlTriggerCycle = "SELECT * FROM pm_trigger_cycle
		WHERE id_cycle= '$cy'
		GROUP BY datetime
		ORDER BY datetime DESC"; 
	$resultTriggerCycle = mysqli_query($connexion, $sqlTriggerCycle ) or die(mysqli_error());
	    
	while ($dataTriggerCycle = mysqli_fetch_array($resultTriggerCycle)) {
		echo '<option>'.$dataTriggerCycle['edition'].' - '.date("d.m.Y G:i", strtotime($dataTriggerCycle['datetime']));
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

    <form action="<?php $_SERVER['PHP_SELF']; ?>#cycle<?php echo $dataCycleAtt['id_cycle']; ?>" method="post">
	<table class="tableur">
		<thead>
			
		</thead>
		<tbody>
			<tr>
				<td>Titre du cycle</td>
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
			<tr>
				<td>Dates</td>
				<td><?php echo utf8_encode($dataCycleAtt['date']); ?></td>
			</tr>
			<tr>
				<td>Intro(s) par</td>
				<td><?php echo utf8_encode($dataCycleAtt['intro']); ?></td>
			</tr>
			<tr>
				<td>Notules par</td>
				<td><?php echo utf8_encode($dataCycleAtt['notule']); ?></td>
			</tr>
			<tr>
				<td>Photos par</td>
				<td><?php echo utf8_encode($dataCycleAtt['photo']); ?></td>
			</tr>
			<tr>
				<td>En présence de</td>
				<td><?php echo utf8_encode($dataCycleAtt['presence']); ?></td>
			</tr>
			<tr>
				<td>Logos</td>
				<td><?php echo utf8_encode($dataCycleAtt['logo']); ?></td>
			</tr>
			<tr>
				<td>PDF bàt</td>
				<td><?php echo utf8_encode($dataCycleAtt['bat']); ?></td>
			</tr>
			<tr>
				<td>Infos</td>
				<td><?php echo utf8_encode($dataCycleAtt['info']); ?></td>
			</tr>
			<tr>
				<td>Commentaire</td>
				<td><?php echo utf8_encode($dataCycleAtt['commentaire']); ?></td>
			</tr>
		</tbody>
	</table>
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
			$sqlCopieStatut = "SELECT * FROM pm_copie WHERE id_film ='$id_film' AND id_film > 0 ORDER BY statut"; // nom de la table ! requette
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
			$sqlCopieStatut = "SELECT * FROM pm_copie WHERE id_film ='$id_film' AND id_film > 0 ORDER BY statut"; // nom de la table ! requette
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
<!--##########--><hr class="clear" id="cycle" /><!--##############################################################################################################################-->
</div>
