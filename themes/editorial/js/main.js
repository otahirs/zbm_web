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


	$(function() {

		// vars
		var	$window = $(window),
			$head = $('head'),
			$body = $('body'),
			$main = $('#main'),
			$sidebar = $('#sidebar'),
			$links = $('#nav-links');

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
			// 
			$window.on('load', () => {
				$sidebar.css("opacity", "1");
			})

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
			  var main = document.getElementById('main');
			  var slideout = new Slideout({
			    'panel': main,
					'menu': document.getElementById('sidebar'),
					'padding': $sidebar.css("width").slice(0, -2),
					'easing': 'ease-in-out',
					'tolerance': 100
				});		
				
				// resize padding because sidebar width is dynamic
				$(window).on('resize',function(){
					setTimeout(function(){
						if(skel.breakpoint("large").active){
							slideout.padding = $sidebar.css("width").slice(0, -2);
						}
					}, 100);					
				});

				// prevent menu opening when touch is further from screen edge than slideout._tolerance
				main.addEventListener('touchstart', function(eve) {
					let offset = eve.touches[0].pageX;
				
					if (slideout._orientation !== 1) {
						offset = window.innerWidth - offset;
					}
				
					slideout._preventOpen = slideout._preventOpen || (offset > slideout._tolerance && !slideout.isOpen());
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

			
			
				
			// Scroll lock.
			// Note: If you do anything to change the height of the sidebar's content, be sure to
			// trigger 'resize.sidebar-lock' on $window so stuff doesn't get out of sync.			
		/*		
				var  $sidebar_inner = $sidebar.children('.inner');
				$window.on('load.sidebar-lock', function() {

					var sh, wh, st;

					// Reset scroll position to 0 if it's 1.
						if ($window.scrollTop() == 1)
							$window.scrollTop(0);

					$window
						.on('scroll.sidebar-lock', function() {

							var x, y;

							// IE<10? Bail.
								if (skel.vars.IEVersion < 10)
									return;

							// <=large? Bail.
								if (skel.breakpoint('large').active) {

									$sidebar_inner
										.data('locked', 0)
										.css('position', '')
										.css('top', '');

									return;

								}

							// Calculate positions.
								x = Math.max(sh - wh, 0);
								y = Math.max(0, $window.scrollTop() - x);

							// Lock/unlock.
								if ($sidebar_inner.data('locked') == 1) {

									if (y <= 0)
										$sidebar_inner
											.data('locked', 0)
											.css('position', '')
											.css('top', '');
									else
										$sidebar_inner
											.css('top', -1 * x);

								}
								else {

									if (y > 0)
										$sidebar_inner
											.data('locked', 1)
											.css('position', 'fixed')
											.css('top', -1 * x);

								}

						})
						.on('resize.sidebar-lock', function() {

							// Calculate heights.
								wh = $window.height();
								sh = $sidebar_inner.outerHeight() + 30;

							// Trigger scroll.
								$window.trigger('scroll.sidebar-lock');

						})
						.trigger('resize.sidebar-lock');

					});
		*/

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

		// Autohide navlinks to submenu				
				function calcWidth() {
					var navwidth = 0; 
					$('#links > li').each(function() {
						navwidth += $(this).outerWidth( true );
					});
					
					var availablespace = $('#nav-links').width() - 10;
					
					if (navwidth > availablespace && !$('#links > li').first().hasClass("more")) {
						var lastItem = $('#links > li:not(.more)').last();
						lastItem.attr('data-width', lastItem.outerWidth(true));
						lastItem.prependTo($('#links .more ul'));
						calcWidth();
					} else {	
						var firstMoreElement = $('#links li.more li').first();
						while(navwidth + firstMoreElement.data('width') < availablespace) {
							firstMoreElement.insertBefore($('#links .more'));
							navwidth += firstMoreElement.data('width');
							firstMoreElement = $('#links li.more li').first();		
						}
					}
				  
					if ($('.more li').length > 0) {
						$('.more').css('display','block');
					} else {
						$('.more').css('display','none');
					}
				}
				
				$(window).on('load',function(){
					calcWidth();
					$links.css("opacity", "1");
				});
				$(window).on('resize',function(){
					calcWidth()
					setTimeout(calcWidth, 300);
				});
				

				var linksBtn = document.getElementById('links-more-btn'),
						linksMore = document.getElementById('links-more-ul');   

				// open nad close on button click
				linksBtn.addEventListener('click', () => {
					if(linksMore.style.display == "block"){
						linksMore.style.display = "none";
					}
					else{
						linksMore.style.display = "block";
					}
				})

				// close when clicked outside button
				document.addEventListener('click', (e) => {
					if(!linksBtn.contains(e.target)){
						linksMore.style.display = "none";
					}
				});

				// close on main menu manipulation
				slideout.on('translatestart', () => { linksMore.style.display = "none" });
				
	});

	

})(jQuery);
