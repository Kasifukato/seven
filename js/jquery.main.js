jQuery(function(){
	initSvgSprite();
	initMagnificPopup();
	initSlickSlider();
	initFixedHeader();
	initAccordion();
	// initAnimatedCounts();
	initOpenClose();
	initTabs();
	// initIsotope();
	// initSmoothScroll();
	initStickyFixed();
	initSmoothScroll();
	initMenuOpener();
	initDateRangePicker();
	// initStickyRoll();
	initPreventEmptyAnchor();
});


function initSvgSprite() {
	jQuery("body").prepend(window.SVG_SPRITES.sprite);
}

function initPreventEmptyAnchor() {
	// $(".svg-convert").svgConvert();
	jQuery('a[href="#"]').click(function(e) {
		e.preventDefault();
	});
}

var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
	|| /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) { 
	isMobile = true;
}

const initAnimatedCounts = () => {
	const ease = (n) => {
		// https://github.com/component/ease/blob/master/index.js
		return --n * n * n + 1;
	};
	const observer = new IntersectionObserver((entries) => {
		entries.forEach((entry) => {
			if (entry.isIntersecting) {
			// Once this element is in view and starts animating, remove the observer,
			// because it should only animate once per page load.
				observer.unobserve(entry.target);
				const countToString = entry.target.getAttribute('data-countTo');
				const countTo = parseFloat(countToString);
				const duration = parseFloat(entry.target.getAttribute('data-animateDuration'));
				const countToParts = countToString.split('.');
				const precision = countToParts.length === 2 ? countToParts[1].length : 0;
				const startTime = performance.now();
				const step = (currentTime) => {
					const progress = Math.min(ease((currentTime  - startTime) / duration), 1);
					entry.target.textContent = (progress * countTo).toFixed(precision);
					if (progress < 1) {
						animationFrame = window.requestAnimationFrame(step);
					} else {
						window.cancelAnimationFrame(animationFrame);
					}
				};
				let animationFrame = window.requestAnimationFrame(step);
			}
		});
	});
	document.querySelectorAll('[data-animateDuration]').forEach((target) => {
		target.setAttribute('data-countTo', target.textContent);
		target.textContent = '0';
		observer.observe(target);
	});
};

function initAccordion() {
	// jQuery(this).text(jQuery(this).text() == 'Expand all' ? 'Close all' : 'Expand all');
	jQuery('.trip__itinerary .jsxIti').click(function(){
		if (jQuery(this).hasClass('collapse')) {
			jQuery(this).text('Close All').removeClass('collapse');
			jQuery('.trip__itinerary .trip__itinerary-title').addClass('active');
			jQuery('.trip__itinerary .trip__itinerary-slide').slideDown();
		} else {
			jQuery(this).text('Expand All').addClass('collapse');
			jQuery('.trip__itinerary .trip__itinerary-title').removeClass('active');
			jQuery('.trip__itinerary .trip__itinerary-slide').slideUp();
		}
	});

	jQuery('.trip__itinerary .trip__itinerary-title').click(function(){
		if (jQuery(this).hasClass('active')) {
			jQuery(this).removeClass('active');
			jQuery(this).siblings('.trip__itinerary-slide').slideUp();
		} else {
			jQuery('.trip__itinerary .trip__itinerary-slide').slideUp();
			jQuery('.trip__itinerary .trip__itinerary-title').removeClass('active');
			jQuery(this).addClass('active');
			jQuery(this).siblings('.trip__itinerary-slide').slideDown();
		}
	});
}

