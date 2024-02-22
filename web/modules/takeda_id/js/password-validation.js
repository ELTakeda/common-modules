(function ($) {
    $(document).ready(function () {
        var $passwordValidationInfo = $(".password-validation-info");
        var $passwordInput = $('.password-criteria-check');

        $('.password-criteria-list').hide();
        $('#edit-password2').attr('disabled', 'disabled');

        $passwordInput.on('input', function () {
            var password = $(this).val();
            var criteriaMatched = validatePasswordCriteria(password);

            if (criteriaMatched) {
                $(this).removeAttr('title');
                $('.password-criteria-list').hide();
                $('#edit-password2').removeAttr("disabled");
            } else {
                $('.password-criteria-list').show();
                $('#edit-password2').attr('disabled', 'disabled');
                $(this).attr('title', 'Password does not meet the criteria.');
            }
        });
        $passwordInput.on("focus", function () {
            $passwordValidationInfo.addClass("visible");
        });
        $passwordInput.on("blur", function () {
            $passwordValidationInfo.removeClass("visible");
        });


        $('#edit-password2').on('input', function () {
            var password = $(this).val();
            var pass = $('#edit-password1').val();
            var criteriaMatched = validatePasswordCriteria(password);
            var submitDisabled = $('.force-submit').attr('disabled');

            if (criteriaMatched && pass == password) {
                $('#password-confirmation-message').append('<span>Password match</span>');
                $('.force-submit').removeAttr("disabled");
            } else if (typeof submitDisabled !== 'undefined' || submitDisabled !== false) {
                $('.force-submit').attr('disabled', 'disabled');
                $('#password-confirmation-message').text('');
            }

        });

        var allEyes = document.querySelectorAll(".eye");
        for (var i = 0; i < allEyes.length; i++) {
            var currentEyeToggle = allEyes[i];
            currentEyeToggle.addEventListener("click", function (e) {
                var inputName = this.id;
                var currentInput = document.querySelector('input[name="' + inputName + '"]');
                var type = currentInput.getAttribute("type") === "password" ? "text" : "password";
                currentInput.setAttribute("type", type);
                this.classList.toggle("eye--open");
            });
        }
    });

    function validatePasswordCriteria(inputPassword) {
        var enteredPassword = inputPassword;
        var passwordIsValid = true;

        var passwordValidations = {
            length: false,
            capital: false,
            letter: false,
            number: false,
            special: false,
            haveNoEmptySpaces: true
        }

        // Validations
        if (enteredPassword.length >= 8) {
            passwordValidations.length = true;
        }

        jQuery.each(enteredPassword.split(""), function (index, char) {
            if (char.match(/[A-Z]{1}/g)) {
                passwordValidations.capital = true;
            }

            if (char.match(/[a-z]{1}/g)) {
                passwordValidations.letter = true;
            }

            if (!isNaN(char)) {
                passwordValidations.number = true;
            }

            if (char === " ") {
                passwordValidations.haveNoEmptySpaces = false;
            }
        });

        var specialCharsRegEx = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
        if (specialCharsRegEx.test(enteredPassword)) {
            passwordValidations.special = true;
        }

        // update validation info box
        jQuery.each(passwordValidations, function (key, isValid) {
            var $currentInfoText = jQuery(".js-validation-info-" + key);
            if (isValid) {
                $currentInfoText.removeClass("validation-icon-invalid");
                $currentInfoText.addClass("validation-icon-valid");
            } else {
                passwordIsValid = false;
                $currentInfoText.removeClass("validation-icon-valid");
                $currentInfoText.addClass("validation-icon-invalid");
            }
        });

        return passwordIsValid;
    }
})(jQuery);