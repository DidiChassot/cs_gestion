<?php
/**
* INDEX RESERVATION / SM1 - Gestion des inscriptions
*
* Gestion des inscriptions
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 29.04.2015 - 27.07.2015
*/

/************************************* APPLICATION *************************************/
?>
<h2>Gestion des événements</h2>

<div class="left demi">
  <h3>Événements</h3>
	
  <table class="table">
    <thead>
      <tr>
	<th>ID</th>
	<th>Événements à venir</th>
	<th>Date</th>
	<th>Liste</th>
      </tr>
    </thead>
    <tbody>
<?php
/****************************************************************************************************************************************/
/********************************************************* AFFICHAGE DES EVENTS *********************************************************/
/****************************************************************************************************************************************/

    $sqlEventAtt = "SELECT re_event.id_event, re_event.titre, date, COUNT(DISTINCT re_reserv.id_reserv) AS reserv FROM re_event
			LEFT JOIN re_reserv
			ON re_reserv.id_event = re_event.id_event
			WHERE date >= CURDATE()
			AND concours IS NULL
			GROUP BY re_event.id_event
			ORDER BY date ";
    $resultEventAtt = mysqli_query($connexion, $sqlEventAtt ) or die(mysqli_error());
	   
    while ($dataEventAtt = mysqli_fetch_array($resultEventAtt)) {
      // définir quel event a été sélectionné	
      if ($event == $dataEventAtt['id_event'] ) {
	echo '<tr class="select">'; // pour l'altérnance des couleurs
      } else {
	echo '<tr>'; // pour l'altérnance des couleurs
      }
      //id de la catégorie
      echo '<td>'.$dataEventAtt['id_event'].'</td>';
      
      //titre de la catégorie
      echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=event&amp;add=0&amp;event='.$dataEventAtt['id_event'].'">'.utf8_encode($dataEventAtt['titre']).'</a></td>'; //renvoi l'id de la catégorie en "GET"
      
      //date de l'évent
      echo '<td>'.date("d-m-Y H:i",strtotime($dataEventAtt['date'])).'</td>';
      
      //n. de liste
      echo '<td>'.$dataEventAtt['reserv'].'</td>';
			    
      echo '</tr>'; //fermeture de la ligne
    }
?>
    </tbody>
  </table>
</div>

