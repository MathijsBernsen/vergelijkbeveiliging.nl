jQuery(document).ready(function( $ ) {
	// $('[data-pafe-form-booking-item]').each(function(){
	// 	var options = JSON.parse( $(this).attr('data-pafe-form-booking-item-options') );

	// 	if(typeof options['pafe_form_booking_date_field'] !== 'undefined') {
	// 	    var dateFieldID = '#form-field-' + options['pafe_form_booking_date_field'].replace('[field id=\"', '').replace('\"]', '');
	// 	    $(dateFieldID).addClass('pafe-form-booking-date');
	// 	}
	// });

	// $(document).on('change','.pafe-form-booking-date',function(){
	// 	var date = $(this).val(),
	// 		formID = $(this).attr('data-pafe-form-builder-form-id'),
	// 		$bookingItem = $('[data-pafe-form-booking-item][data-pafe-form-builder-form-id="' + formID + '"]');

	// 	if ($bookingItem.length > 0) {
	// 		$bookingItem = $bookingItem.eq(0);
	// 		var $bookingForm = $bookingItem.closest('[data-pafe-form-booking]');

	// 		var bookingOptions = JSON.parse( $bookingItem.attr('data-pafe-form-booking-item-options') );

	// 		var data = {
	// 			'action': 'pafe_form_booking',
	// 			'date': date,
	// 			'post_id': bookingOptions.pafe_form_booking_post_id,
	// 			'element_id': bookingOptions.pafe_form_booking_element_id,
	// 		};

	//         $.post($('[data-pafe-ajax-url]').data('pafe-ajax-url'), data, function(response) {
	//         	$bookingForm.html(response);
	// 		});
	// 	}
	// });
});