function limit() {

    var x1 = document.getElementById("input1");
    var x2 = document.getElementById("input2");
    var x3 = document.getElementById("input3");

    if (x1.value.length > 3) {
        x1.value = x1.value.slice(0, 3);
    }

    if (x2.value.length > 3) {
        x2.value = x2.value.slice(0, 3);
    }

    if (x3.value.length > 3) {
        x3.value = x3.value.slice(0, 3);
    }
}

jQuery(document).ready(function () {
    //---Elements---
    var $firstInput = jQuery('#input1');
    var $secondInput = jQuery('#input2');
    var $thirdInput = jQuery('#input3');
    var $firstAlertField = jQuery('.js-alert--first');
    var $secondAlertField = jQuery('.js-alert--second');
    var $thirdAlertField = jQuery('.js-alert--third');
    var $calculateButton = jQuery('.js-calculate-button');
    var $resultDiv = jQuery('.js-ad-results-container');
    var $resultField = jQuery('.js-result');
    var $resetButton = jQuery('.js-reset-button');
    var $alertCloseButton = jQuery('.js-close-btn');
    var $expectedResultField = jQuery('.js-expected-result');

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

        if (secondInputValue < 10 || secondInputValue > 100) {
            $secondAlertField.show()
        }
        else {
            $secondAlertField.hide();
        }
    });

    $thirdInput.keyup(function (event) {
        var thirdInputValue = $thirdInput.val();

        if (thirdInputValue < 0 || thirdInputValue > 100) {
            $thirdAlertField.show();
        } else {
            $thirdAlertField.hide()
        }
    });

    $calculateButton.click(function (event) {
        var firstInputValue = Number($firstInput.val());
        var secondInputValue = Number($secondInput.val());
        var thirdInputValue = Number($thirdInput.val());
        var result = firstInputValue * secondInputValue;

        if (isNaN(secondInputValue)) {
            secondInputValue = 0;
        }

        if (isNaN(secondInputValue)) {
            secondInputValue = 0;
        }

        if (isNaN(thirdInputValue)) {
            thirdResultValue = 0;
        }

        var result = firstInputValue * thirdInputValue;
        var expectedResult = 2 * thirdInputValue + secondInputValue;
        var expectedResultString =
            "Expected peak factor VIII activity after administration is " + String(expectedResult) + '%' + " or IU/dL.";

        $resultField.text(result);
        $expectedResultField.text(expectedResultString);
        $resultDiv.removeClass("ad-hidden");
    });

    $resetButton.click(function (event) {
        $firstInput.val("");
        $secondInput.val("");
        $thirdInput.val("");
        $resultDiv.addClass("ad-hidden");;
        $firstAlertField.hide();
        $secondAlertField.hide();
        $thirdAlertField.hide();
        $expectedResultField.text('');
    });

    $alertCloseButton.click(function (event) {
        jQuery(this).closest('.js-ad-alert').hide();
    });
});
