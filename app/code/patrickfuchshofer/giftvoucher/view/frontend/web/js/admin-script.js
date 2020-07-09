(function( $ ) {
 
	$('#voucher_bgcolor, #voucher_color').wpColorPicker();

	$('.wpgiftv-row .nav-tab').on('click', function(e) {
		e.preventDefault();
		$('.wpgiftv-row .nav-tab').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');
		var tab = $(this).attr('href');
		$('.wpgiftv-row .tab-content').removeClass('tab-content-active');
		$(tab).addClass('tab-content-active');
	});
     
})( jQuery );