function initMenuOpener() {
	jQuery('.jsxNavTgr').click(function() {
		jQuery('body').toggleClass('navActive');
	});

	jQuery('.jsxSearchTgr').click(function() {
		jQuery('body').toggleClass('searchActive');
	});

	jQuery('.jsxDrop').click(function() {
		jQuery(this).parents('li').toggleClass('dropActive');
		jQuery(this).parents('li').siblings().removeClass('dropActive');
	});

	jQuery('.team__member').click(function() {
		jQuery(this).addClass('hover');
		jQuery(this).siblings().removeClass('hover');
	});

	jQuery('.jsxTeamClose').click(function() {
		jQuery('.team__member').removeClass('hover');
	});

	if(!isMobile) {
		jQuery('.meganav , .dropnav').mouseleave(function(){
			jQuery(this).parents().removeClass('dropActive');
		});
	}


	jQuery('.jsxNavBack').click(function() {
		jQuery('body').removeClass('meganavActive');
	});
	// jQuery(document).click(function(event) {
	// 	if (!jQuery(event.target).closest(".megamenu > .dropdown, .dropdown .dropdown-menu").length) {
	// 		jQuery(".megamenu > .dropdown").removeClass('drop-active');
	// 	}
	// });
}

function initOpenClose() {
	jQuery('.jsxFqEa').click(function(){
		jQuery('.trip__faq-item').addClass('faqitem-active');
		jQuery('.trip__faq--detail').slideDown();
	});
	jQuery('.jsxFqCa').click(function(){
		jQuery('.trip__faq-item').removeClass('faqitem-active');
		jQuery('.trip__faq--detail').slideUp();
	});

	jQuery('.trip__faq--title').click(function() {
		jQuery(this).parents('.trip__faq-item').toggleClass('faqitem-active');
		jQuery(this).siblings('.trip__faq--detail').slideToggle();
	});

	jQuery(".jsxVidPlay").click(function(){
		jQuery(this).parents('.mediatape').addClass('onplay');
		var videoURL = jQuery('#player01').prop('src');
		videoURL += "&autoplay=1";
		$('#player01').prop('src',videoURL);
	});
}

function initFixedHeader() {
	// var stickyOffset = jQuery('.site__header').height();
	var stickyOffset = 5;
	var sticky = jQuery('#wrapper');
	var top_fixed;
	top_fixed = jQuery('.site__header').offset().top;
	if (top_fixed >= stickyOffset) sticky.addClass('is_stuck');
	jQuery(window).scroll(function(){
		scroll = jQuery(window).scrollTop();
		if (scroll >= stickyOffset){
			sticky.addClass('is_stuck');
		} else {
			sticky.removeClass('is_stuck');
		}
	});
}

function initIsotope() {
	// external js: isotope.pkgd.js


	// init Isotope
	var $grid = $('.project__grid').isotope({
		itemSelector: '.project__element',
		layoutMode: 'fitRows',
	});

	// // filter functions
	// var filterFns = {
	// 	// show if number is greater than 50
	// 	numberGreaterThan50: function() {
	// 	var number = $(this).find('.number').text();
	// 	return parseInt( number, 10 ) > 50;
	// 	},
	// 	// show if name ends with -ium
	// 	ium: function() {
	// 	var name = $(this).find('.name').text();
	// 	return name.match( /ium$/ );
	// 	}
	// };

	// bind filter button click
	$('.button-group').on( 'click', 'button', function() {
		var filterValue = $( this ).attr('data-filter');
		// use filterFn if matches value
		// filterValue = filterFns[ filterValue ] || filterValue;
		$grid.isotope({ filter: filterValue });
	});


	// change is-checked class on buttons
	$('.button-group').each( function( i, buttonGroup ) {
		var $buttonGroup = $( buttonGroup );
		$buttonGroup.on( 'click', 'button', function() {
			$buttonGroup.find('.is-checked').removeClass('is-checked');
			$( this ).addClass('is-checked');
		});
	});
}

