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
$outputCsv .= utf8_decode('Nom;Prénom;Place;Categorie');  //afficher les entête désirées
            $outputCsv .= "\n";

//requete suivant la rubrique
$requete = "SELECT nom, prenom, place, re_categorie.titre
	    FROM re_inscription
	    JOIN re_categorie
	    ON re_categorie.id_categorie = '$id_categorie'
	    WHERE id_reserv = '$id_reserv'
	    AND actif='1'
	    ORDER BY nom";  //attention à la variable de rubrique
$sql = mysqli_query($connexion, $requete ) or die(mysqli_error());
if(mysqli_num_rows($sql) > 0) {

    while($Row = mysqli_fetch_assoc($sql)) {
        
	// On parcours $Row et on ajout chaque valeur à cette ligne
	foreach($Row as $clef => $valeur)
		if ($clef == 'date_crea') {$outputCsv .= date($valeur).';';}
		else {$outputCsv .= $valeur.';'; } //reprend les choses sélectionnés dans la requete
	    
	// Suppression du ; qui traine à la fin
	$outputCsv = rtrim($outputCsv, ';');
    
	// Saut de ligne
	$outputCsv .= "\n";
	$fileName = $id_event.'_'.$id_categorie.'_'.utf8_encode($Row['titre']).'.csv';
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