<?php
/*** PAGE ***/
// 
//Cindy Chassot 22.15.2015
//© Cinémathèque suisse

//récupératin du cookie "onglet" pour afficher l'accueil de l'application
if (isset($_COOKIE['indexApp'])) {
    $app = $_COOKIE['indexApp'];
    include($app.'/index.php');
}
else {
    echo 'Sélectionnez un onglet';
}

?>
    </div>
    <footer>
        2015 © Cinémathèque suisse | <?php echo $_COOKIE['geLogCon']; ?> <a href="index.php?deconnexion=true">déconnexion</a>
    </footer>