function initDateRangePicker() {
	if(jQuery('.jsxDateCheckins').length) {
		jQuery('.jsxDateCheckins').daterangepicker({
			locale: {
				format: 'YYYY/MM/DD'
			}
		});
		// reseting the placeholder value for first load
		jQuery('.jsxDateCheckins').val('');
		jQuery('.jsxDateCheckins').attr("placeholder","Check In → Check Out");
	}

//--------------------------------------------------------------------------------

	if(jQuery('.jsxDateFlight').length) {
		jQuery('.jsxDateFlight').daterangepicker({
			locale: {
				format: 'YYYY/MM/DD'
			}
		});
		// reseting the placeholder value for first load
		jQuery('.jsxDateFlight').val('');
		jQuery('.jsxDateFlight').attr("placeholder","Departure Date → Return Date");
	}

//--------------------------------------------------------------------------------

	if(jQuery('.jsxDatevolunteer').length) {
		jQuery('.jsxDatevolunteer').daterangepicker({
			locale: {
				format: 'YYYY/MM/DD'
			}
		});
		// reseting the placeholder value for first load
		jQuery('.jsxDatevolunteer').val('');
		jQuery('.jsxDatevolunteer').attr("placeholder","Start Date → End Date");
	}

//--------------------------------------------------------------------------------

	if(jQuery('.jsxDateTravel').length) {
		jQuery('.jsxDateTravel').daterangepicker({
			singleDatePicker: true,
			showDropdowns: true,
			locale: {
				format: 'YYYY/MM/DD'
			}
		});
		// reseting the placeholder value for first load
		jQuery('.jsxDateTravel').val('');
		jQuery('.jsxDateTravel').attr("placeholder","Select Travel Date");
	}
}

