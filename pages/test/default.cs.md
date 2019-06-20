---
title: test
date: '2019-06-19'
process:
    markdown: false
---





    
  


<script>
$(document).ready(function() {
	
	function calcWidth() {
		var navwidth = 300;
		var morewidth = $('#links .more').outerWidth(true);
		$('#links > li:not(.more)').each(function() {
			navwidth += $(this).outerWidth( true );
		});
		
		//var availablespace = $('nav').outerWidth(true) - morewidth;
		var availablespace = $('#nav-links').width() - morewidth;
	  
		if (navwidth > availablespace) {
			var lastItem = $('#links > li:not(.more)').last();
			lastItem.attr('data-width', lastItem.outerWidth(true));
			lastItem.prependTo($('#links .more ul'));
			calcWidth();
		} else {
			
		var firstMoreElement = $('#links li.more li').first();
		if (navwidth + firstMoreElement.data('width') < availablespace) {
			firstMoreElement.insertBefore($('#links .more'));
		}
	}
	  
	if ($('.more li').length > 0) {
		$('.more').css('display','block');
		} else {
			$('.more').css('display','none');
		}
	}

	$(window).on('resize load',function(){
		calcWidth();
	});
});
</script>
        