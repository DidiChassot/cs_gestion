<?php
/**
* INC/LIST_FORM.PHP
*
* Affichage du formulaire suivant la catégorie avec le contenu du formulaire remplir par le visiteur.
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 08.07.2015
*/


/************************************* APPLICATION *************************************/
//rechercher les form de la catégorie
$sql_form = "SELECT * FROM fm_champ WHERE id_form = '$id_form' AND visible = 1 ORDER BY ordre";
$result_form = mysqli_query($connexion, $sql_form) or die(mysqli_error());	
while ($data_form = mysqli_fetch_array($result_form)) {
	if($data_form['type'] < 90) {
	echo '<div>';
	      echo '<label for="champ'.$data_form['ordre'].'">'.utf8_encode($data_form['titre']);
	      if($data_form['required']==1) echo ' <span class="import">*</span>'; 
	      echo'</label>';
	}
	switch ($data_form['type']) {
	    case 0:
		break;
	    case 1:
		echo inputText('champ'.$data_form['ordre'], $dataInscripSelect['champ'.$data_form['ordre']]);
		echo '</div>';
		break;
	    case 2:
		echo textArea('champ'.$data_form['ordre'], $dataInscripSelect['champ'.$data_form['ordre']]);
		echo '</div>';
		break;
	    case 3:
		echo inputCheck('champ'.$data_form['ordre'], $data_form['valeur'], $dataInscripSelect['champ'.$data_form['ordre']]);
		echo '</div>';
		break;
	    case 4:
		echo inputRadio('champ'.$data_form['ordre'], $data_form['valeur'], $dataInscripSelect['champ'.$data_form['ordre']]);
		echo '</div>';
		break;
	    case 5:
		echo inputSelect('champ'.$data_form['ordre'], $data_form['valeur'], $dataInscripSelect['champ'.$data_form['ordre']]);
		echo '</div>';
		break;
	    case 6:
		echo inputEmail('champ'.$data_form['ordre'], $dataInscripSelect['champ'.$data_form['ordre']]);
		echo '</div>';
		break;
	    case 7:
		echo inputDate('champ'.$data_form['ordre'], $dataInscripSelect['champ'.$data_form['ordre']]);
		echo '</div>';
		break;
	    case 98:
		echo '<h3 style="float:none;">'.utf8_encode($data_form['titre']).'</h3>';
		break;
	    case 99:
		echo '</div><div class="right demi">';
	}
}
?>

	
