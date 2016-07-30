<?php
/**
* MENU _RESERVATIONS
*
* Génération du sous-menu suivant les droits de l'user
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 29.04.2015
*/

?>
    <ul id="sousmenu">
<?php
//Le SuperAdmin a tous les droits -> foreach du tableau $sousMenu
if($_COOKIE['geLogCon']==='CineSupAdm'){
    foreach($sousMenu as $cle => $element) {
        if('_sm'.+$cle===$_COOKIE['sousMenu']) {
            echo '<li class="actif"><a href="index.php?sm=_sm'.$cle.'&amp;cat=e">'.$element.'</a></li>';
        } else echo '<li><a href="index.php?sm=_sm'.$cle.'&amp;cat=e">'.$element.'</a></li>';
    }
    
} else {
    //récupérer les onglets du sous-menu ! suivant les droits de l'utilisateur
    $sql_sm = "SELECT rubrique, categorie FROM ge_user, ge_useracces, ge_rubrique, ge_app, ge_role
                WHERE ge_user.login = '".$_COOKIE['geLogCon']."'
                AND ge_user.id_user = ge_useracces.id_user
                AND ge_useracces.id_role = ge_rubrique.id_role
                AND ge_useracces.id_role = ge_role.id_role
                AND ge_app.id_app = ge_role.id_app
                AND ge_app.dossier = '".$_COOKIE['indexApp']."'";
    $result_sm = mysqli_query($connexion, $sql_sm ) or die(mysqli_error());
    //boucle pour récupérer tous les onglets
    while ($data_sm = mysqli_fetch_array($result_sm)) {
        if($data_sm['rubrique']!=='99') { //pas de sous-menu 99
            $rubCookie = '_sm'.$data_sm['rubrique'];
            if($rubCookie===$_COOKIE['sousMenu']) { //link pour envoyer le cookie de sous-menu
                echo '<li class="actif"><a href="'.$_SERVER['PHP_SELF'].'?sm=_sm'.$data_sm['rubrique'].'&amp;cat='.$data_sm['categorie'].'">'.$sousMenu[$data_sm["rubrique"]].'</a></li>';
            } else echo '<li><a href="'.$_SERVER['PHP_SELF'].'?sm=_sm'.$data_sm['rubrique'].'&amp;cat='.$data_sm['categorie'].'">'.$sousMenu[$data_sm["rubrique"]].'</a></li>';
        }
    }
}
?>
    </ul>