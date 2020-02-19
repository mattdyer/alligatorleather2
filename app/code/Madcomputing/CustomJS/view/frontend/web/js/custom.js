require([
        'jquery'
], function ($) {
    $(function(){
		var buckleResult = $.get('/images/buckle-images/map.json',showBuckleImages);



	});

	function showBuckleImages(buckleImageMap){
		$('.options-list label').each(function(){
			var optionLabel = $(this);
			var nameSpan = $('span', optionLabel).first()

			var buckle = buckleImageMap[$.trim(nameSpan.text())];

			if(buckle){
				var imagePath = '/images/buckle-images/' + buckle.filename;
				optionLabel.append('<img src="' + imagePath + '">');
				optionLabel.addClass('buckle-option-container')
			}
			
		});
	}
});

