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

    if (document.getElementById('combo-slide-counter')) return;

    widgetEl.style.position = 'relative';

    var realTotal = [].filter.call(swiper.slides, function (s) {
      return !s.classList.contains('swiper-slide-duplicate');
    }).length;

    var counter = document.createElement('div');
    counter.id  = 'combo-slide-counter';
    counter.setAttribute('aria-hidden', 'true');
    counter.innerHTML =
      '<span id="csc-cur">1</span>' +
      '<span class="csc-sep">/</span>' +
      '<span id="csc-tot">' + realTotal + '</span>';

    widgetEl.appendChild(counter);

    function update() {
      var el = document.getElementById('csc-cur');
      if (el) el.textContent =
        (swiper.realIndex !== undefined ? swiper.realIndex : swiper.activeIndex) + 1;
    }

    swiper.on('slideChange', update);
    update();
  }

  /* =============================================
     LOOP CAROUSEL COUNTERI  (X / Y u nav baru)
     ============================================= */
  var loopCarouselIds = [
    'elementor-element-652b08a',
    'elementor-element-297c149',
    'elementor-element-f8a648b'
  ];

  function initLoopCounter(widgetEl) {
    var swiperEl = widgetEl.querySelector('.swiper-initialized') ||
                   widgetEl.querySelector('.swiper');
    if (!swiperEl) return false;

    // Swiper not yet initialized
    if (!swiperEl.classList.contains('swiper-initialized')) return false;

    // Loop-carousel widget has NO .elementor-widget-container — use widgetEl directly
    var container = widgetEl.querySelector('.elementor-widget-container') || widgetEl;

    // Prevent duplicate
    if (container.querySelector('.combo-loop-counter')) return true;

    // Count real (non-duplicate) slides
    var realSlides = swiperEl.querySelectorAll('.swiper-slide:not(.swiper-slide-duplicate)');
    var realTotal  = realSlides.length;
    if (realTotal === 0) return false;

    var counter = document.createElement('div');
    counter.className = 'combo-loop-counter';
    counter.setAttribute('aria-hidden', 'true');
    counter.textContent = '1 / ' + realTotal;
    container.appendChild(counter);

    var pagination = widgetEl.querySelector('.swiper-pagination');

    function updateCounter() {
      // Prefer swiper instance for accurate realIndex
      if (swiperEl.swiper) {
        var idx = (swiperEl.swiper.realIndex !== undefined
          ? swiperEl.swiper.realIndex
          : swiperEl.swiper.activeIndex) + 1;
        counter.textContent = idx + ' / ' + realTotal;
        return;
      }
      // Fallback: read active bullet position
      if (pagination) {
        var bullets = pagination.querySelectorAll('.swiper-pagination-bullet');
        var active  = pagination.querySelector('.swiper-pagination-bullet-active');
        if (active && bullets.length) {
          var pos = Array.prototype.indexOf.call(bullets, active) + 1;
          counter.textContent = pos + ' / ' + realTotal;
        }
      }
    }

    // Watch pagination bullets for active-class changes
    if (pagination) {
      var observer = new MutationObserver(updateCounter);
      observer.observe(pagination, {
        attributes:      true,
        subtree:         true,
        attributeFilter: ['class']
      });
    }

    // Also hook into swiper instance if available
    if (swiperEl.swiper) {
      swiperEl.swiper.on('slideChange', updateCounter);
    }

    updateCounter();
    return true;
  }

  function initAllLoopCounters(attempt) {
    attempt = attempt || 0;
    var pending = [];

    loopCarouselIds.forEach(function (cls) {
      var el = document.querySelector('.' + cls);
      if (!el) return;
      if (!initLoopCounter(el)) pending.push(cls);
    });

    if (pending.length && attempt < 20) {
      setTimeout(function () { initAllLoopCounters(attempt + 1); }, 300);
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    setTimeout(initSlideCounter, 500);
    setTimeout(initAllLoopCounters, 600);
  });

})();
