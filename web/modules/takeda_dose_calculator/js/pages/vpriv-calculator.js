jQuery(document).ready(function () {
  // ----- Elements -----
  var $weightInput = jQuery(".js-vp-weight-input");
  var $resetButton = jQuery(".js-vp-reset-button");

  // table fields
  var $vials15 = jQuery("#vials15");
  var $vials30 = jQuery("#vials30");
  var $vials60 = jQuery("#vials60");
  var $ur15 = jQuery("#ur15");
  var $ur30 = jQuery("#ur30");
  var $ur60 = jQuery("#ur60");

  // ----- Attach Event Listeners -----
  $weightInput.on("input", inputMaxLengthHandler);
  $weightInput.on("keyup", calculationHandler);
  $resetButton.on("click", resetCalculatorHandler);

  // ----- Event Handlers -----
  function calculationHandler(event) {
    var patientWeight = $weightInput.val();
    var weeks = 4.3452;

    var vials15 = (15 * patientWeight) / 400;
    var vials30 = (30 * patientWeight) / 400;
    var vials60 = (60 * patientWeight) / 400;

    var ur15 = vials15 * weeks;
    var ur30 = vials30 * weeks;
    var ur60 = vials60 * weeks;

    $vials15.val(vials15.toFixed(2));
    $vials30.val(vials30.toFixed(2));
    $vials60.val(vials60.toFixed(2));

    $ur15.val(ur15.toFixed(2));
    $ur30.val(ur30.toFixed(2));
    $ur60.val(ur60.toFixed(2));
  };

  function resetCalculatorHandler(event) {
    $weightInput.val("");
    $vials15.val("");
    $vials30.val("");
    $vials60.val("");
    $ur15.val("");
    $ur30.val("");
    $ur60.val("");
  }

  function inputMaxLengthHandler(event) {
    var maxLength = $weightInput.data("max_length");
    var currentValue = $weightInput.val();

    if (currentValue.length > maxLength) {
      $weightInput.val(currentValue.slice(0, maxLength));
    }
  }
});



