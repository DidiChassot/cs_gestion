<?php
/*** lOGIN ***/
// 
//Cindy Chassot 21.01.2015 - 26.01.2015
//© Cinémathèque suisse

?>
		<div id="login">
			<h1>connection au système de gestion</h1>
			<?php echo $message; ?>
			<form method="post" action="index.php">
				<fieldset>
					<label for="pseudo">login </label><input name="pseudo" type="text" id="pseudo" /><br />
					<label for="password">mot de passe </label><input type="password" name="password" id="password" />
					<input type="submit" value="Connexion" />
				</fieldset>
			</form>
		</div>

