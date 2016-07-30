<?php
/**
* INC/AFFICHE_FORM.PHP
*
* Affichage du formulaire suivant la catégorie.
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 08.07.2015
*/


/************************************* APPLICATION *************************************/
//rechercher les form de la catégorie
if($langue == 'fr') {
$sql_form = "SELECT * FROM fm_champ
	    WHERE id_form = '$id_form'
	    AND visible = 1
	    ORDER BY ordre";
} else {
$sql_form = "SELECT * FROM fm_champ
	    LEFT JOIN fm_champ_trad
	    ON fm_champ_trad.id_champ = fm_champ.id_champ
	    WHERE id_form = '$id_form'
	    AND visible = 1
	    AND (langue = '$langue' OR langue IS NULL)
	    ORDER BY ordre";
}
$result_form = mysqli_query($connexion, $sql_form) or die(mysqli_error());	
while ($data_form = mysqli_fetch_array($result_form)) {
	$valeur = $data_form['valeur'];
	if($data_form['valeur_trad']) $valeur = $data_form['valeur_trad'];
	if($data_form['type'] < 90) {
	echo '<div>';
	      echo '<label for="champ'.$data_form['ordre'].'">';
	      if($data_form['titre_trad']) {
		echo utf8_encode($data_form['titre_trad']);
	      }else {
		echo utf8_encode($data_form['titre']);
	      }
	      if($data_form['required']==1) echo ' <span class="import">*</span>'; 
	      echo'</label>';
	}
	switch ($data_form['type']) {
	    case 0:
		break;
	    case 1:
		echo inputText('champ'.$data_form['ordre'],'');
		echo '</div>';
		break;	
	    case 2:
		if($valeur == '$title') {
			echo textArea('champ'.$data_form['ordre'], $title); //uniquement pour la diffusion - affichage du titre du film
		}else {
			echo textArea('champ'.$data_form['ordre'], '');
		}
		echo '</div>';
		break;
	    case 3:
		echo inputCheck('champ'.$data_form['ordre'], $valeur,'');
		echo '</div>';
		break;
	    case 4:
		echo inputRadio('champ'.$data_form['ordre'], $valeur,'');
		echo '</div>';
		break;
	    case 5:
		echo inputSelect('champ'.$data_form['ordre'], $valeur,'');
		echo '</div>';
		break;
	    case 6:
		echo inputEmail('champ'.$data_form['ordre'],'');
		echo '</div>';
		break;
	    case 7:
		echo inputDate('champ'.$data_form['ordre'],'');
		echo '</div>';
		break;
	    case 98:
		echo '<h3 style="float:none;">';
		if($data_form['titre_trad']) {
		  echo utf8_encode($data_form['titre_trad']);
		}else {
		  echo utf8_encode($data_form['titre']);
		}
		echo '</h3>';
		break;
	    case 99:
		echo '</div><div class="right demi">';
	}
}
?>

	
