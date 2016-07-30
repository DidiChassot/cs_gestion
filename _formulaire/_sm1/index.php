<?php
/**
* INDEX FORMULAIRES / 
*
* Gestion des formulaires pour tous les département. Ce dernier prend en compte le numéro de $sousMenu[] pour s'y référer dans la base
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 07.09.2015
*/


/************************************* APPLICATION *************************************/
?>
<h2>Gestion des formulaires</h2>

<div class="left demi">
  <h3>Formulaires</h3>
<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
    <input type="hidden" value="form" name="zone">
    <input type="hidden" value="1" name="add">
    <input type="submit" class="btn" value="Ajouter">
  </form>
<?php } ?>
	
  <table class="table">
    <thead>
      <tr>
	<th>ID</th>
	<th>Titre</th>
	<th>Email</th>
	<th>Traductions</th>
	<th>Inscriptions</th>
<?php if($_COOKIE['role']=='e') echo '<th style="width:55px;">&nbsp; </th>'; ?>
      </tr>
    </thead>
    <tbody>
<?php
/****************************************************************************************************************************************/
/********************************************************* AFFICHAGE DES FORMULAIRES *********************************************************/
/****************************************************************************************************************************************/

    $sqlFormAtt = "SELECT * FROM fm_form WHERE num_dep = '$num_dep' AND actif='1'";
    $resultFormAtt = mysqli_query($connexion, $sqlFormAtt ) or die(mysqli_error());
	   
    while ($dataFormAtt = mysqli_fetch_array($resultFormAtt)) {
      // définir si le film a été sélectionné	
      if ($id_form == $dataFormAtt['id_form'] ) {
	echo '<tr class="select">'; // pour l'altérnance des couleurs
      } else {
	echo '<tr>'; // pour l'altérnance des couleurs
      }
      //id de la catégorie
      echo '<td>'.$dataFormAtt['id_form'].'</td>';
      
      //titre de la catégorie
      echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=form&amp;add=0&amp;id_form='.$dataFormAtt['id_form'].'">'.utf8_encode($dataFormAtt['titre']).'</a></td>'; //renvoi l'id de la catégorie en "GET"
      
      //email recevant le mail
      echo '<td>'.$dataFormAtt['email'].'</td>';
      
      //Traductions
      $trad = ' ';
      $n = '0';
      $sqlTrad = "SELECT langue FROM fm_form_trad
		    WHERE id_form = '$dataFormAtt[id_form]'
		    ORDER BY langue"; // nom de la table ! requette
      $resultTrad = mysqli_query($connexion, $sqlTrad ) or die(mysqli_error());
      while ($dataTrad = mysqli_fetch_array($resultTrad)) {
	      if($n==0) {$trad.='';} else {$trad.=' | ';}
	      $trad.= $dataTrad['langue'];
	      $n++;
      }		
      echo '<td>'.$trad.'</td>';
      
      //n. de nouvelle demande / toutes
      $sqlListInscrip = "SELECT COUNT(id) AS numb_inscip FROM fm_inscription WHERE id_form = $dataFormAtt[id_form] AND actif='1' AND statut = 0 AND num_dep IS NULL";
      $resultListInscrip = mysqli_query($connexion, $sqlListInscrip ) or die(mysqli_error());
      $dataListInscrip = mysqli_fetch_array($resultListInscrip);
      //Sur nombre de films listés
      $sqlListInscrip2 = "SELECT COUNT(id) AS numb_inscip FROM fm_inscription WHERE id_form = $dataFormAtt[id_form] AND actif='1' AND num_dep IS NULL";
      $resultListInscrip2 = mysqli_query($connexion, $sqlListInscrip2 ) or die(mysqli_error());
      $dataListInscrip2 = mysqli_fetch_array($resultListInscrip2);
      echo '<td>';
      echo $dataListInscrip['numb_inscip'].' / '.$dataListInscrip2['numb_inscip'];
      echo '</td>';
      
      //Formulaire d'édition / suppression
      if($_COOKIE['role']=='e') {	
	echo '<td>';
	    echo '<a class="btn_visio" href="http://action.cinematheque.ch/formulaire/?f='.$dataFormAtt['id_form'].'" target="_blank" title="Aperçu">V</a>';
	  echo '<a title="édition des champs" class="btn_modif" href="'.$_SERVER['PHP_SELF'].'?internal=link&amp;sm=_smi0&amp;cat=e&amp;var&#91;id_form&#93;='.$dataFormAtt['id_form'].'">V</a>';
	  echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimer ce formulaire?\')">
	      <input type="hidden" name="id_form" value="'.$dataFormAtt['id_form'].'">
	      <input name="deleteForm" class="btn_suppr" type="submit" value="Supprimer" title="Supprimer le formulaire">
	  </form>';
	echo '</td>';
      }
			    
      echo '</tr>'; //fermeture de la ligne
    }
