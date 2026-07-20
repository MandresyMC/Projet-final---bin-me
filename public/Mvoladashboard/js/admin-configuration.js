(function () {
  "use strict";

  /* ---- Add a new prefix pill ---- */
  var prefixForm = document.querySelector("[data-prefix-form]");
  var prefixInput = document.querySelector("[data-prefix-input]");
  var prefixList = document.querySelector("[data-prefix-list]");

  if (prefixForm && prefixInput && prefixList) {
    prefixForm.addEventListener("submit", function (event) {
      event.preventDefault();

      var value = prefixInput.value.trim();
      if (!/^\d{3}$/.test(value)) {
        prefixInput.focus();
        return;
      }

      var exists = Array.prototype.some.call(
        prefixList.querySelectorAll(".pill__value"),
        function (el) { return el.textContent.trim() === value; }
      );
      if (exists) {
        prefixInput.value = "";
        prefixInput.focus();
        return;
      }

      var pill = document.createElement("div");
      pill.className = "pill";
      pill.innerHTML =
        '<span class="pill__value">' + value + '</span>' +
        '<button type="button" class="pill__toggle" data-state="on">DESACTIVER</button>';

      prefixList.appendChild(pill);
      window.mvolaAdminWireToggle(pill.querySelector(".pill__toggle"));

      prefixInput.value = "";
      prefixInput.focus();
    });
  }

  /* ---- Taxes & frais form ---- */
  var taxesForm = document.querySelector("[data-taxes-form]");
  var taxesBody = document.querySelector("[data-taxes-body]");
  var taxesEmpty = document.querySelector("[data-taxes-empty]");
  var taxesNote = document.querySelector("[data-taxes-note]");
  var typeSelect = document.querySelector("[data-taxes-type]");
  var minInput = document.querySelector("[data-taxes-min]");
  var maxInput = document.querySelector("[data-taxes-max]");
  var fraisInput = document.querySelector("[data-taxes-frais]");

  function showNote(message, kind) {
    if (!taxesNote) {
      return;
    }
    taxesNote.textContent = message;
    taxesNote.className = "taxes-form__note" + (kind ? " is-" + kind : "");
    taxesNote.hidden = !message;
  }

  function formatAr(value) {
    return Number(value).toLocaleString("fr-FR") + " Ar";
  }

  function toggleDepotFree() {
    var isDepot = typeSelect.value === "depot";
    [minInput, maxInput, fraisInput].forEach(function (input) {
      input.disabled = isDepot;
    });
    if (isDepot) {
      showNote("Le dépôt MVola est gratuit : aucun frais à configurer.", "info");
    } else {
      showNote("");
    }
  }

  if (typeSelect) {
    typeSelect.addEventListener("change", toggleDepotFree);
    toggleDepotFree();
  }

  if (taxesForm && taxesBody) {
    taxesForm.addEventListener("submit", function (event) {
      event.preventDefault();

      if (typeSelect.value === "depot") {
        return;
      }

      var min = parseFloat(minInput.value);
      var max = parseFloat(maxInput.value);
      var frais = parseFloat(fraisInput.value);

      if (isNaN(min) || isNaN(max) || isNaN(frais) || min < 0 || frais < 0) {
        showNote("Merci de remplir tous les champs avec des valeurs valides.", "error");
        return;
      }

      if (max <= min) {
        showNote("Le montant max doit être supérieur au montant min.", "error");
        return;
      }

      var typeLabel = typeSelect.options[typeSelect.selectedIndex].text;

      var row = document.createElement("tr");
      row.setAttribute("data-taxes-row", "");
      row.innerHTML =
        "<td>" + typeLabel + "</td>" +
        "<td>" + formatAr(min) + "</td>" +
        "<td>" + formatAr(max) + "</td>" +
        "<td>" + formatAr(frais) + "</td>" +
        '<td><button type="button" class="admin-btn admin-btn--orange admin-btn--small" data-taxes-delete>Supprimer</button></td>';

      taxesBody.appendChild(row);
      wireDelete(row.querySelector("[data-taxes-delete]"));

      minInput.value = "";
      maxInput.value = "";
      fraisInput.value = "";
      showNote("Nouvelle règle de frais ajoutée.", "info");
      updateEmptyState();
    });
  }

  function updateEmptyState() {
    if (!taxesBody || !taxesEmpty) {
      return;
    }
    var hasRows = taxesBody.querySelectorAll("[data-taxes-row]").length > 0;
    taxesEmpty.hidden = hasRows;
  }

  function wireDelete(btn) {
    if (!btn) {
      return;
    }
    btn.addEventListener("click", function () {
      var row = btn.closest("[data-taxes-row]");
      if (row) {
        row.remove();
      }
      updateEmptyState();
    });
  }

  document.querySelectorAll("[data-taxes-delete]").forEach(wireDelete);
  updateEmptyState();
})();
