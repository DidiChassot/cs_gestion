<?php 
/**
* Paramètres de programmation drag&drop de l'ordre de tableau
*
* Pour l'ordre des entrée de journal du bulletin
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author Cindy Chassot
* @version    0.1 - 15.07.2015
*/
?>
	<script type="text/javascript">
	$(function() {				
		$("#listeJournal tbody.content").sortable({
			placeholder: 'highlight', // classe à ajouter à l'élément fantome
			update: function(){// callback quand l'ordre de la liste est changé
				var NewOrder = $('#listeJournal tbody.content').sortable('serialize');// récupération des données à envoyer
				console.log(NewOrder);
				
				
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_ordre_journal.php',
					data: NewOrder,
					type: "POST",
					success: function(json) {
						//alert(NewOrder);
						location.reload();
					}
				});
 
			}
		});		
		$("#listeJournal tbody.content").disableSelection();
	});
 
	$(function() {				
		$("#listePub tbody.content").sortable({
			placeholder: 'highlight', // classe à ajouter à l'élément fantome
			update: function(){// callback quand l'ordre de la liste est changé
				var NewOrder = $('#listePub tbody.content').sortable('serialize');// récupération des données à envoyer
				console.log(NewOrder);
				
				
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_ordre_pub.php',
					data: NewOrder,
					type: "POST",
					success: function(json) {
						//alert(NewOrder);
						location.reload();
					}
				});
 
			}
		});		
		$("#listePub tbody.content").disableSelection();
	});
 
	</script> 