document.addEventListener("DOMContentLoaded", function () {
  // Get DOM elements
  const toggleButton = document.getElementById("navbarToggle");
  const menu = document.querySelector(".navbar__menu");
  const navbar = document.querySelector(".navbar");

  // Toggle mobile menu
  if (toggleButton && menu) {
    toggleButton.addEventListener("click", function (e) {
      e.stopPropagation();
      this.classList.toggle("active");
      menu.classList.toggle("show");
    });
  }

  // Close menu when clicking outside
  document.addEventListener("click", function (event) {
    if (menu && menu.classList.contains("show")) {
      const isClickInsideMenu = menu.contains(event.target);
      const isClickOnToggle = toggleButton.contains(event.target);

      if (!isClickInsideMenu && !isClickOnToggle) {
        menu.classList.remove("show");
        toggleButton.classList.remove("active");
      }
    }
  });

  // Handle window resize
  window.addEventListener("resize", function () {
    if (window.innerWidth > 992 && menu) {
      menu.classList.remove("show");
      if (toggleButton) {
        toggleButton.classList.remove("active");
      }
    }
  });

  // Add scroll behavior
  let lastScroll = 0;
  const scrollThreshold = 10;

  window.addEventListener("scroll", function () {
    if (!navbar) return;

    const currentScroll = window.pageYOffset;

    // Add background on scroll
    if (currentScroll > 50) {
      navbar.classList.add("scrolled");
    } else {
      navbar.classList.remove("scrolled");
    }

    // Hide/show navbar on scroll
    if (Math.abs(currentScroll - lastScroll) < scrollThreshold) {
      return;
    }

    if (currentScroll > lastScroll && currentScroll > 100) {
      // Scrolling down & past the threshold
      navbar.classList.add("nav-hidden");
    } else {
      // Scrolling up
      navbar.classList.remove("nav-hidden");
    }

    lastScroll = currentScroll;
  });

  // Add active class to current page link
  const currentPath = window.location.pathname;
  const navLinks = document.querySelectorAll(".navbar__link");

  navLinks.forEach((link) => {
    if (link.getAttribute("href") === currentPath) {
      link.classList.add("active");
    }
  });

  // Handle dropdown menus if they exist
  const dropdowns = document.querySelectorAll(".navbar__dropdown");

  dropdowns.forEach((dropdown) => {
    const trigger = dropdown.querySelector(".dropdown-trigger");
    const content = dropdown.querySelector(".dropdown-content");

    if (trigger && content) {
      trigger.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Close other dropdowns
        dropdowns.forEach((otherDropdown) => {
          if (otherDropdown !== dropdown) {
            otherDropdown.classList.remove("active");
          }
        });

        dropdown.classList.toggle("active");
      });
    }
  });

  // Close dropdowns when clicking outside
  document.addEventListener("click", function () {
    dropdowns.forEach((dropdown) => {
      dropdown.classList.remove("active");
    });
  });
});
