<?php /*!
 * Paramètres de programmation pour FullCalendar v2.1.1
 * Docs & License: cinémathèque suisse
 * (c) 2014 Cindy Chassot
 */
?>
<script>
	$(document).ready(function() {
		
		/* initialize the external events
		-----------------------------------------------------------------*/
	
		
	
		
		/* initialize the calendar
		-----------------------------------------------------------------*/
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next',
				center: 'title',
				right: ''
			},
			defaultDate: '<?php echo $defaultDate; ?>', //Récupération de la variable date du jour/date sélectionnée
			editable: false,
			//events: "http://localhost/calendar/event.php",
			<?php include($_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'].'/inc/event.php'); ?>
			eventRender: function(event, element) {
				element.find('.fc-title').html(element.find('.fc-title').text());
			},
			timeFormat: 'HH:mm', // uppercase H for 24-hour clock
			axisFormat: 'HH:mm',
			scrollTime: '11:00:00',
			fixedWeekCount: false,
			droppable: false, // this allows things to be dropped onto the calendar !!!
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
						//location.reload();
					}
				});
			},
			
			selectable: true,
			selectHelper: true,
// Glisser les films dans une séance
		});		
	});
	$(document).ready(function() {
		
		/* initialize the external events
		-----------------------------------------------------------------*/
	
		
	
		
		/* initialize the calendar
		-----------------------------------------------------------------*/
		$('#calendar2').fullCalendar({
			header: {
				left: '',
				center: 'title',
				right: 'prev,next'
			},
			defaultDate: '<?php echo $defaultDate2; ?>', //Récupération de la variable date du jour/date sélectionnée
			editable: false,
			//events: "http://localhost/calendar/event.php",
			<?php include($_COOKIE['indexApp'].'/'.$_COOKIE['sousMenu'].'/inc/event.php'); ?>
			eventRender: function(event, element) {
				element.find('.fc-title').html(element.find('.fc-title').text());
			},
			timeFormat: 'HH:mm', // uppercase H for 24-hour clock
			axisFormat: 'HH:mm',
			scrollTime: '11:00:00',
			fixedWeekCount: false,
			droppable: false, // this allows things to be dropped onto the calendar !!!
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
						//location.reload();
					}
				});
			},
			
			selectable: true,
			selectHelper: true,
// Glisser les films dans une séance
		});		
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