function initSlickSlider() {
	const marker = document.querySelector('.site__nav .jsxMarker');
	const navlist = document.querySelectorAll('.site__nav > ul > li');
	const dnavitem = document.querySelector('.site__nav > ul > li:first-child')
	const dfpos = dnavitem.getBoundingClientRect();
	marker.style.left = dfpos.x + 'px';
	marker.style.top = dfpos.y + 'px';
	marker.style.width = dfpos.width + 'px';
	marker.style.height = dfpos.height + 'px';
	navlist.forEach(list_item=> {
		list_item.addEventListener('mouseover', () => {
			marker.classList.add('active');
			let position = list_item.getBoundingClientRect();
			marker.style.left = position.x + 'px';
			marker.style.top = position.y + 'px';
			marker.style.width = position.width + 'px';
			marker.style.height = position.height + 'px';
		});
		list_item.addEventListener('mouseout', () => {
			marker.classList.remove('active');
		});
	});


	// const marker = document.querySelector('#marker')
	// const item = document.querySelectorAll('.site__nav ul li a')
	// function indicator(e){
	// 	marker.style.top = e.offsetTop+'px';
	// 	marker.style.width = e.offsetWidth+'px';

	// }
	// item.forEach(link=>{
	// 	link.addEventListener('mousemove', (e)=>{
	// 		indicator(e.target)
	// 	})
	// });


	if(jQuery(".jsxPrimeSlide").length) {
		jQuery('.jsxPrimeSlide').slick({
			mobileFirst: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: false,
			dots: true,
			fade: true,
			cssEase: 'linear',
			pauseOnHover: true,
			autoplay: true,
			autoplaySpeed: 5000
		});
	}

	if(jQuery(".jsxReview").length) {
		jQuery('.jsxReview').slick({
			mobileFirst: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: false,
			dots: true,
			fade: true,
			cssEase: 'linear',
			pauseOnHover: true,
			autoplay: true,
			autoplaySpeed: 5000
		});
	}

	if(jQuery(".jsxCritiqueSlips").length) {
		jQuery('.jsxCritiqueSlips').slick({
			mobileFirst: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			pauseOnHover: true,
			autoplay: true,
			autoplaySpeed: 5000,
			centerMode: true,
			slidesToShow: 1,
			centerPadding: '0',
			prevArrow:"<button type='button' class='slick-prev'><span class='icon-prev'></span></button>",
			nextArrow:"<button type='button' class='slick-next'><span class='icon-next'></span></button>",
			responsive: [
			{
				breakpoint: 767,
				settings: {
					centerPadding: '100px',
				}
			},
			{
				breakpoint: 859,
				settings: {
					centerPadding: '150px',
				}
			},
			{
				breakpoint: 991,
				settings: {
					centerPadding: '150px',
				}
			},
			{
				breakpoint: 1023,
				settings: {
					centerPadding: '180px',
				}
			},
			{
				breakpoint: 1299,
				settings: {
					centerPadding: '300px',
				}
			}
			]
		});
	}

	if(jQuery(".jsxSays").length) {
		jQuery('.jsxSays').slick({
			mobileFirst: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			pauseOnHover: true,
			autoplay: true,
			autoplaySpeed: 5000,
			prevArrow:"<button type='button' class='slick-prev'><span class='icon-prev'></span></button>",
			nextArrow:"<button type='button' class='slick-next'><span class='icon-next'></span></button>",
			responsive: [
			{
				breakpoint: 767,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
				}
			}
			]
		});
	}

	if(jQuery(".jsxRvSays").length) {
		jQuery('.jsxRvSays').slick({
			mobileFirst: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			pauseOnHover: true,
			autoplay: true,
			autoplaySpeed: 5000,
			prevArrow:"<button type='button' class='slick-prev'><span class='icon-prev'></span></button>",
			nextArrow:"<button type='button' class='slick-next'><span class='icon-next'></span></button>",
			responsive: [
			{
				breakpoint: 767,
				settings: {
					slidesToShow: 2,
					slidesToScroll: 1,
				}
			},
			{
				breakpoint: 991,
				settings: {
					slidesToShow: 3,
					slidesToScroll: 1,
				}
			}
			]
		});
	}

	// if(jQuery(".jsxDestinyGlid").length) {
	// 	jQuery('.jsxDestinyGlide').slick({
	// 		infinite: false,
	// 		slidesToShow: 1,
	// 		slidesToScroll: 1,
	// 		arrows: false,
	// 		fade: true,
	// 		asNavFor: '.jsxDestinySelect'
	// 	});

	// 	jQuery('.jsxDestinySele').slick({
	// 		slidesToShow: 4,
	// 		slidesToScroll: 1,
	// 		asNavFor: '.jsxDestinyGlide',
	// 		focusOnSelect: true,
	// 		prevArrow:"<button type='button' class='slick-prev'><span class='icon-prev'></span></button>",
	// 		nextArrow:"<button type='button' class='slick-next'><span class='icon-next'></span></button>",
	// 		infinite: false
	// 	});
	// }


	// if(jQuery(".jsxPlaCard").length) {
	// 	jQuery('.jsxPlaCard').slick({
	// 		mobileFirst: true,
	// 		slidesToShow: 1,
	// 		slidesToScroll: 1,
	// 		prevArrow:"<button type='button' class='slick-prev'><span class='icon-angle-left'></span></button>",
	// 		nextArrow:"<button type='button' class='slick-next'><span class='icon-angle-right'></span></button>",
	// 		fade: true,
	// 		cssEase: 'linear',
	// 		pauseOnHover: true,
	// 		autoplaySpeed: 5000
	// 	});
	// }

	// if(jQuery(".jsxFeatureGlide").length) {
	// 	jQuery('.jsxFeatureGlide').slick({
	// 		mobileFirst: true,
	// 		slidesToShow: 1,
	// 		slidesToScroll: 1,
	// 		arrows: true,
	// 	});
	// }

	// if(jQuery(".jsxFeatureGlide").length) {
	// 	jQuery('.jsxFeatureGlide').slick({
	// 		mobileFirst: true,
	// 		slidesToShow: 1,
	// 		slidesToScroll: 1,
	// 		prevArrow:"<button type='button' class='slick-prev'><span class='icon-prev'></span></button>",
	// 		nextArrow:"<button type='button' class='slick-next'><span class='icon-next'></span></button>",
	// 		responsive: [
	// 		{
	// 			breakpoint: 575,
	// 			settings: {
	// 				slidesToShow: 2,
	// 				slidesToScroll: 1,
	// 			}
	// 		},
	// 		{
	// 			breakpoint: 991,
	// 			settings: {
	// 				slidesToShow: 3,
	// 				slidesToScroll: 1,
	// 			}
	// 		}
	// 		]
	// 	});
	// }

	// if(jQuery(".jsxActivityGlide").length) {
	// 	jQuery('.jsxActivityGlide').slick({
	// 		mobileFirst: true,
	// 		slidesToShow: 1,
	// 		slidesToScroll: 1,
	// 		prevArrow:"<button type='button' class='slick-prev'><span class='icon-prev'></span></button>",
	// 		nextArrow:"<button type='button' class='slick-next'><span class='icon-next'></span></button>",
	// 		responsive: [
	// 		{
	// 			breakpoint: 575,
	// 			settings: {
	// 				slidesToShow: 2,
	// 				slidesToScroll: 1,
	// 			}
	// 		}
	// 		]
	// 	});
	// }

	// if(jQuery(".jsxMementoGlide").length) {
	// 	jQuery('.jsxMementoGlide').slick({
	// 		mobileFirst: true,
	// 		adaptiveHeight: true,
	// 		slidesToShow: 1,
	// 		slidesToScroll: 1,
	// 		arrows: false,
	// 		dots: true,
	// 		autoplay: false
	// 	});
	// }

	// if(jQuery(".jsxSnapGlider").length) {
	// 	jQuery('.jsxSnapGlider').slick({
	// 		mobileFirst: true,
	// 		prevArrow:"<button type='button' class='slick-prev'><span class='icon-prev'></span></button>",
	// 		nextArrow:"<button type='button' class='slick-next'><span class='icon-next'></span></button>",
	// 		slidesToShow: 2,
	// 		slidesToScroll: 1,
	// 		autoplay: false,
	// 		responsive: [
	// 		{
	// 			breakpoint: 767,
	// 			settings: {
	// 				slidesToShow: 3,
	// 				slidesToScroll: 1,
	// 			}
	// 		},
	// 		{
	// 			breakpoint: 991,
	// 			settings: {
	// 				slidesToShow: 3,
	// 				slidesToScroll: 1,
	// 			}
	// 		}
	// 		]
	// 	});
	// }
}

