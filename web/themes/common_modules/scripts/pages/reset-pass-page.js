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
            const passwordInputEl = document.querySelector(".js-tcm-password-input");
            const confirmPasswordInputEl = document.querySelector(".js-tcm-confirm-password-input");

            const inputsValidationStatus = {
                passwordIsValid: passwordValidation(passwordInputEl),
                passwordsMatchIsValid: matchPasswordsValidation(passwordInputEl, confirmPasswordInputEl),
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
});