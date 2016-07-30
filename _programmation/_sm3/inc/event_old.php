<?php
/*** INC/EVENT - PROGRAMMATION - SM3***/
// 
//Cindy Chassot 26.01.2015 - 30.03.15
//© Cinémathèque suisse

/*  AFFICHAGE DES SEANCES ET EVENTS DANS LE CALENDRIER*/

//1. Les événemements
//2. Toutes les séances du bulletin
//3. Les séances du film sélectionné


//affichage de la variable (json)
echo "events: [";

/********* 1. LES EVENEMENTS *********/

//requête qui récupère les événements
$sql_allday = "SELECT * FROM pm_allday WHERE start <= '$end_bulletin' OR end <= '$start_bulletin' AND actif = 'a' ORDER BY start";
$result_allday = mysqli_query($connexion, $sql_allday) or die(mysqli_error());
while ($data_allday = mysqli_fetch_array($result_allday)) {
/***génération des séances au format json...***/
        // remplacement de l'apostrophe
        $caract = array(utf8_decode("'"));
        $new_caract = array(utf8_encode("&acute;"));
        $titre = str_replace($caract, $new_caract, utf8_encode($data_allday["titre"]));

   echo "{
                    id_seance: '".$data_allday['id']."',
                    title: '".$titre."',
                    start: '".$data_allday['start']."',";
   if ($data_allday['end']=='0000-00-00') {
        $end = NULL;
   } else {
        $end = $data_allday['end'];
        $end = strtotime($end . " +1 day");
        $end = date("Y-m-d", $end);
        
        echo "end: '".$end."',";
   }
  // echo "           end: '".$data_allday['end']."',";
   echo "
                    textColor: '#FFFFFF',
                    backgroundColor: '#444444',
        },";
}


/********* 2. SEANCES BULLETIN *********/

// requête qui récupère les séances du bulletin envoyé
$end_bulletin = strtotime($end_bulletin . " +1 day");
$end_bulletin = date("Y-m-d", $end_bulletin);

