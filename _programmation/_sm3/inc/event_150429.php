<?php
/*** INC/EVENT - PROGRAMMATION - SM3***/
// 
//Cindy Chassot 26.01.2015 - 02.02.15
//© Cinémathèque suisse

/*** CONSTANTES***/
$caract = array(utf8_decode("'"));
$new_caract = array(utf8_encode("&acute;")); //modification de l'apostrophe pour l'affichage JSON
$new_caractSql = array(utf8_encode("''")); // modification de l'apostrophe POUR REQUETE SQL

/*  AFFICHAGE DES SEANCES ET EVENTS DANS LE CALENDRIER*/

//1. Les événemements
//2. Toutes les séances du bulletin
     //- par bulletin sélectioné
     //- par film sélectionné
     //- par cycle sélectionné


//affichage de la variable (json)
echo "events: ["; //ouverture du calendrier

/********* 1. LES EVENEMENTS *********/

//requête qui récupère les événements
$sql_allday = "SELECT * FROM pm_allday WHERE start <= '$end_bulletin' OR end <= '$start_bulletin' AND actif = 'a' ORDER BY start";
$result_allday = mysqli_query($connexion, $sql_allday) or die(mysqli_error());
while ($data_allday = mysqli_fetch_array($result_allday)) {
     $titre = str_replace($caract, $new_caract, utf8_encode($data_allday["titre"]));

   echo "{
          id_seance: '".$data_allday['id']."',
          title: '".$titre."',
          start: '".$data_allday['start']."',";
   // END                 
   if ($data_allday['end']=='0000-00-00') {
        $end = NULL;
   } else {
        $end = $data_allday['end'];
        $end = strtotime($end . " +1 day");
        $end = date("Y-m-d", $end);
        
        echo "end: '".$end."',";
   }
  
   echo "
          textColor: '#FFFFFF',
          backgroundColor: '#444444',
        },";
}


/********* 2. SEANCES BULLETIN *********/

// requête qui récupère les séances du bulletin envoyé
$end_bulletin = strtotime($end_bulletin . " +1 day");
$end_bulletin = date("Y-m-d", $end_bulletin);

$sql = "SELECT id_seance FROM pm_seance
        WHERE id_seance = 1";

if ($action=='film') {
  /************************/  
  /***SELECTION PAR FILM***/  
  /************************/  
    $sql = "SELECT *, pm_salle.titre AS titre_salle, pm_seance.titre AS titre_seance FROM pm_seance
            JOIN pm_film_seance
            ON pm_seance.id_seance = pm_film_seance.id_seance
            JOIN pm_salle
            ON pm_salle.id_salle = pm_seance.id_salle
            WHERE pm_film_seance.id_film = '$id_film'
            AND pm_seance.actif = 'a'
            AND pm_seance.start > '$start_bulletin'
            AND pm_seance.start <= '$end_bulletin'";
            
}elseif ($action=='cycle') {
  /************************/  
  /***SELECTION PAR CYCLE**/  
  /************************/  
    $sql = "SELECT *, pm_salle.titre AS titre_salle, pm_seance.titre AS titre_seance FROM pm_seance
            JOIN pm_film_seance
            ON pm_seance.id_seance = pm_film_seance.id_seance
            JOIN pm_salle
            ON pm_salle.id_salle = pm_seance.id_salle
            WHERE pm_film_seance.id_cycle = '$id_cycle'
            AND pm_seance.actif = 'a'
            AND pm_seance.start > '$start_bulletin'
            AND pm_seance.start <= '$end_bulletin'";
            
} elseif ($id_bulletin) {
  /////***********************/  
  /***SELECTION DU BULLETIN***/  
  /////***********************/  
    $sql = "SELECT *, pm_salle.titre AS titre_salle, pm_seance.titre AS titre_seance FROM pm_seance
            JOIN pm_salle
            ON pm_salle.id_salle = pm_seance.id_salle
            WHERE pm_seance.actif = 'a'
            AND pm_seance.start > '$start_bulletin'
            AND pm_seance.start <= '$end_bulletin'";
            
} 

/*** REQUETE SQL POUR AFFICHER LES SéANCES**/
$result = mysqli_query($connexion, $sql) or die(mysqli_error());
while ($data = mysqli_fetch_array($result)) {
    // sélection des informations du (1er) film pour afficher le titre et la couleur
    $sql_movie = "SELECT * FROM pm_film_seance
                JOIN pm_film
                ON pm_film_seance.id_film = pm_film.id_film
                JOIN pm_cycle
                ON pm_film_seance.id_cycle = pm_cycle.id_cycle
                WHERE pm_film_seance.id_seance='$data[id_seance]'
                ORDER BY pm_film_seance.ordre";
    $result_movie = mysqli_query($connexion, $sql_movie) or die(mysqli_error());
    $data_movie = mysqli_fetch_array($result_movie);
    
    

    $titre = '';
    $pos = ''; //pas d'affichage si aucune informations
    /****** Titre de séance ******/      
    if($data['titre_seance']) {
        //remplacement de caractères
        $titre = str_replace($caract, $new_caractSql, $data["titre_seance"]);
        
        // affichage du numéro de la séance
        $start_seance = $data['start'];
        $pos = 1;
        $sql_pos = "SELECT * FROM pm_seance
                    WHERE actif = 'a'
                    AND titre = '$titre'
                    AND id_seance > '9'
                    AND start < '$start_seance'
                    ORDER BY start";
        $result_pos = mysqli_query($connexion, $sql_pos) or die(mysqli_error());
        while ($data_pos = mysqli_fetch_array($result_pos)) {
            $pos +=1;
        }
        
        // remplacement de l'apostrophe POUR L'AFFICHAGE
        $titre = str_replace($caract, $new_caract, utf8_encode($data["titre_seance"]));
        
    } else {
        // affichage du numéro de la séance
        $id_film = $data_movie['id_film'];
        $start_seance = $data['start'];
        $pos = '';
        if ($data_movie['titre_o']) $pos = 1; // numéro seulement si 1film dans séance
        $sql_pos = "SELECT * FROM pm_film_seance
                    JOIN pm_seance
                    ON pm_film_seance.id_seance = pm_seance.id_seance
                    WHERE pm_film_seance.id_film = '$id_film'
                    AND pm_seance.actif = 'a'
                    AND pm_seance.id_seance > '9'
                    AND start < '$start_seance'
                    ORDER BY start";
        $result_pos = mysqli_query($connexion, $sql_pos) or die(mysqli_error());
        if(mysqli_num_rows($result_pos) > 0) {
            while ($data_pos = mysqli_fetch_array($result_pos)) {
                $pos +=1;
            }
        }
        $titre = str_replace($caract, $new_caract, utf8_encode($data_movie['titre_o']));
    }
    //salle et numéro de la projection
    $titre .= '<br />'.substr(utf8_encode($data['titre_salle']), 0, 3).' | '.$pos.' ';
    //commenaires
    if($data['commentaire']) {$titre .= '| '.str_replace($caract, $new_caract, utf8_encode($data['commentaire']));}
    
/***génération des séances au format json...***/
   echo " {
           id_seance: '".$data['id_seance']."',
           title: '".$titre."',
           start: '".$data['start']."',
           end: '".$data['end']."',";
               
           //border
           if ($id == $data['id_seance']) { // border pour la sélection
               echo "borderColor: '#ffb300',";
           } elseif ($data['event']==1){ // border pour les événements
               echo "borderColor: '#000a01',";
           }
           
           //couleur du cycle
           echo "backgroundColor: '".$data_movie['couleur']."',";
    echo "  },";
}

echo '],'; //fermeture du calendrier
?>