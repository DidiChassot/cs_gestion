<?php
include('../../../inc/ge_connexion.php');

$id_film=$_POST['id_film']; //'mov'=id_film|'pan
$id_seance=$_POST['id_seance']; //id de la séance
$id_cycle=$_POST['cycle'];
$cat=$_POST['cat'];
$ordre='';
 
/****Ajout de film***/
if ($cat=='mov') {
    //requète sur pm_film_seance pour récupérer l'ordre
    $sql_search = "SELECT ordre FROM pm_film_seance WHERE id_seance='$id_seance' ORDER BY ordre DESC";
    $result_search = mysqli_query($connexion, $sql_search) or die(mysqli_error());
    //récupération des donneés	
    if($data_search = mysqli_fetch_array($result_search)) {
	$ordre = $data_search['ordre'];
        $ordre = $ordre + 1;
	// recherche du "end" de la séance
	$sql_seance = "SELECT end FROM pm_seance WHERE id_seance='$id_seance'";
	$result_seance = mysqli_query($connexion, $sql_seance) or die(mysqli_error());
	$data_seance = mysqli_fetch_array($result_seance);
	$end = strtotime($data_seance['end']);
    } else {
	$ordre = 1;
	//reprend le "start" de la séance.
	$sql_seance = "SELECT start FROM pm_seance WHERE id_seance='$id_seance'";
	$result_seance = mysqli_query($connexion, $sql_seance) or die(mysqli_error());
	$data_seance = mysqli_fetch_array($result_seance);
	$end = strtotime($data_seance['start']);
    }
    
    //ajout du lien du film
    $sql_add = "INSERT INTO pm_film_seance (id_film, id_seance, id_cycle, ordre, categorie) VALUES ('$id_film', '$id_seance', '$id_cycle', '$ordre', '$cat')";
    $result_add = mysqli_query($connexion, $sql_add) or die(mysqli_error());
    
    //récupération de durée de la copie pour l'ajouter à la séance
    $sql_copie = "SELECT duree FROM pm_copie WHERE id_film='$id_film' ORDER BY statut DESC";
    $result_copie = mysqli_query($connexion, $sql_copie) or die(mysqli_error());
    $data_copie = mysqli_fetch_array($result_copie);
	//add duréee
	$end = $end + ($data_copie['duree']*60);
	$end = date("Y-m-d G:i:s", $end);
	
    //mise à jour du end de la séance avec la durée du film
    $sql_up = "UPDATE pm_seance SET end='$end' WHERE id_seance='$id_seance'";
    $result_up = mysqli_query($connexion, $sql_up) or die(mysqli_error());
    
/****Ajout du panier***/
} elseif ($cat=='pan') {
    //récupérer les informations de la séance dans le panier (sauf start,end)
    $sql_sel = "SELECT * FROM pm_seance WHERE id_seance='$id_film'";
    $result_sel = mysqli_query($connexion, $sql_sel) or die(mysqli_error());	
    while ($data_seance = mysqli_fetch_array($result_sel)) {
        $titre = $data_seance['titre'];
        $salle = $data_seance['id_salle']; // requete à faire
        $event = $data_seance['event'];
        $commentaire = $data_seance['commentaire'];
    }
    //ajouter à la séance les informations
    $sql_up = "UPDATE pm_seance SET titre='$titre', id_salle='$salle', event='$event', commentaire='$commentaire' WHERE id_seance='$id_seance'";
    $result_up = mysqli_query($connexion, $sql_up) or die(mysqli_error());
    //récupérer les films liés à la séance et dupliquer les entrer avec la nouvelle séance
    $sql_selfi = "SELECT * FROM pm_film_seance WHERE id_seance='$id_film'";
    $result_selfi = mysqli_query($connexion, $sql_selfi) or die(mysqli_error());	
    while ($data_selfi = mysqli_fetch_array($result_selfi)) {
        $id_movie = $data_selfi['id_film'];
        $id_cycle = $data_selfi['id_cycle'];
        $ordre = $data_selfi['ordre'];
        $cat = $data_selfi['categorie'];
        
        $sql_add = "INSERT INTO pm_film_seance (id_film, id_seance, id_cycle, ordre, categorie) VALUES ('$id_movie', '$id_seance', '$id_cycle', '$ordre', '$cat')";
        $result_add = mysqli_query($connexion, $sql_add) or die(mysqli_error());
    }
    
/****Ajout de la corbeille***/
} elseif ($cat=='corb') {
    //récupérer les informations de la séance sélectionnée
    $sql_sel = "SELECT start, end FROM pm_seance WHERE id_seance='$id_seance'";
    $result_sel = mysqli_query($connexion, $sql_sel) or die(mysqli_error());	
    $data_sel = mysqli_fetch_array($result_sel);
        $start = $data_sel['start'];
        $end = $data_sel['end'];
    
    //modifier l'ancienne séance avec la nouvelle date
    $sql_up = "UPDATE pm_seance SET start='$start', end='$end', actif='a' WHERE id_seance='$id_film'";
    $result_up = mysqli_query($connexion, $sql_up) or die(mysqli_error());
    //suppression de l'entrée inutile
    $sql_del = "DELETE FROM pm_seance WHERE id_seance='$id_seance'";
    $result_del = mysqli_query($connexion, $sql_del) or die(mysqli_error());
    
}
?>