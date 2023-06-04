(function( $ ) {
	'use strict';

	 $(function() {

		 /**
		  * Check/uncheck the checkbox as the form is displayed.
		  *
		  * The quick edit box is printed once for the entire table. When it is shown, we need to update the checkbox
		  * to match the post it is being displayed for.
		  */
		 $( '.editinline' ).on( 'click', function( e ) {

			 var doesOpenTickets = $(this).closest('tr').find('.opens-ticket').first().data('opens-ticket');

			 // $('#omniform-kb-ticket').prop('checked', doesOpenTickets );

			 setTimeout(myTimer, 10);
			 function myTimer() {
				 $('#omniform-kb-ticket').prop('checked', doesOpenTickets );
			 }
		 });

	 });

})( jQuery );
