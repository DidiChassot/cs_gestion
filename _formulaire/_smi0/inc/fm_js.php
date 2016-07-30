<?php /*!
 * Paramètres de programmation drag&drop de l'ordre de tableau
// 
//Cindy Chassot 16.02.2015
//© Cinémathèque suisse
 */
?>
	<script type="text/javascript">
	$(function() {		
		$("#listeChamp tbody.content").sortable({
			placeholder: 'highlight', // classe à ajouter à l'élément fantome
			update: function(){// callback quand l'ordre de la liste est changé
				var NewOrder = $('#listeChamp tbody.content').sortable('serialize');// récupération des données à envoyer
				console.log(NewOrder);
				//alert(NewOrder);
				//$.post('ajax.php',NewOrder); // appel ajax au fichier ajax.php avec l'ordre des photos
				
				var id_form = parseInt('<?php echo $id_form; ?>') ;
				
				//var arrayId = NewOrder.split(',');
				
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_ordre_champ.php',
					data: NewOrder+'&id_form='+id_form ,
					type: "POST",
					success: function(json) {
						//alert(NewOrder);
						//location.reload();
					}
				});
 
			}
		});		
		$("#listeChamp tbody.content").disableSelection();
	});
 
	</script> 