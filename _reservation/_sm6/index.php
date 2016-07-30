<?php
/**
* INDEX RESERVATION / SM6 - Gestion des catégorie
*
* Gestion des catégorie de liste de réservation avec les textes
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 29.04.2015
*/

/************************************* APPLICATION *************************************/
?>
<h2>Gestion des catégories</h2>

<div class="left">
  <h3>Catégories</h3>
<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
    <input type="hidden" value="cat" name="zone">
    <input type="hidden" value="1" name="add">
    <input type="submit" class="btn" value="Ajouter">
  </form>
<?php } ?>
	
  <table class="table">
    <thead>
      <tr>
	<th>ID</th>
	<th>Titre</th>
<?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
      </tr>
    </thead>
    <tbody>
<?php
/**********************************************************************************************************************************************/
/********************************************************* AFFICHAGE DES CATEGORIES ***********************************************************/
/**********************************************************************************************************************************************/
    $sqlCatAtt = "SELECT id_categorie, titre FROM re_categorie"; // nom de la table ! requette
    $resultCatAtt = mysqli_query($connexion, $sqlCatAtt ) or die(mysqli_error());
	   
    while ($dataCatAtt = mysqli_fetch_array($resultCatAtt)) {
      // définir si le film a été sélectionné	
      if ($cat == $dataCatAtt['id_categorie'] ) {
	echo '<tr class="select">'; // pour l'altérnance des couleurs
      } else {
	echo '<tr>'; // pour l'altérnance des couleurs
      }
      //id de la catégorie
      echo '<td>'.$dataCatAtt['id_categorie'].'</td>';
      //titre de la catégorie
      echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=cat&amp;add=0&amp;cat='.$dataCatAtt['id_categorie'].'">'.utf8_encode($dataCatAtt['titre']).'</a></td>'; //renvoi l'id de la catégorie en "GET"

      //Formulaire d'édition / suppression
      if($_COOKIE['role']=='e') {	
	echo '<td>';
	if($dataCatAtt['id_categorie'] != '1' && $dataCatAtt['id_categorie'] != '2' && $dataCatAtt['id_categorie'] != '5') {
	  echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette catégorie?\')">
	      <input type="hidden" name="id_categorie" value="'.$dataCatAtt['id_categorie'].'">
	      <input name="deleteCat" class="btn_suppr" type="submit" value="Supprimer">
	  </form>';
	}
	echo '</td>';
      }

      echo '</tr>'; //fermeture de la ligne
    }
?>
    </tbody>
  </table>
</div>

<?php
/********************************************************* FORMULAIRE ADD/MODIF CATEGORIES *********************************************************/
if($_REQUEST['zone']=='cat' || $_REQUEST['zone']=='form') {
	if($add == 1) {
	  $titre1 = 'Ajout';
	  
	} else {
	  $sqlCatSelect = "SELECT * FROM re_categorie WHERE id_categorie ='$cat'"; // nom de la table ! requette
	  $resultCatSelect = mysqli_query($connexion, $sqlCatSelect ) or die(mysqli_error());
	  $dataCatSelect = mysqli_fetch_array($resultCatSelect);
		
	  $titre1 = 'Modifier '.$dataCatSelect['titre'];
	}
?>
<div class="right"></div>

<div class="middle">
	<h4><?php echo $titre1; ?></h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="titre">Titre</label>
		<input id="titre" type="text" name="titre"  value="<?php echo utf8_encode($dataCatSelect['titre']); ?>">
	    </div>
	    <input type="hidden" name="id_categorie" value="<?php echo $cat; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveCat" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>

<hr class="clear">
  
<div class="left">
  <h3>Formulaire</h3>
<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
    <input type="hidden" name="zone" value="form">
    <input type="hidden" name="cat" value="<?php echo $cat; ?>">
    <input type="hidden" name="add" value="2">
    <input type="submit" class="btn" value="Ajouter">
  </form>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    <input type="hidden" name="id_categorie" value="<?php echo $cat; ?>">
    <input type="submit" class="btn" name="addInscForm" value="Insérer">
  </form>
<?php } ?>
	
  <table class="table" id="listeForm">
    <thead>
      <tr>
	<th>Titre</th>
<?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
      </tr>
    </thead>
    <tbody class="content">
