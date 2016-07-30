<?php
/*** Fonction Administration***
 *
 * Cindy Chassot 15.04.2015
 * © Cinémathèque suisse
 */

/**
* Nettoyage des variables envoyées dans un input
*
* @param integer|string $var variable envoyée par POST
* @return cleanVar Retourne la variable nettoyée
*/
function clean($var) {
	//pour l'apostrophe
	$text = array("'");
	$result = array("''");
	$var = str_replace($text, $result, $var);
	//effacement d'espaces et retours maladroit
	$cleanVar = utf8_decode(trim($var));
	
	return $cleanVar;
}

/**
* Nettoyage des variables envoyées dans un textarée
*
* @param integer|string $var variable envoyée par POST
* @return cleanVar Retourne la variable nettoyée
*/
function cleanTextarea($var) {
	//remplacer les balises
	$text = array('<p>', '</p>', '<em>', '</em>', '<strong>', '</strong>');
	$result = array('', '<br>', '<i>', '</i>', '<b>', '</b>');
	$cleanVar = str_replace($text, $result, $var);
	//lancement de clean()
	$cleanVar = clean($cleanVar);

	return $cleanVar;
}

/* ****************************** INSERTION-MODIFICATION ROLE & RUBRIQUES****************************** */
/**
* Enregistrement du role dans ge_role
*
* @param 
* @return $id_role pour afficher les informations dans le formulaire
*/
function saveRole() {
	include('ge_connexion.php');
	$intitule = clean($_POST['intitule']);
	$id_app = clean($_POST['id_app']);
	$id_role = clean($_POST['id_role']);
	$rubrique = $_POST['rubrique'];
	
	// 1. GE_ROLE
	if($id_role > 0) {
		// A. Mise à jour
		$sql = "UPDATE ge_role SET intitule='$intitule' WHERE id_role='$id_role'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
	} else {
		// A. Insertion d'un nouveau role
		$sql = "INSERT INTO ge_role (id_app, intitule)
				VALUES ('$id_app', '$intitule')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
		// B. récupération de l'id du role
		$sqlRole = "SELECT id_role FROM ge_role WHERE intitule ='$intitule' AND id_app ='$id_app' ORDER BY id_role DESC"; // nom de la table ! requette
		$resultRole = mysqli_query($connexion, $sqlRole ) or die(mysqli_error());
		$dataRole = mysqli_fetch_array($resultRole);
		$id_role = $dataRole['id_role'];
	}
		
		
	// 2. Suppression des entrées déjà existantes (sauf 99)
	$sql = "DELETE FROM ge_rubrique WHERE id_role = '$id_role' AND rubrique <> '99'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
	// 3. insertion des nouvelles rubriques
	foreach($rubrique as $cle => $element) {
		// Insertion des rubriques
		$sql = "INSERT INTO ge_rubrique (id_role, rubrique, categorie)
			VALUES ('$id_role', '$cle', '$element')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
	
	return $id_role;
}

/* ****************************** SUPPRESSION ROLE & RUBRIQUES ****************************** */
/**
* Suppression du role dans ge_role
*
* @param 
* @return
*/
function deleteRole() {
	include('ge_connexion.php');
	$id_app = clean($_POST['id_app']);
	$id_role = clean($_POST['id_role']);
	
	// 1. suppression
	$sql = "DELETE FROM ge_role WHERE id_role='$id_role'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	// 2. Suppression des entrées déjà existantes (sauf 99)
	$sqlRubrique = "DELETE FROM ge_rubrique WHERE id_role = '$id_role'";
	$resultRubrique = mysqli_query($connexion, $sqlRubrique) or die(mysqli_error());
}


/* ****************************** INSERTION-MODIFICATION USERACCES****************************** */
/**
* Enregistrement du role dans ge_role
*
* @param 
* @return $id_role pour afficher les informations dans le formulaire
*/
function saveUseracces() {
	include('ge_connexion.php');
	$id_user = clean($_POST['id_user']);
	$id_role = clean($_POST['id_role']);
	$id_acces = clean($_POST['id_acces']);
	
	// 1. GE_USER
	if($id_acces>0) {
		// A. Mise à jour
		$sql = "UPDATE ge_useracces SET id_role='$id_role' WHERE id_user='$id_user' AND id_acces='$id_acces'";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
	} else {
		// A. Insertion d'un nouveau role
		$sql = "INSERT INTO ge_useracces (id_user, id_role)
				VALUES ('$id_user', '$id_role')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	}
}

/* ****************************** SUPPRESSION USERACCES ****************************** */
/**
* Suppression des acces dans ge_useracces
*
* @param 
* @return
*/
function deleteUseracces() {
	include('ge_connexion.php');
	$id_acces = clean($_POST['id_acces']);
	
	// 1. suppression
	$sql = "DELETE FROM ge_useracces WHERE id_acces='$id_acces'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
}

/* ****************************** INSERTION-MODIFICATION User****************************** */
/**
* Enregistrement du role dans ge_role
*
* @param 
* @return $id_role pour afficher les informations dans le formulaire
*/
function saveUser() {
	include('ge_connexion.php');
	$id_user = clean($_POST['id_user']);
	$login = clean($_POST['login']);
	if($_POST['password']) {$password = md5(clean($_POST['password']));}
	
	// 1. GE_USER
	if($id_user > 0) {
		// A. Mise à jour
		if($password) {
			$sql = "UPDATE ge_user SET login='$login', password='$password' WHERE id_user='$id_user'";
		} else {
			$sql = "UPDATE ge_user SET login='$login' WHERE id_user='$id_user'";
		}
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
		return $id_user;
		
	} else {
		// A. Insertion d'un nouveau role
		$sql = "INSERT INTO ge_user (login, password)
				VALUES ('$login', '$password')";
		$result = mysqli_query($connexion, $sql) or die(mysqli_error());
		
		// B. récupération de l'id du role
		$sqlUser = "SELECT id_user FROM ge_user WHERE login ='$login' AND password ='$password' ORDER BY id_user DESC"; // nom de la table ! requette
		$resultUser = mysqli_query($connexion, $sqlUser ) or die(mysqli_error());
		$dataUser = mysqli_fetch_array($resultUser);
		$id_user = $dataUser['id_user'];
		
		return $id_user;
	}
}

/* ****************************** SUPPRESSION USER ****************************** */
/**
* Suppression des acces dans ge_useracces
*
* @param 
* @return
*/
function deleteUser() {
	include('ge_connexion.php');
	$id_user = clean($_POST['id_user']);
	
	// 1. suppression ge_user
	$sql = "DELETE FROM ge_user WHERE id_user='$id_user'";
	$result = mysqli_query($connexion, $sql) or die(mysqli_error());
	
	// 2. suppression ge_useracces
	$sql2 = "DELETE FROM ge_useracces WHERE id_user='$id_user'";
	$result2 = mysqli_query($connexion, $sql2) or die(mysqli_error());
	
}

?>