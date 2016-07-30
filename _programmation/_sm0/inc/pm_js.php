<?php 
/**
* Paramètres de programmation drag&drop de l'ordre de tableau
*
* Pour l'ordre des films du tableau
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 18.06.2015
*/
?>
	<script type="text/javascript">
	$(function() {				
		$("#listeFilm tbody.content").sortable({
			placeholder: 'highlight', // classe à ajouter à l'élément fantome
			update: function(){// callback quand l'ordre de la liste est changé
				var NewOrder = $('#listeFilm tbody.content').sortable('serialize');// récupération des données à envoyer
				console.log(NewOrder);
				
				var id_cycle = '<?php echo $cy; ?>' ;
				
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_ordre_film.php',
					data: NewOrder+'&id_cycle='+id_cycle ,
					type: "POST",
					success: function(json) {
						//alert(NewOrder);
						location.reload();
					}
				});
 
			}
		});		
		$("#listeFilm tbody.content").disableSelection();
	});
 
	</script> 