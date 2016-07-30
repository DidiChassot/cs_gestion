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
	$text = array('<p>', '</p>', '<em>', '</em>', '<strong>', '</strong>', '', 'egrave;', '', '', '’', '‘');
	$result = array('', '<br>', '<i>', '</i>', '<b>', '</b>', '', 'è', '&#8217;', '&#8216;', '&#8217;', '&#8216;' );
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
/******************************* TRAITEMENT DES FORMS *******************************/

/*** 1. Input type text***/
function inputText($name, $content) {
	$inputText = '<input type="text" id="'.$name.'" name="'.$name.'[]" value="'.utf8_encode($content).'">';
	
	return $inputText;
}
/*** 2. Textarea***/
function textArea($name, $content) {
	$inputText = '<textarea id="'.$name.'" name="'.$name.'[]">'.utf8_encode($content).'</textarea>';
	
	return $inputText;
}
/*** 3. Input type checkbox***/
function inputCheck($name, $valeur, $content) {
	$arrayValue = explode(',', $valeur);
	$arrayContent = explode(',', $content);
	$inputText = '';
	foreach($arrayValue as $cle => $element) {
	    $inputText .= '<span class="block"><input type="checkbox" id="'.$name.'" name="'.$name.'[]" value="'.utf8_encode($element).'"';
	    foreach($arrayContent as $cle => $contenu) {
	      if($element == $contenu) {
		$inputText .= ' checked';
	      }
	    }
	    $inputText .= '>'.utf8_encode($element).'</span>';
	}

	return $inputText;
}
/*** 4. Input type radio***/
function inputRadio($name, $valeur, $content) {
	$arrayValue = explode(',', $valeur);
	$inputText = '';
	foreach($arrayValue as $cle => $element) {
	    $inputText .= '<span class="block"><input type="radio" id="'.$name.'" name="'.$name.'[]" value="'.utf8_encode($element).'"';
	    if($element == $content) {
		$inputText .= ' checked';
	      }
	    $inputText .= '>'.utf8_encode($element).'</span>';
	}
	
	return $inputText;
}
/*** 5. Input type select***/
function inputSelect($name, $valeur, $content) {
	$arrayValue = explode(',', $valeur);
	$arrayContent = explode(',', $content);
	$inputText = '';
	$inputText = '<select id="'.$name.'" name="'.$name.'[]">';
	foreach($arrayValue as $cle => $element) {
	    $inputText .= '<option value="'.utf8_encode($element).'"';
	    foreach($arrayContent as $cle => $contenu) {
	      if($element == $contenu) {
		$inputText .= ' selected';
	      }
	    }
	    $inputText .= '>'.utf8_encode($element).'</option>';
	}
	$inputText .= '</select>';
	
	return $inputText;
}
/*** 6. Input type mail***/
function inputEmail($name, $content) {
	$inputText = '<input type="email" id="'.$name.'" name="'.$name.'[]" value="'.utf8_encode($content).'">';
	
	return $inputText;
}
/*** 7. Input type date***/
function inputDate($name, $content) {
	$inputText = '<input type="date" id="'.$name.'" name="'.$name.'[]" value="'.utf8_encode($content).'">';
	
	return $inputText;
}

/** Formulaires
 * 
 * saveForm()
 * deleteForm()
 * saveTrad()
 * saveChamp()
 * deleteChamp()
 * saveChampTrad()
 * addInscForm()
 */

