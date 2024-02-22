document.addEventListener("DOMContentLoaded", function () {
  // --- Init Page ---
  initHeader();
  initMobileMenu();
  initBackToTopBtn();

  // --- Init Functions ---
  function initHeader() {
      // --- Elements ---
      const headerEl = document.querySelector(".js-tcm-header");
      const openBottomHeaderButtonEls = document.querySelectorAll(".js-open-bottom-menu");
      const allBottomSectionEls = document.querySelectorAll(".js-header-bottom-section");
      const calculatorsBottomSectionEl = document.querySelector(".js-header-bottom-calculators");
      const eventsBottomSectionEl = document.querySelector(".js-header-bottom-events");
      const mobileUserMenuEl = document.querySelector(".js-mobile-user-menu-el");
      const mobileUserMenuBtnEl = document.querySelector(".js-mobile-user-menu-btn");
      const phoneSearchButtonEl = document.querySelector(".js-phone-search-button");
      const phoneSearchContainerEl = document.querySelector(".js-phone-search-container");

      // --- Attach Event Listeners ---
      openBottomHeaderButtonEls.forEach(function (openBottomBtnEl) {
          openBottomBtnEl.addEventListener("click", toggleHeaderBottomHandler);
      });
      phoneSearchButtonEl.addEventListener("click", mobileSearchToggle);
      mobileUserMenuBtnEl.addEventListener("click", mobileUserMenuToggle);

      // --- Event Handlers ---
      function toggleHeaderBottomHandler(event) {
          const currentButton = event.target;

          if (currentButton.classList.contains("open")) {
              closeBottomSections();
          } else {
              openBottomSection(currentButton);
          }
      }

      function mobileSearchToggle() {
          if (phoneSearchContainerEl.classList.contains("tcm-hidden")) {
              headerEl.classList.add("height-auto");
              phoneSearchContainerEl.classList.remove("tcm-hidden");
          } else {
              phoneSearchContainerEl.classList.add("tcm-hidden");
              headerEl.classList.remove("height-auto");
          }
      }

      function mobileUserMenuToggle(event) {
          mobileUserMenuEl.classList.toggle("open");
      }
      // --- Utils ---
      function closeBottomSections() {
          openBottomHeaderButtonEls.forEach(function (button) {
              button.classList.remove("open");
          });

          allBottomSectionEls.forEach(function (bottomSectionEl) {
              bottomSectionEl.classList.add("tcm-hidden");
          });
          headerEl.classList.remove("max-height-1000");
      }

      function openBottomSection(currentButton) {
          const bottomSectionType = currentButton.dataset.bottom_section_type;
          const allSubSectionLinks = Array.from(document.querySelectorAll(".tcm-header__bottom-nav-item > a"));
          closeBottomSections();
          currentButton.classList.add("open");
          allSubSectionLinks.forEach(function (link) {
              if(link.href == document.location.href) {
		  link.classList.add('active');
              }
          })
          headerEl.classList.add("max-height-1000");
          switch (bottomSectionType) {
              case "calculators": calculatorsBottomSectionEl.classList.remove("tcm-hidden"); break;
              case "events": eventsBottomSectionEl.classList.remove("tcm-hidden"); break;
          }
      }
  }

  function initMobileMenu() {
      // --- Elements ---
      const mobileMenuEl = document.querySelector(".js-mobile-menu");
      const mobileMenuOpenButtonEl = document.querySelector(".js-open-mobile-menu-btn");
      const mobileMenuCloseButtonEl = document.querySelector(".js-mobile-menu-close-button");
      const mobileMenuDropdownEls = document.querySelectorAll(".js-mobile-menu-dropdown");


      // --- Attach Event Listeners ---
      mobileMenuOpenButtonEl.addEventListener("click", openMobileMenuHandler);
      mobileMenuCloseButtonEl.addEventListener("click", closeMobileMenuHandler);

      mobileMenuDropdownEls.forEach(function (dropdownEl) {
          dropdownEl.addEventListener("click", toggleDropdownHandler);
      });

      // --- Event Handlers ---
      function openMobileMenuHandler(event) {
          mobileMenuEl.classList.add("open");
      }

      function closeMobileMenuHandler(event) {
          mobileMenuEl.classList.remove("open");
      }

      function toggleDropdownHandler(event) {
          const currentDropdownEl = event.currentTarget;
          if (currentDropdownEl.classList.contains("open")) {
              currentDropdownEl.classList.remove("open");
          } else {
              resetMobileDropdowns();
              currentDropdownEl.classList.add("open");
          }
      }

      // --- Utils ---
      function resetMobileDropdowns() {
          mobileMenuDropdownEls.forEach(function (dropdownEl) {
              dropdownEl.classList.remove("open");
          });
      }
  }

  function initBackToTopBtn() {
      // --- Elements ---
      const headerEl = document.querySelector(".js-tcm-header");
      const backToTopBtnEl = document.querySelector(".js-back-to-top-btn");

      // --- Attach Event Listeners ---
      backToTopBtnEl.addEventListener("click", backToTopBtnHandler);

      // --- Event handlers ---
      function backToTopBtnHandler(event) {
          headerEl.scrollIntoView({ behavior: "smooth" });
      }
  }
});