<?php
/**
* INDEX Détail formulaire - ajout/édition de champs
*
* sous menu caché _smi0
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 01.04.2015 - 13.07.2015
*/

//sélection des information du formulaire
$sqlFormSelect = "SELECT * FROM fm_form WHERE id_form = '$id_form'"; // nom de la table ! requette
$resultFormSelect = mysqli_query($connexion, $sqlFormSelect ) or die(mysqli_error());
$dataFormSelect = mysqli_fetch_array($resultFormSelect);
?>

<div class="left demi">
	
  <h3>Formulaire: <?php echo utf8_encode($dataFormSelect['titre']); ?></h3>
<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
    <input type="hidden" name="zone" value="form">
    <input type="hidden" name="id_form" value="<?php echo $id_form; ?>">
    <input type="hidden" name="add" value="2">
    <input type="submit" class="btn" value="Ajouter">
  </form>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
    <input type="hidden" name="id_form" value="<?php echo $id_form; ?>">
    <input type="submit" class="btn" name="addInscForm" value="Insérer">
  </form>
<?php } ?>
	
  <table class="table" id="listeChamp">
    <thead>
      <tr>
	<th>Titre</th>
	<th>Type</th>
	<th>Requis</th>
	<th>Visible</th>
<?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
      </tr>
    </thead>
    <tbody class="content">
