(function () {
  "use strict";

  var destInput = document.querySelector("[data-txf-destination]");
  var form = document.querySelector("[data-txf-form]");
  var submitBtn = document.querySelector("[data-txf-submit]");

  if (destInput) {
    destInput.addEventListener("input", function () {
      var digits = destInput.value.replace(/\D/g, "").slice(0, 9);
      var groups = [digits.slice(0, 2), digits.slice(2, 4), digits.slice(4, 7), digits.slice(7, 9)];
      destInput.value = groups.filter(Boolean).join(" ");
    });
  }

  if (form && submitBtn) {
    form.addEventListener("submit", function () {
      submitBtn.classList.add("is-loading");
      submitBtn.disabled = true;
    });
  }
})();

const container = document.getElementById('destinations-container');
const btnAdd = document.getElementById('btn-add-destination');

if (container && btnAdd) {

    btnAdd.addEventListener('click', () => {

        const item = document.createElement('div');
        item.className = 'txf-destination-item';

        item.innerHTML = `
            <div class="txf-phone">
                <span class="txf-phone__prefix">+261</span>
                <input
                    class="txf-phone__input"
                    type="tel"
                    inputmode="numeric"
                    name="numero_user_destination[]"
                    placeholder="38 63 456 98"
                    maxlength="12"
                    required
                >
            </div>

            <button type="button" class="btn-remove-destination">
                Supprimer
            </button>
        `;

        container.appendChild(item);
    });

    container.addEventListener('click', (e) => {
        if (e.target.classList.contains('btn-remove-destination')) {
            e.target.closest('.txf-destination-item').remove();
        }
    });

}