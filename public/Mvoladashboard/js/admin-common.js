(function () {
  "use strict";

  function wireToggle(btn) {
    btn.addEventListener("click", function () {
      var pill = btn.closest(".pill");
      var isOn = btn.getAttribute("data-state") === "on";

      if (isOn) {
        btn.setAttribute("data-state", "off");
        btn.textContent = "ACTIVER";
        if (pill) {
          pill.classList.add("is-off");
        }
      } else {
        btn.setAttribute("data-state", "on");
        btn.textContent = "DESACTIVER";
        if (pill) {
          pill.classList.remove("is-off");
        }
      }
    });
  }

  document.querySelectorAll(".pill__toggle").forEach(wireToggle);
  window.mvolaAdminWireToggle = wireToggle;

  // Quick-nav active state on scroll
  var links = document.querySelectorAll("[data-quicknav-link]");
  var sections = document.querySelectorAll("[data-quicknav-target]");

  if (links.length && sections.length && "IntersectionObserver" in window) {
    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }
          links.forEach(function (link) {
            link.classList.toggle(
              "is-active",
              link.getAttribute("href") === "#" + entry.target.id
            );
          });
        });
      },
      { rootMargin: "-40% 0px -50% 0px" }
    );

    sections.forEach(function (section) {
      observer.observe(section);
    });
  }
})();
