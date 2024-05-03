(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
     *
     * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
     *
     * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	$( document ).ready( function() {
        //On change payment method, refresh order review section
		$( 'body' ).on( 'change', 'input[name="payment_method"]', function() {
			$( 'body' ).trigger( 'update_checkout' );
		} );

        //On change state field, refresh order review section
		if ( $( '#billing_state' ).length ) {
			$( 'body' ).on( 'change', '#billing_state', function() {
				$( 'body' ).trigger( 'update_checkout' );
			} );
		}
        $(document).on('found_variation', 'form.cart', function( event, variation ) { 
            $('.dpad_variation').hide();
            $('.dpad_variation_'+variation.variation_id).show();       
        });
        $(document).on( 'click', '.reset_variations', function() {
            $('.dpad_variation').hide();
        });
	} );

})( jQuery );