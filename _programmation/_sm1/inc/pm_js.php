<?php /*!
 * Paramètres de programmation drag&drop de l'ordre de tableau
// 
//Cindy Chassot 16.02.2015
//© Cinémathèque suisse
 */
?>
	<script type="text/javascript">
	$(function() {		
		$("#listeCycle tbody.content").sortable({
			placeholder: 'highlight', // classe à ajouter à l'élément fantome
			update: function(){// callback quand l'ordre de la liste est changé
				var NewOrder = $('#listeCycle tbody.content').sortable('serialize');// récupération des données à envoyer
				console.log(NewOrder);
				
				var id_bulletin = '<?php echo $bu; ?>' ;
				
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_ordre_cycle.php',
					data: NewOrder+'&id_bulletin='+id_bulletin ,
					type: "POST",
					success: function(json) {
						//alert(NewOrder);
						location.reload();
					}
				});
 
			}
		});		
		$("#listeCycle tbody.content").disableSelection();
	});
		
	
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