jQuery(document).ready(function () {
  // ----- Init Page -----
  initCalculator();

  // ----- Init Functions -----
  function initCalculator() {
    // Alert Elements
    var $weightAlert = jQuery(".js-ad-alert-weight");
    var $uiDlAlert = jQuery(".js-ad-alert-ui-dl");
    var $uiKgAlert = jQuery(".js-ad-alert-ui-kg");
    var $alertCloseButton = jQuery(".js-ad-alert-close-button");
    // Input Elements
    var $allInputs = jQuery(".js-ad-input");
    var $weightInput = jQuery(".js-ad-weight-input ");
    var $uiDlInput = jQuery(".js-ad-ui-dl-input");
    var $uiKgInput = jQuery(".js-ad-ui-kg-input");
    // Button Elements
    var $calculateButton = jQuery(".js-ad-calculate-button");
    var $resetButton = jQuery(".js-ad-reset-button");
    // Results
    var $resultsContainer = jQuery(".js-ad-results-container");
    var $uiResultField = jQuery(".ad-calculator__result--ui");
    var $percentResultField = jQuery(".js-result-percent");

    // --- Attach Event Listeners ---
    $allInputs.on("input", inputLengthLimiter);
    $weightInput.on("keyup", weightInputValidation);
    $uiDlInput.on("keyup", uiDlInputValidation);
    $uiKgInput.on("keyup", uiKgInputValidation);
    $alertCloseButton.on("click", closeAlertHandler);
    $calculateButton.on("click", calculateHandler);
    $resetButton.on("click", resetHandler);

    // --- Event Handlers ---

    // Input Validation
    function inputLengthLimiter(event) {
      var $currentInput = jQuery(this);
      var currentValue = $currentInput.val();
      var maxLength = $currentInput.data("max_length");

      if (currentValue.length > maxLength) {
        $currentInput.val(currentValue.slice(0, maxLength));
      }
    }

    function weightInputValidation(event) {
      var $currentInput = jQuery(this);
      var currentValue = Number($currentInput.val());
      if (currentValue < 1 || currentValue > 150) {
        $weightAlert.addClass("calc-a-visible");
      } else {
        $weightAlert.removeClass("calc-a-visible");
      }
    }

    function uiDlInputValidation(event) {
      var $currentInput = jQuery(this);
      var currentValue = Number($currentInput.val());
      if (currentValue < 0 || currentValue > 100) {
        $uiDlAlert.addClass("calc-a-visible");
      } else {
        $uiDlAlert.removeClass("calc-a-visible");
      }
    }

    function uiKgInputValidation(event) {
      var $currentInput = jQuery(this);
      var currentValue = Number($currentInput.val());
      if (currentValue < 10 || currentValue > 100) {
        $uiKgAlert.addClass("calc-a-visible");
      } else {
        $uiKgAlert.removeClass("calc-a-visible");
      }
    }

    function closeAlertHandler(event) {
      var $currentCloseButton = jQuery(this);
      var $currentAlert = $currentCloseButton.closest(".js-ad-alert.calc-a-visible");
      $currentAlert.removeClass("calc-a-visible");
    }

    // Calculate
    function calculateHandler(event) {
      var currentWeightValue = Number($weightInput.val());
      var currentUiDlValue = Number($uiDlInput.val());
      var currentUiKgValue = Number($uiKgInput.val());

      var uiNumberResult = currentWeightValue * currentUiKgValue;
      var uiStringResult = uiNumberResult + " UI";

      var percentNumberResult = 2 * currentUiKgValue + currentUiDlValue;
      var percentStringResult = percentNumberResult + " %";

      $uiResultField.text(uiStringResult);
      $percentResultField.text(percentStringResult);
      $resultsContainer.removeClass("ad-hidden");
    }

    function resetHandler(event) {
      $weightInput.val("");
      $uiDlInput.val("");
      $uiKgInput.val("");
      $resultsContainer.addClass("ad-hidden");
      $uiResultField.text("");
      $percentResultField.text("");
    }
  }
});
















