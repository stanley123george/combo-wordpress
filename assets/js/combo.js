/**
 * Combo IT — frontend scripts
 */

(function () {

  /* =============================================
     HERO SLIDE COUNTER  (1 / 3)
     ============================================= */
  function initSlideCounter() {
    var swiperEl =
      document.querySelector('.elementor-element-d5deb9e .swiper-container') ||
      document.querySelector('.elementor-element-d5deb9e .swiper');

    if (!swiperEl || !swiperEl.swiper) {
      setTimeout(initSlideCounter, 300);
      return;
    }

    var swiper   = swiperEl.swiper;
    var widgetEl = document.querySelector('.elementor-element-d5deb9e');
    if (!widgetEl) return;

    // Prevent duplicate
    if (document.getElementById('combo-slide-counter')) return;

    widgetEl.style.position = 'relative';

    var counter = document.createElement('div');
    counter.id  = 'combo-slide-counter';
    counter.setAttribute('aria-hidden', 'true');

    counter.innerHTML =
      '<span id="csc-cur">1</span>' +
      '<span class="csc-sep">/</span>' +
      '<span id="csc-tot">' + swiper.slides.length + '</span>';

    widgetEl.appendChild(counter);

    function update() {
      var el = document.getElementById('csc-cur');
      if (el) el.textContent =
        (swiper.realIndex !== undefined ? swiper.realIndex : swiper.activeIndex) + 1;
    }

    swiper.on('slideChange', update);
    update();
  }

  document.addEventListener('DOMContentLoaded', function () {
    setTimeout(initSlideCounter, 500);
  });

})();
