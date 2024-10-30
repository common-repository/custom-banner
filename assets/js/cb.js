jQuery(document).ready(function($) {
	loadCustomBanner(bannerVars.bannerAutoplay, bannerVars.bannerDelay);
	function loadCustomBanner(autoplay, delay) {
		let swiperArgs = {
			direction: "horizontal",
			speed: 400,
			loop: true,
			navigation: {
		        nextEl: ".banner-next",
		        prevEl: ".banner-prev",
		    },
		}
		if (autoplay === "on" && !isNaN(delay)) {
			swiperArgs.autoplay = {
		        delay: delay * 1000,
		        disableOnInteraction: false,
		    }
		}
		let bannerSwiper = new Swiper(".bannerSwiper", swiperArgs);
	}
});