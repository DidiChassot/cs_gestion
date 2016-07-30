<?php
/*** Fonction programmation***
 *
 * Cindy Chassot 04.02.2015 - 13.04.2015
 * © Cinémathèque suisse
 */

/***Correction des variables***/
function clean($var) {
	//pour l'apostrophe
	$text = array("'");
	$result = array("''");
	$var = str_replace($text, $result, $var);
	//effacement d'espaces et retours maladroit
	$cleanVar = utf8_decode(trim($var));
	
	return $cleanVar;
}

/***Remplacement des caractères pour textarea***/
function cleanTextarea($var) {
	//remplacer les balises
	$text = array('<p>', '</p>', '<em>', '</em>', '<strong>', '</strong>', '<br>', '’', '€');
	$result = array('', '<br>', '<i>', '</i>', '<b>', '</b>', '', '\'', '&euro;');
	$cleanVar = str_replace($text, $result, $var);
	//lancement de clean()
	$cleanVar = clean($cleanVar);
	//suppression <br> de fin
	//$cleanVar = substr($cleanVar, 0, -4);

	return $cleanVar;
}

/***Correction de l'affichage de caractères depuis typo3***/
function afficheHtml($var) {
	
	$text = array(utf8_decode(''), '<i>', '</i>', '<b>', '</b>', '<br>', '<br />', '');
	$result = array(utf8_encode('&#339;'), '', '', '', '', '
', '
', '');
	$cleanVar = str_replace($text, $result, $var);
	
	return $cleanVar;

}

/** BULLETIN
 * 
 * saveBulletin()
 * deleteBulletin()
 * deleteLinkBulletinCycle()
 */

/* ****************************** INSERTION-MODIFICATION BULLETIN****************************** */
function saveBulletin() {
	include('pm_connexion.php');
	$numero = clean($_POST['numero']);
	$titre = clean($_POST['titre']);
	$start = clean($_POST['start']);
	$end = clean($_POST['end']);
	$commentaire = cleanTextarea($_POST['commentaire']);
	$id_bulletin = clean($_POST['id_bulletin']);
	$vacances = cleanTextarea($_POST['vacances']);
	$indisponibilite = cleanTextarea($_POST['indisponibilite']);
	
	//
	if ($id_bulletin > 1) {
		$sql = "UPDATE pm_bulletin SET numero='$numero', titre='$titre', start='$start', end='$end', commentaire='$commentaire', vacances='$vacances', indisponibilite='$indisponibilite' WHERE id_bulletin='$id_bulletin'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else {
		$sql = "INSERT INTO pm_bulletin (numero, titre, start, end, commentaire, vacances, indisponibilite)
			VALUES ('$numero', '$titre', '$start', '$end', '$commentaire', '$vacances', '$indisponibilite')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
		//ajout des séances uniquement à la création du bulletin.
		addSeance($start, $end);
	}
}
/* **************************** SUPPRIMER BULLETIN**************************** */
function deleteBulletin() {
	include('pm_connexion.php');
	$sql = "DELETE FROM pm_bulletin WHERE id_bulletin ='$_POST[id_bulletin]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* **************************** SUPPRIMER LIEN ENTRE BULLETIN ET CYCLE**************************** */
function deleteLinkBulletinCycle() {
	include('pm_connexion.php');
	$sql = "DELETE FROM pm_bulletin_cycle WHERE id_bulletin ='$_POST[id_bulletin]' AND id_cycle ='$_POST[id_cycle]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	$sql1 = "UPDATE pm_cycle SET bulletin='0' WHERE id_cycle ='$_POST[id_cycle]'";
	$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
	
}

/** CYCLE
 * 
 * saveCycle()
 * saveCycleCartouche($id_cycle)
 * deleteCycle()
 * deleteFinalCycle()
 * dobbleCycle()
 */
/* ****************************** INSERTION-MODIFICATION CYCLE****************************** */
function saveCycle() {
	include('pm_connexion.php');
	$titre = cleanTextarea($_POST['titre']);
	$titre_simple = clean($_POST['titre_simple']);
	$date = clean($_POST['date']);
	$commentaire = cleanTextarea($_POST['commentaire']);
	$id_categorie = clean($_POST['id_categorie']);
	$couleur = clean($_POST['couleur']);
	$bulletin = clean($_POST['bulletin']);
	$id_cycle = clean($_POST['id_cycle']);
	$id_bulletin = clean($_POST['id_bulletin']);
	if($_POST['numero']) $bulletin = clean($_POST['numero']);
	// Récupération id_bulletin
	if($bulletin > 1) { // si l'envoi du numéro de bulletin depui _sm0
		// recherche du id_bulletin dont numero = $bulletin
		$sql0 = "SELECT id_bulletin FROM pm_bulletin WHERE numero = '$bulletin'"; 
		$result0 = mysqli_query($connexion, $sql0 ) or die(mysqli_error());
		$data0 = mysqli_fetch_array($result0);
		if($data0['id_bulletin']) {
			$id_bulletin = $data0['id_bulletin']; // récupération de id_bulletin
		} else {
			$bulletin = 0;
		}
	}
	if($id_cycle > 1 && $_POST['id_bulletin'] == FALSE) {
		$sql = "UPDATE pm_cycle SET titre='$titre', commentaire='$commentaire', bulletin='$bulletin', actif='a' WHERE id_cycle='$id_cycle'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
	} elseif ($id_cycle > 1) { // si c'est un update
		$sql = "UPDATE pm_cycle SET titre='$titre', titre_simple='$titre_simple', date='$date', commentaire='$commentaire', couleur='$couleur', bulletin='$bulletin', actif='a' WHERE id_cycle='$id_cycle'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else { // autrement insert
		$sql = "INSERT INTO pm_cycle (titre, titre_simple, date, commentaire, couleur, bulletin)
			VALUES ('$titre', '$titre_simple', '$date', '$commentaire', '$couleur', '$bulletin')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
		//recherche du id_cycle fraichement créé sert pour la 2ème partie
		$sqlCycle = "SELECT id_cycle FROM pm_cycle WHERE titre ='$titre' ORDER BY id_cycle DESC";
		$resultCycle = mysqli_query($connexion, $sqlCycle ) or die(mysqli_error());
		$dataCycle = mysqli_fetch_array($resultCycle);
		$id_cycle = $dataCycle['id_cycle']; // HEY!
		
		// création du cartouche
		$sql1 = "INSERT INTO pm_cartouche (id_cycle)
			VALUES ('$id_cycle')";
		$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
	}
	// insertion du lien dans pm_bulletin_cycle
	if ($id_bulletin > 1) { //si un Link à un bulletin
		//recherche d'un lien déjà existant
		$sqlBulletinCycle = "SELECT id FROM pm_bulletin_cycle WHERE id_cycle ='$id_cycle' AND id_bulletin ='$id_bulletin'";
		$resultBulletinCycle = mysqli_query($connexion, $sqlBulletinCycle ) or die(mysqli_error());
		$dataBulletinCycle = mysqli_fetch_array($resultBulletinCycle);
		if(!$dataBulletinCycle['id']) {
			
			//requète sur pm_film_seance pour récupérer l'ordre
			$sql_search = "SELECT * FROM pm_bulletin_cycle WHERE id_bulletin='$id_bulletin' ORDER BY ordre DESC";
			$result_search = mysqli_query($connexion, $sql_search) or die(mysqli_error());
			//récupération des donneés	
			if($data_search = mysqli_fetch_array($result_search)) {
			    $ordre = $data_search['ordre'];
			    $ordre = $ordre + 1;
			} else $ordre = 1;
			
			$sql1 = "INSERT INTO pm_bulletin_cycle (id_bulletin, id_cycle, ordre)
				VALUES ('$id_bulletin', '$id_cycle', '$ordre')";
			$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
		} // HEY!
		
	}
	// modification de la catégorie du cycle
	if ($id_categorie > 0) { 
		$sql = "UPDATE pm_bulletin_cycle SET id_categorie='$id_categorie' WHERE id_cycle='$id_cycle' AND id_bulletin = '$id_bulletin'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	}
	
	//refresh de la page
	//echo '<script language="Javascript">document.location.replace("'.$_SERVER['SCRIPT_NAME']."?".$_SERVER['QUERY_STRING'].'"); </script>';
}
/* **************************** INSERTION-MODIFICATION CYCLE / CARTOUCHE**************************** */
function saveCycleCartouche() {
	include('pm_connexion.php');
	$id_cycle = clean($_POST['id_cycle']);
	$id_catouche = clean($_POST['id_cartouche']);
	// pour l'édition du cycle et du cartouche
	$edition = clean($_POST['edition']);
	
	/* "Lock" des lignes dans la base de données */
	if($edition) {
		//mise à jour du cartouche
		$sql = "UPDATE pm_cartouche SET edition='$edition', lockdate=NOW() WHERE id='$id_cartouche' AND id_cycle='$id_cycle' AND edition IS NULL";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		//mise à jour du cycle
		$sql = "UPDATE pm_cycle SET edition='$edition', lockdate=NOW() WHERE id_cycle='$id_cycle' AND edition IS NULL";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
	/* Mise à jour des données */
	} else {
		//INITIALISATION DES VARIABLES
		$date = cleanTextarea($_POST['date']);
		$commentaire = cleanTextarea($_POST['commentaire']);
		
		//pm_cartouche
		$intro = clean($_POST['intro']);
		$notule = clean($_POST['notule']);
		$presence = clean($_POST['presence']);
		$photo = clean($_POST['photo']);
		$logo = cleanTextarea($_POST['logo']);
		$bat = cleanTextarea($_POST['bat']);
		//$info = cleanTextarea($_POST['info']);
		
		//lancement du trigger de log
		triggerCycleCartouche();
		//mise à jour du cartouche
		$sql = "UPDATE pm_cartouche SET intro='$intro', notule='$notule', presence='$presence', photo='$photo', logo='$logo', bat='$bat', edition=NULL, lockdate=NULL WHERE id='$id_cartouche' AND id_cycle='$id_cycle'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());		
		//mise à jour des informations du cycle
		$sql2 = "UPDATE pm_cycle SET date='$date', commentaire ='$commentaire', edition=NULL, lockdate=NULL WHERE id_cycle='$id_cycle'";
		$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
	}
	
	/* "Unlock" des lignes */
	deleteLockdate('pm_cycle');
	deleteLockdate('pm_cartouche');
}

/* **************************** SUPPRIMER CYCLE statut a > i**************************** */
function deleteCycle() {
	include('pm_connexion.php');
	//mise à jour du cycle
	$sql = "UPDATE pm_cycle SET actif='i', bulletin='0' WHERE id_cycle='$_POST[id_cycle]'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	//supprimer le link avec le bulletin
	if($_POST['id_bulletin']) {
		$sql1 = "DELETE FROM pm_bulletin_cycle WHERE id_bulletin ='$_POST[id_bulletin]' AND id_cycle ='$_POST[id_cycle]'";	
		$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
	}
}

/* **************************** SUPPRIMER CYCLE**************************** */
function deleteFinalCycle() {
	include('pm_connexion.php');
	//suppression final du cycle
	$sql = "DELETE FROM pm_cycle WHERE id_cycle='$_POST[id_cycle]'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	//suppression final du cartouche
	$sql = "DELETE FROM pm_cartouche WHERE id_cycle='$_POST[id_cycle]'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	//suppression du lien avec les films
	$sql = "DELETE FROM pm_cycle_film WHERE id_cycle='$_POST[id_cycle]'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
}

/* **************************** DUPPLIQUER CYCLE**************************** */
function dobbleCycle() {
	include('pm_connexion.php');
	$id_cycle = clean($_POST['id_cycle']);
	
	//1. recherche les infos du cycle pour les duppliquer
	$sqlCycle = "SELECT titre, titre_simple, date, commentaire, couleur, intro, notule, presence, photo, logo, bat, info 
	FROM pm_cycle
	JOIN pm_cartouche
	ON pm_cycle.id_cycle = pm_cartouche.id_cycle
	WHERE pm_cycle.id_cycle ='$id_cycle'";
	$resultCycle = mysqli_query($connexion, $sqlCycle ) or die(mysqli_error());
	$dataCycle = mysqli_fetch_array($resultCycle);
	//récupération des données
	foreach($dataCycle as $cle => $element) {
		$$cle = clean(utf8_encode($element));
	}
	$titre = 'copie - '.$titre; 
	
	//2. insertion d'une nouvelle entrée
	$sql = "INSERT INTO pm_cycle (titre, titre_simple, date, commentaire, couleur)
		VALUES ('$titre', '$titre_simple', '$date', '$commentaire', '$couleur')";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	//3. récupération de cet id pour le nouveau cartouche
	$sqlIdCycle = "SELECT id_cycle FROM pm_cycle WHERE titre ='$titre' ORDER BY id_cycle DESC";
	$resultIdCycle = mysqli_query($connexion, $sqlIdCycle ) or die(mysqli_error());
	$dataIdCycle = mysqli_fetch_array($resultIdCycle);
	//récupération des données
	$newId_cycle = $dataIdCycle['id_cycle'];
	
	//4. récupération des informations 
	//ajouter le cartouche
	$sql2 = "INSERT INTO pm_cartouche (id_cycle, intro, notule, presence, photo, logo, bat, info)
		VALUES ('$newId_cycle', '$intro', '$notule', '$presence', '$photo', '$logo', '$bat', '$info')";
	$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
	
	//Récupération des films liés
	$sql_film = "SELECT id_film FROM pm_cycle_film WHERE id_cycle = '$id_cycle'";
	$result_film = mysqli_query($connexion, $sql_film) or die(mysqli_error());
	//boucle pour récupérer toute les donneés	
	while ($data_film = mysqli_fetch_array($result_film)) {
		$id_film = $data_film['id_film'];
		//insertion d'une nouvelle entrée
		$sql = "INSERT INTO pm_cycle_film (id_cycle, id_film)
			VALUES ('$newId_cycle', '$id_film')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
	
}

/** FILM
 * 
 * saveFilm()
 * linkMultiCycleFilm()
 * linkMultiNewFilm()
 * deleteLinkCycleFilm()
 * deselectFilm()
 * selectFim()
 * deleteFilm()
 * synchroMovie()
 */

/* ****************************** INSERTION-MODIFICATION FILM****************************** */
function saveFilm() {
	include('pm_connexion.php');
	$prefix_titre_o = clean($_POST['prefix_titre_o']);
	$titre_o = clean($_POST['titre_o']);
	$prefix_titre_fr = clean($_POST['prefix_titre_fr']);
	$titre_fr = clean($_POST['titre_fr']);
	$annee_prod = clean($_POST['annee_prod']);
	$pays_prod = clean($_POST['pays_prod']);
	$prefix_realisateur = clean($_POST['prefix_realisateur']);
	$realisateur = clean($_POST['realisateur']);
	$prefix_acteur = clean($_POST['prefix_acteur']);
	$acteur = clean($_POST['acteur']);
	$age_legal = clean($_POST['age_legal']);
	$age_sugg = clean($_POST['age_sugg']);
	if($_POST['film_famille']) {$film_famille = 1;} else $film_famille = 0;
	if($_POST['distri']) {$distri = clean($_POST['distri']);} else $distri = 0;
	$id_imdb = clean($_POST['id_imdb']);
	$ayants_droits = cleanTextarea($_POST['ayants_droits']);
	$remarque = cleanTextarea($_POST['remarque']);
	$filemaker = cleanTextarea($_POST['filemaker']);
	$id_film = clean($_POST['id_film']);
	$id_cycle = clean($_POST['id_cycle']);
	//
	if ($id_film > 1) {
		$sqlLink = "SELECT * FROM pm_cycle_film WHERE id_film ='$id_film' AND id_cycle ='$id_cycle'"; // nom de la table ! requette
		$resultLink = mysqli_query($connexion, $sqlLink ) or die(mysqli_error());
		if(mysqli_num_rows($resultLink)) { //si le film est déjà lié au cycle
			$sql1 = "UPDATE pm_film SET prefix_titre_o='$prefix_titre_o', titre_o='$titre_o', prefix_titre_fr='$prefix_titre_fr', titre_fr='$titre_fr', annee_prod='$annee_prod', pays_prod='$pays_prod', prefix_film_director='$prefix_realisateur', realisateur='$realisateur', prefix_film_actor='$prefix_acteur', acteur='$acteur', age_legal='$age_legal', age_sugg='$age_sugg', film_famille='$film_famille', distri='$distri', id_imdb='$id_imdb', ayants_droits='$ayants_droits', remarque='$remarque', filemaker='$filemaker' WHERE id_film='$id_film'";
			$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
			//echo 'update1 fait';
			
		} else {
			$sql1 = "UPDATE pm_film SET prefix_titre_o='$prefix_titre_o', titre_o='$titre_o', prefix_titre_fr='$prefix_titre_fr', titre_fr='$titre_fr', annee_prod='$annee_prod', pays_prod='$pays_prod', prefix_film_director='$prefix_realisateur', realisateur='$realisateur', prefix_film_actor='$prefix_acteur', acteur='$acteur', age_legal='$age_legal', age_sugg='$age_sugg', film_famille='$film_famille', distri='$distri', id_imdb='$id_imdb', ayants_droits='$ayants_droits', remarque='$remarque', filemaker='$filemaker' WHERE id_film='$id_film'";
			$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
			//echo 'update2 fait';
		
			$sql2 = "INSERT INTO pm_cycle_film (id_cycle, id_film)
				VALUES ('$id_cycle', '$id_film')";
			$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
			//echo 'insert2 fait';
		}
		
	} else {
		$sql1 = "INSERT INTO pm_film (prefix_titre_o, titre_o, prefix_titre_fr, titre_fr, annee_prod, pays_prod, prefix_film_director, realisateur, prefix_film_actor, acteur, age_legal, age_sugg, film_famille, distri, id_imdb, ayants_droits, remarque, filemaker)
			VALUES ('$prefix_titre_o', '$titre_o', '$prefix_titre_fr', '$titre_fr', '$annee_prod', '$pays_prod', '$prefix_realisateur', '$realisateur', '$prefix_acteur', '$acteur', '$age_legal', '$age_sugg', '$film_famille', '$distri', '$id_imdb', '$ayants_droits', '$remarque', '$filemaker')";
		$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
		
		if($id_cycle > 1) {
			$sqlFilm = "SELECT id_film FROM pm_film WHERE titre_o ='$titre_o' AND annee_prod ='$annee_prod' AND realisateur ='$realisateur'"; // nom de la table ! requette
			$resultFilm = mysqli_query($connexion, $sqlFilm ) or die(mysqli_error());
			$dataFilm = mysqli_fetch_array($resultFilm);
			$id_film2 = $dataFilm['id_film'];
			if($id_film2 > 1) {
				$sql2 = "INSERT INTO pm_cycle_film (id_cycle, id_film)
					VALUES ('$id_cycle', '$id_film2')";
				$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
				//echo 'insert '.$id_film2.' fait';
			}
		}
	}
	
}
/* **************************** SUPPRIMER Lien entre CYCLE et FILM**************************** */
function linkMultiCycleFilm() {
	include('pm_connexion.php');
	$id_cycle = clean($_POST['id_cycle']);
	
	foreach ($link as $id_film) {
		$sql = "INSERT INTO pm_cycle_film (id_cycle, id_film)
			VALUES ('$id_cycle', '$id_film')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		//echo 'Liens faits avec '.$id_film.'<br>';
	}	
}
/* **************************** SUPPRIMER Lien entre CYCLE et FILM**************************** */
function linkMultiNewFilm() {
	include('pm_connexion.php');
	$id_cycle = clean($_POST['id_cycle']);
	
	//ajout de lien entre le film qui existe et le cycle
	if($link) {
		foreach ($link as $id_film) {
			$sql = "INSERT INTO pm_cycle_film (id_cycle, id_film)
				VALUES ('$id_cycle', '$id_film')";
			$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		}		
	}
	
	//création d'une nouvelle fiche et connection avec le cycle
	if($newFilm) {
		
		//Exécution d'une requête multiple: création de la table provisoire + REMPLISSAGE DE LA TABLE
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
		
		//boucle sur les films checkés
		foreach ($newFilm as $id_film) {
			$sql = "SELECT titre_o, annee_prod, realisateur FROM pm_film_add WHERE id_film='$id_film'";
			$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			if(mysqli_num_rows($result)) { //si le film est déjà lié au cycle
				$data = mysqli_fetch_array($result);
				$titre = clean($data['titre_o']);
				$real = clean($data['realisateur']);
				
				//ajout de la fiche dans pm_film
				$sqlAdd = "INSERT INTO pm_film (titre_o, annee_prod, realisateur)VALUES ('".$titre."', '".$data['annee_prod']."', '".$real."')";
				$resultAdd = mysqli_query($connexion, $sqlAdd) or die(mysqli_error());
				
				//sélection de l'id nouvellement crée -> link au cycle
				if($id_cycle > 1) {
					$sqlFilm = "SELECT id_film FROM pm_film WHERE titre_o ='".$titre."' AND annee_prod ='".$data['annee_prod']."' AND realisateur ='".$real."'"; // nom de la table ! requette
					$resultFilm = mysqli_query($connexion, $sqlFilm ) or die(mysqli_error());
					$dataFilm = mysqli_fetch_array($resultFilm);
					$id_film2 = $dataFilm['id_film'];
					if($id_film2 > 1) {
						$sql2 = "INSERT INTO pm_cycle_film (id_cycle, id_film)
							VALUES ('$id_cycle', '$id_film2')";
						$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
					}
				}
				
			}
		}
	}
	
}
/* **************************** SYNCHRONISATION MULTIPLE ORACLE**************************** */
function linkSynchroMultiFilm() {
	include('pm_connexion.php');
	$id_cycle = clean($_POST['id_cycle']);
	$baseselect = clean($_POST['baseselect']);
	
	//base des variables
	$prefix_titre_o = '';
	$titre_o = '';
	$prefix_titre_fr = '';
	$titre_fr = '';
	$annee_prod = '';
	$pays_prod = '';
	$realisateur = '';
	
	//1. récupération de tous les id d'oracle
	foreach ($link as $id_film) {
		
		//test si ce film n'est pas encore synchronisé
		$sqlTestSynchro = "SELECT id FROM pm_film_film WHERE id_foreign = '$id_film' AND base = '$baseselect'"; // nom de la table ! requette
		$resultTestSynchro = mysqli_query($connexion, $sqlTestSynchro ) or die(mysqli_error());
		$dataTestSynchro = mysqli_fetch_array($resultTestSynchro);
		if(empty($dataTestSynchro['id']) ) {
			//1.2 récupérer toutes les informations du film
			$sqlInfoFilm = "SELECT * FROM or_film WHERE id_film = '$id_film'"; // nom de la table ! requette
			$resultInfoFilm = mysqli_query($connexion, $sqlInfoFilm ) or die(mysqli_error());
			$dataInfoFilm = mysqli_fetch_array($resultInfoFilm);
			//récupération des données
			foreach($dataInfoFilm as $cle => $element) {
				$$cle = $element;
			}
			
			//2. insertion des données dans la table pm_film
			$sql = "INSERT INTO pm_film (prefix_titre_o, titre_o, prefix_titre_fr, titre_fr, annee_prod, pays_prod, realisateur)
				VALUES ('$prefix_titre_o', '$titre_o', '$prefix_titre_fr', '$titre_fr', '$annee_prod', '$pays_prod', '$realisateur')";
			$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			//récupération de l'id 
			$sqlFilm = "SELECT id_film FROM pm_film WHERE titre_o ='$titre_o' AND annee_prod ='$annee_prod' AND realisateur ='$realisateur' ORDER BY id_film DESC"; // nom de la table ! requette
			$resultFilm = mysqli_query($connexion, $sqlFilm ) or die(mysqli_error());
			$dataFilm = mysqli_fetch_array($resultFilm);
			$id_newFilm = $dataFilm['id_film'];
			
			//3. Synchronisation Oracle / Pm
			$sqlInsertSynchro = "INSERT INTO pm_film_film (id_film, id_foreign, base)
					     VALUES ('$id_newFilm', '$id_film', '$baseselect')";
			$resultInsertSynchro = mysqli_query($connexion, $sqlInsertSynchro) or die(mysqli_error());
			
			//3.2 Synchronisation des copies si la base est Oracle
			if($baseselect == '1') {
				$sqlListCopie = "SELECT * FROM or_copie WHERE id_film = $id_film";
				$resultListCopie = mysqli_query($connexion, $sqlListCopie ) or die(mysqli_error());
				while ($dataListCopie = mysqli_fetch_array($resultListCopie)) {
					
					foreach($dataListCopie as $cle => $element) {
						$$cle = $element;
					}
					if(!isset($dataSynchroCopie['id_copie'])) {
						$sqlInsertCopie = "INSERT INTO pm_copie (id_film, provenance, duree, format, version, soustitre, intertitre, id_foreign, cote)
								   VALUES ('$id_newFilm', 'CS', '$duree', '$format', '$version', '$soustitre', '$intertitre', '$id_copie', '$cote')";
						$resultInsertCopie = mysqli_query($connexion, $sqlInsertCopie) or die(mysqli_error());
					}
				}
			}
			
			//4. lien du NOUVEAU film au cycle
			if($id_cycle>1) {
				$sql2 = "INSERT INTO pm_cycle_film (id_cycle, id_film)
					VALUES ('$id_cycle', '$id_newFilm')";
				$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
			}
			
			
		}
		
	}
}
/* **************************** SUPPRIMER Lien entre CYCLE et FILM**************************** */
function deleteLinkCycleFilm() {
	include('pm_connexion.php');
	$sql = "DELETE FROM pm_cycle_film WHERE id_film ='$_POST[id_film]' AND id_cycle ='$_POST[id_cycle]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* **************************** DESELECTIONNER FILM**************************** */
function deselectFilm() {
	include('pm_connexion.php');
	$sql = "UPDATE pm_cycle_film SET actif='i' WHERE id_film ='$_POST[id_film]' AND id_cycle ='$_POST[id_cycle]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* **************************** reSELECTIONNER FILM**************************** */
function selectFilm() {
	include('pm_connexion.php');
	$sql = "UPDATE pm_cycle_film SET actif='a' WHERE id_film ='$_POST[id_film]' AND id_cycle ='$_POST[id_cycle]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* **************************** SUPPRIMER FILM**************************** */
function deleteFilm() {
	include('pm_connexion.php');
	//suppression dans pm_film
	$sql = "DELETE FROM pm_film WHERE id_film ='$_POST[id_film]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	//suppression dans pm_copie
	$sql = "DELETE FROM pm_cartouche WHERE id_film ='$_POST[id_film]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	//suppression des liens avec des cycles
	$sql = "DELETE FROM pm_cycle_film WHERE id_film ='$_POST[id_film]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* **************************** SYNCHRONISATION FILM**************************** */
function synchroMovie() {
	include('pm_connexion.php');
	$id_search = clean($_POST['id_search']);
	$baseselect = clean($_POST['baseselect']);
	$id_cycle = clean($_POST['id_cycle']);
	
	if($_POST['id_film']>0) {$id_film = clean($_POST['id_film']);}
	
	//base des variables
	$prefix_titre_o = '';
	$titre_o = '';
	$prefix_titre_fr = '';
	$titre_fr = '';
	$annee_prod = '';
	$pays_prod = '';
	$prefix_realisateur = '';
	$realisateur = '';
	$prefix_acteur = '';
	$acteur = '';
	$age_legal = '';
	$age_sugg = '';
	
	
	//insertion du film dans la base PM si aucun id
	if($id_film) {
		//update
		$sqlFilm = "SELECT * FROM pm_film WHERE id_film = '$id_film'"; // nom de la table ! requette
		$resultFilm = mysqli_query($connexion, $sqlFilm ) or die(mysqli_error());
		$dataFilm = mysqli_fetch_array($resultFilm);
		//
		
		//check pour éviter les remplacement de données malencontreuse
		
		$sqlTestSynchro = "SELECT id_foreign FROM pm_film_film WHERE id_film = '$id_film' AND base = '$baseselect'"; // nom de la table ! requette
		$resultTestSynchro = mysqli_query($connexion, $sqlTestSynchro ) or die(mysqli_error());
		$dataTestSynchro = mysqli_fetch_array($resultTestSynchro);
		if(empty($dataTestSynchro['id_foreign']) || $dataTestSynchro['id_foreign'] == $id_search ) {
			//
			if($_POST['s_prefix_titre_o']) {$prefix_titre_o = clean($_POST['prefix_titre_o']);} else {$prefix_titre_o = clean($dataFilm['prefix_titre_o']);}
			if($_POST['s_titre_o']) {$titre_o = clean($_POST['titre_o']);} else {$titre_o = clean($dataFilm['titre_o']);}
			if($_POST['s_prefix_titre_fr']) {$prefix_titre_fr = clean($_POST['prefix_titre_fr']);} else {$prefix_titre_fr = clean($dataFilm['prefix_titre_fr']);}
			if($_POST['s_titre_fr']) {$titre_fr = clean($_POST['titre_fr']);} else {$titre_fr = clean($dataFilm['titre_fr']);}
			if($_POST['s_annee_prod']) {$annee_prod = clean($_POST['annee_prod']);} else {$annee_prod = $dataFilm['annee_prod'];}
			if($_POST['s_pays_prod']) {$pays_prod = clean($_POST['pays_prod']);} else {clean($pays_prod = $dataFilm['pays_prod']);}
			if($_POST['s_prefix_realisateur']) {$prefix_realisateur = clean($_POST['prefix_realisateur']);} else {$prefix_realisateur = clean($dataFilm['prefix_realisateur']);}
			if($_POST['s_realisateur']) {$realisateur = clean($_POST['realisateur']);} else {$realisateur = clean($dataFilm['realisateur']);}
			if($_POST['s_prefix_acteur']) {$prefix_acteur = clean($_POST['prefix_acteur']);} else {$prefix_acteur = clean($dataFilm['prefix_acteur']);}
			if($_POST['s_acteur']) {$acteur = clean($_POST['acteur']);} else {$acteur = clean($dataFilm['acteur']);}
			if($_POST['s_age_legal']) {$age_legal = clean($_POST['age_legal']);} else {$age_legal = $dataFilm['age_legal'];}
			if($_POST['s_age_sugg']) {$age_sugg = clean($_POST['age_sugg']);} else {$age_sugg = $dataFilm['age_sugg'];}
			if($_POST['s_film_famille']) {$film_famille = 1;} else {$film_famille = $dataFilm['film_famille'];}
			//
			$sql = "UPDATE pm_film SET prefix_titre_o='$prefix_titre_o', titre_o='$titre_o', prefix_titre_fr='$prefix_titre_fr', titre_fr='$titre_fr', annee_prod='$annee_prod', pays_prod='$pays_prod', prefix_film_director='$prefix_realisateur', realisateur='$realisateur', prefix_film_actor='$prefix_acteur', acteur='$acteur', age_legal='$age_legal', age_sugg='$age_sugg', film_famille='$film_famille' WHERE id_film='$id_film'";
			$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		}
		
	} else {
		$sqlTestSynchro = "SELECT id FROM pm_film_film WHERE id_foreign = '$id_search' AND base = '$baseselect'"; // nom de la table ! requette
		$resultTestSynchro = mysqli_query($connexion, $sqlTestSynchro ) or die(mysqli_error());
		$dataTestSynchro = mysqli_fetch_array($resultTestSynchro);
		if(empty($dataTestSynchro['id']) ) {
			$prefix_titre_o = clean($_POST['prefix_titre_o']);
			$titre_o = clean($_POST['titre_o']);
			$prefix_titre_fr = clean($_POST['prefix_titre_fr']);
			$titre_fr = clean($_POST['titre_fr']);
			$annee_prod = clean($_POST['annee_prod']);
			$pays_prod = clean($_POST['pays_prod']);
			$prefix_realisateur = clean($_POST['prefix_realisateur']);
			$realisateur = clean($_POST['realisateur']);
			$prefix_acteur = clean($_POST['prefix_acteur']);
			$acteur = clean($_POST['acteur']);
			$age_legal = clean($_POST['age_legal']);
			$age_sugg = clean($_POST['age_sugg']);
			if($film_famille == 'x') {$film_famille = 1;}
			
			$sql = "INSERT INTO pm_film (prefix_titre_o, titre_o, prefix_titre_fr, titre_fr, annee_prod, pays_prod, prefix_film_director, realisateur, prefix_film_actor, acteur, age_legal, age_sugg, film_famille)
				VALUES ('$prefix_titre_o', '$titre_o', '$prefix_titre_fr', '$titre_fr', '$annee_prod', '$pays_prod', '$prefix_realisateur', '$realisateur', '$prefix_acteur', '$acteur', '$age_legal', '$age_sugg', '$film_famille')";
			$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			//récupération de l'id
			$sqlFilm = "SELECT id_film FROM pm_film WHERE titre_o ='$titre_o' AND annee_prod ='$annee_prod' AND realisateur ='$realisateur' ORDER BY id_film DESC"; // nom de la table ! requette
			$resultFilm = mysqli_query($connexion, $sqlFilm ) or die(mysqli_error());
			$dataFilm = mysqli_fetch_array($resultFilm);
			$id_film = $dataFilm['id_film'];
			
			if($id_cycle>1) {//lien du film au cycle
				$sql2 = "INSERT INTO pm_cycle_film (id_cycle, id_film)
					VALUES ('$id_cycle', '$id_film')";
				$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
			}
		}
		
	}
	
	//SYNCHRONISATION
	//check que le film ne soit pas déjà synchronisé avec cette base
	$sqlSynchro = "SELECT id FROM pm_film_film WHERE (id_foreign ='$id_search' AND base = '$baseselect') OR (id_film ='$id_film' AND base = '$baseselect')"; // nom de la table ! requette
	$resultSynchro = mysqli_query($connexion, $sqlSynchro ) or die(mysqli_error());
	$dataSynchro = mysqli_fetch_array($resultSynchro);
	if(!isset($dataSynchro['id'])) {
		$sqlInsertSynchro = "INSERT INTO pm_film_film (id_film, id_foreign, base)
				     VALUES ('$id_film', '$id_search', '$baseselect')";
		$resultInsertSynchro = mysqli_query($connexion, $sqlInsertSynchro) or die(mysqli_error());
		
		//Synchronisation des copies si la base est Oracle
		if($baseselect == '1') {
			$sqlListCopie = "SELECT * FROM or_copie WHERE id_film = $id_search";
			$resultListCopie = mysqli_query($connexion, $sqlListCopie ) or die(mysqli_error());
			while ($dataListCopie = mysqli_fetch_array($resultListCopie)) {
				
				$sqlSynchroCopie = "SELECT id_copie FROM pm_copie WHERE id_foreign ='$dataListCopie[id_copie]'"; // nom de la table ! requette
				$resultSynchroCopie = mysqli_query($connexion, $sqlSynchroCopie ) or die(mysqli_error());
				$dataSynchroCopie = mysqli_fetch_array($resultSynchroCopie);
				if(!isset($dataSynchroCopie['id_copie'])) {
					$sqlInsertCopie = "INSERT INTO pm_copie (id_film, provenance, duree, format, version, soustitre, intertitre, id_foreign, cote)
							   VALUES ('$id_film', 'CS', '$dataListCopie[duree]', '$dataListCopie[format]', '$dataListCopie[version]', '$dataListCopie[soustitre]', '$dataListCopie[intertitre]', '$dataListCopie[id_copie]', '$dataListCopie[cote]')";
					$resultInsertCopie = mysqli_query($connexion, $sqlInsertCopie) or die(mysqli_error());
				}
			}
		}
		
	}
	
	
	return $dataFilm['id_film'];
	
}

/** COPIES
 * 
 * saveCopie()
 * deleteCopie()
 * deleteFilm()
 * actuCopie()
 */

/* ****************************** INSERTION-MODIFICATION COPIE****************************** */
function saveCopie() {
	include('pm_connexion.php');
	$provenance = clean($_POST['provenance']);
	$duree = clean($_POST['duree']);
	$format = clean($_POST['format']);
	$cryptage = clean($_POST['cryptage']);
	$etat = clean($_POST['etat']);
	$version = clean($_POST['version']);
	$soustitre = clean($_POST['soustitre']);
	$intertitre = clean($_POST['intertitre']);
	$commentaire = cleanTextarea($_POST['commentaire']);
	if($id_nom) {$id_nom = clean($_POST['id_nom']);}
	$statut = clean($_POST['statut']);
	$id_copie = clean($_POST['id_copie']);
	$id_film = clean($_POST['id_film']);
	//
	if ($id_copie > 1) {
		$sql = "UPDATE pm_copie SET provenance='$provenance', duree='$duree', format='$format', cryptage='$cryptage', etat='$etat', version='$version', soustitre='$soustitre', intertitre='$intertitre', commentaire='$commentaire', id_nom='$id_nom', statut='$statut' WHERE id_copie='$id_copie'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		//echo 'update fait';
			
	} else {
		$sql = "INSERT INTO pm_copie (id_film, provenance, duree, format, cryptage, etat, version, soustitre, intertitre, commentaire, id_nom, statut)
			VALUES ('$id_film', '$provenance', '$duree', '$format', '$cryptage', '$etat', '$version', '$soustitre', '$intertitre', '$commentaire', '$cryptage', '$statut')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
	
}
/* **************************** SUPPRIMER COPIE**************************** */
function deleteCopie() {
	include('pm_connexion.php');
	$sql = "DELETE FROM pm_copie WHERE id_copie ='$_POST[id_copie]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}

/* **************************** SYNCHRONISATION COPIE**************************** */
function actuCopie() {
	include('pm_connexion.php');
	$id_film = clean($_POST['id_film']);
	
	// 1. MàJ des données
	$sql = "SELECT or_copie.duree, or_copie.format, or_copie.version, or_copie.soustitre, or_copie.intertitre, or_copie.cote, pm_copie.id_copie FROM pm_copie 
		JOIN or_copie
		ON pm_copie.id_foreign = or_copie.id_copie
		WHERE pm_copie.id_film = '$id_film'
		AND id_foreign IS NOT NULL";
	$result = mysqli_query($connexion, $sql ) or die(mysqli_error());
	while ($data = mysqli_fetch_array($result)) {
		$sqlDo = "UPDATE pm_copie SET duree='$data[duree]', format='$data[format]', version='$data[version]', soustitre='$data[soustitre]', intertitre='$data[intertitre]', cote='$data[cote]' WHERE id_copie='$data[id_copie]'";
		$resultDo = mysqli_query($connexion, $sqlDo) or die(mysqli_error());
	}
	
	// 2. -> suppression...
	$sql2 = "SELECT pm_copie.id_copie FROM pm_copie 
		LEFT JOIN or_copie
		ON pm_copie.id_foreign = or_copie.id_copie
		WHERE pm_copie.id_film = '$id_film'
		AND id_foreign IS NOT NULL
		AND or_copie.id_copie IS NULL";
	$result2 = mysqli_query($connexion, $sql2 ) or die(mysqli_error());
	while ($data2 = mysqli_fetch_array($result2)) {
		$sqlDo2 = "UPDATE pm_copie SET statut='6' WHERE id_copie='$data2[id_copie]'";
		$resultDo2 = mysqli_query($connexion, $sqlDo2) or die(mysqli_error());
	}
	
	// 3. insertion new
	$sql1 = "SELECT id_foreign FROM pm_film_film WHERE id_film='$id_film' AND base='1'";
	$result1 = mysqli_query($connexion, $sql1 ) or die(mysqli_error());
	$data1 = mysqli_fetch_array($result1);
	
	$sql3 = "SELECT or_copie.id_copie, or_copie.duree, or_copie.format, or_copie.version, or_copie.soustitre, or_copie.intertitre, or_copie.cote FROM or_copie
		LEFT JOIN pm_copie
		ON or_copie.id_copie = pm_copie.id_foreign
		WHERE or_copie.id_film = '$data1[id_foreign]'
		AND pm_copie.id_foreign IS NULL";
	$result3 = mysqli_query($connexion, $sql3 ) or die(mysqli_error());
	while ($data3 = mysqli_fetch_array($result3)) {
		$sqlInsertCopie = "INSERT INTO pm_copie (id_film, provenance, duree, format, version, soustitre, intertitre, id_foreign, cote)
				   VALUES ('$id_film', 'CS', '$data3[duree]', '$data3[format]', '$data3[version]', '$data3[soustitre]', '$data3[intertitre]', '$data3[id_copie]', '$data3[cote]')";
		$resultInsertCopie = mysqli_query($connexion, $sqlInsertCopie) or die(mysqli_error());
	}
	
}

/**
 * DIVERS CODES
 */

/*****Ajout des séances à la création d'un bulletin****/
function addSeance($start, $end) {
	include('pm_connexion.php');
	$start_bul = strtotime($start);
	$end_bul = strtotime($end);
	   
	for ($start_day = $start_bul; $start_day <= $end_bul; $start_day = strtotime(date("Y-m-d", $start_day) . " +1 day")) {
	    $start_day1 = date("Y-m-d 15:00:00", $start_day);
	    $start_day2 = date("Y-m-d 18:30:00", $start_day);
	    $start_day3 = date("Y-m-d 21:00:00", $start_day);
	    
	    $end_day1 = date("Y-m-d 16:00:00", $start_day);
	    $end_day2 = date("Y-m-d 19:30:00", $start_day);
	    $end_day3 = date("Y-m-d 22:00:00", $start_day);
	
		$sql1 = "INSERT INTO pm_seance (start, end) VALUES ('$start_day1', '$end_day1')";
		$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
	
		$sql2 = "INSERT INTO pm_seance (start, end) VALUES ('$start_day2', '$end_day2')";
		$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
	
		$sql3 = "INSERT INTO pm_seance (start, end) VALUES ('$start_day3', '$end_day3')";
		$result3 = mysqli_query($connexion, $sql3) or die(mysqli_error());
	}
}

/********************************************************************************************************/
/* ADD ALLDAY   		                							*/
/* Cette fonction ajoute des séances "événements" dans la base             				*/
/* $titre = titre de l'event, $start=date début, $end=date fin, $categorie:0=vacances/1=indisponibilité */
/********************************************************************************************************/
function pmAddAllday() {
include('pm_connexion.php');
	$titre = clean($_POST['titre']);
	$start = clean($_POST['start']);
	if($_POST['end'] > '2015-01-01') {
		$end = clean($_POST['end']);
	} else { $end = $_POST['start']; }
	$categorie = clean($_POST['categorie']);
	$id_salle = clean($_POST['id_salle']);
	$id = clean($_POST['allday']);
	//
	if($id) {
		$sql = "UPDATE pm_allday SET titre='$titre', start='$start', end='$end', categorie='$categorie', id_salle='$id_salle' WHERE id ='$id'";
	} else {
		$sql = "INSERT INTO pm_allday (titre, start, end, categorie, id_salle) VALUES ('$titre', '$start', '$end', $categorie, $id_salle)";
	}
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	//refresh de la page
	header( "refresh:1;" );
}

/* **************************** EFFACER ALLDAY **************************** */
function pmDeleteAllday() {
//$connexion = mysqli_connect('localhost', 'root', '', 'gestion');
include('pm_connexion.php');
	//$sql = "UPDATE pm_allday SET actif='i' WHERE id ='$_POST[id]'";	
	$sql = "DELETE FROM pm_allday WHERE id ='$_POST[id]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	//
	
	//refresh de la page
	header( "refresh:1;" );
}

/********************************************************************************************************/
/* Save Merci   		                							*/
/* Enregistre le champ merci du bulletin			           				*/
/* $id_bulletin, $merci */
/********************************************************************************************************/
function saveMerci() {
include('pm_connexion.php');
	$merci = cleanTextarea($_POST['merci']);
	//
	$id_bulletin = clean($_POST['id_bulletin']);
	
	$sql = "UPDATE pm_bulletin SET merci='$merci' WHERE id_bulletin='$id_bulletin'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}

/********************************************************************************************************/
/* ADD JOURNAL   		                							*/
/* Cette fonction ajoute des entrée dans le journal d'un bulletin             				*/
/* $id_bulletin, ($id_journal), $titre, $redacteur, $photo, $categorie:0=event/1=autre, $statut:0=validé/1=refusé */
/********************************************************************************************************/
function saveJournal() {
include('pm_connexion.php');
	$titre = clean($_POST['titre']);
	$categorie = clean($_POST['categorie']);
	$redacteur = clean($_POST['redacteur']);
	$photo = clean($_POST['photo']);
	$statut = clean($_POST['statut']);
	//
	$id_journal = clean($_POST['id_journal']);
	$id_bulletin = clean($_POST['id_bulletin']);
	
	if($id_journal > 0) {
		$sql1 = "UPDATE pm_journal SET titre='$titre', categorie='$categorie', redacteur='$redacteur', photo='$photo', statut='$statut' WHERE id_journal='$id_journal'";
		$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
		
	} else {
		$sql = "INSERT INTO pm_journal (id_bulletin, titre, categorie, redacteur, photo, statut) VALUES ('$id_bulletin', '$titre', '$categorie', '$redacteur', '$photo', '$statut')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
	}
	
}

/* **************************** EFFACER JOURNAL **************************** */
function deleteJournal() {
include('pm_connexion.php');	
	$sql = "DELETE FROM pm_journal WHERE id_journal ='$_POST[id_journal]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());

}



/********************************************************************************************************/
/* ADD PUBLICITé   		                							*/
/* Cette fonction ajoute des entrée dans les publicité d'un bulletin           				*/
/* $id_bulletin, ($id_pub), $titre, $commentaire 							*/
/********************************************************************************************************/
function savePub() {
include('pm_connexion.php');
	$titre = clean($_POST['titre']);
	$commentaire = cleanTextarea($_POST['commentaire']);
	//
	$id_pub = clean($_POST['id_pub']);
	$id_bulletin = clean($_POST['id_bulletin']);
	
	if($id_pub > 0) {
		$sql1 = "UPDATE pm_pub SET titre='$titre', commentaire='$commentaire' WHERE id_pub = '$id_pub'";
		$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
		
	} else {
		$sql = "INSERT INTO pm_pub (id_bulletin, titre, commentaire) VALUES ('$id_bulletin', '$titre', '$commentaire')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
	}
	
}

/* **************************** EFFACER PUB **************************** */
function deletePub() {
include('pm_connexion.php');	
	$sql = "DELETE FROM pm_pub WHERE id_pub ='$_POST[id_pub]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());

}

/****************************************************************************************/
/* ADD TRIGGER                   						   	*/
/* Ajout des modifications dans la table pm_trigger_cycle en prenant compte de l'user	*/
/*        	      									*/
/****************************************************************************************/
function triggerCycleCartouche() {
	include('pm_connexion.php');
	//pm_cycle
	$id_cycle = clean($_POST['id_cycle']);
	$date_new = cleanTextarea($_POST['date']);
	$commentaire_new = cleanTextarea($_POST['commentaire']);
	
	//pm_cartouche
	$intro_new = clean($_POST['intro']);
	$notule_new = clean($_POST['notule']);
	$presence_new = clean($_POST['presence']);
	$photo_new = clean($_POST['photo']);
	$logo_new = cleanTextarea($_POST['logo']);
	$bat_new = cleanTextarea($_POST['bat']);
	//$info_new = cleanTextarea($_POST['info']);
	
	//Récupération des anciennes données
	$sqlCycleOld = "SELECT date, intro, notule, presence, photo, logo, bat, info, commentaire, pm_cycle.edition FROM pm_cycle
		JOIN pm_cartouche
		ON pm_cycle.id_cycle = pm_cartouche.id_cycle
		WHERE pm_cycle.id_cycle = '$id_cycle'";
	$resultCycleOld = mysqli_query($connexion, $sqlCycleOld ) or die(mysqli_error());
	$dataCycleOld = mysqli_fetch_array($resultCycleOld);
	
	$date_old = $dataCycleOld['date'];
	$intro_old = $dataCycleOld['intro'];
	$notule_old = $dataCycleOld['notule'];
	$presence_old = $dataCycleOld['presence'];
	$photo_old = $dataCycleOld['photo'];
	$logo_old = $dataCycleOld['logo'];
	$bat_old = $dataCycleOld['bat'];
	//$info_old = $dataCycleOld['info'];
	$commentaire_old = $dataCycleOld['commentaire'];
	
	$edition = $dataCycleOld['edition'];

	/**Comparaison entre les variable**/
	//remise à zero des variables
	$date = '';
	$intro = '';
	$notule = '';
	$presence = '';
	$photo = '';
	$logo = '';
	$bat = '';
	//$info = '';
	$commentaire = '';
	//ifs
	if ($date_old !== $date_new) $date=$date_new;
	if ($intro_old !== $intro_new) $intro=$intro_new;
	if ($notule_old !== $notule_new) $notule=$notule_new;
	if ($presence_old !== $presence_new) $presence=$presence_new;
	if ($photo_old !== $photo_new) $photo=$photo_new;
	if ($logo_old !== $logo_new) $logo=$logo_new;
	if ($bat_old !== $bat_new) $bat=$bat_new;
	//if ($info_old !== $info_new) $info=$info_new;
	if ($commentaire_old !== $commentaire_new) $commentaire=$commentaire_new;

	//enregistrement
	if ($date || $intro || $notule || $presence || $photo || $logo || $bat || $commentaire) {
		$sql1 = "INSERT INTO pm_trigger_cycle (id_cycle, date, intro, notule, presence, photo, logo, bat, commentaire, edition, datetime)
			VALUES ('$id_cycle', '$date', '$intro', '$notule', '$presence', '$photo', '$logo', '$bat', '$commentaire', '$edition', NOW())";
		$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
	}
}

/******************************************************************************/
/* DELETE LOCKDATE ($table, $connexion = NULL           		      */
/* Cette fonction lève les verrous perimés de la table spécifiée              */
/* $table : Nom de la table contenant les lignes à déverrouiller       	      */
/******************************************************************************/
function deleteLockdate($table) {

define('VALIDITE_VERROU', 10);

	include('pm_connexion.php');
	
    if (empty ($table) ) {
        return 'Un paramètre $table est vide';
    }
    
    // suppression des verrous obsoletes
    $sql = 'UPDATE '.$table.'
              SET edition = NULL, lockdate = NULL
              WHERE NOW() >= ( DATE_ADD(lockdate, INTERVAL '.VALIDITE_VERROU.' MINUTE) )';
    $result = mysqli_query($connexion, $sql) or die(mysqli_error());

    if ( !$result) {
        die('Requête invalide : <br>'.$sql. '<br>' . mysql_error());
    } else {
        return TRUE;
    }
}





?>