function initStickyFixed() {
	if(jQuery(".jsxScroller").length) {
		"use strict";
		var $sticky = jQuery('.jsxScroller');

		$sticky.hcSticky({
			mobileFirst: true,
			stickTo: '.trip__enclose',
			stickyClass: 'is_stuck',
			responsive: {
				768: {
					top: 70
				},
				992: {
					top: 88
				}
			}
		});
	}

	if(jQuery(".dnp").length) {
		"use strict";
		var $sticky = jQuery('.dnp');

		$sticky.hcSticky({
			mobileFirst: true,
			stickTo: '.trip__box',
			stickyClass: 'is_stuck',
			disable: true,
			responsive: {
				768: {
					top: 10
				},
				992: {
					disable: false,
					top: 148
				}
			}
		});
	}

	// if(jQuery(".rip__action").length) {
	// 	"use strict";
	// 	var $stickyatn = jQuery('.trip__action');

	// 	$stickyatn.hcSticky({
	// 		mobileFirst: true,
	// 		stickTo: '.trip__box',
	// 		stickyClass: 'is_stuck',
	// 		top: 0,
	// 		disable: true,
	// 		responsive: {
	// 			992: {
	// 				disable: false
	// 			}
	// 		}
	// 	});
	// }

	// if(jQuery(".blog__summary").length) {
	// 	"use strict";
	// 	var $stickyblg = jQuery('.blog__summary');

	// 	$stickyblg.hcSticky({
	// 		mobileFirst: true,
	// 		stickTo: '.blog__plot',
	// 		stickyClass: 'is_stuck',
	// 		top: 0,
	// 		responsive: {
	// 			768: {
	// 				top: 0
	// 			}
	// 		}
	// 	});
	// }

	// if(jQuery(".travel__guide-intro").length) {
	// 	"use strict";
	// 	var $sticky = jQuery('.travel__guide-title');

	// 	$sticky.hcSticky({
	// 		mobileFirst:true,
	// 		stickTo: '.travel__guide-intro',
	// 		stickyClass: 'is_stuck',
	// 		top: 0,
	// 		responsive: {
	// 			768: {
	// 				top: 0
	// 			}
	// 		}
	// 	});
	// 	// stickyClass: 'sticky',
	// }

	// jQuery('.trip-scroller a').click(function(){
	// 	var scrl = jQuery(this).html();
	// 	jQuery('.jsxScroller').html(scrl).removeClass('scractive');
	// 	jQuery('.trip__scroller ul').slideUp();
	// });

	// jQuery('.jsxScroller').click(function(){
	// 	jQuery(this).toggleClass('scractive');
	// 	jQuery('.trip__scroller ul').slideToggle();
	// });
}

