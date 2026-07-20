(function () {
  "use strict";

  function animateCount(el) {
    var target = parseFloat(el.getAttribute("data-count-target"));
    var suffix = el.getAttribute("data-count-suffix") || "";
    var isDecimal = target % 1 !== 0;
    var duration = 900;
    var start = null;

    function step(timestamp) {
      if (start === null) {
        start = timestamp;
      }
      var progress = Math.min((timestamp - start) / duration, 1);
      var eased = 1 - Math.pow(1 - progress, 3);
      var current = target * eased;
      el.textContent = (isDecimal ? current.toFixed(1) : Math.round(current).toLocaleString("fr-FR")) + suffix;

      if (progress < 1) {
        requestAnimationFrame(step);
      }
    }

    requestAnimationFrame(step);
  }

  document.querySelectorAll("[data-count-target]").forEach(animateCount);

  var bars = document.querySelectorAll("[data-bar-target]");
  requestAnimationFrame(function () {
    setTimeout(function () {
      bars.forEach(function (bar) {
        bar.style.height = bar.getAttribute("data-bar-target") + "%";
      });
    }, 100);
  });
})();
