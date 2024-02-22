const paginationClasses = {
    current: "ninlaro-pagination__button--current",
    locked: "ninlaro-pagination__button--locked",
    unlocked: "ninlaro-pagination__button--unlocked",
    ready: "ninlaro-pagination__button--ready",
}

document.addEventListener("DOMContentLoaded", function () {

    // --- State ---
    const results = {
        firstSectionScore: 0,
        secondSectionScore: 0,
        thirdSectionScore: 0
    }


    const pageButtonEls = {
        btnSection1: document.querySelector(".js-nin-page-btn-1"),
        btnSection2: document.querySelector(".js-nin-page-btn-2"),
        btnSection3: document.querySelector(".js-nin-page-btn-3"),
    }


    // --- Init Page ---
    initPagination();
    initCalculator();
    // --- Init Functions ---

    function initCalculator() {
        // --- Elements ---
        const firstSectionRadioInputEls = document.querySelectorAll(".js-calc-section-1 .js-nin-radio-input");
        const firstSectionQuestionEls = document.querySelectorAll(".js-calc-section-1 .js-ninlaro-question");
        const firstSectionScoreFieldEl = document.querySelector(".js-calc-section-1 .js-section-score");

        const secondSectionRadioInputEls = document.querySelectorAll(".js-calc-section-2 .js-nin-radio-input");
        const secondSectionQuestionEls = document.querySelectorAll(".js-calc-section-2 .js-ninlaro-question");
        const secondSectionScoreFieldEl = document.querySelector(".js-calc-section-2 .js-section-score");

        const thirdSectionRadioInputEls = document.querySelectorAll(".js-calc-section-3 .js-nin-radio-input");
        const thirdSectionQuestionEls = document.querySelectorAll(".js-calc-section-3 .js-ninlaro-question");
        const thirdSectionScoreFieldEl = document.querySelector(".js-calc-section-3 .js-section-score");

        const ageInputContainerEl = document.querySelector(".js-age-input-container");
        const patientAgeInputEl = document.querySelector(".js-patient-age-input");
        const calculateButtonEl = document.querySelector(".js-final-result-calculate-btn");

        const finalResultBlockEl = document.querySelector(".js-final-result-block");
        const charlsonFinalEl = document.querySelector(".js-charlson-final");
        const adlFinalEl = document.querySelector(".js-adl-final");
        const iadlFinalEl = document.querySelector(".js-iadl-final");
        const totalFinalEl = document.querySelector(".js-total-final");
        const finalResultSpanEl = document.querySelector(".js-final-result");

        // --- Attach Event Listeners ---
        firstSectionRadioInputEls.forEach(function (radioInputEl) {
            radioInputEl.addEventListener("change", firstSectionChangeHandler);
        });
        secondSectionRadioInputEls.forEach(function (radioInputEl) {
            radioInputEl.addEventListener("change", secondSectionChangeHandler);
        });
        thirdSectionRadioInputEls.forEach(function (radioInputEl) {
            radioInputEl.addEventListener("change", thirdSectionChangeHandler);
        });

        calculateButtonEl.addEventListener("click", calculateHandler);

        // --- Event Handlers ---
        function firstSectionChangeHandler(event) {
            const answeredQuestions = document.querySelectorAll(".js-calc-section-1 .js-nin-radio-input:checked");
            const isFormValid = formValidation(answeredQuestions, firstSectionQuestionEls);
            results.firstSectionScore = 0;
            if (isFormValid) {
                results.firstSectionScore = calculateRadioScore(answeredQuestions);
                displaySectionResult(firstSectionScoreFieldEl, results.firstSectionScore);
                updatePagination("firstSection");
            }
        }

        function secondSectionChangeHandler(event) {
            const answeredQuestions = document.querySelectorAll(".js-calc-section-2 .js-nin-radio-input:checked");
            const isFormValid = formValidation(answeredQuestions, secondSectionQuestionEls);
            results.secondSectionScore = 0;
            if (isFormValid) {
                results.secondSectionScore = calculateRadioScore(answeredQuestions);
                displaySectionResult(secondSectionScoreFieldEl, results.secondSectionScore);
                updatePagination("secondSection");
            }
        }

        function thirdSectionChangeHandler(event) {
            const answeredQuestions = document.querySelectorAll(".js-calc-section-3 .js-nin-radio-input:checked");
            const isFormValid = formValidation(answeredQuestions, thirdSectionQuestionEls);
            results.thirdSectionScore = 0;
            if (isFormValid) {
                results.thirdSectionScore = calculateRadioScore(answeredQuestions);
                displaySectionResult(thirdSectionScoreFieldEl, results.thirdSectionScore);
                updatePagination("thirdSection");
                ageInputContainerEl.classList.add("visible");
            }
        }

        function calculateHandler(event) {
            event.preventDefault();
            const partialScores = getPartialScores();
            const ageScore = getAgeScore();
            const totalScore = getTotalScore(partialScores, ageScore);
            const finalResult = getFinalResult(totalScore, ageScore);

            displayFinalResults(partialScores, totalScore, finalResult);
        }

        // --- Utils ---
        function formValidation(answeredQuestions, allQuestions) {
            if (answeredQuestions.length === allQuestions.length) {
                return true;
            } else {
                return false;
            }
        }

        function calculateRadioScore(answeredQuestions) {
            let result = 0;
            answeredQuestions.forEach(function (answer) {
                result += Number(answer.value);
            });

            return result;
        }

        function displaySectionResult(resultEl, result) {
            const scoreSpan = resultEl.querySelector("span");
            scoreSpan.textContent = result;
            resultEl.classList.add("visible");
        }

        function getPartialScores() {
            const partialScores = {
                charlsonScore: 0,
                adlScore: 0,
                iadlScore: 0
            }

            if (results.firstSectionScore >= 2) {
                partialScores.charlsonScore = 1;
            }

            if (results.secondSectionScore <= 4) {
                partialScores.adlScore = 1;
            }

            if (results.thirdSectionScore <= 5) {
                partialScores.iadlScore = 1;
            }

            return partialScores;
        }

        function getAgeScore() {
            const patientAge = patientAgeInputEl.value;
            let ageScore = 0;

            if (patientAge >= 76 && patientAge <= 80) {
                ageScore = 1;
            } else if (patientAge >= 81) {
                ageScore = 2;
            }

            return ageScore;
        }

        function getTotalScore(partialScores, ageScore) {
            let totalScore = 0;
            for (const key in partialScores) {
                totalScore += partialScores[key];
            }

            totalScore += ageScore;

            return totalScore;
        }

        function getFinalResult(totalScore, ageScore) {
            const scoreSum = totalScore + ageScore;
            let finalResult = "";

            if (scoreSum === 0) {
                finalResult = "Fit"
            } else if (scoreSum === 1) {
                finalResult = "Fit-intermediate";
            } else if (scoreSum >= 2) {
                finalResult = "Fragile";
            }

            return finalResult;
        }

        function displayFinalResults(partialScores, totalScore, finalResult) {
            charlsonFinalEl.textContent = partialScores.charlsonScore;
            adlFinalEl.textContent = partialScores.adlScore;
            iadlFinalEl.textContent = partialScores.iadlScore;
            totalFinalEl.textContent = totalScore;
            finalResultSpanEl.textContent = finalResult;
            pageButtonEls.btnSection3.classList.add(paginationClasses.ready);
            finalResultBlockEl.classList.add("visible");
        }
    }

    function initPagination() {
        // --- Elements ---
        const ninlaroCalcEl = document.querySelector(".js-ninlaro-calc");
        const pageButtonEls = document.querySelectorAll(".js-nin-page-btn");
        const sectionEls = {
            section1: document.querySelector(".js-calc-section-1"),
            section2: document.querySelector(".js-calc-section-2"),
            section3: document.querySelector(".js-calc-section-3"),
        }

        // --- Attach Event Listeners ---
        pageButtonEls.forEach(function (pageBtnEl) {
            pageBtnEl.addEventListener("click", openPageHandler);
        });

        // --- Event Handlers ---
        function openPageHandler(event) {
            const currentButton = event.currentTarget;

            if (currentButton.classList.contains(paginationClasses.locked)) {
                return;
            }

            const currentSection = currentButton.dataset.section;
            const currentVisibleSection = document.querySelector(".js-calc-section.visible");
            currentVisibleSection.classList.remove("visible");
            sectionEls[currentSection].classList.add("visible");

            currentButton.classList.remove("ninlaro-pagination__button--unlocked");
            currentButton.classList.add("ninlaro-pagination__button--current");
            ninlaroCalcEl.scrollIntoView({ behavior: "smooth" });
        }
    }

    // --- Utils ---
    function updatePagination(currentSection) {
        if (currentSection === "firstSection") {
            pageButtonEls.btnSection1.classList.remove(paginationClasses.current);
            pageButtonEls.btnSection1.classList.add(paginationClasses.ready);
            pageButtonEls.btnSection2.classList.remove(paginationClasses.locked);
            pageButtonEls.btnSection2.classList.add(paginationClasses.unlocked);
        } else if (currentSection === "secondSection") {
            pageButtonEls.btnSection2.classList.remove(paginationClasses.current);
            pageButtonEls.btnSection2.classList.add(paginationClasses.ready);
            pageButtonEls.btnSection3.classList.remove(paginationClasses.locked);
            pageButtonEls.btnSection3.classList.add(paginationClasses.unlocked);
        } else if (currentSection === "thirdSection") {
            pageButtonEls.btnSection2.classList.remove(paginationClasses.current);
            pageButtonEls.btnSection2.classList.add(paginationClasses.ready);
        }
    }
});