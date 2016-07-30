<?php
/**
* INDEX _Administration
*
* Interface permettant la gestion des roles des applications ainsi que les utilisateurs
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 16.04.2015 - 16.09.2015
*/

/**************************************************************************************
 * ****************************** DEFINITION DES VARIABLES ****************************
 * ***********************************************************************************/
$zone = '';
$user = '';
$role = '';

require_once($_COOKIE['indexApp']."/inc/ge_functions.php"); //paramêtres js pour les effets ajax

//Récupération des variables $_GET envoyées
foreach ($_GET as $key => $value) {
	$$key = $value;
}

/**************************************************************************************/

$saveRole = '';
$deleteRole = '';
$saveUseracces = '';
$deleteUseracces = '';
$saveUser = '';
$_deleteUser = '';

//Récupération des variables $_POST envoyées
foreach ($_POST as $key => $value) {
	$$key = $value;
}
 
/**************************************************************************************
 * ****************************** TRAITEMENT DES DEMANDES ****************************
 * ***********************************************************************************/
if($saveRole) $id_role = saveRole();
if($deleteRole) deleteRole();
if($saveUseracces) saveUseracces();
if($deleteUseracces) deleteUseracces();
if($saveUser) $id_user = saveUser();
if($_deleteUser) deleteUser();

/**************************************************************************************
 * ****************************** APPLICATION *****************************************
 * ***********************************************************************************/ 