/* ****************************** INSERTION-MODIFICATION FORM****************************** */
function saveForm() {
	include('inc/ge_connexion.php');
	$id_form = clean($_POST['id_form']);
	$num_dep = clean($_POST['num_dep']);
	
	$titre = clean($_POST['titre']);
	$email = clean($_POST['email']);
	$remarque = cleanTextarea($_POST['remarque']);
	
	//
	if ($id_form > 0) {
		$sql = "UPDATE fm_form SET titre='$titre', email='$email', remarque='$remarque' WHERE id_form='$id_form'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else {
		$sql = "INSERT INTO fm_form (titre, email, remarque, num_dep)
			VALUES ('$titre', '$email', '$remarque', '$num_dep')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
}

/* ****************************** DELETE FORM ****************************** */
function deleteForm() {
	include('inc/ge_connexion.php');
	$id_form = clean($_POST['id_form']);
	
	$sql = "UPDATE fm_form SET actif='0' WHERE id_form='$id_form'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
}

/* ****************************** INSERTION D'UNE TRADUCTION ****************************** */
function saveTrad() {
	include('inc/ge_connexion.php');
	$id_form = clean($_POST['id_form']);
	$langue = clean($_POST['langue']);
	$id = clean($_POST['id_trad']);
	
	$titre_trad = clean($_POST['titre_trad']);
	$remarque_trad = cleanTextarea($_POST['remarque_trad']);
	
	//
	if ($id > 0) {
		$sql = "UPDATE fm_form_trad SET titre_trad='$titre_trad', remarque_trad='$remarque_trad' WHERE id='$id'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else {
		$sql = "INSERT INTO fm_form_trad (id_form, titre_trad, remarque_trad, langue)
			VALUES ('$id_form', '$titre_trad', '$remarque_trad', '$langue')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
			
}
/* ****************************** INSERTION-MODIFICATION FORMULAIRE****************************** */
function saveChamp() {
	include('inc/ge_connexion.php');
	$titre = clean($_POST['titre']);
	$type = clean($_POST['type']);
	$valeur = cleanTextarea($_POST['valeur']);
	if($_POST['required']) {$required = 1;} else $required = 0;
	if($_POST['visible']) {$visible = 1;} else $required = 0;
	
	$id_form = clean($_POST['id_form']);
	$id_champ = clean($_POST['id_champ']);
	
	//
	if ($id_champ > 0) {
		$sql = "UPDATE fm_champ SET titre='$titre', type='$type', valeur='$valeur', required='$required', visible='$visible' WHERE id_champ='$id_champ'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else {
		$sql0 = "SELECT ordre FROM fm_champ WHERE id_form = '$id_form' ORDER BY ordre DESC";
		$result0 = mysqli_query($connexion, $sql0) or die(mysqli_error());
		$data0 = mysqli_fetch_array($result0);
		if($data0['ordre'] == FALSE) {
			$ordre = "1";
		} else {
			$ordre = $data0['ordre'];
			$ordre ++;
		}
		
		$sql = "INSERT INTO fm_champ (id_form, titre, type, valeur, required, visible, ordre)
			VALUES ('$id_form', '$titre', '$type', '$valeur', '$required', '$visible', '$ordre')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
}
/* **************************** SUPPRIMER Lien entre CYCLE et FILM**************************** */
function deleteChamp() {
	include('inc/ge_connexion.php');
	$sql = "DELETE FROM fm_champ WHERE id_champ ='$_POST[id_champ]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
/* ****************************** INSERTION D'UNE TRADUCTION ****************************** */
function saveChampTrad() {
	include('inc/ge_connexion.php');
	$id_champ = clean($_POST['id_champ']);
	$langue = clean($_POST['langue']);
	$id = clean($_POST['id_trad']);
	
	$titre_trad = clean($_POST['titre_trad']);
	$valeur_trad = cleanTextarea($_POST['valeur_trad']);
	
	//
	if ($id > 0) {
		$sql = "UPDATE fm_champ_trad SET titre_trad='$titre_trad', valeur_trad='$valeur_trad' WHERE id='$id'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
			
	} else {
		$sql = "INSERT INTO fm_champ_trad (id_champ, titre_trad, valeur_trad, langue)
			VALUES ('$id_champ', '$titre_trad', '$valeur_trad', '$langue')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
			
}
/* **************************** INSERER FORMULAIRE**************************** */
function addInscForm() {
	include('inc/ge_connexion.php');
	$id_form = clean($_POST['id_form']);
	$num_dep = clean($_POST['num_dep']);
	//initiation des variable
	$institution = 'Institution';
	$prenom = clean('Prénom');
	$nom = 'Nom';
	$telephone = clean('Téléphone privé');
	$email = 'Email';
	$adresse = 'Adresse';
	$npa = 'NPA';
	$lieu = 'Lieu';
	$commentaire = 'Commentaire';
	$champ1 = '';
	$champ2 = '';
	$champ3 = '';
	$champ4 = '';
	$champ5 = '';
	$champ6 = '';
	$champ7 = '';
	$champ8 = '';
	$champ9 = '';
	$champ10 = '';
	$champ11 = '';
	$champ12 = '';
	$champ13 = '';
	$champ14 = '';
	$champ15 = '';
	$champ16 = '';
	$champ17 = '';
	$champ18 = '';
	$champ19 = '';
	$champ20 = '';
	$remarque = 'Remarque';
	$statut = 'Statut';
	
	//recherche des champs et mise en place des doubles variables $$dataFormAtt['name']
	$sqlFormAtt = "SELECT titre, ordre FROM fm_champ WHERE id_form = '$id_form' AND type <> '99'"; // nom de la table ! requette
	$resultFormAtt = mysqli_query($connexion, $sqlFormAtt ) or die(mysqli_error());
	while ($dataFormAtt = mysqli_fetch_array($resultFormAtt)) {
		//$$dataFormAtt['ordre'] = cleanApp($dataFormAtt['titre']);
		$libelle_format = 'champ'.$dataFormAtt['ordre'];
		$$libelle_format = cleanApp($dataFormAtt['titre']);
	}
	// insertion de l'inscription.
	$sql = "INSERT INTO fm_inscription (id_form, num_dep, institution, prenom, nom, email, adresse, npa, lieu, telephone, commentaire,
		champ1, champ2, champ3, champ4, champ5, champ6, champ7, champ8, champ9, champ10, champ11, champ12, champ13, champ14, champ15, champ16, champ17, champ18, champ19, champ20, 
		remarque, statut)
		
		VALUES ('$id_form', '$num_dep', '$institution', '$prenom', '$nom', '$email', '$adresse', '$npa', '$lieu', '$telephone', '$commentaire',
		'$champ1', '$champ2', '$champ3', '$champ4', '$champ5', '$champ6', '$champ7', '$champ8', '$champ9', $champ10', $champ11', $champ12', $champ13', $champ14', $champ15', $champ16', $champ17', $champ18', $champ19', $champ20', 
		'$remarque', '$statut')";
		
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}

/** Inscriptions
 * 
 * addInsc()
 * modIns()
 * deleteInsc()
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

	$id_form = clean($_POST['id_form']);
	//initiation des variable
	$institution = clean($_POST['institution']);
	$prenom = clean($_POST['prenom']);
	$nom = clean($_POST['nom']);
	$telephone = clean($_POST['telephone']);
	$email = clean($_POST['email']);
	$adresse = clean($_POST['adresse']);
	$npa = clean($_POST['npa']);
	$lieu = clean($_POST['lieu']);
	$commentaire = cleanTextarea($_POST['commentaire']);
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
	$champ10 = '';
	foreach($_POST['champ10'] as $element) {
		$champ10 .= $element.',';
	} $champ10 = cleanArray($champ10);
	$champ11 = '';
	foreach($_POST['champ11'] as $element) {
		$champ11 .= $element.',';
	} $champ11 = cleanArray($champ11);
	$champ12 = '';
	foreach($_POST['champ12'] as $element) {
		$champ12 .= $element.',';
	} $champ12 = cleanArray($champ12);
	$champ13 = '';
	foreach($_POST['champ13'] as $element) {
		$champ13 .= $element.',';
	} $champ13 = cleanArray($champ13);
	$champ14 = '';
	foreach($_POST['champ14'] as $element) {
		$champ14 .= $element.',';
	} $champ14 = cleanArray($champ14);
	$champ15 = '';
	foreach($_POST['champ15'] as $element) {
		$champ15 .= $element.',';
	} $champ15 = cleanArray($champ15);
	$champ16 = '';
	foreach($_POST['champ16'] as $element) {
		$champ16 .= $element.',';
	} $champ16 = cleanArray($champ16);
	$champ17 = '';
	foreach($_POST['champ17'] as $element) {
		$champ17 .= $element.',';
	} $champ17 = cleanArray($champ17);
	$champ18 = '';
	foreach($_POST['champ18'] as $element) {
		$champ18 .= $element.',';
	} $champ18 = cleanArray($champ18);
	$champ19 = '';
	foreach($_POST['champ19'] as $element) {
		$champ19 .= $element.',';
	} $champ19 = cleanArray($champ19);
	$champ20 = '';
	foreach($_POST['champ20'] as $element) {
		$champ20 .= $element.',';
	} $champ20 = cleanArray($champ20);
	
	$sql = "INSERT INTO fm_inscription (id_form, institution, prenom, nom, email, adresse, npa, lieu, telephone, commentaire,
		champ1, champ2, champ3, champ4, champ5, champ6, champ7, champ8, champ9, champ10, champ11, champ12, champ13, champ14, champ15, champ16, champ17, champ18, champ19, champ20)
		
		VALUES ('$id_form', '$institution', '$prenom', '$nom', '$email', '$adresse', '$npa', '$lieu', '$telephone', '$commentaire',
		'$champ1', '$champ2', '$champ3', '$champ4', '$champ5', '$champ6', '$champ7', '$champ8', '$champ9', '$champ10', '$champ11', '$champ12', '$champ13', '$champ14', '$champ15', '$champ16', '$champ17', '$champ18', '$champ19', '$champ20')";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	
}
/* **************************** MODIFIER INSCRIPTION**************************** */
function modInsc() {
	if (file_exists('inc/ge_connexion.php')) {
		include('inc/ge_connexion.php');
	}
	//if connexion via extérieur
	if (file_exists('../cs_gestion/inc/ge_connexion.php')) {
		include('../cs_gestion/inc/ge_connexion.php');
	} 

	$id_form = clean($_POST['id_form']);
	$id_demande = clean($_POST['id_demande']);
	//initiation des variable
	$institution = clean($_POST['institution']);
	$prenom = clean($_POST['prenom']);
	$nom = clean($_POST['nom']);
	$telephone = clean($_POST['telephone']);
	$email = clean($_POST['email']);
	$adresse = clean($_POST['adresse']);
	$npa = clean($_POST['npa']);
	$lieu = clean($_POST['lieu']);
	$commentaire = cleanTextarea($_POST['commentaire']);
	
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
	$champ10 = '';
	foreach($_POST['champ10'] as $element) {
		$champ10 .= $element.',';
	} $champ10 = cleanArray($champ10);
	$champ11 = '';
	foreach($_POST['champ11'] as $element) {
		$champ11 .= $element.',';
	} $champ11 = cleanArray($champ11);
	$champ12 = '';
	foreach($_POST['champ12'] as $element) {
		$champ12 .= $element.',';
	} $champ12 = cleanArray($champ12);
	$champ13 = '';
	foreach($_POST['champ13'] as $element) {
		$champ13 .= $element.',';
	} $champ13 = cleanArray($champ13);
	$champ14 = '';
	foreach($_POST['champ14'] as $element) {
		$champ14 .= $element.',';
	} $champ14 = cleanArray($champ14);
	$champ15 = '';
	foreach($_POST['champ15'] as $element) {
		$champ15 .= $element.',';
	} $champ15 = cleanArray($champ15);
	$champ16 = '';
	foreach($_POST['champ16'] as $element) {
		$champ16 .= $element.',';
	} $champ16 = cleanArray($champ16);
	$champ17 = '';
	foreach($_POST['champ17'] as $element) {
		$champ17 .= $element.',';
	} $champ17 = cleanArray($champ17);
	$champ18 = '';
	foreach($_POST['champ18'] as $element) {
		$champ18 .= $element.',';
	} $champ18 = cleanArray($champ18);
	$champ19 = '';
	foreach($_POST['champ19'] as $element) {
		$champ19 .= $element.',';
	} $champ19 = cleanArray($champ19);
	$champ20 = '';
	foreach($_POST['champ20'] as $element) {
		$champ20 .= $element.',';
	} $champ20 = cleanArray($champ20);
	
	$remarque = cleanTextarea($_POST['remarque']);
	$statut = clean($_POST['statut']);
	
	
	$sql = "UPDATE fm_inscription SET institution='$institution', prenom='$prenom', nom='$nom', email='$email', adresse='$adresse', npa='$npa', lieu='$lieu', telephone='$telephone', commentaire='$commentaire',
		champ1='$champ1', champ2='$champ2', champ3='$champ3', champ4='$champ4', champ5='$champ5', champ6='$champ6', champ7='$champ7', champ8='$champ8', champ9='$champ9', champ10='$champ10', champ11='$champ11', champ12='$champ12', champ13='$champ13', champ14='$champ14', champ15='$champ15', champ16='$champ16', champ17='$champ17', champ18='$champ18', champ19='$champ19', champ20='$champ20', 
		remarque='$remarque', statut='$statut' WHERE id='$id_demande'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
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
	$sql = "DELETE FROM fm_inscription WHERE id ='$_POST[id]'";	
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}
?>