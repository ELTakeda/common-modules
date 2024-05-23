document.addEventListener("DOMContentLoaded", function () {
    // ----- Init Page -----
    initForm();

    // ----- Init Functions -----
    function initForm() {
        // --- Elements ---
        const registerFormEl = document.querySelector(".js-tcm-page-form");
        const submitButtonEl = document.querySelector(".js-tcm-page-form-submit-button");
        const formInputEls = document.querySelectorAll(".js-tcm-page-form-input");

        initShowHidePassword();

        // --- Attach Event Listeners ---
        formInputEls.forEach(function (element) {
            element.addEventListener("blur", labelPositioningHandler);
        });

        submitButtonEl.addEventListener("click", formSubmitHandler);

        // --- Event Handler Functions ---

        function labelPositioningHandler(event) {
            const currentInput = event.target;
            const currentInputValue = currentInput.value.trim();

            if (currentInputValue.length > 0) {
                currentInput.classList.add("not-empty-value");
            } else {
                currentInput.classList.remove("not-empty-value");
            }
        }

        function formSubmitHandler(event) {
            event.preventDefault();
            const isFormValid = formValidation();

            if (isFormValid) {
                registerFormEl.submit();
            } else {
                scrollToFirstInvalidInput();
                activatePnChangeValidation();
                return;
            }
        }

        // --- Form Validation Function ---
        function formValidation() {
            // -- Elements --
            const userFirstNameInputEl = document.querySelector(".js-tcm-first-name-input");
            const userLastNameInputEl = document.querySelector(".js-tcm-last-name-input");
            const countrySelectInputEl = document.querySelector(".js-tcm-country-select-input");
            const userEmailInputEl = document.querySelector(".js-tcm-email-input");
            const passwordInputEl = document.querySelector(".js-tcm-password-input");
            const confirmPasswordInputEl = document.querySelector(".js-tcm-confirm-password-input");
            const requiredCheckboxEls = document.querySelectorAll(".js-checkbox-required");

            const inputsValidationStatus = {
                firstNameIsValid: lengthValidation(userFirstNameInputEl, 2),
                lastNameIsValid: lengthValidation(userLastNameInputEl, 2),
                countrySelectIsValid: selectValidation(countrySelectInputEl),
                userEmailIsValid: emailValidation(userEmailInputEl),
                passwordIsValid: passwordValidation(passwordInputEl),
                passwordsMatchIsValid: matchPasswordsValidation(passwordInputEl, confirmPasswordInputEl),
                requiredCheckboxIsChecked: checkboxValidation(requiredCheckboxEls),
                reCaptchaIsValid: reCaptchaValidation(),
                reCaptchaIsValid: true,
            }

            let formIsValid = true;
            for (const value of Object.values(inputsValidationStatus)) {
                if (!value) {
                    formIsValid = false;
                    break;
                }
            }

            return formIsValid;
        }

        // ----- Validation Functions ---
        function lengthValidation(input, minLength) {
            const currentValue = input.value.trim();
            const isValid = currentValue.length >= minLength;
            showValidationError(input, isValid);
            return isValid;
        }

        function emailValidation(emailInput) {
            const regex = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]{2,}$/;
            const emailValue = emailInput.value.trim();
            const isEmailValid = regex.test(emailValue);
            showValidationError(emailInput, isEmailValid);
            return isEmailValid;
        }

        function passwordValidation(passwordInputEl) {
            const enteredPassword = passwordInputEl.value.trim();
            let passwordIsValid = true;

            const passwordValidations = {
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

            const passwordArray = enteredPassword.split("");

            passwordArray.forEach(function (char) {
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

            const specialCharsRegEx = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/;
            if (specialCharsRegEx.test(enteredPassword)) {
                passwordValidations.special = true;
            }

            for (const [key, isValid] of Object.entries(passwordValidations)) {
                if (!isValid) {
                    passwordIsValid = false;
                }
            };

            showValidationError(passwordInputEl, passwordIsValid);
            return passwordIsValid;
        }

        function matchPasswordsValidation(passwordInput, confirmPasswordInput) {
            const password = passwordInput.value.trim();
            const confirmPassword = confirmPasswordInput.value.trim();
            const isPasswordsMatched = password === confirmPassword && password !== "";
            showValidationError(confirmPasswordInput, isPasswordsMatched);
            return isPasswordsMatched;
        }

        function selectValidation(selectEl) {
            const selectedValue = selectEl.value;
            const isValid = selectedValue === "select" ? false : true;
            showValidationError(selectEl, isValid);
            return isValid;
        }

        function checkboxValidation(checkboxElements) {
            let isAllChecked = true;
            checkboxElements.forEach(function (checkbox) {
                const isCheckboxChecked = checkbox.checked;
                showValidationError(checkbox, isCheckboxChecked, true);
                if (!checkbox.checked) {
                    isAllChecked = false;
                }
            });

            return isAllChecked;
        }

        function reCaptchaValidation() {
            const reCaptchaValidationMessageEl = document.querySelector(".js-recaptcha-validation-message");
            try {
                var response = grecaptcha.getResponse();
                if (response.length === 0) {
                    reCaptchaValidationMessageEl.classList.remove("tcm-hidden");
                    return false;
                }
                reCaptchaValidationMessageEl.classList.add("tcm-hidden");
                return true;

            } catch (e) {
                reCaptchaValidationMessageEl.classList.remove("tcm-hidden");
                return false;
            }
        }

        // ----- Utils -----
        function showValidationError(input, isInputValid, isCheckbox = false) {
            const currentInputEl = input;
            const currentInputLabelEl = currentInputEl.closest(".tcm-page-form__input-label");
            const currentErrorMessageEl = currentInputLabelEl.nextElementSibling;

            if (isInputValid) {
                if (isCheckbox) {
                    currentInputLabelEl.classList.remove("st-invalid-checkbox");
                    currentInputLabelEl.classList.remove("invalid");
                    return;
                }

                currentInputLabelEl.classList.remove("invalid");
                currentErrorMessageEl.classList.add("tcm-hidden");
            } else {
                if (isCheckbox) {
                    currentInputLabelEl.classList.add("st-invalid-checkbox");
                    currentInputLabelEl.classList.add("invalid");
                    return;
                }

                currentInputLabelEl.classList.add("invalid");
                currentErrorMessageEl.classList.remove("tcm-hidden");
            }
        }

        function scrollToFirstInvalidInput() {
            const firstInvalidInput = document.querySelector(".tcm-page-form__input-label.invalid");
            firstInvalidInput.scrollIntoView({ behavior: "smooth" });
        }

        function initShowHidePassword() {
            // --- Elements ---
            const togglePassButtonEls = document.querySelectorAll(".js-toggle-password-btn");

            // --- Attach Event Listeners ---
            togglePassButtonEls.forEach(function (buttonEl) {
                buttonEl.addEventListener("click", togglePasswordHandler);
            });

            // ---  Event Handlers ---
            function togglePasswordHandler(event) {
                const currentButtonEl = event.currentTarget;
                const currentPassInputEl = currentButtonEl.parentNode.querySelector("input");

                if (currentButtonEl.classList.contains("open")) {
                    currentButtonEl.classList.remove("open");
                    currentPassInputEl.type = "password";
                } else {
                    currentButtonEl.classList.add("open");
                    currentPassInputEl.type = "text";
                }
            }
        }

        function activatePnChangeValidation() {
            formInputEls.forEach(function (inputEl) {
                inputEl.addEventListener("change", formValidation);
                inputEl.addEventListener("keyup", formValidation);
            });
        }
    }
    function fillFormFromCookieIfTokenPresent() {
        // Check if the URL contains the InvitationToken parameter
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('InvitationToken')) {
            function getCookie(name) {
                const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
                if (match) return match[2];
            }

            const cookieData = getCookie('STYXKEY_takedaiduserinvitationinfo');

            if (cookieData) {
                const jsonData = JSON.parse(decodeURIComponent(cookieData));
                document.querySelector('.js-tcm-first-name-input').value = 'Israel' || '';
                document.querySelector('.js-tcm-last-name-input').value = jsonData.QueryStrings.lastName || '';
                document.querySelector('.js-tcm-email-input').value = jsonData.QueryStrings.email || '';
            }
        }
    }
});