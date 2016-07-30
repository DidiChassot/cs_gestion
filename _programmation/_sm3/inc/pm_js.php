<?php
/**
* Paramètres de programmation pour FullCalendar v2.1.1
*
* @copyright  Cinémathèque suisse (c) 2015 CHASSOT Cindy
* @author     Cindy Chassot
* @version    0.1 - 22.04.2015
*/
?>
<script>
	$(document).ready(function() {
		
		/* initialize the external events
		-----------------------------------------------------------------*/
	
		$('#external-events .fc-event').each(function() {
		
			// create an Event Object (http://arshaw.com/fullcalendar/docs/event_data/Event_Object/)
			// it doesn't need to have a start or end
			var eventObject = {
				title: $.trim($(this).text()), // use the element's text as the event title
				id: $.trim($(this).attr('value')), // utilise la valeur de l'attribue "value" pour l'id du film
				cycle: $.trim($(this).attr('cycle')), // utilise la valeur de l'attribue "cycle" pour l'id du cycle
				cat: 'mov',
			};
			
			// store the Event Object in the DOM element so we can get to it later
			$(this).data('eventObject', eventObject);
			
			// make the event draggable using jQuery UI
			$(this).draggable({
				zIndex: 999,
				revert: true,      // will cause the event to go back to its
				revertDuration: 0  //  original position after the drag
			});
			
		});
		$('#panier .fc-event').each(function() {
			var eventObject = {
				title: $.trim($(this).text()), // use the element's text as the event title
				id: $.trim($(this).attr('value')), // utilise la valeur de l'attribue "value" pour l'id de l'événement
				cycle: 0, // valeur 0
				cat: 'pan',
			};
			
			$(this).data('eventObject', eventObject);
			
			$(this).draggable({
				zIndex: 999,
				revert: true,      // will cause the event to go back to its
				revertDuration: 0  //  original position after the drag
			});
			
		});
		$('#corbeille .fc-event').each(function() {
			var eventObject = {
				title: $.trim($(this).text()), // use the element's text as the event title
				id: $.trim($(this).attr('value')), // utilise la valeur de l'attribue "value" pour l'id de l'événement
				cycle: 0, // valeur 0
				cat: 'corb',
			};
			
			$(this).data('eventObject', eventObject);
			
			$(this).draggable({
				zIndex: 999,
				revert: true,      // will cause the event to go back to its
				revertDuration: 0  //  original position after the drag
			});
			
		});
	
		
		/* initialize the calendar
		-----------------------------------------------------------------*/
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: '<?php echo $defaultDate; ?>', //Récupération de la variable date du jour/date sélectionnée
			editable: true,
			<?php require_once($_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'].'/inc/event.php'); ?>
			eventRender: function(event, element) {
				element.find('.fc-title').html(element.find('.fc-title').text());
			},
			timeFormat: 'HH:mm', // uppercase H for 24-hour clock
			axisFormat: 'HH:mm',
			scrollTime: '11:00:00',
			fixedWeekCount: false,
			droppable: true, // this allows things to be dropped onto the calendar !!!
			drop: function(event) { // this function is called when something is dropped
			
				// retrieve the dropped element's stored Event Object
				var originalEventObject = $(this).data('eventObject');
				
				//variable de la séance sélectionnée
				var id_seance = parseInt('<?php echo $id; ?>') ;
				
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/add_movie.php',
					data: 'id_film='+originalEventObject.id+'&id_seance='+id_seance+'&cat='+originalEventObject.cat+'&cycle='+originalEventObject.cycle ,
					type: "POST",
					success: function(json) {
						//alert("OK");
						location.reload();
					}
				});
			},
			
			
			selectable: true,
			selectHelper: true,
//Ajout de séances
			select: function(start, end) { //ajout de séances quand on clic sur une case
				var time = prompt('Heure');
				if (time) {
					start = $.fullCalendar.moment(start,moment.ISO_8601).format("YYYY-MM-D")+' '+time; //date du jour + champ
					end = $.fullCalendar.moment(end,moment.ISO_8601).format("YYYY-MM-D HH:mm:ss");
					$.ajax({
						url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/add_events.php',
						data: 'start='+start+'&end='+end ,
						type: "POST",
						success: function(json) {
							//alert('OK'); // pop-up d'information "ok"
							location.reload(); //lancer le rafraichissement de la page
						}
					});
					calendar.fullCalendar('renderEvent',{
						start: start,
						end: end,
						},
						true // make the event "stick"
					);
				}
				calendar.fullCalendar('unselect');
			},
//Clic sur une séance - affichage du formulaire
			eventClick: function(event) {
				//récupération de la variable du début de bulletin
				 var start_bulletin = '<?php echo $start; ?>' ;
				 var id_bulletin = '<?php echo $id_bulletin; ?>' ;
				//Ouverture de la même page avec l'ID de l'événement envoyé en "GET"
				window.open('index.php?action=clic&start='+start_bulletin+'&id='+event.id_seance+'&id_bulletin='+id_bulletin, '_self');
			},
			eventDrop: function(event, delta) {
					start = $.fullCalendar.moment(event.start).format('YYYY-MM-DD HH:mm:ss');
					end = $.fullCalendar.moment(event.end).format('YYYY-MM-DD HH:mm:ss');
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_events.php',
					data: 'start='+ start +'&end='+ end +'&id_seance='+ event.id_seance ,
					type: "POST",
					success: function(json) {
						//alert("OK");
						//location.reload();
					}
				});
			},
			eventResize: function(event) {
					start = $.fullCalendar.moment(event.start).format('YYYY-MM-DD HH:mm:ss');
					end = $.fullCalendar.moment(event.end).format('YYYY-MM-DD HH:mm:ss');
				$.ajax({
					url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_events.php',
					data: 'start='+ start +'&end='+ end +'&id_seance='+ event.id_seance ,
					type: "POST",
					success: function(json) {
						//alert("OK");
						//location.reload();
					}
				});
			 
			},
// Glisser les films dans une séance
		});		
	});
        
</script>

<!-- Script pour l'ordre des films dans une séance-->
<script type="text/javascript">
$(function() {		
	$("#listeFilm tbody.content").sortable({
		placeholder: 'highlight', // classe à ajouter à l'élément fantome
		update: function(){// callback quand l'ordre de la liste est changé
			var NewOrder = $('#listeFilm tbody.content').sortable('serialize');// récupération des données à envoyer
			console.log(NewOrder);
			//alert(NewOrder);
			//$.post('ajax.php',NewOrder); // appel ajax au fichier ajax.php avec l'ordre des photos
			
			 var id_seance = '<?php echo $id; ?>' ;
			
			//var arrayId = NewOrder.split(',');
			
			$.ajax({
				url: '<?php echo $_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'];?>/inc/update_ordre_film.php',
				data: NewOrder+'&id_seance='+id_seance ,
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
	
/*** Récupération du idMenu pour le menu déroulant ***/	
	function afficheMenu(obj){
	
		var idMenu     = obj.id;
		var currentLocation =  document.location.href;
		var start_bulletin = '<?php echo $start; ?>' ;
		var id_bulletin = '<?php echo $id_bulletin; ?>' ;
		var action = '<?php echo $action; ?>' ;
		var id = '<?php echo $id; ?>' ;
		
		top.document.location = "index.php?action="+action+"&start="+start_bulletin+"&id_bulletin="+id_bulletin+"&id="+id+"&idMenu="+idMenu;
			
	}

</script> 