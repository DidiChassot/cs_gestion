<?php
// Connexion MySQL
if (file_exists('inc/ge_connexion.php')) {
	include('inc/ge_connexion.php');
}
//if connexion via extérieur
if (file_exists('../../inc/ge_connexion.php')) {
	include('../../inc/ge_connexion.php');
}

foreach ($_GET as $key => $value) { //récupération de la variable envoyer en GET
	$$key = $value;
}
// la variable qui va contenir les données CSV
$outputCsv = '';

//requete suivant la rubrique
$outputCsv .= utf8_decode('Film;date;url');  //afficher les entête désirées
            $outputCsv .= "\n";

//requete suivant la rubrique
$requete = "SELECT titre_o, text_info, url FROM `re_event`
	JOIN re_reserv
	ON re_reserv.id_event = re_event.id_event
	JOIN pm_film
	ON pm_film.id_film = re_event.id_film
	WHERE concours IS NOT NULL
	AND date >= CURDATE()";  
$sql = mysqli_query($connexion, $requete ) or die(mysqli_error());
if(mysqli_num_rows($sql) > 0) {

    while($Row = mysqli_fetch_assoc($sql)) {
        
	// On parcours $Row et on ajout chaque valeur à cette ligne
	foreach($Row as $clef => $valeur) 
		if ($clef == 'url') {$outputCsv .= 'http://action.cinematheque.ch/concours/?url='.$valeur.';';}
		else {$outputCsv .= html_entity_decode($valeur).';'; } //reprend les choses sélectionnés dans la requete
		
	// Suppression du ; qui traine à la fin
	$outputCsv = rtrim($outputCsv, ';');
    
	// Saut de ligne
	$outputCsv .= "\n";
    }

} else {
    exit('Aucune donnée à enregistrer.');
}
// Nom du fichier final
$fileName = 'export_concours.csv';

// Entêtes (headers) PHP qui vont bien pour la création d'un fichier Excel CSV
header("Content-disposition: attachment; filename=".$fileName);
header("Content-Type: application/force-download");
header("Content-Transfer-Encoding: application/vnd.ms-excel\n");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0, public");
header("Expires: 0");

echo $outputCsv;
exit();
?>