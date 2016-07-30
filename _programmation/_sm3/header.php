<?php
/*** HEADER calendrier - PROGRAMMATION - SM3***/
// 
//Cindy Chassot 26.01.2015 - 28.01.15
//© Cinémathèque suisse
?>

<link href='<?php echo $_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/"; ?>css/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo $_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/"; ?>css/fullcalendar.print.min.css' rel='stylesheet' media='print' />
<!--css print pour programmation-->
<link rel="stylesheet" type="text/css" href="<?php echo $_COOKIE['indexApp']; ?>/css/pm_print.css" media="print" />
<script src='<?php echo $_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/"; ?>js/moment.min.js'></script>
<script src='<?php echo $_COOKIE['indexApp']."/"; ?>js/jquery.min.js'></script>
<script src='<?php echo $_COOKIE['indexApp']."/"; ?>js/jquery-ui.custom.min.js'></script>
<script src='<?php echo $_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/"; ?>js/fullcalendar.min.js'></script>
<script src='<?php echo $_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/"; ?>js/fr.js'></script>

<?php
/*** intitation des variables ***/
$id = '';
$id_bulletin = '';
$start = '';
$action = 'neutre';
$envoi = '';
$idMenu;

include("inc/ge_connexion.php");
include($_COOKIE['indexApp']."/".$_COOKIE['sousMenu']."/inc/pm_functions.php");

if ($action=='neutre' || $action=='clic' || $action=='form') {
	$calendar = "bulletin";

} elseif ($action=='film') {
	$calendar = $id_film;
}

if ($action=='clic'){ // si on clic sur une séance
	//requette sql
	$sql_plage = "SELECT * FROM pm_seance WHERE id_seance='$id'";
	$result_plage = mysqli_query($connexion, $sql_plage) or die(mysqli_error());
	//récupération des donneés	
	if($data_plage = mysqli_fetch_array($result_plage)) {
		$start = $data_plage['start'];
		$defaultDate = $start; //date du bulletin sélectionné
	} else {
	//requette sql
		$sql_plage = "SELECT * FROM pm_bulletin WHERE id_bulletin='$id_bulletin'";
		$result_plage = mysqli_query($connexion, $sql_plage) or die(mysqli_error());
		//récupération des donneés	
		$data_plage = mysqli_fetch_array($result_plage);
			$start = $data_plage['start'];
			$defaultDate = $start; //date du bulletin sélectionné
		}	
} elseif ($start) { // si la $start est déja remplie
	$defaultDate = $start;
	
} elseif ($id_bulletin) { // si id_bulletin est envoyé
	//requette sql
	$sql_plage = "SELECT * FROM pm_bulletin WHERE id_bulletin='$id_bulletin'";
	$result_plage = mysqli_query($connexion, $sql_plage) or die(mysqli_error());
	//récupération des donneés	
	$data_plage = mysqli_fetch_array($result_plage);
		$start = $data_plage['start'];
		$defaultDate = $start; //date du bulletin sélectionné
		
} else { // sinon date d'aujourd'hui par default
	$defaultDate = date('Y-m-d'); //date du jour
}

/*** Récupération du start et end du bulletin ***/
$sql_plage = "SELECT * FROM pm_bulletin WHERE id_bulletin='$id_bulletin'";
$result_plage = mysqli_query($connexion, $sql_plage) or die(mysqli_error());
//récupération des donneés	
$data_plage = mysqli_fetch_array($result_plage);
	$start_bulletin = $data_plage['start'];
	$end_bulletin = $data_plage['end'];

?>