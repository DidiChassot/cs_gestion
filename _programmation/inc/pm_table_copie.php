<?php
        while ($dataCopieAtt = mysqli_fetch_array($resultCopieAtt)) {
	    // définir si la copie a été sélectionné	
	    if ($co == $dataCopieAtt['id_copie'] ) {
		echo '<tr class="select">'; // pour l'altérnance des couleurs
	    } else {
		echo '<tr>'; // pour l'altérnance des couleurs
	    }
	    if($dataCopieAtt['provenance'])$provenance = $dataCopieAtt['provenance'];
	    echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=copie&amp;bu='.$bu.'&amp;cy='.$cy.'&amp;fi='.$fi.'&amp;co='.$dataCopieAtt['id_copie'].'#copie">'.html_entity_decode(utf8_encode($provenance)).'</a></td>'; //renvoi l'id du cycle en "GET"
	    echo '<td>'.utf8_encode($dataCopieAtt['format']).'</td>';
	    echo '<td>'.utf8_encode($dataCopieAtt['version']).'</td>';
	    echo '<td>'.utf8_encode($dataCopieAtt['soustitre']).'</td>';
	    echo '<td>'.utf8_encode($dataCopieAtt['duree']).'</td>';
	    echo '<td>';
//requète pour récupérer la liste des salles
$sql_stat = "SELECT statut FROM pm_copie_stat WHERE id_stat = '$dataCopieAtt[statut]'";
$result_stat = mysqli_query($connexion, $sql_stat) or die(mysqli_error());
$data_stat = mysqli_fetch_array($result_stat);
echo utf8_encode($data_stat['statut']);
	    echo '</td>';
	    echo '<td>'.utf8_encode($dataCopieAtt['cote']).'</td>';
	    //Formulaire d'édition / suppression
	    if($_COOKIE['role']=='e') {	
		echo '<td>';
			echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=film&amp;bu='.$bu.'&amp;cy='.$cy.'&amp;fi='.$fi.'#copie" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cette inscription?\')">
				<input type="hidden" name="id_copie" value="'.$dataCopieAtt['id_copie'].'">
				<input name="deleteCopie" class="btn_suppr" type="submit" value="Supprimer">';
		echo '</form></td>';
	    }
			
	    echo '</tr>'; //fermeture de la ligne
	}
?>