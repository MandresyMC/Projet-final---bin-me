(function () {
  "use strict";

  function formatDigits(input) {
    var digits = input.value.replace(/\D/g, "").slice(0, 9);
    var groups = [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4, 7), digits.slice(7, 9)];
    input.value = groups.filter(Boolean).join(" ");
  }

  var form = document.querySelector("[data-txf-form]");
  var submitBtn = document.querySelector("[data-txf-submit]");
  var container = document.getElementById("destinations-container");
  var btnAdd = document.getElementById("btn-add-destination");
  var fraisRetraitCheckbox = document.querySelector("[data-txf-frais-retrait]");
  var fraisRetraitHint = document.querySelector("[data-txf-frais-retrait-hint]");
  var prefixes = Array.isArray(window.txfPrefixes) ? window.txfPrefixes : [];

  // Prefixes tries du plus long au plus court pour trouver le match le plus specifique.
  var prefixesTries = prefixes.slice().sort(function (a, b) {
    return b.prefixe.length - a.prefixe.length;
  });

  function groupePourNumero(numero) {
    var digits = numero.replace(/\D/g, "");
    if (digits.length < 2) {
      return null;
    }
    for (var i = 0; i < prefixesTries.length; i++) {
      if (digits.indexOf(prefixesTries[i].prefixe) === 0) {
        return String(prefixesTries[i].proprietaire_nom).toLowerCase() === "local" ? "local" : "autre";
      }
    }
    return null;
  }

  function destinationInputs() {
    return container ? Array.prototype.slice.call(container.querySelectorAll("[data-txf-destination]")) : [];
  }

  function majDisponibiliteFraisRetrait() {
    if (!fraisRetraitCheckbox) {
      return;
    }

    var groupes = destinationInputs()
      .map(function (input) { return groupePourNumero(input.value); })
      .filter(function (g) { return g !== null; });

    var uniquementLocal = groupes.length > 0 && groupes.every(function (g) { return g === "local"; });

    fraisRetraitCheckbox.disabled = !uniquementLocal;
    if (!uniquementLocal) {
      fraisRetraitCheckbox.checked = false;
    }

    if (fraisRetraitHint) {
      fraisRetraitHint.hidden = uniquementLocal || groupes.length === 0;
    }
  }

  function attacherInput(input) {
    input.addEventListener("input", function () {
      formatDigits(input);
      majDisponibiliteFraisRetrait();
    });
  }

  destinationInputs().forEach(attacherInput);
  majDisponibiliteFraisRetrait();

  if (container && btnAdd) {
    btnAdd.addEventListener("click", function () {
      var item = document.createElement("div");
      item.className = "txf-destination-item";

      item.innerHTML =
        '<div class="txf-phone">' +
          '<span class="txf-phone__prefix">+261</span>' +
          '<input class="txf-phone__input" type="tel" inputmode="numeric" ' +
            'name="numero_user_destination[]" placeholder="38 63 456 98" ' +
            'maxlength="12" required data-txf-destination>' +
        "</div>" +
        '<button type="button" class="btn-remove-destination" aria-label="Supprimer ce numéro">' +
          "Supprimer" +
        "</button>";

      container.appendChild(item);
      attacherInput(item.querySelector("[data-txf-destination]"));
      majDisponibiliteFraisRetrait();
    });

    container.addEventListener("click", function (e) {
      if (e.target.classList.contains("btn-remove-destination")) {
        e.target.closest(".txf-destination-item").remove();
        majDisponibiliteFraisRetrait();
      }
    });
  }

  if (form && submitBtn) {
    form.addEventListener("submit", function () {
      submitBtn.classList.add("is-loading");
      submitBtn.disabled = true;
    });
  }
})();
