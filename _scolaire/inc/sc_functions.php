<?php
/**
* FONCTION RESERVATION
*
* Liste des fonctions de réservations
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 29.04.2015 - 13.07.15
*/

/***Correction des variables uutf8_encode***/
function cleanApp($var) {
	//pour l'apostrophe
	$text = array("'");
	$result = array("''");
	$var = str_replace($text, $result, $var);
	//effacement d'espaces et retours maladroit
	$cleanVar = trim($var);
	
	return $cleanVar;
}

/***Correction des variables utf8_decode***/
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
	$cleanVar = substr($cleanVar, 0, -4);

	return $cleanVar;
}

/***Correction des variables dans un Array[]***/
function cleanArray($var) {
	//pour la virgule de fin
	$cleanVar = clean($var);
	$cleanVar = substr($cleanVar, 0, -1);
	
	return $cleanVar;
}

/***Correction de l'affichage de caractères depuis typo3***/
function afficheTypo($var) {

	$caract = array(utf8_decode('  '), utf8_decode('XXANDXX'), utf8_decode(''), utf8_decode(''), utf8_decode(''), utf8_decode('...'), utf8_decode(''), utf8_decode(''), utf8_decode('~'), utf8_decode(''), utf8_decode(' '));
	$new_caract = array(utf8_encode(' '), utf8_encode('&#38;'), utf8_encode('&#339;'), utf8_encode('&#338;'), '&#8230;', '&#8230;', '&#8217;', '&#8216;', utf8_encode('&#126;'), '-', '');
	$cleanVar = str_replace($caract, $new_caract, $var);
	
	return $cleanVar;

}

/***Correction de l'affichage de caractères depuis typo3***/
function afficheHtml($var) {
	
	$text = array('<i>', '</i>', '<b>', '</b>');
	$result = array('', '', '', '');
	$cleanVar = str_replace($text, $result, $var);
	
	return $cleanVar;

}
/** Event
 * 
 * saveEvent()
 * deleteEvent()
 * addMovie()
 * deleteLink()
 * exportNews()
 */