?>
    </tbody>
  </table>
</div>
<?php if($zone == 'form' || $zone == 'demande') { ?>
  <div class="right demi">
    <?php
    if($add == '1') {
	echo '<h4>Ajout</h4>';
	
    } else {
	$zone = 'demande';  
    
	$sqlFormSelect = "SELECT * FROM fm_form WHERE id_form ='$id_form'"; // nom de la table ! requette
	$resultFormSelect = mysqli_query($connexion, $sqlFormSelect ) or die(mysqli_error());
	$dataFormSelect = mysqli_fetch_array($resultFormSelect);
	
	echo '<h4>Modification</h4>';
	
      
    }
    ?>
    
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="titre">Titre</label>
		<input id="titre" type="text" name="titre"  value="<?php echo utf8_encode($dataFormSelect['titre']); ?>">
	    </div>
	    <div>
		<label for="email">Email</label>
		<input id="email" type="text" name="email"  value="<?php echo utf8_encode($dataFormSelect['email']); ?>">
	    </div>
	    <div>
		<label for="remarque">Remarques</label>
		<textarea id="remarque" name="remarque"><?php echo utf8_encode($dataFormSelect['remarque']); ?></textarea>
	    </div>
	    <input type="hidden" name="id_form" value="<?php echo $id_form; ?>">
	    <input type="hidden" name="num_dep" value="<?php echo $num_dep; ?>">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveForm" class="btn" value="Enregistrer">'; 
		  if($id_form) { echo '<input type="submit" name="traduction" class="btn" value="Traduction">'; } } ?>
	</form>
  </div>
  
<?php
/****************************************************************************************************************************
 * ****************************************** ZONE DE TRADUCTION ************************************************************
 * **************************************************************************************************************************/
if($traduction == 'Traduction') {
    //traduction allemande
    $sqlFormTradDe = "SELECT * FROM fm_form_trad WHERE id_form ='$id_form' AND langue = 'de'"; // nom de la table ! requette
    $resultFormTradDe = mysqli_query($connexion, $sqlFormTradDe ) or die(mysqli_error());
    $dataFormTradDe = mysqli_fetch_array($resultFormTradDe);
    
    //traduction anglaise
    $sqlFormTradEn = "SELECT * FROM fm_form_trad WHERE id_form ='$id_form' AND langue = 'en'"; // nom de la table ! requette
    $resultFormTradEn = mysqli_query($connexion, $sqlFormTradEn ) or die(mysqli_error());
    $dataFormTradEn = mysqli_fetch_array($resultFormTradEn);
    
    //traduction italiennne
    $sqlFormTradIt = "SELECT * FROM fm_form_trad WHERE id_form ='$id_form' AND langue = 'it'"; // nom de la table ! requette
    $resultFormTradIt = mysqli_query($connexion, $sqlFormTradIt ) or die(mysqli_error());
    $dataFormTradIt = mysqli_fetch_array($resultFormTradIt);
?>
<hr class="clear">
<div class="left">
  <h4>Traduction allemande</h4>
  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<div>
	    <label for="titre_trad">Titre</label>
	    <input id="titre_trad" type="text" name="titre_trad"  value="<?php echo utf8_encode($dataFormTradDe['titre_trad']); ?>">
	</div>
	<div>
	    <label for="remarque_trad">Remarques</label>
	    <textarea id="remarque_trad" name="remarque_trad"><?php echo utf8_encode($dataFormTradDe['remarque_trad']); ?></textarea>
	</div>
	<input type="hidden" name="id_form" value="<?php echo $id_form ?>">
	<input type="hidden" name="id_trad" value="<?php echo $dataFormTradDe['id'];; ?>">
	<input type="hidden" name="langue" value="de">
	<input type="hidden" name="traduction" value="Traduction">
	<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveTrad" class="btn" value="Enregistrer">'; } ?>
    </form>
</div>
<div class="right">
  <h4>Traduction italienne</h4>
  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<div>
	    <label for="titre_trad">Titre</label>
	    <input id="titre_trad" type="text" name="titre_trad"  value="<?php echo utf8_encode($dataFormTradIt['titre_trad']); ?>">
	</div>
	<div>
	    <label for="remarque_trad">Remarques</label>
	    <textarea id="remarque_trad" name="remarque_trad"><?php echo utf8_encode($dataFormTradIt['remarque_trad']); ?></textarea>
	</div>
	<input type="hidden" name="id_form" value="<?php echo $id_form; ?>">
	<input type="hidden" name="id_trad" value="<?php echo $dataFormTradIt['id']; ?>">
	<input type="hidden" name="langue" value="it">
	<input type="hidden" name="traduction" value="Traduction">
	<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveTrad" class="btn" value="Enregistrer">'; } ?>
    </form>
</div>
<div class="middle">
  <h4>Traduction anglaise</h4>
  <form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	<div>
	    <label for="titre_trad">Titre</label>
	    <input id="titre_trad" type="text" name="titre_trad"  value="<?php echo utf8_encode($dataFormTradEn['titre_trad']); ?>">
	</div>
	<div>
	    <label for="remarque_trad">Remarques</label>
	    <textarea id="remarque_trad" name="remarque_trad"><?php echo utf8_encode($dataFormTradEn['remarque_trad']); ?></textarea>
	</div>
	<input type="hidden" name="id_form" value="<?php echo $id_form; ?>">
	<input type="hidden" name="id_trad" value="<?php echo $dataFormTradEn['id']; ?>">
	<input type="hidden" name="langue" value="en">
	<input type="hidden" name="traduction" value="Traduction">
	<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveTrad" class="btn" value="Enregistrer">'; } ?>
    </form>
</div>
<?php
}

