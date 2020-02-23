require([
        'jquery'
], function ($) {
    $(function(){
		var buckleResult = $.get('/images/buckle-images/map.json',showBuckleImages);

		showInitialImage();

	});

	function showBuckleImages(buckleImageMap){
		$('.options-list label').each(function(){
			var optionLabel = $(this);
			var nameSpan = $('span', optionLabel).first();

			var buckle = buckleImageMap[$.trim(nameSpan.text())];

			if(buckle){
				var imagePath = '/images/buckle-images/' + buckle.filename;
				optionLabel.append('<img src="' + imagePath + '">');
				optionLabel.addClass('buckle-option-container');
			}
			
		});
	}
	
	function showInitialImage(){
		$('.field > label > span').each(function(){
			var nameSpan = $(this);
			
			if(nameSpan.text() == 'Initials'){
				
				var control = nameSpan.closest('.field').find('.control');
				
				var imagePath = '/images/initials.jpg';
				control.append('<img class="initials-option-image" src="' + imagePath + '">');
				control.addClass('initials-option-container');
			}
			
		});
	}
	
});

