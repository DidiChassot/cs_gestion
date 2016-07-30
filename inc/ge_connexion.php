<?php
/*** CONNEXION A LA BASE DE DONNEES ***/
// 
//Cindy Chassot 15.01.2015 - 21.01.2015
//© Cinémathèque suisse


$connexion = mysqli_connect('localhost', 'root', '', 'action_press');


//$connexTypo3 = mysqli_connect('localhost', 'action_cs', 'FEJUi4H62s3tfTPE', 'typo3_prod');

/***convertion des variables***/
foreach ($_GET as $key => $value) {
	$$key = $value;
}
foreach ($_POST as $key => $value) {
	$$key = $value;
}


$connO = new mysqli("localhost", "root", "", "action_press");
if ($connO->connect_errno) {
    echo "Echec lors de la connexion à MySQL : " . $connO->connect_error;
}
/*


$mysqli = new mysqli("example.com", "user", "password", "database");
if ($mysqli->connect_errno) {
    echo "Echec lors de la connexion à MySQL : " . $mysqli->connect_error;
}
$res = $connO->query("SELECT 'choices to please everybody.' AS _msg FROM DUAL");
$row = $res->fetch_assoc();
echo $row['_msg'];
*/

?>