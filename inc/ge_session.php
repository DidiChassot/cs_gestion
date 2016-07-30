<?php
/**
* SESSIONS
*
* Mise en places de variables de session
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 13.07.2015
*/

/*
 * Éviter le renvoi répétitif d'un formulaire en rafraîchissant
 * Solution trouvé sur OpenClassrooms
 * https://openclassrooms.com/courses/eviter-le-renvoi-repetitif-d-un-formulaire-en-rafraichissant
 */

// génération des variable de session + rafraichissement
if(!empty($_POST) OR !empty($_FILES)) {
    $_SESSION['sauvegarde'] = $_POST ;
    $_SESSION['sauvegardeFILES'] = $_FILES ;
    
    $fichierActuel = $_SERVER['PHP_SELF'] ;
    if(!empty($_SERVER['QUERY_STRING'])) {
        $fichierActuel .= '?' . $_SERVER['QUERY_STRING'] ;
    }
    
    header('Location: ' . $fichierActuel);
    exit;
}

// génération de "fausses" varaible post
if(isset($_SESSION['sauvegarde'])) {
    $_POST = $_SESSION['sauvegarde'] ;
    $_FILES = $_SESSION['sauvegardeFILES'] ;
    
    unset($_SESSION['sauvegarde'], $_SESSION['sauvegardeFILES']);
}


?>