/****************************************************************************************************************************
 * ****************************************** AFFICHAGE DES DEMANDES ********************************************************
 * **************************************************************************************************************************/
if($zone == 'demande') {
?>
<hr class="clear">
  <div>
    <h3>Demandes</h3>
    <div class="right">
      <form action="_formulaire/inc/export.php" method="get">
	<div class="input21">
	  <label for="start">Date début</label>
	  <input type="date" name="start" id="start" placeholder="2015-01-01">
	</div>
	<div class="input22">
	  <label for="end">Date fin</label>
	  <input type="date" name="end" id="end" placeholder="2015-12-31">
	</div>
	<input type="hidden" name="zone" value="form">
	<input type="hidden" name="id_form" value="<?php echo $id_form; ?>">
	<input type="hidden" name="num_dep" value="<?php echo $num_dep; ?>">
	<input type="submit" class="btn" value="Export">
      </form>
    </div>
  

<?php
/************************************* Formulaire d'édition des demandes *************************************/
if($id_demande) {
  
$sqlInscripSelect = "SELECT * FROM fm_inscription WHERE id ='$id_demande'"; // nom de la table ! requette
$resultInscripSelect = mysqli_query($connexion, $sqlInscripSelect ) or die(mysqli_error());
$dataInscripSelect = mysqli_fetch_array($resultInscripSelect);

?>
<hr class="clear">
<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
<div class="left demi">
		<div class="left demi">
			<div>
				<label for="institution">Institution</label>
				<input type="text" name="institution" id="institution" value="<?php echo utf8_encode($dataInscripSelect['institution']); ?>">
			</div>
			<div>
				<label for="prenom">Prénom</label>
				<input type="text" name="prenom" id="prenom" value="<?php echo utf8_encode($dataInscripSelect['prenom']); ?>">
			</div>
			<div>
				<label for="nom">Nom</label>
				<input type="text" name="nom" id="nom" value="<?php echo utf8_encode($dataInscripSelect['nom']); ?>">
			</div>
			<div>
				<label for="telephone">N° de téléphone</label>
				<input type="text" name="telephone" id="telephone" value="<?php echo utf8_encode($dataInscripSelect['telephone']); ?>">
			</div>
			<div>
				<label for="email">Email</label>
				<input type="email" name="email" id="email" value="<?php echo utf8_encode($dataInscripSelect['email']); ?>">
			</div>
		</div>
		<div class="right demi">
			<div>
				<label for="adresse">Adresse</label>
				<input type="text" name="adresse" id="adresse" value="<?php echo utf8_encode($dataInscripSelect['adresse']); ?>">
			</div>
			<div>
				<label for="npa">NPA</label>
				<input type="text" name="npa" id="npa" value="<?php echo utf8_encode($dataInscripSelect['npa']); ?>">
			</div>
			<div>
				<label for="lieu">Lieu</label>
				<input type="text" name="lieu" id="lieu" value="<?php echo utf8_encode($dataInscripSelect['lieu']); ?>">
			</div>
			<div>
				<label for="commentaire">Commentaire</label>
				<textarea name="commentaire" id="commentaire"><?php echo utf8_encode($dataInscripSelect['commentaire']); ?></textarea>
			</div>
		</div>
		<hr class="clear">
		<div class="left demi">
<?php include($_COOKIE['indexApp']."/inc/liste_form.php"); ?>
		</div>
</div>
<div class="right demi" style="border-left:1px dotted #babac0; padding-left:15px;">
<?php include($_COOKIE['indexApp']."/inc/liste_form_priv.php"); ?>
			<div>
				<label for="remarque">Remarques</label>
				<textarea name="remarque" id="remarque" value="<?php echo utf8_encode($dataInscripSelect['remarque']); ?>"></textarea>
			</div>
			<div>
				<label for="statut">Statut</label>
				<select id="statut" name="statut">
				  <option <?php if($dataInscripSelect['statut']==0) echo 'selected="selected"'; ?> value="0">Nouveau</option>
				  <option <?php if($dataInscripSelect['statut']==1) echo 'selected="selected"'; ?> value="1">Traité</option>
				</select>
			</div>
		
<?php	//variable à envoyer propre à la page
	echo '<input type="hidden" name="id_form" value="'.$id_form.'">';
	echo '<input type="hidden" name="id_demande" value="'.$id_demande.'">';
	echo '<input type="submit" name="modInsc" class="btn" value="Enregistrer">';
?>
</div>
	</form>	
	<div class="clear"></div>


<?php } /************************************* Fin du formulaire d'édition *************************************/?>
  
    <table class="table clear" >
      <thead>
	<tr>
	  <th>Contact</th>
	  <th>Date</th>
  <?php
//affichage des libellé du formulaire
$champs;
$sql_form = "SELECT titre, type, ordre FROM fm_champ WHERE id_form = '$id_form' ORDER BY ordre";
$result_form = mysqli_query($connexion, $sql_form) or die(mysqli_error());	
while ($data_form = mysqli_fetch_array($result_form)) {
  if($data_form['type'] < '90') {
    echo '<th>'.utf8_encode($data_form['titre']).'</th>';
    $champs[] = $data_form['ordre'];
  }
}
  ?>
	  <th>Statut</th>
	  <?php if($_COOKIE['role']=='e') {?><th style="width:45px;">&nbsp; </th><?php } ?>
	</tr>
      </thead>
      <tbody>
<?php
//liste des inscriptions
$sqlInscripAtt = "SELECT * FROM fm_inscription WHERE id_form = '$id_form' AND num_dep IS NULL ORDER BY date_crea DESC";
$resultInscripAtt = mysqli_query($connexion, $sqlInscripAtt) or die(mysqli_error());	
while ($dataInscripAtt = mysqli_fetch_array($resultInscripAtt)) {
      if ($id_demande == $dataInscripAtt['id'] ) {
	echo '<tr class="select">'; // pour l'altérnance des couleurs
      } else {
	echo '<tr>'; // pour l'altérnance des couleurs
      }
      //champs par default
	echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=form&amp;add=0&amp;id_form='.$id_form.'&amp;id_demande='.$dataInscripAtt['id'].'">'.utf8_encode($dataInscripAtt['nom']).' '.utf8_encode($dataInscripAtt['prenom']).'</a></td>';
	echo '<td>'.date("d.m.Y",mktime($dataInscripAtt['date_crea'])).'</td>';
	
	//boucle pour récupérer les informations champ1-9 sans la séparation
	for($i = 0; $i < count($champs); ++$i) {
	  $libelle = 'champ'.$champs[$i];
	  echo '<td>'.utf8_encode($dataInscripAtt[$libelle]).'</td>';
	}
	
	//statut de la demande
	echo '<td>';
	if($dataInscripAtt['statut'] == 0){echo 'new';}else{echo 'trait&eacute;';}
	echo '</td>';
	
	//Formulaire d'édition / suppression
	if($_COOKIE['role']=='e') {	
	  echo '<td>';
	    echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=form&amp;add=0&amp;id_form='.$id_form.'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimer cette demande?\')">
		<input type="hidden" name="id" value="'.$dataInscripAtt['id'].'">
		<input name="deleteInsc" class="btn_suppr" type="submit" value="Supprimer" title="supprimer la demande">
	    </form>';
	  echo '</td>';
	}
      echo '</tr>';
}
?>
      </tbody>
    </table>
  </div>
<?php } //fin demande
} //fin form ?>
<hr class="clear">