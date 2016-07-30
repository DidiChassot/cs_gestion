<?php
/*** MENU GENERAL ***/
// 
//Cindy Chassot 15.01.2015 - 22.01.2015
//© Cinémathèque suisse

//$res = mysqli_query($mysqli, "SELECT 'Please, do not use ' AS _msg FROM DUAL");
?>
<nav>
    <ul>
<?php
if($_COOKIE['geLogCon']==='CineSupAdm'){
    //récupérer les onglets du menu principal ! suivant les droits
    $sql_menu = "SELECT * FROM ge_app ORDER BY ordre";
    $result_menu = mysqli_query($connexion, $sql_menu ) or die(mysqli_error());
    //boucle pour récupérer tous les onglets	
    while ($data_menu = mysqli_fetch_array($result_menu)) {
        if($data_menu['dossier']===$_COOKIE['indexApp']) {
            echo '<li class="actif"><a href="index.php?onglet='.$data_menu['dossier'].'">'.utf8_encode($data_menu['titre']).'</a></li>';
        } else echo '<li><a href="index.php?onglet='.$data_menu['dossier'].'">'.utf8_encode($data_menu['titre']).'</a></li>';
    }
} else {
    //récupérer les onglets du menu principal ! suivant les droits de l'utilisateur
    $sql_menu = "SELECT * FROM ge_user, ge_useracces, ge_role, ge_app
                WHERE ge_user.login = '".$_COOKIE['geLogCon']."'
                AND ge_user.id_user = ge_useracces.id_user
                AND ge_useracces.id_role = ge_role.id_role
                AND ge_role.id_app = ge_app.id_app
                ORDER BY ge_app.ordre";
    $result_menu = mysqli_query($connexion, $sql_menu ) or die(mysqli_error());
    $accesAdmin = false; //déclaration de l'administration
    //boucle pour récupérer tous les onglets
    while ($data_menu = mysqli_fetch_array($result_menu)) {
        if($data_menu['dossier']===$_COOKIE['indexApp']) {
            echo '<li class="actif"><a href="index.php?onglet='.$data_menu['dossier'].'">'.utf8_encode($data_menu['titre']).'</a></li>';
        } else echo '<li><a href="index.php?onglet='.$data_menu['dossier'].'">'.utf8_encode($data_menu['titre']).'</a></li>';
        
        $sql_admin = "SELECT * FROM ge_rubrique
        WHERE id_role = '".$data_menu['id_role']."'
        AND rubrique = 99";
        $result_admin = mysqli_query($connexion, $sql_admin) or die(mysqli_error());
        if(mysqli_num_rows($result_admin) > 0) {
            $accesAdmin = true;
        }
    }
    
    if($accesAdmin) {
        if($_COOKIE['indexApp']==='_administration') {
            echo '<li class="actif"><a href="index.php?onglet=_administration">Administration</a></li>';
        } else echo '<li><a href="index.php?onglet=_administration">Administration</a></li>';
    }
}
?>
    </ul>
</nav>

