document.addEventListener("DOMContentLoaded", function () {
    // ----- Init Page -----
    initForm();

    // ----- Init Functions -----
    function initForm() {
        // --- Elements ---
        const connectorForm = document.querySelector(".js-sfmc-form");
        const submitButtonEl = document.querySelector(".js-sfmc-submit-btn");
        const formInputEls = document.querySelectorAll(".js-sfmc-form-input");

        // --- Attach Event Listeners ---
        submitButtonEl.addEventListener("click", formSubmitHandler);

        // --- Event Handler Functions ---
        function formSubmitHandler(event) {
            event.preventDefault();
            const isFormValid = formValidation();

            if (isFormValid) {
                connectorForm.submit();
            } else {
                scrollToFirstInvalidInput();
                activateOnChangeValidation();
                return;
            }
        }

        // --- Form Validation Function ---
        function formValidation() {
            // -- Elements --
            const userFirstNameInputEl = document.querySelector(".js-sfmc-first-name-input");
            const userLastNameInputEl = document.querySelector(".js-sfmc-last-name-input");
            const userEmailInputEl = document.querySelector(".js-sfmc-email")
            const userConfirmEmailInputEl = document.querySelector(".js-sfmc-confirm-email");
            const countrySelectInput = document.querySelector(".js-sfmc-country-select-input");
            const buSelectInput = document.querySelector(".js-sfmc-bu-select-input");
            const concentCheckboxInputEls = document.querySelectorAll(".js-sfmc-concents-checkbox");

            const inputsValidationStatus = {
                firstNameIsValid: lengthValidation(userFirstNameInputEl, 1),
                lastNameIsValid: lengthValidation(userLastNameInputEl, 1),
                userEmailIsValid: emailValidation(userEmailInputEl),
                confirmEmailIsValid: matchEmailsValidation(userEmailInputEl, userConfirmEmailInputEl),
                countrySelectIsValid: selectValidation(countrySelectInput),
                buSelectIsValid: selectValidation(buSelectInput),
                checkboxConcentsIsValid: checkboxValidation(concentCheckboxInputEls),
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

        function matchEmailsValidation(emailInput, confirmEmailInput) {
            const email = emailInput.value.trim();
            const confirmEmail = confirmEmailInput.value.trim();
            const isEmailsMatched = email === confirmEmail && email !== "";
            showValidationError(confirmEmailInput, isEmailsMatched);
            return isEmailsMatched;
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

        // ----- Utils -----
        function showValidationError(input, isInputValid, isCheckbox = false) {
            const currentInputEl = input;
            const currentInputContainer = currentInputEl.closest(".js-required-input-pair");

            if (isInputValid) {
                if (isCheckbox) {
                    currentInputContainer.classList.remove("invalid");
                    return;
                }

                currentInputContainer.classList.remove("invalid");
            } else {
                if (isCheckbox) {
                    currentInputContainer.classList.add("invalid");
                    return;
                }

                currentInputContainer.classList.add("invalid");
            }
        }

        function scrollToFirstInvalidInput() {
            const firstInvalidInput = document.querySelector(".js-required-input-pair.invalid");
            firstInvalidInput.scrollIntoView({ behavior: "smooth" });
        }

        function activateOnChangeValidation() {
            formInputEls.forEach(function (inputEl) {
                inputEl.addEventListener("change", formValidation);
                inputEl.addEventListener("keyup", formValidation);
            });
        }
    }
});