<?php
/********************************************************* FORMULAIRE ADD/MODIF EVENTS *********************************************************/
if($_REQUEST['zone']=='event' || $_REQUEST['zone']=='reserv' ) {
?>

<hr class="clear" name="liste" id="liste">

<h2><?php echo $dataEventSelect['titre']; ?></h2>
<div class="left">
<h3>Listes de réservations</h3>

<table class="table">
  <thead>
    <tr>
      <th>Cat.</th>
      <th>Titre</th>
      <th>Url</th>
      <th>Quotas</th>
      <th>Réserv.</th>
    </tr>
  </thead>
  <tbody>
<?php 
/****************************************************************************************************************************************/
/********************************************************* LISTE DES RESERVATIONS *********************************************************/
/****************************************************************************************************************************************/ 
$sqlReservAtt = "SELECT id_reserv, id_categorie, titre, quotas, url FROM re_reserv WHERE id_event = '$event'"; // nom de la table ! requette
$resultReservAtt = mysqli_query($connexion, $sqlReservAtt ) or die(mysqli_error());   
while ($dataReservAtt = mysqli_fetch_array($resultReservAtt)) {
      // définir quelle liste a été sélectionné	
      if ($reserv == $dataReservAtt['id_reserv'] ) {
	echo '<tr class="select">'; // pour l'altérnance des couleurs
      } else {
	echo '<tr>'; // pour l'altérnance des couleurs
      }
    //catégorie
    echo '<td>'.$dataReservAtt['id_categorie'].'</td>';
    
    //titre
    echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=reserv&amp;event='.$event.'&amp;reserv='.$dataReservAtt['id_reserv'].'&amp;cat='.$dataReservAtt['id_categorie'].'#liste">'.utf8_encode($dataReservAtt['titre']).'</a></td>';
    
    //url
    echo '<td>'.$dataReservAtt['url'].'</td>';
    
    //Quotas
    echo '<td>'.$dataReservAtt['quotas'].'</td>';
    
    //place déja prise -> liste
$sql_Allreserv = "SELECT SUM(place) AS place FROM re_inscription WHERE id_reserv = '$dataReservAtt[id_reserv]' GROUP BY id_reserv"; // nom de la table ! requette
$result_Allreserv = mysqli_query($connexion, $sql_Allreserv ) or die(mysqli_error());   
$data_Allreserv = mysqli_fetch_array($result_Allreserv);
    if(($dataReservAtt['quotas'] - $data_Allreserv['place']) < 2 ) {
      echo '<td class="import">';
    } elseif(($dataReservAtt['quotas'] - $data_Allreserv['place']) < 10 ) {
      echo '<td class="warning">';
    } else echo '<td>';
    echo $data_Allreserv['place'].'</td>';
    
  echo '</tr>';
}
?>
  </tbody>
</table>
</div>

<?php if($_REQUEST['zone']=='reserv') { ?>
<div class="right">

  <form style="margin-top:-35px;margin-bottom:30px;" action="_reservation/inc/export.php" method="get">
    <input type="hidden" name="zone" value="reserv">
    <input type="hidden" name="id_event" value="<?php echo $event; ?>">
    <input type="hidden" name="id_reserv" value="<?php echo $reserv; ?>">
    <input type="hidden" name="id_categorie" value="<?php echo $cat; ?>">
    <input type="submit" class="btn" value="Export">
  </form>

  <form style="margin-top:-35px;margin-bottom:30px;float: right;" action="_reservation/inc/export_portes.php" method="get">
    <input type="hidden" name="zone" value="reserv">
    <input type="hidden" name="id_event" value="<?php echo $event; ?>">
    <input type="hidden" name="id_reserv" value="<?php echo $reserv; ?>">
    <input type="hidden" name="id_categorie" value="<?php echo $cat; ?>">
    <input type="submit" class="btn" value="Export Portes">
  </form>
<?php
if($add == 1) {
  echo '<h4>Ajout</h4>';
  echo '<form action="'.$_SERVER['PHP_SELF'].'?add=0&amp;zone=reserv&amp;event='.$event.'&amp;reserv='.$reserv.'#liste" method="post">';
    include($_COOKIE['indexApp']."/inc/affiche_form.php");
    //variable à envoyer propre à la page
    echo '<input type="hidden" name="id_reserv" value="'.$reserv.'">';
    echo '<input type="submit" name="addInsc" class="btn" value="Enregistrer">';
  echo '</form>';
  
} elseif($mod == 1) { 
    echo $sqlInscripSelect = "SELECT * FROM re_inscription WHERE id ='$inscrip'"; // nom de la table ! requette
    $resultInscripSelect = mysqli_query($connexion, $sqlInscripSelect ) or die(mysqli_error());
    $dataInscripSelect = mysqli_fetch_array($resultInscripSelect);

?>
<h4>Modification</h4>
<form action="<?php $_SERVER['PHP_SELF']; ?>?add=0&amp;zone=reserv&amp;event=<?php echo $event; ?>&amp;reserv=<?php echo $reserv; ?>#liste" method="post">
  <div>
    <label for="prenom">Prénom</label>
    <input type="text" name="prenom" id="prenom" value="<?php echo utf8_encode($dataInscripSelect['prenom']); ?>">
  </div>
  <div>
    <label for="nom">Nom</label>
    <input type="text" name="nom" id="nom" value="<?php echo utf8_encode($dataInscripSelect['nom']); ?>">
  </div>
  <div>
    <label for="email">Email</label>
    <input type="text" name="email" id="email" value="<?php echo utf8_encode($dataInscripSelect['email']); ?>">
  </div>
  <div>
    <label for="place">Nombre d'invitations souhaitées</label>
    <input type="text" name="place" id="place" value="<?php echo utf8_encode($dataInscripSelect['place']); ?>">
  </div>
  <input type="hidden" name="id" value="<?php echo $dataInscripSelect['id']; ?>">
  <input type="hidden" name="id_reserv" value="<?php echo $reserv; ?>">
  <input type="submit" name="addInsc" class="btn" value="Enregistrer">
</form>
<?php } ?>
</div>

<div class="middle">
  <h4>Liste des inscriptions</h4>
<?php if($_COOKIE['role']=='e') { ?>
  <form class="action" action="<?php $_SERVER['PHP_SELF']; ?>#liste" style="margin-top:-35px;" method="get">
    <input type="hidden" name="zone" value="reserv">
    <input type="hidden" name="add" value="1">
    <input type="hidden" name="event" value="<?php echo $event; ?>">
    <input type="hidden" name="reserv" value="<?php echo $reserv; ?>">
    <input type="submit" class="btn" value="Ajouter">
  </form>
<?php } ?>
  <table class="table">
    <thead>
      <tr>
	<th>Contact</th>
	<th>Email</th>
	<th>Places</th>
<?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
      </tr>
    </thead>
    <tbody>
<?php
/**********************************************************************************************************************************************/
/********************************************************* AFFICHAGE DES INSCRIPTIONS *********************************************************/
/**********************************************************************************************************************************************/

    $sqlInscripAtt = "SELECT id, CONCAT(nom, ' ', prenom) AS contact, email, place FROM re_inscription WHERE id_reserv = '$reserv' AND actif = '1' ORDER BY nom";
    $resultInscripAtt = mysqli_query($connexion, $sqlInscripAtt ) or die(mysqli_error()); 
    while ($dataInscripAtt = mysqli_fetch_array($resultInscripAtt)) {
      echo '<tr>';
	//Contact
	echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=reserv&amp;mod=1&amp;event='.$event.'&amp;reserv='.$reserv.'&amp;inscrip='.$dataInscripAtt['id'].'#liste">'.utf8_encode($dataInscripAtt['contact']).'</a></td>';
	
	//Email
	echo '<td>'.$dataInscripAtt['email'].'</td>';
	
	//Place
	echo '<td>'.$dataInscripAtt['place'].'</td>';
	
	
	//Formulaire d'édition / suppression
	if($_COOKIE['role']=='e') {	
	  echo '<td>';
	    echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'?zone=reserv&event='.$event.'&reserv='.$reserv.'&cat='.$cat.'#liste" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cet événement?\')">
		<input type="hidden" name="id" value="'.$dataInscripAtt['id'].'">
		<input name="deleteInsc" class="btn_suppr" type="submit" value="Supprimer">
	    </form>';
	  echo '</td>';
	}
      echo '</tr>';
    }
?>
    </tbody>
  </table>
</div>
<?php } /*fin reserv*/ ?>
<?php } /*fin event*/?>
<hr class="clear">