if($calendar=='bulletin') {
  /************************/  
  /***SELECTION DU BULLETIN***/  
  /************************/  
    $sql = "SELECT *, pm_salle.titre AS titre_salle, pm_seance.titre AS titre_seance FROM pm_seance
            JOIN pm_salle
            ON pm_salle.id_salle = pm_seance.id_salle
            WHERE pm_seance.actif = 'a'
            AND pm_seance.start > '$start_bulletin'
            AND pm_seance.start <= '$end_bulletin'";
    $result = mysqli_query($connexion, $sql) or die(mysqli_error());
    while ($data = mysqli_fetch_array($result)) {
        // sélection des informations du (1er) film   
        $sql_movie = "SELECT * FROM pm_film_seance, pm_film, pm_cycle
                    WHERE pm_film_seance.id_seance='$data[id_seance]'
                    AND pm_film_seance.id_film = pm_film.id_film
                    AND pm_film_seance.id_cycle = pm_cycle.id_cycle
                    ORDER BY pm_film_seance.ordre";
        $result_movie = mysqli_query($connexion, $sql_movie) or die(mysqli_error());
        $data_movie = mysqli_fetch_array($result_movie);
        
        // affichage pour le titre (séance ou film)
        $titre = '';
        $pos = '';
        if($data['titre_seance']) {
            //modification du titre POUR REQUETE SQL
            $caract = array(utf8_decode("'"));
            $new_caract = array(utf8_encode("''"));
            $titre = str_replace($caract, $new_caract, $data["titre_seance"]);
            
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
            $caract = array(utf8_decode("'"));
            $new_caract = array(utf8_encode("&acute;"));
            $titre_affiche = str_replace($caract, $new_caract, $data["titre_seance"]);
            $titre = html_entity_decode(utf8_encode($titre_affiche));
            
        } else {
            // affichage du numéro de la séance
            $id_film = $data_movie['id_film'];
            $start_seance = $data['start'];
            $pos = '';
            if ($data_movie['titre_o']) $pos = 1; // numéro seulement si 1film dans séance
            $sql_pos = "SELECT * FROM pm_film_seance, pm_seance
                        WHERE pm_film_seance.id_film = '$id_film'
                        AND pm_film_seance.id_seance = pm_seance.id_seance
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
            // remplacement de l'apostrophe
            $caract = array(utf8_decode("'"));
            $new_caract = array(utf8_encode("&acute;"));
            $titre_affiche = str_replace($caract, $new_caract, $data_movie['titre_o']);
            // titre de la séance
            $titre = utf8_encode($titre_affiche);
        }
        $titre .= '<br />'.substr(utf8_encode($data['titre_salle']), 0, 3).' | '.$pos.' ';
        if ($data['commentaire']) {$titre .= '| '.utf8_encode($data['commentaire']);}
        
    /***génération des séances au format json...***/
       echo "
                {
                        id_seance: '".$data['id_seance']."',
                        title: '".$titre."',
                        start: '".$data['start']."',
                        end: '".$data['end']."',";
                        // border pour les événements
                        if ($data['event']==1){
                            echo "borderColor: '#000a01',";
                        } 
                        //affichage de la couleur du cycle
                        echo "backgroundColor: '".$data_movie['couleur']."',";
                echo "  },";
    }
} elseif($action=='film') {
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
    $result = mysqli_query($connexion, $sql) or die(mysqli_error());
    while ($data = mysqli_fetch_array($result)) {
        // sélection des informations du (1er) film   
        $sql_movie = "SELECT * FROM pm_film_seance, pm_film, pm_cycle
                    WHERE pm_film_seance.id_seance='$data[id_seance]'
                    AND pm_film_seance.id_film = pm_film.id_film
                    AND pm_film_seance.id_cycle = pm_cycle.id_cycle
                    ORDER BY pm_film_seance.ordre";
        $result_movie = mysqli_query($connexion, $sql_movie) or die(mysqli_error());
        $data_movie = mysqli_fetch_array($result_movie);
        
        // affichage pour le titre (séance ou film)
        $titre = '';
        $pos = '';
        if($data['titre_seance']) {
            //modification du titre POUR REQUETE SQL
            $caract = array(utf8_decode("'"));
            $new_caract = array(utf8_encode("''"));
            $titre = str_replace($caract, $new_caract, $data["titre_seance"]);
            
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
            $caract = array(utf8_decode("'"));
            $new_caract = array(utf8_encode("&acute;"));
            $titre_affiche = str_replace($caract, $new_caract, $data["titre_seance"]);
            $titre = html_entity_decode(utf8_encode($titre_affiche));
            
        } else {
            // affichage du numéro de la séance
            $id_film = $data_movie['id_film'];
            $start_seance = $data['start'];
            $pos = '';
            if ($data_movie['titre_o']) $pos = 1; // numéro seulement si 1film dans séance
            $sql_pos = "SELECT * FROM pm_film_seance, pm_seance
                        WHERE pm_film_seance.id_film = '$id_film'
                        AND pm_film_seance.id_seance = pm_seance.id_seance
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
            // remplacement de l'apostrophe
            $caract = array(utf8_decode("'"));
            $new_caract = array(utf8_encode("&acute;"));
            $titre_affiche = str_replace($caract, $new_caract, $data_movie['titre_o']);
            // titre de la séance
            $titre = utf8_encode($titre_affiche);
        }
        $titre .= '<br />'.substr(utf8_encode($data['titre_salle']), 0, 3).' | '.$pos.' ';
        if ($data['commentaire']) {$titre .= '| '.utf8_encode($data['commentaire']);}
        
    /***génération des séances au format json...***/
       echo "
                {
                        id_seance: '".$data['id_seance']."',
                        title: '".$titre."',
                        start: '".$data['start']."',
                        end: '".$data['end']."',";
                        // border pour les événements
                        if ($data['event']==1){
                            echo "borderColor: '#000a01',";
                        } 
                        //affichage de la couleur du cycle
                        echo "backgroundColor: '".$data_movie['couleur']."',";
                echo "  },";
    }
}
echo '],'; //fermeture de la seance
?>