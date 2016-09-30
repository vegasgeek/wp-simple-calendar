jQuery(document).ready(function($) {
	$( '.control.next, .control.last' ).click( function(e) {

		var direction = $(this).hasClass('next') ? 'next' : 'last';
		var month = parseInt( $('.wpsc-grid').data('month') ) + ( direction == 'next' ? 1 : -1 );
		var yearplus;

		if( direction == 'next' ) {
			if( month > 12 ) {
				month = 1;
				yearplus = 1;
			} else {
				yearplus = 0;
			}
		} else {
			if( month < 1 ) {
				month = 12;
				yearplus = -1;
			} else {
				yearplus = 0;
			}
		}
		var year = parseInt( $('.wpsc-grid').data('year') + ( yearplus ) );

		$.post(
			wpsimplecalendar.ajaxurl+'?action=wpsimplecalendar-'+direction,
			{
				'month':	month,
				'year':		year,
				'category': $('.wpsc-grid').data('category'),
				'location': $('.wpsc-grid').data('location'),
			},
			function(data){
				data = $.parseJSON(data);
				//console.log( { 'title' : data.title, 'grid' : data.grid } );
				$('#wpsc-grid-nav strong').html( data.title );
				$('.wpsc-grid').replaceWith( data.grid );
			}
		);

		return false;
	});
});