function initTabs() {
	jQuery('.destiny__select .jsxDestinySelect').hover(function(){
		var tab_id = jQuery(this).attr('data-tab');
		jQuery(this).addClass('destinyActive');
		jQuery(this).siblings().removeClass('destinyActive');
		jQuery('.destiny__stacks-still').removeClass('destinyActive').hide();
		jQuery("#"+tab_id).addClass('destinyActive').fadeIn(500);
	});
}

function initSmoothScroll() {
	// Cache selectors
	var lastId=0,
	topMenu = jQuery(".jsxScroller ul"),
	topMenuHeight = -6,
	// All list items
	menuItems = topMenu.find("a"),
	// Anchors corresponding to menu items
	scrollItems = menuItems.map(function(){
		// var item = $($(this).attr("href"));
		// if (item.length) { return item; }
		var hrefattr;
		hrefattr = $(this).attr("href");
		if(hrefattr.indexOf('#') > -1) {
			var item = $($(this).attr("href"));
			if (item.length) { return item; }
		}
	});

	// Bind click handler to menu items
	// so we can get a fancy scroll animation
	menuItems.click(function(e){
		var href = $(this).attr("href"),
		offsetTop = href === "#" ? 0 : $(href).offset().top-topMenuHeight+1;
		$('html, body').stop().animate({ 
			scrollTop: offsetTop
		}, 800);
		e.preventDefault();
	});

	// Bind to scroll
	$(window).scroll(function(){
		// Get container scroll position
		var fromTop = $(this).scrollTop()+topMenuHeight;

		// Get id of current scroll item
		var cur = scrollItems.map(function(){
			if ($(this).offset().top < fromTop)
				return this;
		});
		// Get the id of the current element
		cur = cur[cur.length-1];
		var id = cur && cur.length ? cur[0].id : "";

		if (lastId !== id) {
			lastId = id;
			// Set/remove active class
			menuItems
			.parent().removeClass("scractive")
			.end().filter("[href='#"+id+"']").parent().addClass("scractive");
			// var sclbl = menuItems.filter("[href='#"+id+"']").html();
			// jQuery('.jsxScroller').html(sclbl);
		}
	});
}




