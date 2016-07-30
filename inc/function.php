<?php
/*** FUNCTION ***/
// 
//Cindy Chassot 21.01.2015 - 23.02.2015
//© Cinémathèque suisse

//Redirection de l'url à l'appel
function redirGestion($url){
	if (headers_sent()){
		print('<meta http-equiv="refresh" content="0;URL='.$url.'">');
	} else {
		header("Location: $url");
	}
}

// Si erreur -> retour du message
function erreur($err='')
{
   $mess=($err!='')? $err:'Une erreur inconnue s\'est produite';
   exit('<p>'.$mess.'</p>
   <p>Cliquez <a href="./index.php">ici</a> pour revenir à la page d\'accueil</p></div></body></html>');
}

/***Remplacement des jours en français***/
function dateFrancais($var) {
	
	$text = array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
	$modif = array('lu', 'ma', 'me', 'je', 've', 'sa', 'di', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');
	$result = str_replace($text, $modif, $var);

	return $result;
}
?>
