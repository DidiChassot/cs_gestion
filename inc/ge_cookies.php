<?php
/*** COOKIES ***/
// 
//Cindy Chassot 21.01.2015 22.01.2015
//© Cinémathèque suisse
$internal = false;
$onglet = false;
$sm = false;
$deconnexion = false;

foreach ($_GET as $key => $value) {
	$$key = $value;
}

//link interne
if($internal) {
	$temps = 2*24*3600;
	setcookie('sousMenu', $_GET['sm'], time() + $temps); //onglet sous-menu
	setcookie('role', $_GET['cat'], time() + $temps); //role de la page "visionneur" ou "editeur"
	
	foreach ($var as $key => $value) {
		$link = $link.$key.'='.$value.'&';
	    }
	redirGestion ($_SERVER['PHP_SELF'].'?'.$link);
	
} else {
	//Connexion -> création du cookie geLogCon
	//utilisation : $_COOKIE['geLogCon']
	if (!isset($_POST['pseudo'])) {//On est dans la page de formulaire
	    $message='';
	} elseif (empty($_POST['pseudo']) || empty($_POST['password'])) {//Oublie d'un champ
		$message = '<p>Une erreur s\'est produite pendant votre identification. Vous devez remplir tous les champs.</p>';
		$titreApp = "Connexion";
		
	} else {//On check le mot de passe
		$loginConn = $connO->real_escape_string($_POST['pseudo']);
		$res = $connO->query("SELECT * FROM ge_user WHERE login = '".$loginConn."'");
		$data = $res->fetch_assoc();
		//$data=$query->fetch();
		if ($data['password'] == md5($_POST['password'])) {// Acces OK !
			// on définit une durée de vie de notre cookie (en secondes), donc un an dans notre cas
			$temps = 2*24*3600;
			// on envoie un cookie de nom geLogCon portant la valeur LA GLOBULE
			setcookie ("geLogCon", $data['login'], time() + $temps);
			setcookie('indexApp', '_indexApp', time() + $temps); //onglet menu principal
			$message = 'connectée!';
			redirGestion ('index.php');
				
		} else {// Acces pas OK !
		    $message = '<p>Une erreur s\'est produite pendant votre identification.<br /> Le mot de passe ou le pseudo entré n\'est pas correcte.</p>';
		    $titreApp = "Connexion";
		}
	    $res->close();
	}
	
	//changement d'onglet
	if ($onglet) { // chaque onglet du menu principal
		$temps = 2*24*3600;
		setcookie('indexApp', $_GET['onglet'], time() + $temps); //onglet menu principal
		setcookie('sousMenu', '_sm', time() + $temps); //onglet sous-menu
		redirGestion ('index.php');
	}
	
	//changement de sous-menu
	if ($sm) { // chaque onglet du sous-menu
		$temps = 2*24*3600;
		setcookie('sousMenu', $_GET['sm'], time() + $temps); //onglet sous-menu
		setcookie('role', $_GET['cat'], time() + $temps); //role de la page "visionneur" ou "editeur"
		redirGestion ('index.php');
	}
	
	//déconnexion
	if ($deconnexion == true) {
		setcookie('geLogCon', NULL, -1);
		setcookie('indexApp', NULL, -1);
		//setcookie('sousMenu', NULL, -1);
		//setcookie('role', NULL, -1);
		redirGestion ('index.php');
		mysqli_close($connexion);
		
		session_destroy();
	}
}
?>
