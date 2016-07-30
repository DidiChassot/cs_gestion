<?php
/*** Index Gestion ***/
// 
//Cindy Chassot 15.01.2015 - 22.15.2015
//© Cinémathèque suisse

session_start();

include('inc/ge_connexion.php');
include('inc/function.php');
include('inc/ge_cookies.php');
include('inc/ge_session.php');
include('inc/constants.php');

include("header.php");
// on teste la déclaration de notre cookie
if (isset($_COOKIE['geLogCon'])) {
    include("menu.php");
    echo '<div id="content">';
    include("page.php");
}
else {
    include("login.php");
}

?>
    </div>
<?php if($_COOKIE['indexApp'] <> "_programmation") { ?>
    <script type="text/javascript" src="js/tinymce/tinymce.min.js"></script>
    <script type="text/javascript">
    tinymce.init({
        selector: "textarea",
        entities : "raw",
        plugins: [
            "emoticons template paste textcolor colorpicker textpattern"
        ],
        toolbar: "bold italic forecolor"
    });
    </script>
<?php } ?>
</body>
</html>