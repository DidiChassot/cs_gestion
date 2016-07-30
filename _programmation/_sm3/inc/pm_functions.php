<?php
/*** Fonction programmation - SM3***/
// 
//Cindy Chassot 12.12.2014 - 26.02.2015
//© Cinémathèque suisse

/***Effacement des variables***/
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
	$text = array('<p>', '</p>', '<em>', '</em>', '<strong>', '</strong>');
	$result = array('', '<br>', '<i>', '</i>', '<b>', '</b>');
	$cleanVar = str_replace($text, $result, $var);
	//lancement de clean()
	$cleanVar = clean($cleanVar);

	return $cleanVar;
}

/* **************************** SUPPRIMER la séance -> corbeille **************************** */
function pmSupprimer() {
include('pm_connexion.php');	
		// requete sql pour modifier en prenant compte du "id" envoyer par POST
		$sql = "UPDATE pm_seance SET actif='i' WHERE id_seance ='$_POST[id_seance]'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	
	//refresh de la page
	header( "refresh:1;" );
}

/* **************************** EFFACER la séance -> plus de film lié**************************** */
function pmEffacer() {
include('pm_connexion.php');
	// recherche d'entrées pm_film_seance quand id_seance existe
	$sql_search = "SELECT * FROM pm_film_seance WHERE id_seance ='$_POST[id_seance]'";
	$result_search = mysqli_query($connexion, $sql_search) or die(mysqli_error());
	
	// si des films sont liés -> effacement de la séance
	if($data_search = mysqli_fetch_array($result_search)) {
		$sql = "DELETE FROM pm_film_seance WHERE id_seance ='$_POST[id_seance]'";	
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		// requete sql pour FORMATER la séance
		$sql2 = "UPDATE pm_seance SET titre='', commentaire='', event=0 WHERE id_seance ='$_POST[id_seance]'";
		$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
		
	}else { // sinon suppression définitive de la séance
		$sql = "DELETE FROM pm_seance WHERE id_seance ='$_POST[id_seance]'";	
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
	
	//refresh de la page
	header( "refresh:1;" );
}

/* **************************** ENREGISTRER **************************** */
function pmEnregistrer() {
include('pm_connexion.php');
	// transformation des variables envoyées
	$titre = clean($_POST['titre']);
	$start = clean($_POST['start']);
	$end = clean($_POST['end']);
	$id_salle = clean($_POST['id_salle']);
	$commentaire = cleanTextarea($_POST['commentaire']);
	$event = $_POST['event'];
	//
	$id_seance = $_POST['id_seance']; // faut faire attention à ça!!!
	
	// requete sql pour modifier en prenant compte du "id_seance" envoyer par POST
	$sql = "UPDATE pm_seance SET titre='$titre', start='$start', end='$end', id_salle='$id_salle', commentaire='$commentaire', event='$event' WHERE id_seance='$id_seance'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	if($_POST['id_copie']) {
		$id_copie = clean($_POST['id_copie']);
		//ajout de l'information de copie dans la table pm_film_seance
		$sql_copie = "UPDATE pm_film_seance SET id_copie='$id_copie' WHERE id_seance='$id_seance'";
		$result_copie = mysqli_query($connexion, $sql_copie) or die(mysqli_error());
	}
	
	//refresh de la page
	header( "refresh:1;" );
}


/* **************************** DUPLIQUER -> nouvel enregistrement dans le panier **************************** */
function pmDupliquer() {
include('pm_connexion.php');
	//récupération des autres id
	$id = 1;
	for($i=0; $i<$id; $i++) {
	    //requète sur les séances existantes pour récupérer un "id"
	    $sql = "SELECT * FROM pm_seance WHERE id_seance < 10 AND id_seance = '$id' ";
	    $result = mysqli_query($connexion, $sql) or die(mysqli_error());
	    if($data = mysqli_fetch_array($result)) $id ++;
	}
	
	// récupération et transformation des variables envoyées
	$id;
	$titre = clean($_POST['titre']);
	$id_salle = clean($_POST['id_salle']);
	$commentaire = cleanTextarea($_POST['commentaire']);
	$event = clean($_POST['event']);
	$id_seance_old = $_POST['id_seance']; // faut faire attention à ça!!!
	
	// Ajout d'une nouvelle "séance" qui sera stockée dans le panier
	$sql_new = "INSERT INTO pm_seance (id_seance, titre, id_salle, commentaire, event)
		   VALUES ('$id', '$titre', '$id_salle', '$commentaire', '$event')";
	$result_new = mysqli_query($connexion, $sql_new) or die(mysqli_error());
	
	//requète sur pm_film_seance pour récupérer les connexions
	$sql_film = "SELECT * FROM pm_film_seance WHERE id_seance = '$id_seance_old'";
	$result_film = mysqli_query($connexion, $sql_film) or die(mysqli_error());
	//boucle pour récupérer toute les donneés
	while ($data_film = mysqli_fetch_array($result_film)) {
		$id_film = $data_film['id_film'];
		$id_cycle = $data_film['id_cycle'];
		$ordre = $data_film['ordre'];
		$categorie = $data_film['categorie'];
		
		// DUPPLICATION DES ENTRéES PM_FILM_SEANCE
		$sql_add = "INSERT INTO pm_film_seance (id_seance, id_film, id_cycle, ordre, categorie)
			   VALUES ('$id', '$id_film', '$id_cycle', '$ordre', '$categorie')";
		$result_add = mysqli_query($connexion, $sql_add) or die(mysqli_error());
	}
}