/* ****************************** INSERTION-MODIFICATION CATEGORIE****************************** */
function saveEvent() {
	include('inc/ge_connexion.php');
	$titre = cleanTextarea($_POST['titre']);
	$date = clean($_POST['date']);
	$id_salle = clean($_POST['id_salle']);
	$commentaire = cleanTextarea($_POST['commentaire']);
	
	$id_event = clean($_POST['id_event']);
	
	//
	if ($id_event > 0) {
		$sql = "UPDATE re_event SET titre='$titre', date='$date', id_salle='$id_salle', commentaire='$commentaire' WHERE id_event='$id_event'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else {
		
		if($_POST['concours']) {
			$sql = "INSERT INTO re_event (titre, date, id_salle, commentaire, concours)
				VALUES ('$titre', '$date', '$id_salle', '$commentaire', '1')";
			$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
		} else {
			$sql = "INSERT INTO re_event (titre, date, id_salle, commentaire)
				VALUES ('$titre', '$date', '$id_salle', '$commentaire')";
			$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		}
	}
}
/* **************************** SUPPRIMER BULLETIN**************************** */
function deleteEvent() {
	include('inc/ge_connexion.php');
	$sql = "DELETE FROM re_event WHERE id_event ='$_POST[id_event]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* **************************** SUPPRIMER BULLETIN**************************** */
function addMovie() {
	include('inc/ge_connexion.php');
	$id_event = clean($_POST['id_event']);
	$id_film = clean($_POST['id_film']);

	$sql = "UPDATE re_event SET id_film='$id_film' WHERE id_event='$id_event'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());

}
/* **************************** SUPPRIMER BULLETIN**************************** */
function deleteLink() {
	include('inc/ge_connexion.php');
	$id_event = clean($_POST['id_event']);
	$id_film = '0';

	$sql = "UPDATE re_event SET id_film='$id_film' WHERE id_event='$id_event'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());

}
/* **************************** SUPPRIMER BULLETIN**************************** */
function exportNews() {
	include('inc/ge_connexion.php');
	$id_event = clean($_POST['id_event']);

	$sql = "UPDATE re_event SET export='1' WHERE id_event='$id_event'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());

}


/** Reserv
 * 
 * saveReserv()
 * deleteReserv()
 */

/* ****************************** INSERTION-MODIFICATION CATEGORIE****************************** */
function saveReserv() {
	include('inc/ge_connexion.php');
	$id_event = clean($_POST['id_event']);
	$id_categorie = clean($_POST['id_categorie']);
	$titre = clean($_POST['titre']);
	$quotas = clean($_POST['quotas']);
	$url = clean($_POST['url']);
	$text_info = cleanTextarea($_POST['text_info']);
	$text_complet = cleanTextarea($_POST['text_complet']);
	$text_fin = cleanTextarea($_POST['text_fin']);
	
	$id_reserv = clean($_POST['id_reserv']);
	
	//
	if ($id_reserv > 0) {
		$sql = "UPDATE re_reserv SET id_categorie='$id_categorie', titre='$titre', quotas='$quotas', url='$url', text_info='$text_info', text_complet='$text_complet', text_fin='$text_fin' WHERE id_reserv ='$id_reserv'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else {
		$sql = "INSERT INTO re_reserv (id_event, id_categorie, titre, quotas, url, text_info, text_complet, text_fin)
			VALUES ('$id_event', '$id_categorie', '$titre', '$quotas', '$url', '$text_info', '$text_complet', '$text_fin')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
}
/* **************************** SUPPRIMER BULLETIN**************************** */
function deleteReserv() {
	include('inc/ge_connexion.php');
	$sql = "DELETE FROM re_reserv WHERE id_reserv ='$_POST[id_reserv]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}


/** Inscriptions
 * 
 * addInsc()
 * deleteInsc()
 * addMultiLacs()
 */
/* **************************** AJOUTER INSCRIPTION**************************** */
function addInsc() {
	if (file_exists('inc/ge_connexion.php')) {
		include('inc/ge_connexion.php');
	}
	//if connexion via extérieur
	if (file_exists('../cs_gestion/inc/ge_connexion.php')) {
		include('../cs_gestion/inc/ge_connexion.php');
	} 

	$id_reserv = clean($_POST['id_reserv']);
	$id = clean($_POST['id']);
	//initiation des variable
	$politesse = clean($_POST['politesse']);
	$prenom = clean($_POST['prenom']);
	$nom = clean($_POST['nom']);
	$email = clean($_POST['email']);
	$place = clean($_POST['place']);
	$newsletter = clean($_POST['newsletter']);
	$adresse = clean($_POST['adresse']);
	$npa = clean($_POST['npa']);
	$lieu = clean($_POST['lieu']);
	$telephone = clean($_POST['telephone']);
	$champ1 = '';
	foreach($_POST['champ1'] as $element) {
		$champ1 .= $element.',';
	} $champ1 = cleanArray($champ1);
	$champ2 = '';
	foreach($_POST['champ2'] as $element) {
		$champ2 .= $element.',';
	} $champ2 = cleanArray($champ2);
	$champ3 = '';
	foreach($_POST['champ3'] as $element) {
		$champ3 .= $element.',';
	} $champ3 = cleanArray($champ3);
	$champ4 = '';
	foreach($_POST['champ4'] as $element) {
		$champ4 .= $element.',';
	} $champ4 = cleanArray($champ4);
	$champ5 = '';
	foreach($_POST['champ5'] as $element) {
		$champ5 .= $element.',';
	} $champ5 = cleanArray($champ5);
	$champ6 = '';
	foreach($_POST['champ6'] as $element) {
		$champ6 .= $element.',';
	} $champ6 = cleanArray($champ6);
	$champ7 = '';
	foreach($_POST['champ7'] as $element) {
		$champ7 .= $element.',';
	} $champ7 = cleanArray($champ7);
	$champ8 = '';
	foreach($_POST['champ8'] as $element) {
		$champ8 .= $element.',';
	} $champ8 = cleanArray($champ8);
	$champ9 = '';
	foreach($_POST['champ9'] as $element) {
		$champ9 .= $element.',';
	} $champ9 = cleanArray($champ9);
	
	if($id > 0) {
		$sql = "UPDATE re_inscription SET prenom='$prenom', nom='$nom', email='$email', place='$place' WHERE id='$id'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
	}else {
		// insertion de l'inscription.
		$sql = "INSERT INTO re_inscription (id_reserv, politesse, prenom, nom, email, place, newsletter, adresse, npa, lieu, telephone, champ1, champ2, champ3, champ4, champ5, champ6, champ7, champ8, champ9)
			VALUES ('$id_reserv', '$politesse', '$prenom', '$nom', '$email', '$place', '$newsletter', '$adresse', '$npa', '$lieu', '$telephone', '$champ1', '$champ2', '$champ3', '$champ4', '$champ5', '$champ6', '$champ7', '$champ8', '$champ9')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
	
}
/* **************************** AJOUTER INSCRIPTION**************************** */
function addInscGestion() {
	include('inc/ge_connexion.php');
	
	$id_reserv = clean($_POST['id_reserv']);
	$id = clean($_POST['id']);
	//initiation des variable
	$prenom = clean($_POST['prenom']);
	$nom = clean($_POST['nom']);
	$email = clean($_POST['email']);
	$place = clean($_POST['place']);
	
	if($id > 0) {
		$sql = "UPDATE re_inscription SET prenom='$prenom', nom='$nom', email='$email', place='$place' WHERE id='$id'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
	}else {
		// insertion de l'inscription.
		$sql = "INSERT INTO re_inscription (id_reserv, prenom, nom, email, place)
			VALUES ('$id_reserv', '$prenom', '$nom', '$email', '$place')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
	
}
/* **************************** SUPPRIMER INSCRIPTION**************************** */
function deleteInsc() {
	if (file_exists('inc/ge_connexion.php')) {
		include('inc/ge_connexion.php');
	}
	//if connexion via extérieur
	if (file_exists('../cs_gestion/inc/ge_connexion.php')) {
		include('../cs_gestion/inc/ge_connexion.php');
	} 
	$sql = "DELETE FROM re_inscription WHERE id ='$_POST[id]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* **************************** AJOUT MULTIPLE DES LACS**************************** */
function addMultiLacs() {
	if (file_exists('inc/ge_connexion.php')) {
		include('inc/ge_connexion.php');
	}
	//if connexion via extérieur
	if (file_exists('../inc/ge_connexion.php')) {
		include('../inc/ge_connexion.php');
	} 
	//if connexion via extérieur
	if (file_exists('../cs_gestion/inc/ge_connexion.php')) {
		include('../cs_gestion/inc/ge_connexion.php');
	} 

	
	$prenom = clean($_POST['prenom']);
	$nom = clean($_POST['nom']);
	$email = clean($_POST['email']);
	$place = clean($_POST['place']);
	$newsletter = clean($_POST['newsletter']);
	$adresse = clean($_POST['adresse']);
	$npa = clean($_POST['npa']);
	$lieu = clean($_POST['lieu']);
	$telephone = clean($_POST['telephone']);
	
	$reserv = $_POST['reserv'];
	
	foreach ($reserv as $id_reserv) {
		$sql = "INSERT INTO re_inscription (id_reserv, prenom, nom, email, place, newsletter, adresse, npa, lieu, telephone)
			VALUES ('$id_reserv', '$prenom', '$nom', '$email', '$place', '$newsletter', '$adresse', '$npa', '$lieu', '$telephone')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		//echo 'Liens faits avec '.$id_film.'<br>';
	$newsletter = '';
	$adresse = '';
	$npa = '';
	$lieu = '';
	$telephone = '';
	}	
	
}




/** Catégorie
 * 
 * saveCat()
 * deleteCat()
 * saveForm()
 * deleteForm()
 * addInscForm()
 */

/* ****************************** INSERTION-MODIFICATION CATEGORIE****************************** */
function saveCat() {
	include('inc/ge_connexion.php');
	$titre = clean($_POST['titre']);
	
	$id_categorie = clean($_POST['id_categorie']);
	
	//
	if ($id_categorie > 0) {
		$sql = "UPDATE re_categorie SET titre='$titre' WHERE id_categorie='$id_categorie'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else {
		$sql = "INSERT INTO re_categorie (titre)
			VALUES ('$titre')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
		//récupération de l'id
		$sql2 = "SELECT id_categorie FROM re_categorie WHERE titre = '$titre'";
		$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
		$data2 = mysqli_fetch_array($result2);
		$id_categorie = $data2['id_categorie'];
		
		//création des id de base
		$prenom = clean('Prénom');
		$place = clean("Nombre d'invitations souhaitées");
		$place2 = clean ('--- Sélectionner ---,1 place,2 places');
		$newsletter = clean('Inscription à la newsletter de la Cinémathèque suisse');
		$sql3 = "INSERT INTO re_form (id_categorie, titre, name, type, valeur)
			VALUES
			('$id_categorie', 'Politesse', 'politesse', 4, 'Madame,Monsieur'),
			('$id_categorie', '$prenom', 'prenom', 1, ''),
			('$id_categorie', 'Nom', 'nom', 1, ''),
			('$id_categorie', 'Email', 'email', 6, ''),
			('$id_categorie', '$place', 'place', 5, '$place2'),
			('$id_categorie', '$newsletter', 'newsletter', 3, '$newsletter')";
		$result3 = mysqli_query($connexion, $sql3) or die(mysqli_error());
	}
	
	//création des champs de base du formulaire
	

}
/* **************************** SUPPRIMER CATEGORIE**************************** */
function deleteCat() {
	include('inc/ge_connexion.php');
	$sql = "DELETE FROM re_categorie WHERE id_categorie ='$_POST[id_categorie]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* ****************************** INSERTION-MODIFICATION FORMULAIRE****************************** */
function saveForm() {
	include('inc/ge_connexion.php');
	$titre = clean($_POST['titre']);
	$type = clean($_POST['type']);
	$valeur = cleanTextarea($_POST['valeur']);
	
	$id_form = clean($_POST['id_form']);
	$id_categorie = clean($_POST['id_categorie']);
	
	//
	if ($id_form > 0) {
		$sql = "UPDATE re_form SET titre='$titre', type='$type', valeur='$valeur' WHERE id_form='$id_form'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else {
		$sql0 = "SELECT name FROM re_form WHERE id_categorie = '$cat' AND type <> '7' ORDER BY id_form DESC";
		$result0 = mysqli_query($connexion, $sql0) or die(mysqli_error());
		$data0 = mysqli_fetch_array($result0);
		if($data0['name'] == 'newsletter') {
			$name = "1";
		} else {
			$num = substr($data0['name'], -1);
			$num ++;
			$name = 'champ'.$num;
		}
		
		$sql = "INSERT INTO re_form (id_categorie, titre, name, type, valeur)
			VALUES ('$id_categorie', '$titre', '$name', '$type', '$valeur')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
}
/* **************************** SUPPRIMER FORMULAIRE**************************** */
function deleteForm() {
	include('inc/ge_connexion.php');
	$sql = "DELETE FROM re_form WHERE id_form ='$_POST[id_form]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* **************************** SUPPRIMER FORMULAIRE**************************** */
function addInscForm() {
	include('inc/ge_connexion.php');
	$id_categorie = clean($_POST['id_categorie']);
	//initiation des variable
	$politesse = '';
	$prenom = '';
	$nom = '';
	$email = '';
	$place = '';
	$newsletter = '';
	$adresse = 'adresse';
	$npa = 'NPA';
	$lieu = 'Lieu';
	$telephone = clean('Téléphone privé');
	$champ1 = '';
	$champ2 = '';
	$champ3 = '';
	$champ4 = '';
	$champ5 = '';
	$champ6 = '';
	$champ7 = '';
	$champ8 = '';
	$champ9 = '';
	
	//recherche des champs et mise en place des doubles variables $$dataFormAtt['name']
	$sqlFormAtt = "SELECT titre, name FROM re_form WHERE id_categorie = '$id_categorie' AND type <> '7'"; // nom de la table ! requette
	$resultFormAtt = mysqli_query($connexion, $sqlFormAtt ) or die(mysqli_error());
	while ($dataFormAtt = mysqli_fetch_array($resultFormAtt)) {
		$$dataFormAtt['name'] = cleanApp($dataFormAtt['titre']);
	}
	// insertion de l'inscription.
	$sql = "INSERT INTO re_inscription (id_categorie, politesse, prenom, nom, email, adresse, lieu, telephone, champ1, champ2, champ3, champ4, champ5, champ6, champ7, champ8, champ9)
			VALUES ('$id_categorie', '$politesse', '$prenom', '$nom', '$email', '$adresse', '$lieu', '$telephone', '$champ1', '$champ2', '$champ3', '$champ4', '$champ5', '$champ6', '$champ7', '$champ8', '$champ9')";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}

?>