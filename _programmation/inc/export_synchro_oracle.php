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
$outputCsv .= utf8_decode('Cycle;ID FILM;Titre');  //afficher les entête désirées
            $outputCsv .= "\n";

//requete suivant la rubrique
$requete = "SELECT pm_cycle.titre, pm_film.id_film, CONCAT(prefix_titre_o, ' ', titre_o) AS titre_c, numero FROM pm_cycle
	    JOIN pm_bulletin_cycle
	    ON pm_bulletin_cycle.id_cycle = pm_cycle.id_cycle
	    JOIN pm_cycle_film
	    ON pm_cycle.id_cycle = pm_cycle_film.id_cycle
	    JOIN pm_film
	    ON pm_cycle_film.id_film = pm_film.id_film
	    JOIN pm_bulletin
	    ON pm_bulletin.id_bulletin = pm_bulletin_cycle.id_bulletin
	    WHERE pm_bulletin_cycle.id_bulletin='$bu'
	    AND pm_cycle.actif = 'a'
	    ORDER BY pm_bulletin_cycle.ordre";  //attention à la variable de rubrique
$sql = mysqli_query($connexion, $requete ) or die(mysqli_error());
if(mysqli_num_rows($sql) > 0) {

    while($Row = mysqli_fetch_assoc($sql)) {
	$requete2 = "SELECT base FROM pm_film_film WHERE id_film = '$Row[id_film]' AND base = 1";
	$sql2 = mysqli_query($connexion, $requete2 ) or die(mysqli_error());
	if(mysqli_num_rows($sql2) > 0) {
		
		// On parcours $Row et on ajout chaque valeur à cette ligne
		foreach($Row as $clef => $valeur) {
			if ($clef == 'numero') {$$clef = $valeur;}
			else {$outputCsv .= $valeur.';'; }
			
		}
		    
		// Suppression du ; qui traine à la fin
		$outputCsv = rtrim($outputCsv, ';');
	    
		// Saut de ligne
		$outputCsv .= "\n";
		
	} else {
	}
	$fileName = 'export_synchro_typo_'.$numero.'.csv';
    }

} else {
    exit('Aucune donnée à enregistrer.');
}

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