function initMagnificPopup() {
	// single popup
	jQuery('.jsxPopupSingle').magnificPopup({
		type: 'image',
		closeOnContentClick: true,
		image: {
			verticalFit: false,
			tError: 'The image could not be loaded.',
		},
		removalDelay: 500, //delay removal by X to allow out-animation
		callbacks: {
			beforeOpen: function() {
				// just a hack that adds mfp-anim class to markup 
				this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
				this.st.mainClass = this.st.el.attr('data-effect');
			}
		},
		midClick: true
	});

	// gallery popup
	jQuery('.jsxPopupGallery').magnificPopup({
		delegate: 'a[data-effect]',
		type: 'image',
		tLoading: 'Loading image #%curr%...',
		mainClass: 'mfp-img-mobile',
		gallery: {
			enabled: true,
			navigateByImgClick: true,
			preload: [0,1] // Will preload 0 - before current, and 1 after the current image
		},
		image: {
			tError: 'The image could not be loaded.',
		},
		removalDelay: 500, //delay removal by X to allow out-animation
		callbacks: {
			beforeOpen: function() {
				// just a hack that adds mfp-anim class to markup 
				this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
				this.st.mainClass = this.st.el.attr('data-effect');
			}
		},
		midClick: true
	});

	// inline popup
	jQuery('.jsxInline').magnificPopup({
		// fixedBgPos: true,
		// fixedContentPos: false,
		type: 'inline',
		overflowY: 'auto',
		closeBtnInside: true,
		preloader: false,
		midClick: true,
		removalDelay: 500,
		mainClass: 'mfp-inline',
		callbacks: {
			beforeOpen: function() {
				this.st.mainClass = this.st.el.attr('data-effect');
			}
		}
	});

	jQuery('.jsxFrame').magnificPopup({
		disableOn: 700,
		type: 'iframe',
		mainClass: 'mfp-fade',
		removalDelay: 400,
		preloader: false,
		fixedContentPos: false,
		iframe: {
			markup: '<div class="mfp-iframe-scaler">'+
			'<div class="mfp-close"></div>'+
			'<iframe class="mfp-iframe" src="//about:blank" frameborder="0" allowfullscreen allow="autoplay"></iframe>'+
			'</div>',
			patterns: {
				youtube: {
					index: 'youtube.com',
					id: 'v=',
					src: 'https://www.youtube.com/embed/%id%?rel=0&autoplay=1&muted=1',
					allow: 'autoplay'
				}
			}
		},
	});
}


// input type number
function initInputNumber() {
	jQuery('<span class="qty-control"><span class="qty-btn qty-up">+</span><span class="qty-btn qty-down">-</span></span>').insertAfter('input[type="number"]');
	jQuery('.form-number').each(function() {
		countSpinner(this);
	});
}

function countSpinner(control) {
	var spinner = jQuery(control),
	input = spinner.find('input[type="number"]'),
	btnUp = spinner.find('.qty-up'),
	btnDown = spinner.find('.qty-down'),
	step = input.attr('step'),
	min = input.attr('min'),
	max = input.attr('max');

	if (typeof step === "undefined") {
		step = 1;
	}

	btnUp.click(function() {
		var oldValue = parseFloat(input.val());
		if (isNaN(oldValue)) {
			var newVal = parseInt(min) + parseInt(step);
		} else if (oldValue >= max) {
			var newVal = oldValue;
		} else {
			var newVal = parseInt(oldValue) + parseInt(step);
			if (newVal >= max) {
				newVal = max;
			}
		}
		spinner.find("input").val(newVal);
		spinner.find("input").trigger("change");
	});

	btnDown.click(function() {
		var oldValue = parseFloat(input.val());
		if (isNaN(oldValue)) {
			var newVal = min;
		} else if (oldValue <= min) {
			var newVal = oldValue;
		} else {
			var newVal = parseInt(oldValue) - parseInt(step);
			if (newVal <= min) {
				newVal = min;
			}
		}
		spinner.find("input").val(newVal);
		spinner.find("input").trigger("change");
	});

	// disable mousewheel on a input number field when in focus
	// (to prevent Cromium browsers change the value when scrolling)
	// jQuery('form').on('focus', 'input[type=number]', function (e) {
	// 	jQuery(this).on('mousewheel.disableScroll', function (e) {
	// 		e.preventDefault()
	// 	})
	// })
	// jQuery('form').on('blur', 'input[type=number]', function (e) {
	// 	jQuery(this).off('mousewheel.disableScroll')
	// })
}
