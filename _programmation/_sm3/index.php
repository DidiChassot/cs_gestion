<?php
/**
* INDEX calendrier - PROGRAMMATION - SM3
*
* Calendrier pour la programmation de la Cinémathèque suisse par bulletin / par mois
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 26.01.2015 - 13.07.2015
*/


/**************************************************************************************
 * ****************************** Constantees *****************************************
 * ***********************************************************************************/ 
	
if($_COOKIE['role']=='e') { 
    include($_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/inc/pm_js.php"); //paramêtres js pour les effets ajax
} else {
    include($_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/inc/pm_js_simple.php"); //paramêtres js pour les effets ajax sans modifications
}

// remplacement de l'apostrophe
$caract = array(utf8_decode("'"));
$new_caract = array(utf8_encode("&acute;"));


/*************************************************************************************
 * ************************* AFFICHAGE DE L'APPLICATION ******************************
 * ***********************************************************************************/

?>
<div id='wrap'>
	<div id="col_left">
		
	<table class="table">
	    <thead>
		<tr>
		    <th>Bulletin</th>
		    <th></th>
		</tr>
	    </thead>
	    <tbody>
<?php /********************* Affichage des Bulletins Limité au 10 derniers *********************/
	    $sqlBulletinAtt = "SELECT id_bulletin, numero, titre FROM pm_bulletin ORDER BY numero DESC LIMIT 5"; 
	    $resultBulletinAtt = mysqli_query($connexion, $sqlBulletinAtt ) or die(mysqli_error());
	    
	while ($dataBulletinAtt = mysqli_fetch_array($resultBulletinAtt)) {
		// définir si le cycle a été sélectionné	
		if ($id_bulletin == $dataBulletinAtt['id_bulletin'] ) {
		    echo '<tr class="select">'; // pour l'altérnance des couleurs
		} else {
		    echo '<tr>'; // pour l'altérnance des couleurs
		}
		echo '<td><a href="'.$_SERVER['PHP_SELF'].'?action=neutre&amp;id_bulletin='.$dataBulletinAtt['id_bulletin'].'">'.$dataBulletinAtt['numero'].' '.utf8_encode($dataBulletinAtt['titre']).'</a></td>'; //renvoi l'id du cycle en "GET"
		
		//vision 2 mois
		echo '<td><a class="btn_visio" href="'.$_SERVER['PHP_SELF'].'?internal=link&amp;sm=_smi1&amp;cat=e&amp;var&#91;id_bulletin&#93;='.$dataBulletinAtt['id_bulletin'].'">V</a></td>';
			    
		echo '</tr>'; //fermeture de la ligne
	    }
?>
	    </tbody>
	</table>
<!-- LISTE DES BULLETINS-->
<?php
//récupération de l'id envoyé pour l'ajouter dans la session
if($_GET['idMenu']) $_SESSION['idMenu'] = $_GET['idMenu'];
?>
<hr class="clear">
<!--Liste des cycles et films-->
	<div id='external-events'>
<!-- /LISTE DES BULLETINS-->
	    <h4>Liste des films</h4>
	    <ul class="movie menu">
<?php
//numérotation du menu
$num = 1;
//récupérer les cycles
	$sql_cycle = "SELECT pm_cycle.id_cycle, pm_cycle.couleur, pm_cycle.titre_simple FROM pm_bulletin_cycle
		JOIN pm_cycle
		ON pm_bulletin_cycle.id_cycle = pm_cycle.id_cycle
		WHERE pm_bulletin_cycle.id_bulletin='$id_bulletin'
		AND pm_cycle.actif = 'a'
		ORDER BY ordre";
	$result_cycle = mysqli_query($connexion, $sql_cycle) or die(mysqli_error());
//boucle pour récupérer toute les donneés	
	while ($data_cycle = mysqli_fetch_array($result_cycle)) {
		$id_cycle = $data_cycle['id_cycle'];
		echo '<li style="border-left:2px solid '.$data_cycle['couleur'].'"><div class="menu" id="menu'.$num.'" onclick="afficheMenu(this)" style="border-bottom: 2px solid '.$data_cycle['couleur'].'">'.utf8_encode($data_cycle['titre_simple']).'</div>';
		echo '<div id="sousmenu'.$num.'" style="display:none">';
	//récupérer tous les films du cycle
		$sql_film = "SELECT pm_film.id_film, prefix_titre_o, titre_o, COUNT(pm_film_seance.id_seance) AS numb FROM pm_film
			    JOIN pm_cycle_film
			    ON pm_cycle_film.id_film = pm_film.id_film
			    LEFT JOIN pm_film_seance
			    ON (pm_film.id_film = pm_film_seance.id_film AND pm_film_seance.id_seance > '9' AND pm_film_seance.id_cycle = '$id_cycle')
			    WHERE pm_cycle_film.id_cycle='$id_cycle'
			    AND pm_cycle_film.actif = 'a'
			    GROUP BY pm_film.id_film
			    ORDER BY pm_cycle_film.ordre, annee_prod ASC";
		$result_film = mysqli_query($connexion, $sql_film) or die(mysqli_error());
	//boucle pour récupérer toute les donneés	
		while ($data_film = mysqli_fetch_array($result_film)) {
			$id_film = $data_film['id_film'];
			echo '<div class="fc-event';
				
			//sélection si c'est un court métrage	
			$sql_copie = "SELECT * FROM pm_copie WHERE id_film = '$data_film[id_film]' ORDER BY statut DESC";
			$result_copie = mysqli_query($connexion, $sql_copie) or die(mysqli_error());
			$data_copie = mysqli_fetch_array($result_copie);
			if($data_copie['duree']< '60') {
				echo ' court';
			}
		
			echo '" value="'.$id_film.'" cycle="'.$id_cycle.'"><a href="'.$_SERVER['PHP_SELF'].'?action=film&amp;start='.$start.'&amp;id_film='.$id_film.'&amp;id_bulletin='.$id_bulletin.'"><span class="numero">'.$data_film['numb'].'</span>';
			
			if($data_film['prefix_titre_o']) {
				if($data_film['prefix_titre_o']=="L'") {
					echo  "L'";
				} else {
					echo utf8_encode($data_film['prefix_titre_o']).' ';
				}
			}
			echo utf8_encode($data_film['titre_o']);
			echo '</a></div>';	
		}
		
		echo '<a href="'.$_SERVER['PHP_SELF'].'?action=cycle&amp;start='.$start.'&amp;id_cycle='.$id_cycle.'&amp;id_bulletin='.$id_bulletin.'">Afficher toutes les séances...</a>';
		echo '</div>';
		echo '</li>';
	$num++;
	}
?>
			    
					<span class="numero"></span>
			   
			</ul>
		</div>
	</div>
<?php
/*************************************************************************************
 * ************************* AFFICHAGE DU CALENDRIER *********************************
 * ***********************************************************************************/
?>
	<div id='calendar'></div>
<?php
/*************************************************************************************
 * ************************* FORMULAIRE ET PANIERS ***********************************
 * ***********************************************************************************/
?>
	<div id="col_right">
<?php
switch ($action) {
    case 'neutre':
	break;

    case 'form':
	switch ($envoi) {
	    case 'Delete':
		pmDelete();
		break;
	    case 'Envoyer':
		pmAddAllday();
		break;
	    case 'deleteAllDay':
		pmDeleteAllday();
		break;
	}
	break;
    
    case 'clic':
	switch ($envoi) {
	    case 'DeleteLinkMovieSeance':
		pmDeleteLinkMovieSeance();
		break;
	    case 'Effacer':
		pmEffacer();
		break;
	    case 'Enregistrer':
		pmEnregistrer();
		break;
	    case 'Dupliquer':
		pmDupliquer();
		break;
	    case 'En attente':
		pmSupprimer();
		break;
	}
?>

<h2>Modifier la séance</h2>

<?php
/************************************************************************************
 * Sélect sur la séance
 * Récupérer toutes les informations de cette séance pour pouvoir les éditer
 * - Tableau drag&drop des films
 * - Formulaire d'édition
 * - Boutons différents pour les actions *
 ***********************************************************************************/
$sql = "SELECT * FROM pm_seance WHERE id_seance='$_GET[id]'";
$result = mysqli_query($connexion, $sql) or die(mysqli_error());
$data_seance = mysqli_fetch_array($result);
    $id_seance = $data_seance['id_seance'];
    $titre = $data_seance['titre'];
    $start = $data_seance['start'];
    $end = $data_seance['end'];
    $id_salle = $data_seance['id_salle']; // requete à faire
    $commentaire = $data_seance['commentaire'];
    $event = $data_seance['event'];

// 1. récupération des films de la séance
$sql_film = "SELECT pm_film_seance.id_film, prefix_titre_o, titre_o, pm_film_seance.ordre, pm_film_seance.id_copie FROM pm_film_seance
	    JOIN pm_film
	    ON pm_film_seance.id_film = pm_film.id_film
	    WHERE pm_film_seance.id_seance='$id_seance'
	    ORDER BY pm_film_seance.ordre";
$result_film = mysqli_query($connexion, $sql_film) or die(mysqli_error());
?>
<table class="table" id="listeFilm">
    <thead>
        <tr>
	    <th>Ordre</th>
	    <th>titre</th>
<?php if($_COOKIE['role']=='e') echo '<th></th>'; ?>
	</tr>
    </thead >
    <tbody class=content>
<?php
$film_n1 = 1;
while ($data_film = mysqli_fetch_array($result_film)) {
	echo '<tr id="pos_'.$data_film['id_film'].'">';	
		echo '<td>'.$data_film['ordre'].'</td>';
		echo '<td>';
		//titre du film
		if($data_film['prefix_titre_o']) {
				if($data_film['prefix_titre_o']=="L'") {
					echo  "L'";
				} else {
					echo utf8_encode($data_film['prefix_titre_o']).' ';
				}
			}
			echo utf8_encode($data_film['titre_o']).'</td>';
			
			//bouton de suppression
			if($_COOKIE['role']=='e') {	
			echo '<td><form name="modif_seance" action="index.php?action=clic&amp;id='.$id.'&amp;id_bulletin='.$id_bulletin.'" method="POST" onclick="return confirm(\'voulez-vous vraiment supprimé cette séance?\')">
			    <input name="id_seance" type="hidden" value="'.$id.'">
			    <input name="id_film" type="hidden" value="'.$data_film['id_film'].'">
			    <input name="envoi" class="btn_deletelink" type="submit" value="DeleteLinkMovieSeance">
			</form></td>';
		}
	 echo '</tr>';

	// récupération des informations de la copie du 1ER FILM
	if($film_n1==1) {		
		$id_film_n1 = $data_film['id_film'];
		$id_copie_n1 = $data_film['id_copie'];
	}
	//incrémentation pour éviter de récupérer la version autres films
	$film_n1+=1;	
}
?>
    </tbody>	
</table>

<!-- 2. formulaire d'édition de la séance-->
<form name="modif_seance" action="<?php $_SERVER['PHP_SELF']; ?>?action=clic&amp;id_bulletin=<?php echo $id_bulletin; ?>&amp;start=<?php echo $start; ?>&amp;id=<?php echo $id; ?>" method="POST">
	<div>
	    <label for="titre"></label>
	    <input name="titre" type="text" value="<?php echo utf8_encode($titre); ?>" placeholder="Titre">
	</div>
	<div>
	    <label for="id_salle"></label>
	    <select name="id_salle">
<?php
//requète pour récupérer la liste des salles
$sql_salle = "SELECT * FROM pm_salle";
$result_salle = mysqli_query($connexion, $sql_salle) or die(mysqli_error());	
while ($data_salle = mysqli_fetch_array($result_salle)) {
	echo '<option value="'.$data_salle['id_salle'].'"';
	if($id_salle == $data_salle['id_salle']) echo ' selected="selected"';
	echo '>'.utf8_encode($data_salle['titre']).'</option>';	
}
?>
	    </select>
	</div>
	<div>
	    <label for="start"></label>
	    <input class="input21" name="start" type="text" value="<?php echo $start; ?>" placeholder="Date de début">
	    <label for="end"></label>
	    <input class="input22" name="end" type="text" value="<?php echo $end; ?>" placeholder="Date de fin">
	</div> 
<?php
/**
 *Informations de/s copie/s sélectionnée/s
 *Sélectionnée: statut = 1
 */
$sql_copie = "SELECT * FROM pm_copie WHERE id_film = '$id_film_n1' AND statut = '1'";
$result_copie = mysqli_query($connexion, $sql_copie) or die(mysqli_error());
if(mysqli_num_rows($result_copie) > 1) {
	
	echo '<div>
		<label for="id_copie">Copie</label>
		<select name="id_copie">';
		
	while($data_copie = mysqli_fetch_array($result_copie)) {
		echo '<option value="'.$data_copie['id_copie'].'"';
		if($id_copie_n1 == $data_copie['id_copie']) echo ' selected="selected"';
		echo '>'.utf8_encode($data_copie['version']);
		if ($data_copie['soustitre']) echo ' s-t '.utf8_encode($data_copie['soustitre']); 
		if ($data_copie['intertitre']) echo ' i-t '.utf8_encode($data_copie['intertitre']);
		//support
		echo ' / '.utf8_encode($data_copie['format']);
		echo '</option>';	
	}
		echo '</select>
	</div>';
} else {
	$data_copie = mysqli_fetch_array($result_copie);
	//version
	$version = utf8_encode($data_copie['version']);
	if ($data_copie['soustitre']) $version = $version.' s-t '.utf8_encode($data_copie['soustitre']); 
	if ($data_copie['intertitre']) $version = $version.' i-t '.utf8_encode($data_copie['intertitre']);
	//support
	$format = utf8_encode($data_copie['format']);
?>
	<div>
	    <label for="version"></label>
	    <input name="version" type="text" size="30" value="<?php echo $version; ?>" placeholder="Version" readonly>
	</div> 
	<div>
	    <label for="Format"></label>
	    <input name="Format" type="text" value="<?php echo $format; ?>" placeholder="Format" readonly>
	</div>  
<?php
}
?>
	<div style="margin-bottom:10px;">
	    <label for="event"></label>
	    <input name="event" type="hidden" value="0">
	    <input name="event" type="checkbox" <?php if($event==1) echo 'checked="checked" ';?> value="1" style="margin-top:-10px;">Event
	</div>  
	<div>
	    <?php if($_COOKIE['role']=='e') {
		echo '<label for="commentaire">Commentaire</label>';
		echo '<textarea name="commentaire">'.utf8_encode($commentaire).'</textarea>';
	    } else {
		echo '<label for="commentaire"></label>';
		echo '<input name="commentaire" type="text" value="'.substr(utf8_encode($commentaire),0,-4).'" placeholder="Commentaire">';
		}?>
	</div> 
	    <input name="id_seance" type="hidden" value="<?php echo $id_seance; ?>">
	<?php if($_COOKIE['role']=='e') { ?>
	    <input name="envoi" class="btn" type="submit" value="Effacer">
	    <input name="envoi" class="btn" type="submit" value="Enregistrer">
	    <input name="envoi" class="btn" type="submit" value="En attente">
	    <input name="envoi" class="btn" type="submit" value="Dupliquer">
	<?php } ?>
</form>
	
<hr class="clear">
	<?php break;
} ?>
<?php if($_COOKIE['role']=='e') { ?>
<!--LISTE DANS LE PANIER-->
			<table class="table">
			<thead>
			    <tr>
				<th>Dans le panier (dupliquer)</th>
				<th></th>
			    </tr>
			</thead>
			<tbody id="panier">
<?php
//requète sur les séances existantes pour récupérer un "id"
	$sql_pan = "SELECT pm_seance.id_seance, pm_seance.titre, titre_o, couleur FROM pm_seance
		   JOIN pm_film_seance
		   ON pm_seance.id_seance = pm_film_seance.id_seance
		   JOIN pm_film
		   ON pm_film_seance.id_film = pm_film.id_film
		   JOIN pm_cycle
		   ON pm_film_seance.id_cycle = pm_cycle.id_cycle
		   JOIN pm_bulletin_cycle
		   ON pm_film_seance.id_cycle = pm_bulletin_cycle.id_cycle
		   WHERE pm_bulletin_cycle.id_bulletin = '$id_bulletin'
		   AND pm_seance.id_seance < 10
		   GROUP BY pm_seance.id_seance";
	$result_pan = mysqli_query($connexion, $sql_pan) or die(mysqli_error());
	while ($data_pan = mysqli_fetch_array($result_pan)) {
	    
		if($data_pan['titre']) {
		    $titre_affiche = str_replace($caract, $new_caract, $data_pan["titre"]);
		    // titre de la séance
		    $titre = html_entity_decode(utf8_encode($titre_affiche));
		    
		} else {
		    // remplacement de l'apostrophe
		    $titre_affiche = str_replace($caract, $new_caract, $data_pan['titre_o']);
		    // titre de la séance
		    $titre = html_entity_decode(utf8_encode($titre_affiche));
		}
		echo '<tr>
			<td><div style="background-color:'.$data_pan['couleur'].'" class="fc-event" value="'.$data_pan['id_seance'].'">'.$titre.'&nbsp;</div></td>
			<td><form name="modif_seance" action="index.php?action=form&amp;id_bulletin='.$id_bulletin.'" method="POST" onclick="return confirm(\'voulez-vous vraiment supprimé cette séance?\')">
			    <input name="id_seance" type="hidden" value="'.$data_pan['id_seance'].'">
			    <input name="envoi" class="btn_delete" type="submit" value="Delete">
			    </form></td>
		      </tr>';
	}
?>
			</tbody>
			</table>
<hr class="clear">
<!--LISTE DANS LA CORBEILLE-->
			<table class="table">
			<thead>
			    <tr>
				<th>Séances en attente</th>
				<th></th>
			    </tr>
			</thead>
			<tbody id="corbeille">
<?php
//requète sur les séances existantes pour récupérer un "id"
	$sql_corb = "SELECT pm_seance.id_seance, pm_film_seance.ordre, pm_seance.titre, titre_o, couleur FROM pm_seance
		    JOIN pm_film_seance
		    ON pm_film_seance.id_seance = pm_seance.id_seance
		    JOIN pm_film
		    ON pm_film_seance.id_film = pm_film.id_film
		    JOIN pm_cycle
		    ON pm_film_seance.id_cycle = pm_cycle.id_cycle
		    JOIN pm_bulletin_cycle
		    ON pm_film_seance.id_cycle = pm_bulletin_cycle.id_cycle
		    WHERE pm_bulletin_cycle.id_bulletin = '$id_bulletin'
		    AND pm_seance.actif = 'i'
		    AND pm_film_seance.ordre = 1
		    GROUP BY pm_seance.titre";
	$result_corb = mysqli_query($connexion, $sql_corb) or die(mysqli_error());
	//boucle pour récupérer toute les donneés
	while ($data_corb = mysqli_fetch_array($result_corb)) {
	    
		if($data_corb['titre']) {
		    // remplacement de l'apostrophe
		    $titre_affiche = str_replace($caract, $new_caract, $data_corb["titre"]);
		    // titre de la séance
		    $titre = html_entity_decode(utf8_encode($titre_affiche));
		    
		} else {
		    // remplacement de l'apostrophe
		    $titre_affiche = str_replace($caract, $new_caract, $data_corb['titre_o']);
		    // titre de la séance
		    $titre = html_entity_decode(utf8_encode($titre_affiche));
		}
		echo '<tr>
			<td><div style="background-color:'.$data_corb['couleur'].'" class="fc-event" value="'.$data_corb['id_seance'].'">'.$titre.'&nbsp;</div></td>
			<td><form name="modif_seance" action="index.php?action=form&amp;id_bulletin='.$id_bulletin.'" method="POST" onclick="return confirm(\'voulez-vous vraiment supprimé cette séance?\')">
			    <input name="id_seance" type="hidden" value="'.$data_corb['id_seance'].'">
			    <input name="envoi" class="btn_delete" type="submit" value="Delete">
			    </form></td>
		      </tr>';
	}
?>
			</tbody>
			</table>
<hr class="clear">
				<h3>Ajout d'information</h3>
			<form name="add_allDay" action="index.php?action=form&amp;id_bulletin=<?php echo $id_bulletin; ?>" method="POST">
				<div>
				    <label for="titre"></label>
				    <input name="titre" type="text" placeholder="Titre">
				</div>
				<div>
				    <label for="start"></label>
				    <input class="input21" name="start" type="text" placeholder="2015-01-01">
				    <label for="end"></label>
				    <input class="input22" name="end" type="text" placeholder="2015-01-01">
				</div>  
				<div>
				    <label class="input21" for="categorie">Catégorie</label>
				    <select class="input21" name="categorie">
					<option value="0">Vacances / festival</option>
					<option value="1">Indisponibilité</option>
				    </select>
				</div>
				<div>
				    <label class="input21" for="id_salle">Salles</label>
				    <select class="input21"  name="id_salle">
					<option>--</option>
<?php
//requète pour récupérer la liste des salles
$sql_salle = "SELECT * FROM pm_salle";
$result_salle = mysqli_query($connexion, $sql_salle) or die(mysqli_error());

//boucle pour récupérer toute les donneés	
while ($data_salle = mysqli_fetch_array($result_salle)) {
	echo '<option value="'.$data_salle['id_salle'].'"';
	echo '>'.utf8_encode($data_salle['titre']).'</option>';	
}
?>
				    </select>
				</div>
				<input name="envoi" class="btn" type="submit" value="Envoyer">
			</form>

	<table class="table">
	    <thead>
		<tr>
		    <th>Evénements</th>
		    <th></th>
		</tr>
	    </thead>
	    <tbody>
			
<?php
//requête qui récupère les événements
$sql_allday = "SELECT * FROM pm_allday
		WHERE start BETWEEN '$start_bulletin' AND '$end_bulletin'
		OR end BETWEEN '$start_bulletin' AND '$end_bulletin'
		AND actif = 'a'
		ORDER BY start";
$result_allday = mysqli_query($connexion, $sql_allday) or die(mysqli_error());

$tr_n = 1;
while ($data_allday = mysqli_fetch_array($result_allday)) {
		echo '<tr>'; // pour l'altérnance des couleurs
		echo '<td>';
		echo utf8_encode($data_allday['titre']).'</td>'; //renvoi l'id du cycle en "GET"
		
		echo '<td><nobr>';
			echo '<form name="suppr_news" action="index.php?action=form&amp;id_bulletin='.$id_bulletin.'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette inscription?\')">
				<input type="hidden" name="id" value="'.$data_allday['id'].'">
				<input name="envoi" class="btn_suppr" type="submit" value="deleteAllDay">
			     </form>';
		echo '</nobr></td>';
			    
		echo '</tr>'; //fermeture de la ligne
	    }
?>
	    </tbody>
	</table>


<?php } //fin de la condition d'éditeur
?>
		</div>

		<div style='clear:both'></div>
		<div>
<?php
//$all_my_vars = get_defined_vars();
//var_dump($all_my_vars);
?>
</div>
	</div>
	
	
<!--	Pour le menu déroulant des films-->
<script>
	
	var idMenu = '<?php echo $_SESSION['idMenu']; ?>' ; //récupération de idMenu en session	
	var idSousMenu = 'sous' + idMenu;
	var sousMenu   = document.getElementById(idSousMenu);
	
	/*****************************************************/
	/**	on cache tous les sous-menus pour n'afficher    **/
	/** que celui dont le menu correspondant est cliqué **/
	/** où 4 correspond au nombre de sous-menus         **/
	/*****************************************************/
	for(var i = 1; i <= 30; i++){
		if(document.getElementById('sousmenu' + i) && document.getElementById('sousmenu' + i) != sousMenu){
			document.getElementById('sousmenu' + i).style.display = "none";
		}
	}
	
	if(sousMenu){
		//alert(sousMenu.style.display);
		if(sousMenu.style.display == "block"){
			sousMenu.style.display = "none";
		}
		else{
			sousMenu.style.display = "block";
		}
	}	//code
</script>
