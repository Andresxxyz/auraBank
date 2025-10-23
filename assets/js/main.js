/**
* Template Name: Dewi
* Template URL: https://bootstrapmade.com/dewi-free-multi-purpose-html-template/
* Updated: Aug 07 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/


// Coloque este JS para rodar assim que seu site carregar

// Função principal para iniciar tudo
async function iniciarNotificacoes() {
  console.log('Iniciando sistema de notificações...');
  
  // 1. Pedir permissão ao usuário
  let permStatus = await PushNotifications.checkPermissions();
  if (permStatus.receive === 'prompt') {
    permStatus = await PushNotifications.requestPermissions();
  }
  if (permStatus.receive !== 'granted') {
    console.error('Permissão de notificação negada!');
    return;
  }

  // 2. Registrar os botões de ação (Aprovar/Negar)
  await PushNotifications.registerActionTypes({
    types: [
      {
        // ID único para este TIPO de notificação
        id: 'VOTACAO_TRANSAO', 
        actions: [
          {
            id: 'aprovar_voto', // ID da ação
            title: 'Aprovar',
            // Opcional: destructive: false (para cor normal)
          },
          {
            id: 'negar_voto', // ID da ação
            title: 'Negar',
            destructive: true // Marca como ação destrutiva (vermelho)
          }
        ]
      }
    ]
  });

  // 3. Registrar o dispositivo no Firebase para receber notificações
  await PushNotifications.register();
  
  // 4. Adicionar "Ouvintes" (Listeners)
  
  // Ouvinte: Disparado quando o token do dispositivo é gerado
  PushNotifications.addListener('registration', (token) => {
    console.log('Token do dispositivo:', token.value);
    
    // !!! MUITO IMPORTANTE !!!
    // Envie este 'token.value' para seu servidor PHP e salve no
    // banco de dados junto com o ID do usuário.
    // Ex: 
    // fetch('/api/salvar_token_push.php', {
    //   method: 'POST',
    //   headers: {'Content-Type': 'application/json'},
    //   body: JSON.stringify({ token: token.value })
    // });
  });

  // Ouvinte: Disparado quando o usuário clica em um botão (Aprovar/Negar)
  PushNotifications.addListener('pushNotificationActionPerformed', (action) => {
    console.log('Ação da notificação:', action);

    const acaoID = action.actionId;
    const votacaoId = action.notification.data.votacaoId; // Pega o ID da votação

    if (!votacaoId) return;

    if (acaoID === 'aprovar_voto') {
      console.log(`Voto APROVADO para votação ${votacaoId}`);
      // Envie para seu servidor que este usuário APROVOU
      // Ex: fetch(`/api/registrar_voto.php?id=${votacaoId}&voto=aprovar`);
      
    } else if (acaoID === 'negar_voto') {
      console.log(`Voto NEGADO para votação ${votacaoId}`);
      // Envie para seu servidor que este usuário NEGOU
      // Ex: fetch(`/api/registrar_voto.php?id=${votacaoId}&voto=negar`);
    }
  });

  // Ouvinte (Opcional): Disparado se a notificação chegar
  // enquanto o usuário está COM O APP ABERTO.
  PushNotifications.addListener('pushNotificationReceived', (notification) => {
    console.log('Notificação recebida com app aberto:', notification);
    // Aqui você pode mostrar um popup/modal customizado
    // dentro do seu site, já que o usuário já está vendo.
    alert(`Nova votação: ${notification.body}`);
  });
}

// Chame a função principal
// (Certifique-se de importar o Capacitor JS no seu HTML)
// <script src="capacitor.js"></script> 
// (Seu app híbrido com server.url injeta isso automaticamente)
const { PushNotifications } = capacitor.Plugins;
iniciarNotificacoes();
(function () {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
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
  mobileNavToggleBtn.addEventListener('click', mobileNavToogle);

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
      this.parentNode.classList.toggle('active');
      this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
      e.stopImmediatePropagation();
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
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Initiate Pure Counter
   */
  new PureCounter();

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function (swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
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
    let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
    let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
    let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';

    let initIsotope;
    imagesLoaded(isotopeItem.querySelector('.isotope-container'), function () {
      initIsotope = new Isotope(isotopeItem.querySelector('.isotope-container'), {
        itemSelector: '.isotope-item',
        layoutMode: layout,
        filter: filter,
        sortBy: sort
      });
    });

    isotopeItem.querySelectorAll('.isotope-filters li').forEach(function (filters) {
      filters.addEventListener('click', function () {
        isotopeItem.querySelector('.isotope-filters .filter-active').classList.remove('filter-active');
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
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
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

})();

//script para exibir o popup

  let popup = document.querySelector("#btn-pop-up-add-aura");
  let divpopup = document.querySelector(".pop-up-add-aura");
  let divtable = document.querySelector(".tabela-aura");
  popup.addEventListener("click", function (event) {
    
    divpopup.classList.toggle("active");
    divpopup.style.display = "flex";
    divtable.style.display = "none";
    
  });

  //script para fechar o popup (cancel)
  let popupcancel = document.querySelector("#botao-cancel");
  popupcancel.addEventListener("click", function (event) {
    divpopup.classList.remove("active");
    divpopup.style.display = "none";
    divtable.style.display = "table";
  });
