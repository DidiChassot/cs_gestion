<?php
/**
* INC/LIST_FORM_PRIV.PHP
*
* Affichage du formulaire suivant la catégorie avec le contenu du formulaire remplir par le visiteur.
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 08.07.2015
*/

/******************************* TRAITEMENT DES FORMS *******************************/
/************************************* APPLICATION *************************************/
//rechercher les form de la catégorie
$sql_form_priv = "SELECT * FROM fm_champ WHERE id_form = '$id_form' AND visible = 0 ORDER BY ordre";
$result_form_priv = mysqli_query($connexion, $sql_form_priv) or die(mysqli_error());	
while ($data_form_priv = mysqli_fetch_array($result_form_priv)) {
	if($data_form_priv['type'] != 99) {
	echo '<div>';
	      echo '<label for="champ'.$data_form_priv['ordre'].'">'.utf8_encode($data_form_priv['titre']);
	      if($data_form_priv['required']==1) echo ' <span class="import">*</span>'; 
	      echo'</label>';
	}
	switch ($data_form_priv['type']) {
	    case 0:
		break;
	    case 1:
		echo inputText('champ'.$data_form_priv['ordre'], $dataInscripSelect['champ'.$data_form_priv['ordre']]);
		echo '</div>';
		break;
	    case 2:
		echo textArea('champ'.$data_form_priv['ordre'], $dataInscripSelect['champ'.$data_form_priv['ordre']]);
		echo '</div>';
		break;
	    case 3:
		echo inputCheck('champ'.$data_form_priv['ordre'], $data_form_priv['valeur'], $dataInscripSelect['champ'.$data_form_priv['ordre']]);
		echo '</div>';
		break;
	    case 4:
		echo inputRadio('champ'.$data_form_priv['ordre'], $data_form_priv['valeur'], $dataInscripSelect['champ'.$data_form_priv['ordre']]);
		echo '</div>';
		break;
	    case 5:
		echo inputSelect('champ'.$data_form_priv['ordre'], $data_form_priv['valeur'], $dataInscripSelect['champ'.$data_form_priv['ordre']]);
		echo '</div>';
		break;
	    case 6:
		echo inputEmail('champ'.$data_form_priv['ordre'], $dataInscripSelect['champ'.$data_form_priv['ordre']]);
		echo '</div>';
		break;
	    case 7:
		echo inputDate('champ'.$data_form_priv['ordre'], $dataInscripSelect['champ'.$data_form_priv['ordre']]);
		echo '</div>';
	    case 98:
		echo '<h3 style="float:none;">'.utf8_encode($data_form_priv['titre']).'</h3>';
		break;
	    case 99:
		echo '</div><div class="right demi">';
	}
}
?>

	
