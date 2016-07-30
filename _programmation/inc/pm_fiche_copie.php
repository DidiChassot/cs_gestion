<?php
/**
* PM_FICHE_COPIE
*
* Fiche copie repris dans plusieurs sous-menu
*
* @copyright	Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author	Cindy Chassot
* @version	0.1 - 16.07.2015
*/
?>

	   <div>
		<label for="provenance">Provenance</label>
		<input id="provenance" type="text" value="<?php echo utf8_encode($dataCopieSelect['provenance']); ?>" name="provenance" placeholder="">
	    </div>
	    <div>
		<div class="input31">
			<label for="duree">Durée ( min. )</label>
			<input id="duree" type="text" value="<?php echo utf8_encode($dataCopieSelect['duree']); ?>" name="duree" placeholder="">
		</div>
		<div class="input32">
			<label for="format">Format</label>
			<input list="format_donnes" id="format" type="text" value="<?php echo utf8_encode($dataCopieSelect['format']); ?>" name="format" placeholder="">	
			 <datalist id="format_donnes">
			   <option value="35 mm">
			   <option value="16 mm">
			   <option value="DCP">
			   <option value="DVD">
			   <option value="Blu-Ray">
			   <option value="Béta num">
			 </datalist>
		</div>
		<div class="input32">
			<label for="cryptage">Cryptage</label>
			<input id="cryptage" type="text" value="<?php echo utf8_encode($dataCopieSelect['cryptage']); ?>" name="cryptage" placeholder="">
		</div>		
	    </div>
	    <div>
		<label for="etat">Etat de la copie</label>
		<input id="etat" type="text" value="<?php echo utf8_encode($dataCopieSelect['etat']); ?>" name="etat" placeholder="">
	    </div>
	    <div>
		<label for="version">Version</label>
		<input id="version" type="text" value="<?php echo utf8_encode($dataCopieSelect['version']); ?>" name="version" placeholder="">
	    </div>
	    <div>
		<label for="soustitre">Sous-titres</label>
		<input id="soustitre" type="text" value="<?php echo utf8_encode($dataCopieSelect['soustitre']); ?>" name="soustitre" placeholder="">
	    </div>
	    <div>
		<label for="intertitre">Inter-titres</label>
		<input id="intertitre" type="text" value="<?php echo utf8_encode($dataCopieSelect['intertitre']); ?>" name="intertitre" placeholder="">
	    </div>
	    <div>
		<label for="commentaire">Commentaire</label>
		<textarea id="commentaire" name="commentaire"><?php echo afficheHtml(utf8_encode($dataCopieSelect['commentaire'])); ?></textarea>
	    </div>
	    <div>
		<label for="id_nom">Nomenclature</label>
		<select id="id_nom" name="id_nom">
			<option value="NULL">---</option>
<?php
//requète pour récupérer la liste des salles
$sql_nom = "SELECT * FROM pm_copie_nom";
$result_nom = mysqli_query($connexion, $sql_nom) or die(mysqli_error());

//boucle pour récupérer toute les donneés	
while ($data_nom = mysqli_fetch_array($result_nom)) {
	echo '<option value="'.$data_nom['id_nom'].'"';
	if($dataCopieSelect['id_nom'] == $data_nom['id_nom']) echo ' selected="selected"';
	echo '>'.utf8_encode($data_nom['nomenclature']).'</option>';	
}
?>
				    </select>
				</div>
	    <div>
		<label for="statut">Statut</label>
		<select id="statut" name="statut">
<?php
//requète pour récupérer la liste des salles
$sql_stat = "SELECT * FROM pm_copie_stat";
$result_stat = mysqli_query($connexion, $sql_stat) or die(mysqli_error());
echo '<option value="0"';
	if($dataCopieSelect['statut'] == 0) echo ' selected="selected"';
	echo '>-----</option>';	
//boucle pour récupérer toute les donneés	
while ($data_stat = mysqli_fetch_array($result_stat)) {
	echo '<option value="'.$data_stat['id_stat'].'"';
	if($dataCopieSelect['statut'] == $data_stat['id_stat']) echo ' selected="selected"';
	echo '>'.utf8_encode($data_stat['statut']).'</option>';	
}
?>
		</select>
	    </div>