//SuperAdmin
if($_COOKIE['geLogCon']==='CineSupAdm'){ ?>

<div class="left demi">
    <h3>Liste des users</h3>
	<form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
	    <input type="hidden" value="user" name="zone">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
        <table class="table" cellspacing="0" cellpadding="0">
	    <thead>
		<tr>
		    <th>Login</th>
<?php 
$sqlApp = "SELECT titre, id_app FROM ge_app WHERE ordre <> '99' ORDER BY ge_app.ordre";
$resultApp = mysqli_query($connexion, $sqlApp ) or die(mysqli_error());
while ($dataApp = mysqli_fetch_array($resultApp)) {
                    echo '<th>'.utf8_encode($dataApp['titre']).'</th>';
} ?>
                    <th></th>
		</tr>
	    </thead>
            <tbody>
<?php 
        $sqlAllUser = "SELECT login, id_user FROM ge_user WHERE id_user <> '21' ORDER BY login";
        $resultAllUser = mysqli_query($connexion, $sqlAllUser ) or die(mysqli_error());
        while ($dataAllUser = mysqli_fetch_array($resultAllUser)) {
            echo '<tr>';
                echo '<td>'.utf8_encode($dataAllUser['login']).'</td>';
            //boucle sur toutes les app
            $sqlApp2 = "SELECT id_app FROM ge_app WHERE ordre <> '99' ORDER BY ge_app.ordre";
            $resultApp2 = mysqli_query($connexion, $sqlApp2 ) or die(mysqli_error());
            while ($dataApp2 = mysqli_fetch_array($resultApp2)) {
                //selection du role de l'user dans chaque app
                $sqlSelectRole = "SELECT intitule FROM ge_role
                JOIN ge_useracces
                ON ge_role.id_role = ge_useracces.id_role
                WHERE id_app = '$dataApp2[id_app]'
                AND id_user = '$dataAllUser[id_user]'";
                $resultSelectRole = mysqli_query($connexion, $sqlSelectRole ) or die(mysqli_error());
                $dataSelectRole = mysqli_fetch_array($resultSelectRole);
                
                echo '<td>'.$dataSelectRole['intitule'].'</td>';
           }
                echo '<td>
                        <form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'" method="post">
                            <input type="hidden" value="'.$dataAllUser['id_user'].'" name="id_user">
                            <input type="hidden" value="'.$dataAllUser['login'].'" name="login">
			    <input name="user" class="btn_modif" type="submit" value="Edition">
			</form>
                        <form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé ce rôe?\')">
                            <input type="hidden" value="'.$dataAllUser['id_user'].'" name="id_user">
			    <input name="deleteUser" class="btn_suppr" type="submit" value="Supprimer">
			</form></td>';
            echo '</tr>';
        }
    ?>
            </tbody>
        </table>
</div>
<?php if($zone =='user' || $user =='Edition' ) { ?>
<div class="right">
    <h4>Ajout-Modification</h4>
    <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
        <div>
            <label for="login"></label>
            <input type="text" name="login" placeholder="Login" value="<?php echo $login; ?>">
        </div>
        <div>
            <label for="password"></label>
            <input type="text" name="password" placeholder="Password" value="">
        </div>
        <input type="hidden" value="<?php echo $id_user; ?>" name="id_user">
	<input type="submit" name="saveUser" class="btn" value="Enregistrer">
    </form>
</div>
<?php } ?>
<hr class="clear">

<?php
//affichage de toutes les applications
$sqlAdmin = "SELECT dossier, titre, id_app FROM ge_app WHERE ordre <> '99' ORDER BY ge_app.ordre"; 

} else {
// Sélectionner toutes les applications dont l'utilisateur a les droits 99 -> administrateur
$sqlAdmin = "SELECT dossier, titre, ge_app.id_app FROM ge_user
            JOIN ge_useracces
            ON ge_user.id_user = ge_useracces.id_user
            JOIN ge_role
            ON ge_useracces.id_role = ge_role.id_role
            JOIN ge_app
            ON ge_role.id_app = ge_app.id_app
            JOIN ge_rubrique 
            ON ge_role.id_role = ge_rubrique.id_role
            WHERE ge_user.login = '".$_COOKIE['geLogCon']."' 
            AND ge_rubrique.rubrique = '99' ORDER BY ge_app.ordre"; // nom de la table ! requette 
}
$resultAdmin = mysqli_query($connexion, $sqlAdmin ) or die(mysqli_error());
while ($dataAdmin = mysqli_fetch_array($resultAdmin)) {
    echo '<h2>'.utf8_encode($dataAdmin['titre']).'-'.$dataAdmin['id_app'].'</h2>';
    
    //vider la variable avant chaque applications
    $sousMenu = '';
    //récupération des constantes de l'application
    include($dataAdmin['dossier']."/inc/constants.php");
?>

<!-------------------------------------------------------------- ROLES ----------------------------------------------------------------------->
    <div class="left demi">
        <h3>Rôles</h3>
	<form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
	    <input type="hidden" value="role<?php echo $dataAdmin['id_app']; ?>" name="zone">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
        <table class="table" cellspacing="0" cellpadding="0">
	    <thead>
		<tr>
		    <th>Rôles</th>
<?php               //boucle sur le sousMenu pour récupérer tous les éléments
                    foreach($sousMenu as $cle => $element) {
                        echo '<th>'.$element.'</th>';
                    }
?>
                    <th></th>
		</tr>
	    </thead>
	    <tbody>
    <?php
    $sqlRole = "SELECT id_role, id_app, intitule FROM ge_role
                WHERE id_app ='".$dataAdmin['id_app']."'";
    $resultRole = mysqli_query($connexion, $sqlRole) or die(mysqli_error());
    while ($dataRole = mysqli_fetch_array($resultRole)) {
    ?>
                <tr>
                    <td><?php echo utf8_encode($dataRole['intitule']); ?></td>
<?php
                    foreach($sousMenu as $cle => $element) {
                        $sqlRubrique = "SELECT categorie FROM ge_rubrique
                                    WHERE rubrique ='".$cle."'
                                    AND id_role = '".$dataRole['id_role']."'";
                        $resultRubrique = mysqli_query($connexion, $sqlRubrique) or die(mysqli_error());
                        $dataRubrique = mysqli_fetch_array($resultRubrique);
                        echo '<td>'.$dataRubrique['categorie'].'</td>';
                    }
?>
                    <td>
                        <form name="suppr_news" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="hidden" value="<?php echo $dataRole['id_role']; ?>" name="id_role">
                            <input type="hidden" value="<?php echo utf8_encode($dataRole['intitule']); ?>" name="intitule">
			    <input name="role" class="btn_modif" type="submit" value="Edition<?php echo $dataAdmin['id_app']; ?>">
			</form>
                        <form name="suppr_news" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onclick="return confirm('voulez-vous vraiment supprimé ce rôe?')">
                            <input type="hidden" value="<?php echo $dataRole['id_role']; ?>" name="id_role">
			    <input name="deleteRole" class="btn_suppr" type="submit" value="Supprimer">
			</form>
                    </td>
                </tr>
    <?php } ?>
            </tbody>
        </table>

<?php
/********************* Formulaire de ROLE *********************/
if($zone=='role'.$dataAdmin['id_app'] || $role=='Edition'.$dataAdmin['id_app'] ) { ?>
	<h4>Ajout / Modification</h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="intitule"></label>
		<input type="text" name="intitule" placeholder="Intitulé" value="<?php echo $intitule; ?>">
	    </div>
<?php   foreach($sousMenu as $cle => $element) {
            $sqlRubriqueSelect = "SELECT categorie FROM ge_rubrique
                        WHERE rubrique ='".$cle."'
                        AND id_role = '".$id_role."'";
            $resultRubriqueSelect = mysqli_query($connexion, $sqlRubriqueSelect) or die(mysqli_error());
            $dataRubriqueSelect = mysqli_fetch_array($resultRubriqueSelect);
?>
            <div>
		<label class="radio" for="rubrique"><?php echo $element; ?></label>
                <input type="radio" <?php echo 'name="rubrique['.$cle.']"'; if($dataRubriqueSelect['categorie']=='v') {echo ' checked';} ?> value="v">Visioneur
                <input type="radio" <?php echo 'name="rubrique['.$cle.']"'; if($dataRubriqueSelect['categorie']=='e') {echo ' checked';} ?> value="e">Editeur
                <input type="radio" name="rubrique[<?php echo $cle; ?>]" value="">Null
	    </div>
<?php   } ?>
	    <input type="hidden" name="id_app" value="<?php echo $dataAdmin['id_app']; ?>">
	    <input type="hidden" name="id_role" value="<?php echo $id_role; ?>">
	    <input type="submit" name="saveRole" class="btn" value="Enregistrer">
	</form>
        
<?php } ?>
    </div>
<!-------------------------------------------------------------- USER ROLE ----------------------------------------------------------------------->
    <div class="right">
        <h3>Users</h3>
	<form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
	    <input type="hidden" value="user<?php echo $dataAdmin['id_app']; ?>" name="zone">
	    <input type="submit" class="btn" value="Ajouter">
	</form>
        <table class="table" cellspacing="0" cellpadding="0">
	    <thead>
		<tr>
		    <th>Login</th>
		    <th>Rôle</th>
                    <th></th>
		</tr>
	    </thead>
	    <tbody>
    <?php
    $sqlUser = "SELECT login, intitule, ge_useracces.id_user, ge_useracces.id_role, ge_useracces.id_acces FROM ge_role
                JOIN ge_useracces
                ON ge_role.id_role = ge_useracces.id_role
                JOIN ge_user
                ON ge_useracces.id_user = ge_user.id_user
                WHERE ge_role.id_app ='".$dataAdmin['id_app']."'
                ORDER BY login";
    $resultUser = mysqli_query($connexion, $sqlUser ) or die(mysqli_error());
    while ($dataUser = mysqli_fetch_array($resultUser)) {
    ?>
                <tr>
                    <td><?php echo utf8_encode($dataUser['login']); ?></td>
                    <td><?php echo utf8_encode($dataUser['intitule']); ?></td>
                    <td>
                        <form name="suppr_news" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                            <input type="hidden" value="<?php echo $dataUser['id_user']; ?>" name="id_user">
                            <input type="hidden" value="<?php echo $dataUser['id_role']; ?>" name="id_role">
                            <input type="hidden" value="<?php echo $dataUser['id_acces']; ?>" name="id_acces">
			    <input name="user" class="btn_modif" type="submit" value="Edition<?php echo $dataAdmin['id_app']; ?>">
			</form>
                        <form name="suppr_news" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onclick="return confirm('voulez-vous vraiment supprimé ce rôe?')">
                            <input type="hidden" value="<?php echo $dataUser['id_acces']; ?>" name="id_acces">
			    <input name="deleteUseracces" class="btn_suppr" type="submit" value="Supprimer">
			</form>
                    </td>
                </tr>
    <?php } ?>
            </tbody>
        </table>
<?php
/********************* Formulaire de ROLE *********************/
if($zone=='user'.$dataAdmin['id_app'] || $user=='Edition'.$dataAdmin['id_app'] ) {
	if($id_user) {
		$sqlUserSelect = "SELECT ge_user.id_user, id_role, id_acces FROM ge_user
                                JOIN ge_useracces
                                ON ge_user.id_user = ge_useracces.id_user
                                WHERE ge_user.id_user = '$id_user'"; // nom de la table ! requette
		$resultUserSelect = mysqli_query($connexion, $sqlUserSelect ) or die(mysqli_error());
		$dataUserSelect = mysqli_fetch_array($resultUserSelect);
	}
?>
	<h4>Ajout / Modifiacation</h4>
        <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
            <select name="id_user" class="input21">
                <optgroup label="Utilisateurs">
    <?php 
        $sqlAllUser = "SELECT login, id_user FROM ge_user WHERE id_user <> '21' ORDER BY login";
        $resultAllUser = mysqli_query($connexion, $sqlAllUser ) or die(mysqli_error());
        while ($dataAllUser = mysqli_fetch_array($resultAllUser)) {
            echo '<option value="'.$dataAllUser['id_user'].'"';
            if($dataAllUser['id_user']==$id_user) {echo ' selected';}
            echo '>'.utf8_encode($dataAllUser['login']).'</option>';
        }
    ?>  
            </select>
            <select name="id_role" class="input22">
                <optgroup label="Rôles">
    <?php
    $sqlRole = "SELECT id_role, intitule FROM ge_role
                WHERE id_app ='".$dataAdmin['id_app']."'";
    $resultRole = mysqli_query($connexion, $sqlRole) or die(mysqli_error());
    while ($dataRole = mysqli_fetch_array($resultRole)) {
            echo '<option value="'.$dataRole['id_role'].'"';
            if($dataRole['id_role']==$id_role) {echo ' selected';}
            echo '>'.utf8_encode($dataRole['intitule']).'</option>';
        }
    ?>  
            </select>
	    <input type="hidden" name="id_acces" value="<?php echo $id_acces; ?>">
	    <input type="submit" name="saveUseracces" class="btn" value="Enregistrer">
	</form>

<?php } ?>
    </div>

    <!--<div class="middle"></div>-->
    <hr class="clear">
<?php } ?>