/**
* Template Name: Dewi
* Template URL: https://bootstrapmade.com/dewi-free-multi-purpose-html-template/
* Updated: Aug 07 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

console.log('beginning of main'); // Keep this log

(function () {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    // CORRECTION: Check if selectHeader exists
    if (!selectHeader || (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top'))) return; 
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');

  function mobileNavToogle() {
    document.querySelector('body').classList.toggle('mobile-nav-active');
    mobileNavToggleBtn.classList.toggle('bi-list');
    mobileNavToggleBtn.classList.toggle('bi-x');
  }
  // CORRECTION: Check if mobileNavToggleBtn exists
  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener('click', mobileNavToogle);
  }

  /**
   * Hide mobile nav on same-page/hash links
   */
  document.querySelectorAll('#navmenu a').forEach(navmenu => {
    navmenu.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        mobileNavToogle();
      }
    });
  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
    navmenu.addEventListener('click', function (e) {
      e.preventDefault();
      // CORRECTION: Check if parentNode and nextElementSibling exist
      if (this.parentNode && this.parentNode.nextElementSibling) {
        this.parentNode.classList.toggle('active');
        this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
        e.stopImmediatePropagation();
      }
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  // CORRECTION: Check if scrollTop exists before adding listener
  if (scrollTop) { 
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  }

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    // CORRECTION: Check if AOS is defined
    if (typeof AOS !== 'undefined') { 
      AOS.init({
        duration: 600,
        easing: 'ease-in-out',
        once: true,
        mirror: false
      });
    }
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  // CORRECTION: Check if GLightbox is defined
  if (typeof GLightbox !== 'undefined') {
    const glightbox = GLightbox({
      selector: '.glightbox'
    });
  }

  /**
   * Initiate Pure Counter
   */
  // CORRECTION: Check if PureCounter is defined
  if (typeof PureCounter !== 'undefined') {
    new PureCounter();
  }

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function (swiperElement) {
      // CORRECTION: Check if Swiper is defined
      if (typeof Swiper === 'undefined') return; 

      let configEl = swiperElement.querySelector(".swiper-config");
      // CORRECTION: Check if config element exists
      if (!configEl) return; 
      
      let config = {};
      try {
           config = JSON.parse(configEl.innerHTML.trim());
      } catch (e) {
          console.error("Could not parse swiper config", e);
          return; // Skip if config is invalid
      }


      if (swiperElement.classList.contains("swiper-tab")) {
        // Assuming initSwiperWithCustomPagination is defined elsewhere
        if (typeof initSwiperWithCustomPagination === 'function') {
           initSwiperWithCustomPagination(swiperElement, config);
        }
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }
  window.addEventListener("load", initSwiper);


  /**
   * Init isotope layout and filters
   */
  document.querySelectorAll('.isotope-layout').forEach(function (isotopeItem) {
    // CORRECTION: Check if Isotope and imagesLoaded are defined
    if (typeof Isotope === 'undefined' || typeof imagesLoaded === 'undefined') return;

    let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
    let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
    let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';
    let container = isotopeItem.querySelector('.isotope-container');

    // CORRECTION: Check if container exists
    if (!container) return; 

    let initIsotope;
    imagesLoaded(container, function () {
      try { // Add try-catch for safety
          initIsotope = new Isotope(container, {
            itemSelector: '.isotope-item',
            layoutMode: layout,
            filter: filter,
            sortBy: sort
          });
      } catch(e) {
          console.error("Isotope initialization failed", e);
      }
    });

    isotopeItem.querySelectorAll('.isotope-filters li').forEach(function (filters) {
      filters.addEventListener('click', function () {
        // CORRECTION: Check if initIsotope was successfully created
        if (!initIsotope) return; 

        let activeFilter = isotopeItem.querySelector('.isotope-filters .filter-active');
        if (activeFilter) activeFilter.classList.remove('filter-active');
        
        this.classList.add('filter-active');
        initIsotope.arrange({
          filter: this.getAttribute('data-filter')
        });
        if (typeof aosInit === 'function') {
          aosInit();
        }
      }, false);
    });

  });

  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function (e) {
    if (window.location.hash) {
      let section = document.querySelector(window.location.hash);
      // CORRECTION: Check if section exists
      if (section) {
        setTimeout(() => {
          try { // Add try-catch for safety
              let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
              window.scrollTo({
                top: section.offsetTop - parseInt(scrollMarginTop || '0', 10), // Add fallback for parseInt
                behavior: 'smooth'
              });
          } catch (e) {
             console.error("Error scrolling to hash", e);
          }
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    })
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);

  // This is the code that was likely causing the error at line 216 or similar
  // It tries to find elements that might not exist on every page.
  // We need to check if these elements exist before adding listeners.

  let popup = document.querySelector("#btn-pop-up-add-aura");
  let divpopup = document.querySelector(".pop-up-add-aura");
  let divtable = document.querySelector(".tabela-aura");
  
  // CORRECTION: Check if all elements exist before adding listener
  if (popup && divpopup && divtable) { 
      popup.addEventListener("click", function (event) {
        divpopup.classList.toggle("active");
        // Use inline style or toggle a class for display
        divpopup.style.display = divpopup.classList.contains("active") ? "flex" : "none"; 
        divtable.style.display = divpopup.classList.contains("active") ? "none" : "table"; // Or block, depending on original style
      });
  } else {
      console.warn("Popup elements (#btn-pop-up-add-aura, .pop-up-add-aura, .tabela-aura) not found on this page.");
  }


  //script para fechar o popup (cancel)
  let popupcancel = document.querySelector("#botao-cancel");
  
  // CORRECTION: Check if elements exist before adding listener
  if (popupcancel && divpopup && divtable) { 
      popupcancel.addEventListener("click", function (event) {
        divpopup.classList.remove("active");
        divpopup.style.display = "none";
        divtable.style.display = "table"; // Or block
      });
  } else {
      console.warn("Popup cancel button (#botao-cancel) not found on this page.");
  }

})(); 
