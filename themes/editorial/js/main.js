/*
	Editorial by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
*/

(function($) {

	skel.breakpoints({
		xlarge: '(max-width: 1680px)',
		large: '(max-width: 1280px)',
		medium: '(max-width: 980px)',
		small: '(max-width: 736px)',
		xsmall: '(max-width: 480px)',
		'xlarge-to-max': '(min-width: 1681px)',
		'small-to-xlarge': '(min-width: 481px) and (max-width: 1680px)'
	});


	window.addEventListener('DOMContentLoaded', () => {

		// vars
		var	$window = $(window),
			$head = $('head'),
			$body = $('body'),
			$main = $('#main'),
			$links = $('#nav-links'),
			main = document.getElementById('main'),
			sidebar = document.getElementById('sidebar');

		// display none in css on large breakpoint, prevent flash on load
		sidebar.style.display = "block";

		// Disable animations/transitions ...

			// ... until the page has loaded.
				$body.addClass('is-loading');

				$window.on('load', function() {
					setTimeout(function() {
						$body.removeClass('is-loading');
					}, 100);
				});

			// ... when resizing.
				var resizeTimeout;

				$window.on('resize', function() {

					// Mark as resizing.
						$body.addClass('is-resizing');

					// Unmark after delay.
						clearTimeout(resizeTimeout);

						resizeTimeout = setTimeout(function() {
							$body.removeClass('is-resizing');
						}, 100);

				});

		// Fix: Placeholder polyfill.
			$('form').placeholder();

		// Prioritize "important" elements on medium.
			skel.on('+medium -medium', function() {
				$.prioritize(
					'.important\\28 medium\\29',
					skel.breakpoint('medium').active
				);
			});

		// Fixes.

			// Object fit images.
				if (!skel.canUse('object-fit')
				||	skel.vars.browser == 'safari')
					$('.image.object').each(function() {

						var $this = $(this),
							$img = $this.children('img');

						// Hide original image.
							$img.css('opacity', '0');

						// Set background.
							$this
								.css('background-image', 'url("' + $img.attr('src') + '")')
								.css('background-size', $img.css('object-fit') ? $img.css('object-fit') : 'cover')
								.css('background-position', $img.css('object-position') ? $img.css('object-position') : 'center');

					});

		// Sidebar.
			
			// Menu swipe support
			  var slideout = new Slideout({
			    'panel': main,
					'menu': sidebar,
					'padding': $(sidebar).css("width").slice(0, -2),
					'easing': 'ease-in-out',
					'tolerance': 20
				});		

				// resize padding because sidebar width is dynamic
				window.addEventListener('resize', () => {
					slideout._padding = $(sidebar).css("width").slice(0, -2);
					slideout._translateTo = slideout._padding;
					if (!skel.breakpoint('large').active) {
						slideout.close();
					}
					if (slideout.isOpen()) {
						slideout._translateXTo(slideout._translateTo);
					}
				});

				// prevent menu opening when touch is further from screen edge than touchWidth
				var touchWidth = 100;
				main.addEventListener('touchstart', function(eve) {
					let offset = eve.touches[0].pageX;
				
					if (slideout._orientation !== 1) {
						offset = window.innerWidth - offset;
					}
				
					slideout._preventOpen = slideout._preventOpen || (offset > touchWidth && !slideout.isOpen());
				});

			  // Toggle button
			  document.getElementById('toggle').addEventListener('click', function() {
			    slideout.toggle();
			  });	
	
				// close on click outside sidebar
				function closeMenu(e) {
					e.preventDefault();
					slideout.close();
				}

				var $dim = $('.dim');
				slideout
					.on('beforeopen', function() {
						this.panel.classList.add('panel-open');
						$dim.fadeIn(200); 
						
					})
					.on('open', function() {
						this.panel.addEventListener('click', closeMenu);
					})
					.on('beforeclose', function() {
						this.panel.classList.remove('panel-open');
						this.panel.removeEventListener('click', closeMenu);
						$dim.fadeOut(200); 
					});

		// Menu.
			var $menu = $('#menu'),
				$menu_openers = $menu.children('ul').find('.opener');

			// Openers.
				$menu_openers.each(function() {

					var $this = $(this);

					$this.on('click', function(event) {

						// Prevent default.
							event.preventDefault();

						// Toggle.
							$menu_openers.not($this).removeClass('active');
							$this.toggleClass('active');

						// Trigger resize (sidebar lock).
							$window.triggerHandler('resize.sidebar-lock');

					});

				});
		
		// open external links in new windows
		var links = main.getElementsByTagName("a");
		for (var i = 0, linksLength = links.length; i < linksLength; i++) {
			if (links[i].hasAttribute("href") && 
				links[i].hostname != window.location.hostname &&
				!(links[i].firstElementChild && 
					["IMG", "img", "picture", "PICTURE"].includes(links[i].firstElementChild.tagName))) 
			{
				links[i].target = '_blank';
				links[i].classList.add('external-link');
			} 
		}
				
	});

	

})(jQuery);