<?php
/**********************************************************************************************************************************************/
/********************************************************* AFFICHAGE DU FORMULAIRE ************************************************************/
/**********************************************************************************************************************************************/
    $sqlChampAtt = "SELECT * FROM fm_champ WHERE id_form = '$id_form' ORDER BY ordre"; // nom de la table ! requette
    $resultChampAtt = mysqli_query($connexion, $sqlChampAtt ) or die(mysqli_error());
	   
    while ($dataChampAtt = mysqli_fetch_array($resultChampAtt)) {
      // définir si le film a été sélectionné	
      if ($id_champ == $dataChampAtt['id_champ'] ) {
	echo '<tr class="select" id="pos_'.$dataChampAtt['id_champ'].'">'; // pour l'altérnance des couleurs
      } else {
	echo '<tr id="pos_'.$dataChampAtt['id_champ'].'">'; // pour l'altérnance des couleurs
      }
      
      //Intitulé
      echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=form&amp;add=0&amp;id_form='.$id_form.'&id_champ='.$dataChampAtt['id_champ'].'">'.utf8_encode($dataChampAtt['titre']).'</a></td>'; //renvoi l'id de la catégorie en "GET"
      
      //Type
      echo '<td>'.$typeChamp[$dataChampAtt['type']].'</td>';
      //Requis
      echo '<td>';
      if($dataChampAtt['required'] == 1) echo 'x';
      echo '</td>';
      
      //Visible
      echo '<td>';
      if($dataChampAtt['visible'] == 1) echo 'x';
      echo '</td>'; 
      

      //Formulaire d'édition / suppression
	echo '<td>';
      if($_COOKIE['role']=='e') {	
	  echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?id_form='.$id_form.'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimer ce champ?\')">
	      <input type="hidden" name="id_champ" value="'.$dataChampAtt['id_champ'].'">
	      <input name="deleteChamp" class="btn_suppr" type="submit" value="Supprimer">
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
	  $sqlChampSelect = "SELECT * FROM fm_champ WHERE id_champ ='$id_champ'"; // nom de la table ! requette
	  $resultChampSelect = mysqli_query($connexion, $sqlChampSelect ) or die(mysqli_error());
	  $dataChampSelect = mysqli_fetch_array($resultChampSelect);
		
	  $titre2 = 'Modifier '.utf8_encode($dataChampSelect['titre']);
	}
?>

<div class="right demi">
	<h4><?php echo $titre2; ?></h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="titre">Titre</label>
		<input id="titre" type="text" name="titre"  value="<?php echo utf8_encode($dataChampSelect['titre']); ?>">
	    </div>
	    <div>
		<label for="type"></label>
		<select id="type" name="type">
			<option <?php if($dataChampSelect['type']==1) echo 'selected="selected"'; ?> value="1">Text</option>
			<option <?php if($dataChampSelect['type']==2) echo 'selected="selected"'; ?> value="2">Textarea</option>
			<option <?php if($dataChampSelect['type']==3) echo 'selected="selected"'; ?> value="3">Checkbox</option>
			<option <?php if($dataChampSelect['type']==4) echo 'selected="selected"'; ?> value="4">Radio</option>
			<option <?php if($dataChampSelect['type']==5) echo 'selected="selected"'; ?> value="5">Select</option>
			<option <?php if($dataChampSelect['type']==6) echo 'selected="selected"'; ?> value="6">Mail</option>
			<option <?php if($dataChampSelect['type']==7) echo 'selected="selected"'; ?> value="7">Date</option>
			<option <?php if($dataChampSelect['type']==98) echo 'selected="selected"'; ?> value="98">TITRE</option>
			<option <?php if($dataChampSelect['type']==99) echo 'selected="selected"'; ?> value="99">--------</option>
		</select>
	    </div>
	    Uniquement pour "checkbox, radio, select"
	    <div>
		<label for="valeur">Valeur, séparées par une virgules</label>
		<textarea id="valeur" name="valeur"><?php echo utf8_encode($dataChampSelect['valeur']); ?></textarea>
	    </div>
	    <div class="input21">
		<label for="required"></label>
		<input type="checkbox" value="1" name="required" <?php if($dataChampSelect['required']==1) echo 'checked'; ?>>Requis
	    </div>
	    <div class="input22">
		<label for="visible"></label>
		<input type="checkbox" value="1" name="visible"<?php if($dataChampSelect['visible']=='0'){echo 'test'; }else {echo ' checked=""';} ?>>Visible sur le formulaire client
	    </div>
	    <input type="hidden" name="id_champ" value="<?php echo $id_champ; ?>">
	    <input type="hidden" name="id_form" value="<?php echo $id_form; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveChamp" class="btn" value="Enregistrer">'; } ?>
	    <?php if($id_champ) { echo '<input type="submit" name="traduction" class="btn" value="Traduction">'; } ?>
	</form>
</div>
  
<?php
/****************************************************************************************************************************
 * ****************************************** ZONE DE TRADUCTION ************************************************************
 * **************************************************************************************************************************/
if($traduction == 'Traduction') {
    //traduction allemande
    $sqlChampTradDe = "SELECT * FROM fm_champ_trad WHERE id_champ ='$id_champ' AND langue = 'de'"; // nom de la table ! requette
    $resultChampTradDe = mysqli_query($connexion, $sqlChampTradDe ) or die(mysqli_error());
    $dataChampTradDe = mysqli_fetch_array($resultChampTradDe);
    
    //traduction anglaise
    $sqlChampTradEn = "SELECT * FROM fm_champ_trad WHERE id_champ ='$id_champ' AND langue = 'en'"; // nom de la table ! requette
    $resultChampTradEn = mysqli_query($connexion, $sqlChampTradEn ) or die(mysqli_error());
    $dataChampTradEn = mysqli_fetch_array($resultChampTradEn);
    
    //traduction italienne
    $sqlChampTradIt = "SELECT * FROM fm_champ_trad WHERE id_champ ='$id_champ' AND langue = 'it'"; // nom de la table ! requette
    $resultChampTradIt = mysqli_query($connexion, $sqlChampTradIt ) or die(mysqli_error());
    $dataChampTradIt = mysqli_fetch_array($resultChampTradIt);
?>
<hr class="clear">
<div class="left">
  <h4>Traduction allemande</h4>
  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<div>
	    <label for="titre_trad">Titre</label>
	    <input id="titre_trad" type="text" name="titre_trad"  value="<?php echo utf8_encode($dataChampTradDe['titre_trad']); ?>">
	</div>
	<div>
	    <label for="valeur_trad">Valeur</label>
	    <textarea id="valeur_trad" name="valeur_trad"><?php echo utf8_encode($dataChampTradDe['valeur_trad']); ?></textarea>
	</div>
	<input type="hidden" name="id_champ" value="<?php echo $id_champ ?>">
	<input type="hidden" name="id_trad" value="<?php echo $dataChampTradDe['id'];; ?>">
	<input type="hidden" name="langue" value="de">
	<input type="hidden" name="traduction" value="Traduction">
	<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveChampTrad" class="btn" value="Enregistrer">'; } ?>
    </form>
</div>
<div class="right">
  <h4>Traduction italienne</h4>
  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<div>
	    <label for="titre_trad">Titre</label>
	    <input id="titre_trad" type="text" name="titre_trad"  value="<?php echo utf8_encode($dataChampTradIt['titre_trad']); ?>">
	</div>
	<div>
	    <label for="valeur_trad">Valeur</label>
	    <textarea id="valeur_trad" name="valeur_trad"><?php echo utf8_encode($dataChampTradIt['valeur_trad']); ?></textarea>
	</div>
	<input type="hidden" name="id_champ" value="<?php echo $id_champ; ?>">
	<input type="hidden" name="id_trad" value="<?php echo $dataChampTradIt['id']; ?>">
	<input type="hidden" name="langue" value="en">
	<input type="hidden" name="traduction" value="Traduction">
	<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveChampTrad" class="btn" value="Enregistrer">'; } ?>
    </form>
</div>
<div class="middle">
  <h4>Traduction anglaise</h4>
  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<div>
	    <label for="titre_trad">Titre</label>
	    <input id="titre_trad" type="text" name="titre_trad"  value="<?php echo utf8_encode($dataChampTradEn['titre_trad']); ?>">
	</div>
	<div>
	    <label for="valeur_trad">Valeur</label>
	    <textarea id="valeur_trad" name="valeur_trad"><?php echo utf8_encode($dataChampTradEn['valeur_trad']); ?></textarea>
	</div>
	<input type="hidden" name="id_champ" value="<?php echo $id_champ; ?>">
	<input type="hidden" name="id_trad" value="<?php echo $dataChampTradEn['id']; ?>">
	<input type="hidden" name="langue" value="en">
	<input type="hidden" name="traduction" value="Traduction">
	<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveChampTrad" class="btn" value="Enregistrer">'; } ?>
    </form>
</div>
<?php
	}// zone traduction
} //zone=form?>
<!--##########--><hr class="clear" id="cycle" /><!--##############################################################################################################################-->

