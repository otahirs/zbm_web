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
			$main = $('#main');

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
			var $sidebar = $('#sidebar'),
				$sidebar_inner = $sidebar.children('.inner');

			// Inactive by default on <= large.
				skel
					.on('+large', function() {
						$sidebar.addClass('inactive');
						//$main.addClass('is-dimmed');
					})
					.on('-large !large', function() {
						$sidebar.removeClass('inactive');
						$main.removeClass('is-dimmed');
					});

			// Hack: Workaround for Chrome/Android scrollbar position bug.
				if (skel.vars.os == 'android'
				&&	skel.vars.browser == 'chrome')
					$('<style>#sidebar .inner::-webkit-scrollbar { display: none; }</style>')
						.appendTo($head);

			// Toggle.
				if (skel.vars.IEVersion > 9) {

					$('<a href="#sidebar" class="toggle">Toggle</a>')
						.appendTo($sidebar)
						.on('click', function(event) {

							// Prevent default.
								event.preventDefault();
								event.stopPropagation();

							// Toggle.
								$sidebar.toggleClass('inactive');
								$main.toggleClass('is-dimmed');
								
								
						});

				}
			// Menu support for mobile (only when menu displays over page -> smaller thatn "large")
				
			
			function closemenu(){
				event.preventDefault();
				$sidebar.addClass("inactive");
				$main.removeClass('is-dimmed');
			}
			var handled = false;
			
			
			
			skel.on('+large', function() {
				// Swipe to open menu 
				$(".swipe-area").swipe({
					swipeStatus:function(event, phase, direction, distance, duration, fingers){
							if (phase=="move" && direction =="right") {
								$sidebar.removeClass("inactive");
								$main.addClass('is-dimmed');
								return false;
							}
					}
				});
				// Tap or click outside menu to close 
				$(document).on('start touchstart', function(event) { 
					if (!skel.breakpoint('large').active) return;

					if(!$(event.target).closest($sidebar).length) {
						if(!$sidebar.hasClass("inactive")){
							event.stopPropagation();
							if(event.type == "touchstart") {
								handled = true;
								closemenu();
							}
							else if(event.type == "click" && !handled) {
								closemenu();
							}
							else {
								handled = false;
							}
						}
					}
				})       
				
			})
			

			// Events.

				// Link clicks.
					$sidebar.on('click', 'a', function(event) {

						// >large? Bail.
							if (!skel.breakpoint('large').active)
								return;

						// Vars.
							var $a = $(this),
								href = $a.attr('href'),
								target = $a.attr('target');

						// Prevent default.
							event.preventDefault();
							event.stopPropagation();

						// Check URL.
							if (!href || href == '#' || href == '')
								return;

						// Hide sidebar.
							$sidebar.addClass('inactive');

						// Redirect to href.
							setTimeout(function() {

								if (target == '_blank')
									window.open(href);
								else
									window.location.href = href;

							}, 500);

					});

				// Prevent certain events inside the panel from bubbling.
					$sidebar.on('click touchend touchstart touchmove', function(event) {

						// >large? Bail.
							if (!skel.breakpoint('large').active)
								return;

						// Prevent propagation.
							event.stopPropagation();

					});

				// Hide panel on body click/tap.
					/*$body.on('click touchend', function(event) {

						// >large? Bail.
							if (!skel.breakpoint('large').active)
								return;

						// Deactivate.
							$sidebar.addClass('inactive');

					}); */
					
			// Scroll lock.
			// Note: If you do anything to change the height of the sidebar's content, be sure to
			// trigger 'resize.sidebar-lock' on $window so stuff doesn't get out of sync.
		/*
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
					var morewidth = $('#links .more').outerWidth(true) + 30;
					$('#links > li:not(.more)').each(function() {
						navwidth += $(this).outerWidth( true );
					});
					
					//var availablespace = $('nav').outerWidth(true) - morewidth;
					var availablespace = $('#nav-links').width() - morewidth;
					
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
				
				
				$(window).on('resize load',function(){
					setTimeout(calcWidth, 100);
					
				});

				var linksBtn = document.getElementById('links-more-btn'),
					linksMore = document.getElementById('links-more-ul');
				$main.on('click touchend', function(event) { 
					linksMore.style.display = "";
					

				})     
				$(linksBtn).on('click touchend', function(event) {
					linksMore.style.display = "block";
				})


	});

})(jQuery);
