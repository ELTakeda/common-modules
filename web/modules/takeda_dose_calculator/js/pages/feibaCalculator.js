function limit() {
    var x1 = document.getElementById("input1");
    var x2 = document.getElementById("input2");

    if (x1.value.length > 3) {
        x1.value = x1.value.slice(0, 3);
    }

    if (x2.value.length > 3) {
        x2.value = x2.value.slice(0, 3);
    }
}

jQuery(document).ready(function () {
    //---Elements---
    var $firstInput = jQuery('#input1');
    var $secondInput = jQuery('#input2');
    var $firstAlertField = jQuery('.js-alert--first');
    var $secondAlertField = jQuery('.js-alert--second');
    var $calculateButton = jQuery('.js-calculate-button');
    var $resultContainer = jQuery('.js-results-container');
    var $resultField = jQuery('.js-result');
    var $resetButton = jQuery('.js-reset-button');
    var $alertCloseButton = jQuery('.js-ad-alert-close-button');

    $firstInput.keyup(function (event) {
        var firstInputValue = jQuery(this).val();

        if (firstInputValue < 0 || firstInputValue > 150) {
            $firstAlertField.show()
        }
        else {
            $firstAlertField.hide();
        }
    });

    $secondInput.keyup(function (event) {
        var secondInputValue = jQuery(this).val();

        if (secondInputValue < 50 || secondInputValue > 100) {
            $secondAlertField.show()
        }
        else {
            $secondAlertField.hide();
        }
    });

    $calculateButton.click(function (event) {
        var firstInputValue = $firstInput.val();
        var secondInputValue = $secondInput.val();
        var result = firstInputValue * secondInputValue;

        $resultField.text(result.toString().substring(0, 4));
        $resultContainer.removeClass("ad-hidden");
    });

    $resetButton.click(function (event) {
        $firstInput.val("");
        $secondInput.val("");
        $resultContainer.addClass("ad-hidden");;
        $firstAlertField.hide();
        $secondAlertField.hide();
    });

    $alertCloseButton.click(function (event) {
        jQuery(this).closest('.js-ad-alert').hide();
    });
});
