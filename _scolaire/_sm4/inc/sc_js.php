<?php /*!
 * Paramètres de programmation drag&drop de l'ordre de tableau
// 
//Cindy Chassot 16.02.2015
//© Cinémathèque suisse
 */
?>
	<script type="text/javascript">
			
	//drag&drop de l'ordre des formulaires
	$(function() {				
		$("#listeForm tbody.content").sortable({
			placeholder: 'highlight', // classe à ajouter à l'élément fantome
			update: function(){// callback quand l'ordre de la liste est changé
				var NewOrder = $('#listeForm tbody.content').sortable('serialize');// récupération des données à envoyer
				console.log(NewOrder);
				
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_ordre_form.php',
					data: NewOrder,
					type: "POST",
					success: function(json) {
						//alert(NewOrder);
						location.reload();
					}
				});
 
			}
		});		
		$("#listeForm tbody.content").disableSelection();
	});
 
	</script> 