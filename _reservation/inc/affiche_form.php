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

/******************************* TRAITEMENT DES FORMS *******************************/

/*** 1. Input type text***/
function inputText($name) {
	$inputText = '<input type="text" id="'.$name.'" name="'.$name;
	if(substr($name, 0, -1) == 'champ') {
	    $inputText .= '[]';  
	}
	$inputText .= '">';
	
	return $inputText;
}
/*** 2. Textarea***/
function textArea($name) {
	$inputText = '<textarea id="'.$name.'" name="'.$name;
	if(substr($name, 0, -1) == 'champ') {
	    $inputText .= '[]';  
	}
	$inputText .= '"></textarea>';
	
	return $inputText;
}
/*** 3. Input type checkbox***/
function inputCheck($name, $valeur) {
	if($name == 'newsletter') {
	    $inputText = '<span class="block"><input type="checkbox" id="'.$name.'" name="'.$name.'" value="1" onClick="afficher();">'.utf8_encode($valeur).'</span>';
	    $inputText .= '<div id="champ_cache">
			      <label for="adresse">Adresse privée</label>
			      <input type="text" id="adresse" name="adresse">
			      <label for="npa">NPA</label>
			      <input type="text" id="npa" name="npa">
			      <label for="lieu">Lieu</label>
			      <input type="text" id="lieu" name="lieu">
			      <label for="telephone">Téléphone privé</label>
			      <input type="text" id="telephone" name="telephone">
			  </div>';
	}else {
	    $arrayValue = explode(',', $valeur);
	    $inputText = '';
	    foreach($arrayValue as $cle => $element) {
		$inputText .= '<span class="block"><input type="checkbox" id="'.$name.'" name="'.$name.'[]" value="'.utf8_encode($element).'">'.utf8_encode($element).'</span>';
	    }
	}
	
	return $inputText;
}
/*** 4. Input type radio***/
function inputRadio($name, $valeur) {
	$arrayValue = explode(',', $valeur);
	$inputText = '';
	
	foreach($arrayValue as $cle => $element) {
	    $inputText .= '<span class="block"><input type="radio" id="'.$name.'" name="'.$name;
	if(substr($name, 0, -1) == 'champ') {
	    $inputText .= '[]';  
	}
	$inputText .= '" value="'.utf8_encode($element).'">'.utf8_encode($element).'</span>';
	}
	
	return $inputText;
}
/*** 5. Input type select***/
function inputSelect($name, $valeur) {
	$arrayValue = explode(',', $valeur);
	
	$inputText = '<select';
	if($name == 'place') {
	  $inputText .= ' required';
	}
	$inputText .= ' id="'.$name.'" name="'.$name;
	if(substr($name, 0, -1) == 'champ') {
	    $inputText .= '[]';  
	}
	$inputText .= '">';

	foreach($arrayValue as $cle => $element) {
	    $inputText .= '<option value="'.utf8_encode($element).'">'.utf8_encode($element).'</option>';
	}
	$inputText .= '</select>';
	
	return $inputText;
}
/*** 6. Input type mail***/
function inputEmail($name) {
	$inputText = '<input type="email" id="'.$name.'" name="'.$name;
	if(substr($name, 0, -1) == 'champ') {
	    $inputText .= '[]';  
	}
	$inputText .= '" required>';
	
	return $inputText;
}

/************************************* APPLICATION *************************************/
//rechercher les form de la catégorie
$sql_form = "SELECT re_reserv.id_categorie, re_form.titre, name, type, valeur, ordre FROM re_reserv
	    JOIN re_form
	    ON re_reserv.id_categorie = re_form.id_categorie
	    WHERE id_reserv = '$reserv'
	    ORDER BY ordre";
$result_form = mysqli_query($connexion, $sql_form) or die(mysqli_error());	
while ($data_form = mysqli_fetch_array($result_form)) {
	echo '<div>';
	if($data_form['type'] != 7 && $data_form['name'] !== 'newsletter') {
	      echo '<label for="'.$data_form['name'].'">'.utf8_encode($data_form['titre']).'</label>';
	}
	switch ($data_form['type']) {
	    case 0:
		break;
	    case 1:
		echo inputText($data_form['name']);
		break;
	    case 2:
		echo textArea($data_form['name']);
		break;
	    case 3:
		echo inputCheck($data_form['name'], $data_form['valeur']);
		break;
	    case 4:
		echo inputRadio($data_form['name'], $data_form['valeur']);
		break;
	    case 5:
		echo inputSelect($data_form['name'], $data_form['valeur']);
		break;
	    case 6:
		echo inputEmail($data_form['name']);
		break;
	    case 7:
		echo '<hr>';
	}
	echo '</div>';
}
?>
<script type="text/javascript">
  document.getElementById("champ_cache").style.display = "none";
   
  function afficher() {
      var coche = document.getElementById("newsletter");
       
      if(coche.checked) {
	  document.getElementById("champ_cache").style.display = "block";
      } else {
	  document.getElementById("champ_cache").style.display = "none";
      }
  }
</script>

	
