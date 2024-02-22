jQuery(document).ready(function () {
  // ----- Init Page -----
  initCalculator();

  function initCalculator() {
    // Alert Elements
    var $weightAlert = jQuery(".js-alert--first");
    var $percentAlert = jQuery(".js-alert--second");
    var $alertCloseButton = jQuery(".js-ad-alert-close-button");
    // Input Elements
    var $allInputs = jQuery(".js-pr-input");
    var $weightInput = jQuery("#input1");
    var $percentInput = jQuery("#input2");
    // Button Elements
    var $calculateButton = jQuery(".js-calculate-button");
    var $resetButton = jQuery(".js-reset-button");
    // Results
    var $resultsContainer = jQuery(".js-results-container");
    var $uiResultField = jQuery(".js-result-ui");
    var $mlResultField = jQuery(".js-result-ml");

    // --- Attach Event Listeners ---
    $allInputs.on("input", inputLengthLimiter);
    $weightInput.on("keyup", weightInputValidation);
    $percentInput.on("keyup", percentInputValidation);
    $alertCloseButton.on("click", closeAlertHandler);
    $calculateButton.on("click", calculateHandler);
    $resetButton.on("click", resetHandler);

    // --- Event Handlers ---
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
      if (currentValue < 40 || currentValue > 100) {
        $weightAlert.addClass("calc-a-visible ");
      } else {
        $weightAlert.removeClass("calc-a-visible ");
      }
    }

    function percentInputValidation(event) {
      var $currentInput = jQuery(this);
      var currentValue = Number($currentInput.val());
      if (currentValue < 10 || currentValue > 70) {
        $percentAlert.addClass("calc-a-visible");
      } else {
        $percentAlert.removeClass("calc-a-visible");
      }
    }

    function closeAlertHandler(event) {
      var $currentCloseButton = jQuery(this);
      var $currentAlert = $currentCloseButton.closest(".js-ad-alert");
      $currentAlert.removeClass("calc-a-visible");
    }

    function calculateHandler(event) {
      var currentWeightValue = Number($weightInput.val());
      var currentPercentValue = Number($percentInput.val());

      var uiNumberResult = 1.2 * currentWeightValue * currentPercentValue;
      var uiStringResult = uiNumberResult;

      var mlNumberResult = Math.floor(0.04 * currentWeightValue * currentPercentValue);
      var mlStringResult = mlNumberResult;

      $uiResultField.text(uiStringResult);
      $mlResultField.text(mlStringResult);
      $resultsContainer.removeClass("ad-hidden");
    }

    function resetHandler(event) {
      $weightInput.val("");
      $percentInput.val("");
      $resultsContainer.addClass("ad-hidden")
      $uiResultField.text("");
      $mlResultField.text("");
    }
  }
});
















