<?php
/**
* PM_FICHE_FILM
*
* Fiche film repris dans plusieurs sous-menu
*
* @copyright	Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author	Cindy Chassot
* @version	0.1 - 16.07.2015
*/
?>

	    <div class="input31">
		<label for="prefix_titre_o">Préfixe</label>
		<input id="prefix_titre_o" type="text" value="<?php echo utf8_encode($dataFilmSelect['prefix_titre_o']); ?>" name="prefix_titre_o" placeholder="">
	    </div>
	    <div class="input36">
		<label for="titre_o">Titre principal</label>
		<input id="titre_o" type="text" value="<?php echo utf8_encode($dataFilmSelect['titre_o']); ?>" name="titre_o" placeholder="">
	    </div>
	    <div class="input31">
		<label for="prefix_titre_fr">Préfixe</label>
		<input id="prefix_titre_fr" type="text" value="<?php echo utf8_encode($dataFilmSelect['prefix_titre_fr']); ?>" name="prefix_titre_fr" placeholder="">
	    </div>
	    <div class="input36">
		<label for="titre_fr">Titre secondaire</label>
		<input id="titre_fr" type="text" value="<?php echo utf8_encode($dataFilmSelect['titre_fr']); ?>" name="titre_fr" placeholder="">
	    </div>
	    <div class="input31">
		<label for="anne_prod">Année de prod.</label>
		<input id="anne_prod" type="text" value="<?php echo utf8_encode($dataFilmSelect['annee_prod']); ?>" name="annee_prod" placeholder="">
	    </div>
	    <div class="input36">
		<label for="pays_prod">Pays de production</label>
		<input id="pays_prod" type="text" value="<?php echo utf8_encode($dataFilmSelect['pays_prod']); ?>" name="pays_prod" placeholder="">
	    </div>
	    <div>
		<label for="prefix_realisateur"></label>
		<select id="prefix_realisateur" class="input31" name="prefix_realisateur">
			<option <?php if($prefix_realisateur==0) echo 'selected="selected"'; ?> value="0">---</option>
			<option <?php if($prefix_realisateur==1 || $prefix_realisateur==99) echo 'selected="selected"'; ?> value="1">De</option>
			<option <?php if($prefix_realisateur==2) echo 'selected="selected"'; ?> value="2">Documentaire de</option>
			<option <?php if($prefix_realisateur==3) echo 'selected="selected"'; ?> value="3">Film d'animation de</option>
			<option <?php if($prefix_realisateur==4) echo 'selected="selected"'; ?> value="4">Film collectif de</option>
			<option <?php if($prefix_realisateur==5) echo 'selected="selected"'; ?> value="5">Court métrage de</option>
		</select>
		<label for="realisateur"></label>
		<input id="realisateur" class="input36" type="text" value="<?php echo utf8_encode($dataFilmSelect['realisateur']); ?>" name="realisateur" placeholder="Réalisateur(s)">
	    </div>
	    <div>
		<label for="prefix_acteur"></label>
		<select id="prefix_acteur" class="input31" name="prefix_acteur">
			<option <?php if($prefix_acteur==0) echo 'selected="selected"'; ?> value="0">---</option>
			<option <?php if($prefix_acteur==1 || $prefix_acteur==99) echo 'selected="selected"'; ?> value="1">Avec</option>
			<option <?php if($prefix_acteur==2) echo 'selected="selected"'; ?> value="2">Avec les voix de</option>
		</select>
		<label for="acteur"></label>
		<input id="acteur" class="input36" type="text" value="<?php echo utf8_encode($dataFilmSelect['acteur']); ?>" name="acteur" placeholder="Acteur(s)">
	    </div>
	    <div class="input31">
		<label for="age_legal">Age légal</label>
		<input id="age_legal" type="text" value="<?php echo utf8_encode($dataFilmSelect['age_legal']); ?>" name="age_legal" placeholder="">
	    </div>
	    <div class="input32">
		<label for="age_sugg">Age suggéré</label>
		<input id="age_sugg" type="text" value="<?php echo utf8_encode($dataFilmSelect['age_sugg']); ?>" name="age_sugg" placeholder="">
	    </div>
	    <div class="input33">
		<label for="film_famille"></label>
		<input id="film_famille" type="checkbox" <?php if($dataFilmSelect['film_famille']=='1')echo 'checked'; ?> value="1" name="film_famille">Film Famille
	    </div>
	    <div class="input33">
		<label for="distri"></label>
		<input id="distri" type="checkbox" <?php if($dataFilmSelect['distri']=='1')echo 'checked'; ?> value="1" name="distri">Distribution CS
	    </div>
	    <div class="input21">
		<label for="id_imdb">ID Imdb</label>
		<input id="id_imdb"type="text" value="<?php echo utf8_encode($dataFilmSelect['id_imdb']); ?>" name="id_imdb">
	    </div>
	    <div class="clear"></div>
	    <div>
		<label for="ayants_droits">Ayants droits</label>
		<textarea name="ayants_droits"><?php echo afficheHtml(utf8_encode($dataFilmSelect['ayants_droits'])); ?></textarea>
	    </div>
	    <div>
		<label for="remarque">Remarque</label>
		<textarea name="remarque"><?php echo afficheHtml(utf8_encode($dataFilmSelect['remarque'])); ?></textarea>
	    </div>
	    <div>
		<label for="filemaker">Filemaker</label>
		<textarea name="filemaker"><?php echo afficheHtml(utf8_encode($dataFilmSelect['filemaker'])); ?></textarea>
	    </div>