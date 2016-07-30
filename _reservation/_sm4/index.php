<?php
/**
* INDEX RESERVATION / SM4 - Gestion des concours
*
* Gestion des événements avec réservation
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 30.07.2015
*/


/************************************* APPLICATION *************************************/
?>
<h2>Gestion des Concours</h2>

<div class="left">
  <h3>Événements</h3>
<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>" method="get">
    <input type="hidden" value="event" name="zone">
    <input type="hidden" value="1" name="add">
    <input type="submit" class="btn" value="Ajouter">
  </form>
<?php } ?>
  <form class="action" action="_reservation/inc/export_concours.php" method="get">
    <input type="hidden" name="zone" value="reserv">
    <input type="hidden" name="id_event" value="<?php echo $event; ?>">
    <input type="hidden" name="id_reserv" value="<?php echo $reserv; ?>">
    <input type="hidden" name="id_categorie" value="<?php echo $cat; ?>">
    <input type="submit" class="btn" value="Export">
  </form>
	
  <table class="table">
    <thead>
      <tr>
	<th>ID</th>
	<th>Événements à venir</th>
	<th>Date</th>
	<th>Billets</th>
<?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?>
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
			WHERE (MONTH(date) >= MONTH(CURDATE())) OR (YEAR(date) > YEAR(CURDATE()))
			AND concours IS NOT NULL
			GROUP BY re_event.id_event
			ORDER BY date ";
    $resultEventAtt = mysqli_query($connexion, $sqlEventAtt ) or die(mysqli_error());
	   
    while ($dataEventAtt = mysqli_fetch_array($resultEventAtt)) {
      // définir si le film a été sélectionné	
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
      
      //Formulaire d'édition / suppression
      if($_COOKIE['role']=='e') {	
	echo '<td>';
	  echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cet événement?\')">
	      <input type="hidden" name="id_event" value="'.$dataEventAtt['id_event'].'">
	      <input name="deleteEvent" class="btn_suppr" type="submit" value="Supprimer">
	  </form>';
	echo '</td>';
      }
			    
      echo '</tr>'; //fermeture de la ligne
    }
?>
    </tbody>
  </table>
</div>

<?php
/********************************************************* FORMULAIRE ADD/MODIF *********************************************************/
if($_REQUEST['zone']=='event' || $_REQUEST['zone']=='reserv' ) {
	if($add == 0 || $add == 2 ) {
	  $sqlEventSelect = "SELECT * FROM re_event WHERE id_event ='$event'"; // nom de la table ! requette
	  $resultEventSelect = mysqli_query($connexion, $sqlEventSelect ) or die(mysqli_error());
	  $dataEventSelect = mysqli_fetch_array($resultEventSelect);
		
	  $titre1 = 'Modifier '.utf8_encode($dataEventSelect['titre']);
	  $new_event = FALSE;
	
?>
<div class="right">
<!-----------------------------------/Recherche FILM LIER--------------------------------------------------------->
  <?php include($_COOKIE['indexApp']."/inc/rechercher_film.php"); ?>
</div>
<?php 
} else {
	  $titre1 = 'Ajout';
	  $new_event = TRUE;
	  $logo = '<img style="margin-top:35px;" src="http://www.cinematheque.ch/mailing/communique/template/logo.jpg" alt="" /> <img style="float: right;" src="http://www.cinematheque.ch/fileadmin/programme/partner/rts-deux_hd.jpg" alt="" height="85" /><img style="float: right;margin-right:10px;" src="http://www.cinematheque.ch/fileadmin/programme/partner/rts_1ere_hd.jpg" alt="" height="85" />';
}
?>
<div class="middle">
	<h4><?php echo $titre1; ?></h4>
	<form action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
	    <div>
		<label for="titre">Titre</label>
		<textarea id="titre" name="titre"><?php echo utf8_encode($dataEventSelect['titre']); ?></textarea>
	    </div>
	    <div class="input21">
		<label for="date">Date et heure</label>
		<input type="date" min="2015-01-01" placeholder="2015-01-01 20:30" name="date" value="<?php echo utf8_encode($dataEventSelect['date']); ?>">
	    </div>
	    <div class="input22">
		<label for="id_salle">Salle</label>
		<select name="id_salle">
<?php
//requète pour récupérer la liste des salles
$sql_salle = "SELECT id_salle, titre FROM pm_salle";
$result_salle = mysqli_query($connexion, $sql_salle) or die(mysqli_error());	
while ($data_salle = mysqli_fetch_array($result_salle)) {
  echo '<option value="'.$data_salle['id_salle'].'"';
  if($dataEventSelect['id_salle'] == $data_salle['id_salle']) echo ' selected="selected"';
  echo '>'.utf8_encode($data_salle['titre']).'</option>';	
}
?>
		</select>
	    </div>
	    <div>
		<label for="commentaire">Commentaire</label>
		<textarea id="commentaire" name="commentaire"><?php if($new_event){echo $logo;}else {echo utf8_encode($dataEventSelect['commentaire']);} ?></textarea>
	    </div>
	    <input type="hidden" name="id_event" value="<?php echo $event; ?>">
	    <input type="hidden" name="concours" value="1">
	    <?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveEvent" class="btn" value="Enregistrer">'; } ?>
	</form>
</div>

<hr class="clear" id="liste" name="liste">

<h2><?php echo utf8_encode($dataEventSelect['titre']); ?></h2>
<div class="left">
<h3>Listes de réservations</h3>

<?php if($_COOKIE['role']=='e') { ?>
  <form class="" action="<?php $_SERVER['PHP_SELF']; ?>#liste" method="get">
    <input type="hidden" value="reserv" name="zone">
    <input type="hidden" value="2" name="add">
    <input type="hidden" value="<?php echo $event; ?>" name="event">
    <input type="submit" class="btn" value="Ajouter">
  </form>
<?php } ?>

<table class="table">
  <thead>
    <tr>
      <th>Cat.</th>
      <th>Titre</th>
      <th>Url</th>
      <th>Quotas</th>
      <th>Réserv.</th>
  <?php if($_COOKIE['role']=='e') echo '<th style="width:45px;">&nbsp; </th>'; ?> 
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
  echo '<tr>';
    //catégorie
    echo '<td>'.$dataReservAtt['id_categorie'].'</td>';
    
    //titre
    echo '<td><a href="'.$_SERVER['PHP_SELF'].'?zone=reserv&amp;add=0&amp;event='.$event.'&amp;reserv='.$dataReservAtt['id_reserv'].'#liste">'.utf8_encode($dataReservAtt['titre']).'</a></td>';
    
    //url
    echo '<td>'.$dataReservAtt['url'].'</td>';
    
    //Quotas
    echo '<td>'.$dataReservAtt['quotas'].'</td>';
    
    //réservés
$sql_Allreserv = "SELECT SUM(place) AS place FROM re_inscription WHERE id_reserv = '$dataReservAtt[id_reserv]' GROUP BY id_reserv"; // nom de la table ! requette
$result_Allreserv = mysqli_query($connexion, $sql_Allreserv ) or die(mysqli_error());   
$data_Allreserv = mysqli_fetch_array($result_Allreserv);
    if(($dataReservAtt['quotas'] - $data_Allreserv['place']) < 2 ) {
      echo '<td class="import">';
    } elseif(($dataReservAtt['quotas'] - $data_Allreserv['place']) < 10 ) {
      echo '<td class="warning">';
    } else echo '<td>';
    echo $data_Allreserv['place'].'</td>';
    
    //Formulaire d'édition / suppression
    if($_COOKIE['role']=='e') {	
      echo '<td>';
	echo '<form name="suppr_news" action="'.$_SERVER['PHP_SELF'].'#liste" method="post" onclick="return confirm(\'voulez-vous vraiment supprimé cet événement?\')">
	    <input type="hidden" name="id_reserv" value="'.$dataReservAtt['id_reserv'].'">
	    <input name="deleteReserv" class="btn_suppr" type="submit" value="Supprimer">
	</form>';
      echo '</td>';
    }
  echo '</tr>';
}
?>
  </tbody>
