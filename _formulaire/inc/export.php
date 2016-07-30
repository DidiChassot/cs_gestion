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

// Nom du fichier final
$fileName = 'export_form'.$id_form.'.csv';

//requete suivant la rubrique
$outputCsv .= utf8_decode('Institution;Prénom;Nom;Adresse;NPA;Lieu;Téléphone;Email;Commentaire;Champ1;Champ2;Champ3;Champ4;Champ5;Champ6;Champ7;Champ8;Champ9;Remarques;Statut;Date');  //afficher les entête désirées
            $outputCsv .= "\n";

$requete = "SELECT institution, prenom, nom, email, adresse, npa, lieu, telephone, commentaire, champ1, champ2, champ3, champ4, champ5, champ6, champ7, champ8, champ9, remarque, statut, date_crea
	    FROM fm_inscription
	    WHERE id_form = '$id_form'
	    AND actif = '1'
	    AND ((date_crea BETWEEN '$start' AND '$end') OR (num_dep = '$num_dep'))";
$sql = mysqli_query($connexion, $requete) or die(mysqli_error());
while($Row = mysqli_fetch_assoc($sql)) {
	
	// On parcours $Row et on ajout chaque valeur à cette ligne
	foreach($Row as $clef => $valeur)
		if ($clef == 'date_crea') {$outputCsv .= date($valeur).';';}
		else {$outputCsv .= $valeur.';'; } //reprend les choses sélectionnés dans la requete
	    
	// Suppression du ; qui traine à la fin
	$outputCsv = rtrim($outputCsv, ';');
    
	// Saut de ligne
	$outputCsv .= "\n";
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