<?php
/**********************************************************************************************************************************************/
/********************************************************* AFFICHAGE DU FORMULAIRE ************************************************************/
/**********************************************************************************************************************************************/
    $sqlFormAtt = "SELECT * FROM re_form WHERE id_categorie = '$cat' ORDER BY ordre"; // nom de la table ! requette
    $resultFormAtt = mysqli_query($connexion, $sqlFormAtt ) or die(mysqli_error());
	   
    while ($dataFormAtt = mysqli_fetch_array($resultFormAtt)) {
      // définir si le film a été sélectionné	
      if ($form == $dataFormAtt['id_form'] ) {
	echo '<tr class="select" id="pos_'.$dataFormAtt['id_form'].'">'; // pour l'altérnance des couleurs
      } else {
	echo '<tr id="pos_'.$dataFormAtt['id_form'].'">'; // pour l'altérnance des couleurs
      }
      
      //Intitulé
      if($dataFormAtt['name'] != 'politesse' && $dataFormAtt['name'] != 'prenom' && $dataFormAtt['name'] != 'nom' && $dataFormAtt['name'] != 'email' && $dataFormAtt['name'] != 'newsletter' ) {
	echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=form&amp;add=0&amp;cat='.$cat.'&form='.$dataFormAtt['id_form'].'">'.utf8_encode($dataFormAtt['titre']).'</a></td>'; //renvoi l'id de la catégorie en "GET"
      } else {
	echo '<td>'.utf8_encode($dataFormAtt['titre']).'</a></td>';
      }

      //Formulaire d'édition / suppression
	echo '<td>';
      if($_COOKIE['role']=='e' && $dataFormAtt['name'] != 'politesse' && $dataFormAtt['name'] != 'prenom' && $dataFormAtt['name'] != 'nom' && $dataFormAtt['name'] != 'email' && $dataFormAtt['name'] != 'place') {	
	  echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette catégorie?\')">
	      <input type="hidden" name="id_form" value="'.$dataFormAtt['id_form'].'">
	      <input name="deleteForm" class="btn_suppr" type="submit" value="Supprimer">
	  </form>';
      }
	echo '</td>';

      echo '</tr>'; //fermeture de la ligne
    }
?>
    </tbody>
  </table>
</div>

<?php
/********************************************************* FORMULAIRE ADD/MODIF FORMULAIRE *********************************************************/
if($_REQUEST['zone']=='form') {
	if($add == 2) {
	  $titre2 = 'Ajout';
	} else {
	  $sqlFormSelect = "SELECT * FROM re_form WHERE id_form ='$form'"; // nom de la table ! requette
	  $resultFormSelect = mysqli_query($connexion, $sqlFormSelect ) or die(mysqli_error());
	  $dataFormSelect = mysqli_fetch_array($resultFormSelect);
		
	  $titre2 = 'Modifier '.utf8_encode($dataFormSelect['titre']);
	}
?>

<div class="middle">
	<h4><?php echo $titre2; ?></h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="titre">Titre</label>
		<input id="titre" type="text" name="titre"  value="<?php echo utf8_encode($dataFormSelect['titre']); ?>">
	    </div>
	    <div>
		<label for="type"></label>
		<select id="type" name="type">
			<option <?php if($dataFormSelect['type']==1) echo 'selected="selected"'; ?> value="1">Text</option>
			<option <?php if($dataFormSelect['type']==2) echo 'selected="selected"'; ?> value="2">Textarea</option>
			<option <?php if($dataFormSelect['type']==3) echo 'selected="selected"'; ?> value="3">Checkbox</option>
			<option <?php if($dataFormSelect['type']==4) echo 'selected="selected"'; ?> value="4">Radio</option>
			<option <?php if($dataFormSelect['type']==5) echo 'selected="selected"'; ?> value="5">Select</option>
			<option <?php if($dataFormSelect['type']==6) echo 'selected="selected"'; ?> value="6">Mail</option>
			<option <?php if($dataFormSelect['type']==7) echo 'selected="selected"'; ?> value="7">--------</option>
		</select>
	    </div>
	    Uniquement pour "checkbox, radio, select"
	    <div>
		<label for="valeur">Valeur, séparées par une virgules</label>
		<textarea id="valeur" name="valeur"><?php echo utf8_encode($dataFormSelect['valeur']); ?></textarea>
	    </div>
	    <input type="hidden" name="id_categorie" value="<?php echo $cat; ?>">
	    <input type="hidden" name="id_form" value="<?php echo $form; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveForm" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>

<?php } //zone=form?>
<?php } //zone=cat?>
<hr class="clear">

