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
				//alert(NewOrder);
				//$.post('ajax.php',NewOrder); // appel ajax au fichier ajax.php avec l'ordre des photos
				
				var id_bulletin = parseInt('<?php echo '4'; ?>') ;
				
				//var arrayId = NewOrder.split(',');
				
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_ordre_cycle.php',
					data: NewOrder+'&id_bulletin='+id_bulletin ,
					type: "POST",
					success: function(json) {
						//alert(NewOrder);
						//location.reload();
					}
				});
 
			}
		});		
		$("#listeCycle tbody.content").disableSelection();
	});
 
	</script> 