/* **************************** EFFACER TOUT du panier et de la corbeille **************************** */
function pmDelete() {
//$connexion = mysqli_connect('localhost', 'root', '', 'gestion');
include('pm_connexion.php');
	$sql = "DELETE FROM pm_seance WHERE id_seance ='$_POST[id_seance]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	// 
	$sql2 = "DELETE FROM pm_film_seance WHERE id_seance ='$_POST[id_seance]'";	
	$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
	
	//refresh de la page
	header( "refresh:1;" );
}

/* **************************** EFFACER LIEN ENTRE FILM ET SEANCE **************************** */
function pmDeleteLinkMovieSeance() {
include('pm_connexion.php');

	$id_seance = clean($_POST['id_seance']); //récupération de l'id de la séance
	$id_film = clean($_POST['id_film']); //récupération de l'id de la séance
	
	//suppression de l'enregistrement dans pm_film_seance
	$sql1 = "DELETE FROM pm_film_seance WHERE id_seance ='$id_seance' AND id_film ='$id_film'";	
	$result1 = mysqli_query($connexion, $sql1) or die(mysqli_error());
	
	//mise à jour de l'ordre des films
	$sql2 = "SELECT id_film FROM pm_film_seance WHERE id_seance = '$id_seance' ORDER BY ordre";	
	$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
	$ordre = 1;
	$duree = 30;
	if(mysql_num_rows($result2) > 0) { $duree = 0; }
	while ($data2 = mysqli_fetch_array($result2)) {
		$id_filmSelect = $data2['id_film'];
		// update des entrée pour refaire l'ordre
		$sql3 = "UPDATE pm_film_seance SET ordre='$ordre' WHERE id_seance='$id_seance' AND id_film='$id_filmSelect'";
		$result3 = mysqli_query($connexion, $sql3) or die(mysqli_error());
		//incrémentation de l'ordre
		$ordre++;
		
		//récupération de durée de la copie pour l'ajouter à la séance
		$sql_copie = "SELECT duree FROM pm_copie WHERE id_film='$id_filmSelect' ORDER BY statut DESC";
		$result_copie = mysqli_query($connexion, $sql_copie) or die(mysqli_error());
		$data_copie = mysqli_fetch_array($result_copie);
		    //add duréee
		    $duree = $duree + $data_copie['duree'];
	}
	
	/***mise à jour du "end" de la séance***/
	//sélection du start de la séance
	$sql_seance = "SELECT start FROM pm_seance WHERE id_seance='$id_seance'";
	$result_seance = mysqli_query($connexion, $sql_seance) or die(mysqli_error());
	$data_seance = mysqli_fetch_array($result_seance);
	$start = strtotime($data_seance['start']); //convertir la date au format timestamp
	
	//ajout de la duree totale au "start"
	$end = $start + ($duree*60);
	$end = date("Y-m-d G:i:s", $end);
	
	//UPDATE
	$sql_duree = "UPDATE pm_seance SET end='$end' WHERE id_seance='$id_seance'";
	$result_duree = mysqli_query($connexion, $sql_duree) or die(mysqli_error());
	
	//refresh de la page
	header( "refresh:1;" );
}

/* **************************** ADD ALLDAY **************************** */
function pmAddAllday() {
include('pm_connexion.php');
	$titre = clean($_POST['titre']);
	$start = clean($_POST['start']);
	if($_POST['end'] > '2015-01-01') {
		$end = clean($_POST['end']);
	} else { $end = NULL; }
	$categorie = clean($_POST['categorie']);
	$id_salle = clean($_POST['id_salle']);
	//
	$sql = "INSERT INTO pm_allday (titre, start, end, categorie, id_salle) VALUES ('$titre', '$start', '$end', $categorie, $id_salle)";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	//refresh de la page
	//header( "refresh:1;" );
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


?>