</table>
</div>
<?php 
/********************************************************* FORMULAIRE ADD/MODIF *********************************************************/
if($_REQUEST['zone']=='reserv') {
  if($add == 0) {
    $sqlReservSelect = "SELECT * FROM re_reserv WHERE id_reserv ='$reserv'"; // nom de la table ! requette
    $resultReservSelect = mysqli_query($connexion, $sqlReservSelect ) or die(mysqli_error());
    $dataReservSelect = mysqli_fetch_array($resultReservSelect);
		
    $titre2 = 'Modifier '.$dataReservSelect['titre'];
    $new_reserv = FALSE;
	

  } else {
    $titre2 = 'Ajout';
    $new_reserv = TRUE;
  }
?>

<div class="right">
  <h4>Liste des inscriptions</h4>
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

   $sqlInscripAtt = "SELECT id, CONCAT(nom, ' ', prenom) AS contact, email, place FROM re_inscription WHERE id_reserv = '$reserv' AND actif = '1' AND id_categorie = '0' ORDER BY nom";
    $resultInscripAtt = mysqli_query($connexion, $sqlInscripAtt ) or die(mysqli_error()); 
    while ($dataInscripAtt = mysqli_fetch_array($resultInscripAtt)) {
      echo '<tr>';
	//Contact
	echo '<td>'.utf8_encode($dataInscripAtt['contact']).'</td>';
	
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
<div class="middle">

    <h4><?php echo $titre2; ?></h4>
    <form action="<?php $_SERVER['PHP_SELF']; ?>?add=0&amp;zone=reserv&amp;event=<?php echo $event; ?>#liste" method="post">
	
	<div>
	    <label for="titre">Titre</label>
	    <input id="titre" type="text" name="titre" value="<?php echo utf8_encode($dataReservSelect['titre']); ?>">
	</div>
	<div>
	    <label for="quotas">Quotas</label>
	    <input id="quotas" type="text" name="quotas" value="<?php echo utf8_encode($dataReservSelect['quotas']); ?>">
	</div>
	<div>
	    <label for="url">URL</label>
	    <input id="url" type="text" name="url" value="<?php echo utf8_encode($dataReservSelect['url']); ?>">
	</div>
	<div>
	    <label for="text_info">Texte d'information</label>
	    <textarea id="text_info" name="text_info"><?php if($new_reserv){echo "Date complète de/s séance/s";}else {echo utf8_encode($dataReservSelect['text_info']);} ?></textarea>
	</div>
	<div>
	    <label for="text_complet">Texte si plus de billets</label>
	    <textarea id="text_complet" name="text_complet"><?php if($new_reserv){echo "Nous n'avons malheureusement plus de billets au concours.";}else {echo utf8_encode($dataReservSelect['text_complet']);} ?></textarea>
	</div>
	<input type="hidden" name="id_event" value="<?php echo $event; ?>">
	<input type="hidden" name="id_reserv" value="<?php echo $reserv; ?>">
	<input type="hidden" name="id_categorie" value="7">
	<?php if($_COOKIE['role']=='e') { echo '<input type="submit" name="saveReserv" class="btn" value="Enregistrer">'; } ?>
    </form>
</div>

<?php } /*fin reserv*/ ?>
<?php } /*fin event*/?>
<hr class="clear">