function limiter() {
    var x1 = document.getElementById("input1");
    var x2 = document.getElementById("input2");
    var x3 = document.getElementById("input3");

    if (x1.value.length > 3) {
        x1.value = x1.value.slice(0, 3);
    }

    if (x2.value.length > 3) {
        x2.value = x2.value.slice(0, 3);
    }

    if (x3.value.length > 4) {
        x3.value = x3.value.slice(0, 4);
    }
}

function scrollToElement($element) {
    jQuery([document.documentElement, document.body]).animate({
        scrollTop: $element.offset().top - 10
    }, 500);
}

function populate() {
    var weight = jQuery('#input1').val();
    if (weight > 100) {
        weight = 100;
    } else {
        weight = weight;
    }

    var height = jQuery('#input2').val();
    var sc = 0.007184 * height ** 0.725 * weight ** 0.425;

    // Variables set according to radio button (dosage, cycles and infusions)
    var get_dosage_cycles_infusions = set_dosage_cycles_infusions();

    var dosage = get_dosage_cycles_infusions.dosage;
    var cycles = get_dosage_cycles_infusions.cycles;
    var infusions = get_dosage_cycles_infusions.infusions;
    var dosage_adjustment = get_dosage_cycles_infusions.dosage_adjustment;
  
 //mg value
    var mg = set_mg(weight, dosage, cycles, infusions)
    var mg_adjustment = set_mg(weight, dosage_adjustment, cycles, infusions);

    //value vials per vials_treatment
    var vials_treatment = set_vials_treatment(mg);
    var vials_treatment_adjustment = set_vials_treatment(mg_adjustment);
   
 //value vials per cycle
    var vials_cycle = set_vials_cycle(vials_treatment, cycles);
    var vials_cycle_adjustment = set_vials_cycle(vials_treatment_adjustment, cycles);

    //value vials per infusion
    var vials_infusion = set_vials_infusion(vials_cycle, infusions);
    var vials_infusion_adjustment = set_vials_infusion(vials_cycle_adjustment, infusions);

    var doxorubicin = set_avd(sc).doxorubicin;
    var vinblastine = set_avd(sc).vinblastine;
    var dacarbazine = set_avd(sc).dacarbazine;

    var cyclophosphamide = set_chp(sc).cyclophosphamide;
    var prednisone = set_chp(sc).prednisone;
    var doxorubicin2 = set_chp(sc).doxorubicin;

    jQuery('#input3').val(sc.toFixed(2));

    jQuery('#data1').val(dosage);
    jQuery('#data1-2').val(dosage_adjustment);
    jQuery('#data2').val(cycles);
    jQuery('#data2-2').val(cycles);
    jQuery('#data3').val(infusions);
    jQuery('#data3-2').val(infusions);
    jQuery('#data4').val(mg);
    jQuery('#data4-2').val(mg);
    jQuery('#data5').val(vials_cycle.toFixed(1));
    jQuery('#data5-2').val(vials_cycle_adjustment.toFixed(1));
    jQuery('#data6').val(vials_infusion.toFixed(1));
    jQuery('#data6-2').val(vials_infusion_adjustment.toFixed(1));

    jQuery('#result1').val(doxorubicin.toFixed(2));
    jQuery('#result2').val(vinblastine.toFixed(2));
    jQuery('#result3').val(dacarbazine.toFixed(2));
    jQuery('#result4').val(cyclophosphamide.toFixed(2));
    jQuery('#result5').val(doxorubicin2.toFixed(2));
    jQuery('#result6').val(prednisone);

    jQuery('#result1-2').val(doxorubicin.toFixed(2));
    jQuery('#result2-2').val(vinblastine.toFixed(2));
    jQuery('#result3-2').val(dacarbazine.toFixed(2));
    jQuery('#result4-2').val(cyclophosphamide.toFixed(2));
    jQuery('#result5-2').val(doxorubicin2.toFixed(2));
    jQuery('#result6-2').val(prednisone);

    if (jQuery('#data1').val() != 0) {
        jQuery('.data-echelon1').show();
    }
}

function populateRemaining() {
    var weight = jQuery('#input1').val();
    if (weight > 100) {
        weight = 100;
    } else {
        weight = weight;
    }

    var height = jQuery('#input2').val();
    var sc = 0.007184 * height ** 0.725 * weight ** 0.425

    // Variables set according to the radio button (dosage, cycles and infusions).
    var get_dosage_cycles_infusions = set_dosage_cycles_infusions();

    var dosage = get_dosage_cycles_infusions.dosage;
    var cycles = get_dosage_cycles_infusions.cycles;
    var infusions = get_dosage_cycles_infusions.infusions;
    var dosage_adjustment = get_dosage_cycles_infusions.dosage_adjustment;

    //mg value
    var mg = set_mg(weight, dosage, cycles, infusions)
    var mg_adjustment = set_mg(weight, dosage_adjustment, cycles, infusions)

    // value vials per vials_treatment
    var vials_treatment = set_vials_treatment(mg);
    var vials_treatment_adjustment = set_vials_treatment(mg_adjustment);

    //vials value per cycle
    var vials_cycle = set_vials_cycle(vials_treatment, cycles);
    var vials_cycle_adjustment = set_vials_cycle(vials_treatment_adjustment, cycles);

    //value vials per infusion
    var vials_infusion = set_vials_infusion(vials_cycle, infusions);
    var vials_infusion_adjustment = set_vials_infusion(vials_cycle_adjustment, infusions);

    var cyclophosphamide = set_chp(sc).cyclophosphamide;
    var prednisone = set_chp(sc).prednisone;
    var doxorubicin2 = set_chp(sc).doxorubicin;

    jQuery('#input3').val(sc.toFixed(2));

    jQuery('.data-demais #data1').val(dosage)
    jQuery('.data-demais-adjustment #data1, .data-motora #data1').val(dosage_adjustment);
    jQuery('.data-demais #data2').val(cycles);
    jQuery('.data-demais-adjustment #data2, .data-motora #data2').val(cycles);
    jQuery('.data-demais #data3').val(infusions);
    jQuery('.data-demais-adjustment #data3, .data-motora #data3').val(infusions);
    jQuery('.data-demais #data4').val(mg);
    jQuery('.data-demais-adjustment #data4, .data-motora #data4').val(mg);
    jQuery('.data-demais #data5').val(vials_cycle.toFixed(1));
    jQuery('.data-demais-adjustment #data5, .data-motora #data5').val(vials_cycle_adjustment.toFixed(1));
    jQuery('.data-demais #data6').val(vials_infusion.toFixed(1));
    jQuery('.data-demais-adjustment #data6, .data-motora #data6').val(vials_infusion_adjustment.toFixed(1));

    jQuery('#result1-1').val(cyclophosphamide.toFixed(2));
    jQuery('#result2-1').val(doxorubicin2.toFixed(2));
    jQuery('#result3-1').val(prednisone.toFixed(2));

    if (jQuery('.data-demais #data1').val() != 0) {
        jQuery('.data-demais').show();
    }
}

//dosage, cycles and infusions
function set_dosage_cycles_infusions() {
    var dosage = undefined;
    var dosage_adjustment = undefined;
    var cycles = undefined;
    var infusions = undefined;

    if (jQuery('#echelon1').is(':checked')) {
        dosage = 1.2;
        dosage_adjustment = 0.9;
        cycles = 6;
        infusions = 2;

        jQuery(".table-form input").prop('disabled', true);
        jQuery(".container-radio").css('opacity', '0.4');
        jQuery('.table-form label:nth-child(1)').css('opacity', '1');
        jQuery(".field1").show();
        jQuery(".field2").show();
        jQuery(".field3").show();
        jQuery("#dosage3").hide();
        jQuery("#dosage2").hide();
        jQuery("#dosage").show();
        jQuery('.dosage').html(dynamicContent.dosageField.calculatorOneContent);
        jQuery("#obs").show();

        return { dosage, dosage_adjustment, cycles, infusions };

    } else if (jQuery('#pivotal-lh').is(':checked')) {
        dosage = 1.8
        dosage_adjustment = 1.2;
        cycles = 16;
        infusions = 1;

        jQuery(".table-form input").prop('disabled', true);
        jQuery(".container-radio").css('opacity', '0.4');
        jQuery('.table-form label:nth-child(2)').css('opacity', '1');
        jQuery(".field1").show();
        jQuery(".field1").css('float', 'none');

        jQuery("#dosage3").hide();
        jQuery("#dosage2").hide();
        jQuery("#dosage").show();
        jQuery('.dosage').html(dynamicContent.dosageField.calculatorTwoContent);
        jQuery("#obs").show();
        // this fields will stay harcoded
        jQuery('.column1 p').html('cyclophosphamide [C]<sup>8</sup>');
        jQuery('.column2 p').html('doxorubicin [H]<sup>5</sup>');
        jQuery('.column3 p').html('prednisone [P] [CHP]<sup>9</sup>');

        return { dosage, dosage_adjustment, cycles, infusions };

    } else if (jQuery('#aethera').is(':checked')) {
        dosage = 1.8
        dosage_adjustment = 1.2
        cycles = 16;
        infusions = 1;

        jQuery(".table-form input").prop('disabled', true);
        jQuery(".container-radio").css('opacity', '0.4');
        jQuery('.table-form label:nth-child(3)').css('opacity', '1');
        jQuery(".field1").show();
        jQuery(".field1").css('float', 'none');
        jQuery('.dosage').html(dynamicContent.dosageField.calculatorTreeContent);

        return { dosage, dosage_adjustment, cycles, infusions };

    } else if (jQuery('#echelon2').is(':checked')) {
        dosage = 1.8
        dosage_adjustment = 1.2
        cycles = 8;
        infusions = 1;

        jQuery(".table-form input").prop('disabled', true);
        jQuery(".container-radio").css('opacity', '0.4');
        jQuery('.table-form label:nth-child(4)').css('opacity', '1');
        jQuery(".field1").show();
        jQuery(".field2").show();
        jQuery(".field3").show();
        jQuery('.dosage').html(dynamicContent.dosageField.calculatorFourContent);

        return { dosage, dosage_adjustment, cycles, infusions };

    } else if (jQuery('#pivotal-lagcs').is(':checked')) {
        dosage = 1.8
        dosage_adjustment = 1.2
        cycles = 16;
        infusions = 1;

        jQuery(".table-form input").prop('disabled', true);
        jQuery(".container-radio").css('opacity', '0.4');
        jQuery('.table-form label:nth-child(5)').css('opacity', '1');
        jQuery(".field1").show();
        jQuery(".field1").css('float', 'none');
        jQuery('.dosage').html(dynamicContent.dosageField.calculatorFiveContent);

        return { dosage, dosage_adjustment, cycles, infusions };

    } else if (jQuery('#alcanza').is(':checked')) {
        dosage = 1.8
        dosage_adjustment = 1.2
        cycles = 16;
        infusions = 1;

        jQuery(".table-form input").prop('disabled', true);
        jQuery(".container-radio").css('opacity', '0.4');
        jQuery('.table-form label:nth-child(6)').css('opacity', '1');
        jQuery(".field1").show();
        jQuery(".field1").css('float', 'none');
        jQuery('.dosage').html(dynamicContent.dosageField.calculatorSixContent);

        return { dosage, dosage_adjustment, cycles, infusions };
    }

}

// mg value
function set_mg(weight, dosage, cycles, infusions) {
    var mg;
    if (weight <= 100) {
        mg = (weight * dosage) * (cycles * infusions);
    } else {
        mg = (100 * dosage) * (cycles * infusions);
    }

    return mg;
}

//vials value per vials_treatment
function set_vials_treatment(mg) {
    return mg / 50;
}

//vials value per cycle
function set_vials_cycle(vials_treatment, cycles) {
    return vials_treatment / cycles;
}

//vials value per cycle
function set_vials_infusion(vials_cycles, infusions) {
    return vials_cycles / infusions;
}

// AVD e CHP

function set_avd(sc) {
    var doxorubicin = sc * 25;
    var vinblastine = sc * 6;
    var dacarbazine = sc * 375;

    return { doxorubicin, vinblastine, dacarbazine }
}

function set_chp(sc) {
    var cyclophosphamide = sc * 750;
    var doxorubicin = sc * 50;
    var prednisone = 100;

    return { cyclophosphamide, prednisone, doxorubicin }
}

//redo
function redo() {
    jQuery('input').val('');
    jQuery('.data input').val(0);
    jQuery('.result input').val(0);
    jQuery('.data-echelon1-adjustment').addClass('data2');
    jQuery('.data-demais-adjustment').addClass('data2');
    jQuery('.data-echelon1').hide();
    jQuery('.data-demais').hide();
    jQuery('.neuros').hide();
    jQuery(".field1").css('float', 'left');

    jQuery("#dosage3").hide();
    jQuery("#dosage2").hide();
    jQuery("#dosage").show();

    jQuery(".table-form input").prop('disabled', false);
    jQuery(".container-radio").css('opacity', '1');
}

function getDynamicContent() {
    var dynamicContent = {
        dosageField: {
            calculatorOneContent: jQuery('#calculatorOneContent').text(),
            calculatorTwoContent: jQuery('#calculatorTwoContent').text(),
            calculatorTreeContent: jQuery('#calculatorTreeContent').text(),
            calculatorFourContent: jQuery('#calculatorFourContent').text(),
            calculatorFiveContent: jQuery('#calculatorFiveContent').text(),
            calculatorSixContent: jQuery('#calculatorSixContent').text(),
        },
        alerts: {
            suspendDoseGrade1: jQuery('#suspendDoseGrade1').text(),
            suspendDoseGrade2: jQuery('#suspendDoseGrade2').text(),
            nauropatiaSensoryContinueDose: jQuery('#nauropatiaSensoryContinueDose').text(),
            neuropatiaSensoryReduceDose: jQuery('#neuropatiaSensoryReduceDose').text(),
            continueDoseAndScheme: jQuery('#continueDoseAndScheme').text(),
            reduceDoseEveryTwoWeeks: jQuery('#reduceDoseEveryTwoWeeks').text(),
            adcetrisSuspendDoseGrade2: jQuery('#adcetrisSuspendDoseGrade2').text(),
            discontinueTreatment: jQuery('#discontinueTreatment').text(),
            recomendedProphylaxis: jQuery('#recomendedProphylaxis').text(),
        },
        infoBox: {
            cycleInfo: jQuery('#cycleInfo').text(),
            referToLeaflets: jQuery('#referToLeaflets').text()
        }
    }

    return dynamicContent;
}
var dynamicContent;
//input fields
jQuery(document).ready(function () {

    dynamicContent = getDynamicContent();

    if (jQuery('.table-form input').is(':checked')) {
        jQuery("input").prop('disabled', true);
    }

    jQuery('.dose-adjustment').click(function () {
        jQuery('.neuros').show();
    });

    jQuery('.table-form label').click(function () {
        jQuery('.info-box3').hide();
        jQuery('.info-box, .go-back, .insert').show();
    });

    jQuery('.neuro-content .grade').click(function () {
        jQuery('.box-alert1').show();
        jQuery('.box-alert2').hide();

        if ($(window).width() < 480) {
            scrollToElement(jQuery('.box-alert1'));
        }
    });

    jQuery('.neuro-content2 .grade').click(function () {
        jQuery('.box-alert2').show();
        jQuery('.box-alert1').hide();
        if ($(window).width() < 480) {
            scrollToElement(jQuery('.box-alert2'));
        }
    });

    jQuery('.neuropatia').click(function () {
        jQuery('.neuro-content').addClass("flex-column");
        jQuery('.neuro-content2').removeClass("flex-column");
        jQuery(this).addClass('active-bg');
        jQuery(this).removeClass('color-third');
        jQuery('.neutropenia').removeClass('active-bg');
        jQuery('.neutropenia').addClass('color-third');
        jQuery('.box-alert2').hide();
        jQuery('.box-alert1').hide();
        jQuery('.data-echelon1-adjustment').addClass('data2');
        jQuery('.data-demais-adjustment').addClass('data2');
        jQuery('.data-motora').addClass('data2');
        jQuery('.grade').addClass('color-third');
        jQuery('.grade').removeClass('active-bg');
        jQuery('.box-alert-motora').hide();
        jQuery('.info-box-bottom1').hide();
        jQuery('.info-box-bottom2').hide();

        scrollToElement(jQuery('.neuropatia'));
    });

    jQuery('.neutropenia').click(function () {
        jQuery('.neuro-content2').addClass("flex-column");
        jQuery('.neuro-content').removeClass("flex-column");
        jQuery(this).addClass('active-bg');
        jQuery(this).removeClass('color-third');
        jQuery('.neuropatia').removeClass('active-bg');
        jQuery('.neuropatia').addClass('color-third');
        jQuery('.box-alert2').hide();
        jQuery('.box-alert1').hide();
        jQuery('.data-echelon1-adjustment').addClass('data2');
        jQuery('.data-demais-adjustment').addClass('data2');
        jQuery('.data-motora').addClass('data2');
        jQuery('.grade').addClass('color-third');
        jQuery('.grade').removeClass('active-bg');
        jQuery('.info-box-bottom1').hide();
        jQuery('box-alert-motora').hide();
        jQuery('.info-box-bottom2').hide();

        scrollToElement(jQuery('.neuropatia'));
    });

    jQuery('.grade').click(function () {
        jQuery('.grade').addClass('color-third');
        jQuery('.grade').removeClass('active-bg');
        jQuery('.grade').removeClass('active');
        jQuery(this).removeClass('color-third');
        jQuery(this).addClass('active');
        jQuery(this).addClass('active-bg');

        // Neuropatia Options
        if (jQuery(".neuro-head-content").hasClass("neuropatia active-bg") && jQuery(".neuro-content div").hasClass("grade1 active")) {
            if (jQuery('#echelon1').is(':checked')) {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.continueDoseAndScheme);
                jQuery('.info-box-bottom1').text(dynamicContent.infoBox.cycleInfo);
                jQuery('.info-box-bottom1').show();
                jQuery('.info-box-bottom2').text(dynamicContent.infoBox.referToLeaflets);
                jQuery('.info-box-bottom2').show();
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.continueDoseAndScheme);
                jQuery('.data-echelon1-adjustment').addClass('data2');
            } else if (jQuery('.overdose-opt').is(':checked')) {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.continueDoseAndScheme);
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-demais-adjustment').addClass('data2');
            } else if (jQuery('#echelon2').is(':checked')) {
                jQuery('.data-demais-adjustment').addClass('data2');
                jQuery('.data-motora').addClass('data2');
                jQuery('.box-alert-motora').hide();
                jQuery('.info-box-bottom2').show();
                jQuery('.p-box-alert').text(dynamicContent.alerts.continueDoseAndScheme);
            } else {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.continueDoseAndScheme);
                jQuery('.info-box-bottom1').hide();
                jQuery('.info-box-bottom2').hide();
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-demais-adjustment').removeClass('data2');
                jQuery('.data-motora').addClass('data2');
            }
        } else if (jQuery(".neuro-head-content").hasClass("neuropatia active-bg") && jQuery(".neuro-content div").hasClass("grade2 active")) {
            if (jQuery('#echelon1').is(':checked')) {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.reduceDoseEveryTwoWeeks);
                jQuery('.data-echelon1-adjustment').removeClass('data2');
                jQuery('.info-box-bottom1').text(dynamicContent.infoBox.cycleInfo);
                jQuery('.info-box-bottom1').show();
                jQuery('.info-box-bottom2').text(dynamicContent.infoBox.referToLeaflets);
                jQuery('.info-box-bottom2').show();
            } else if (jQuery('.overdose-opt').is(':checked')) {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.alertSuspendDose);
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-demais-adjustment').removeClass('data2');
            } else if (jQuery('#echelon2').is(':checked')) {
                jQuery('.data-demais-adjustment').removeClass('data2');
                jQuery('.data-motora').removeClass('data2');
                jQuery('.p-box-alert').text(dynamicContent.alerts.nauropatiaSensoryContinueDose);
                jQuery('.box-alert-motora').show();
                jQuery('.data-demais-adjustment #data1').val(1.8);
                jQuery('.info-box-bottom2').show();
            } else {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.suspendDoseGrade1);
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.info-box-bottom1').hide();
                jQuery('.data-motora').addClass('data2');
                jQuery('.info-box-bottom2').hide();
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-motora').addClass('data2');
                jQuery('.data-demais-adjustment').removeClass('data2');
                jQuery('.box-alert-motora').hide();
            }
        } else if (jQuery(".neuro-head-content").hasClass("active-bg") && jQuery(".neuro-content div").hasClass("grade3 active")) {
            if (jQuery('#echelon1').is(':checked')) {
                jQuery('.data-echelon1-adjustment').removeClass('data2');
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.adcetrisSuspendDoseGrade2);
                jQuery('.info-box-bottom1').hide();
                jQuery('.info-box-bottom2').text(dynamicContent.infoBox.referToLeaflets);
                jQuery('.info-box-bottom2').show();
            } else if (jQuery('.overdose-opt').is(':checked')) {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.neuropatiaSensoryReduceDose);
                jQuery('.info-box-bottom1').hide();
                jQuery('.info-box-bottom2').hide();
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-demais-adjustment').removeClass('data2');
            } else if (jQuery('#echelon2').is(':checked')) {
                jQuery('.data-demais-adjustment').removeClass('data2');
                jQuery('.data-motora').addClass('data2');
                jQuery('.p-box-alert').text(dynamicContent.alerts.neuropatiaSensoryReduceDose);
                jQuery('.data-demais-adjustment #data1').val(1.2);
                jQuery('.box-alert-motora').hide();
                jQuery('.info-box-bottom2').show();
            } else {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.suspendDoseGrade1);
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-demais-adjustment').removeClass('data2');
                jQuery('.data-motora').addClass('data2');
            }
        } else if (jQuery(".neuro-head-content").hasClass("neuropatia active-bg") && jQuery(".neuro-content div").hasClass("grade4 active")) {
            if (jQuery('#echelon1').is(':checked')) {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.discontinueTreatment);
                jQuery('.info-box-bottom1').hide();
                jQuery('.info-box-bottom2').hide();
            } else if (jQuery('#echelon2').is(':checked')) {
                jQuery('.info-box-bottom2').show();
            } else {
                jQuery('.p-box-alert-dose').text(dynamicContent.alerts.discontinueTreatment);
                jQuery('.data-echelon1-adjustment').addClass('data2');
            }
            jQuery('.p-box-alert-dose').text(dynamicContent.alerts.discontinueTreatment);
            jQuery('.data-echelon1-adjustment').addClass('data2');
            jQuery('.info-box-bottom1').hide();
            jQuery('.info-box-bottom2').hide();
            jQuery('.data-demais-adjustment').addClass('data2');
            jQuery('.box-alert-motora').hide();
            jQuery('.data-motora').addClass('data2');
        }

        // Neutropenia Options 

        if (jQuery(".neuro-head-content").hasClass("neutropenia active-bg") && jQuery(".neuro-content2 div").hasClass("grade1 active")) {
            if (jQuery('#echelon1').is(':checked')) {
                jQuery('.p-box-alert').text(dynamicContent.alerts.recomendedProphylaxis);
                jQuery('.info-box-bottom1').text(dynamicContent.infoBox.cycleInfo);
                jQuery('.info-box-bottom1').show();
                jQuery('.info-box-bottom2').text(dynamicContent.infoBox.referToLeaflets);
                jQuery('.info-box-bottom2').show();
            } else if (jQuery('.overdose-opt').is(':checked')) {
                jQuery('.data-demais-adjustment').addClass('data2');
                jQuery('.p-box-alert').text(dynamicContent.alerts.continueDoseAndScheme);
                jQuery('.texto-adjustment').hide();
            } else if (jQuery('#echelon2').is(':checked')) {
                jQuery('.data-demais-adjustment').addClass('data2');
                jQuery('.data-motora').addClass('data2');
                jQuery('.p-box-alert').text(dynamicContent.alerts.recomendedProphylaxis);
                jQuery('.info-box-bottom2').show();
            } else {
                jQuery('.p-box-alert').text(dynamicContent.alerts.continueDoseAndScheme);
                jQuery('.data-echelon1-adjustment').removeClass('data2');
                jQuery('.info-box-bottom1').hide();
                jQuery('.info-box-bottom2').hide();
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-demais-adjustment').removeClass('data2');
                jQuery('.data-motora').addClass('data2');
                jQuery('.texto-adjustment').hide();
            }
        } else if (jQuery(".neuro-head-content").hasClass("neutropenia active-bg") && jQuery(".neuro-content2 div").hasClass("grade2 active")) {
            if (jQuery('#echelon1').is(':checked')) {
                jQuery('.p-box-alert').text(dynamicContent.alerts.recomendedProphylaxis);
                jQuery('.info-box-bottom1').text(dynamicContent.infoBox.cycleInfo);
                jQuery('.info-box-bottom1').show();
                jQuery('.info-box-bottom2').text(dynamicContent.infoBox.referToLeaflets);
                jQuery('.info-box-bottom2').show();
            } else if (jQuery('.overdose-opt').is(':checked')) {
                jQuery('.data-demais-adjustment').addClass('data2');
                jQuery('.p-box-alert').text(dynamicContent.alerts.continueDoseAndScheme);
                jQuery('.texto-adjustment').hide();
            } else if (jQuery('#echelon2').is(':checked')) {
                jQuery('.data-demais-adjustment').addClass('data2');
                jQuery('.p-box-alert').text(dynamicContent.alerts.recomendedProphylaxis);
                jQuery('.info-box-bottom2').show();
            } else {
                jQuery('.p-box-alert').text(dynamicContent.alerts.continueDoseAndScheme);
                jQuery('.info-box-bottom1').hide();
                jQuery('.info-box-bottom2').hide();
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-demais-adjustment').removeClass('data2');
                jQuery('.data-motora').addClass('data2');
                jQuery('.texto-adjustment').hide();
            }
        } else if (jQuery(".neuro-head-content").hasClass("neutropenia active-bg") && jQuery(".neuro-content2 div").hasClass("grade3 active")) {
            if (jQuery('#echelon1').is(':checked')) {
                jQuery('.p-box-alert').text(dynamicContent.alerts.recomendedProphylaxis);
                jQuery('.info-box-bottom1').text(dynamicContent.infoBox.cycleInfo);
                jQuery('.info-box-bottom1').show();
                jQuery('.info-box-bottom2').text(dynamicContent.infoBox.referToLeaflets);
                jQuery('.info-box-bottom2').show();
            } else if (jQuery('#echelon2').is(':checked')) {
                jQuery('.data-demais-adjustment').addClass('data2');
                jQuery('.p-box-alert').text(dynamicContent.alerts.recomendedProphylaxis);
                jQuery('.info-box-bottom2').show();
            } else if (jQuery('.overdose-opt').is(':checked')) {
                jQuery('.texto-adjustment').show();
                jQuery('.p-box-alert').text(dynamicContent.alerts.suspendDoseGrade2);
            } else {
                jQuery('.p-box-alert').text(dynamicContent.alerts.suspendDoseGrade2);
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.info-box-bottom1').hide();
                jQuery('.info-box-bottom2').hide();
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-demais-adjustment').addClass('data2');
                jQuery('.data-motora').addClass('data2');
                jQuery('.texto-adjustment').hide();
            }
        } else if (jQuery(".neuro-head-content").hasClass("neutropenia active-bg") && jQuery(".neuro-content2 div").hasClass("grade4 active")) {
            if (jQuery('#echelon1').is(':checked')) {
                jQuery('.p-box-alert').text(dynamicContent.alerts.recomendedProphylaxis);
                jQuery('.info-box-bottom1').text(dynamicContent.infoBox.cycleInfo);
                jQuery('.info-box-bottom1').show();
                jQuery('.info-box-bottom2').text(dynamicContent.infoBox.referToLeaflets);
                jQuery('.info-box-bottom2').show();
            } else if (jQuery('.overdose-opt').is(':checked')) {
                jQuery('.texto-adjustment').show()
                jQuery('.p-box-alert').text(dynamicContent.alerts.suspendDoseGrade2);

            } else if (jQuery('#echelon2').is(':checked')) {
                jQuery('.data-demais-adjustment').addClass('data2');
                jQuery('.p-box-alert').text(dynamicContent.alerts.recomendedProphylaxis);
                jQuery('.info-box-bottom2').show();
            } else {
                jQuery('.p-box-alert').text(dynamicContent.alerts.suspendDoseGrade2);
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.info-box-bottom1').hide();
                jQuery('.info-box-bottom2').hide();
                jQuery('.data-echelon1-adjustment').addClass('data2');
                jQuery('.data-demais-adjustment').addClass('data2');
                jQuery('.data-motora').addClass('data2');
            }
        }
    });

    jQuery('input[type=radio][name=protocolo]').change(function () {
        redo();
        set_dosage_cycles_infusions();
    });

    jQuery('.redo, .go-back').click(function () {
        redo();
        jQuery('.field1, .field2, .field3, .info-box, .insert').hide();
        jQuery('.info-box3').show();
        jQuery('.table-form input').prop('checked', false);
        jQuery('.result').hide();
        jQuery('.result2').hide();
        jQuery('.info-box-bottom1, .info-box-bottom2, .info-box3').hide();
        jQuery('.go-back').hide();
        jQuery('.neuro-content').hide();
        jQuery('.neuro-content2').hide();
        jQuery('.neuro-content').removeClass("flex-column");
        jQuery('.neuro-content2').removeClass("flex-column");
        jQuery('.neuropatia').addClass('color-third');
        jQuery('.neuropatia').removeClass('active-bg');
        jQuery('.neutropenia').removeClass('active-bg');
        jQuery('.neutropenia').addClass('color-third');
        jQuery('.box-alert1').hide();
        jQuery('.box-alert2').hide();
        jQuery('.box-alert-motora').hide();
        jQuery('.data-motora').hide();
        jQuery('.data-echelon1-adjustment').addClass('data2');
        jQuery('.grade').addClass('color-third');
        jQuery('.grade').removeClass('active-bg');
        jQuery('.grade').removeClass('active');
    });

    jQuery('.forms-div input').click(function () {

        if (jQuery('#echelon1').is(':checked')) {
            jQuery('.p1').show();
            jQuery('.p2').hide();
            jQuery('.p3').hide();
        } else if (jQuery('#lagcs').is(':checked')) {
            jQuery('.p2').show();
            jQuery('.p1').hide();
            jQuery('.p3').hide();
        } else {
            jQuery('.p3').show();
            jQuery('.p1').hide();
            jQuery('.p2').hide();
        }

        scrollToElement(jQuery('.info-box'));
    });

    jQuery('.fields input').keyup(function () {

        if (jQuery('#echelon1').is(':checked')) {
            if (jQuery('#input1').val() > 0 && jQuery('#input2').val() > 0) {
                populate();
                jQuery('.result').show();
            }
        } else if (jQuery('#echelon2').is(':checked')) {
            if (jQuery('#input1').val() > 0 && jQuery('#input2').val() > 0) {
                populateRemaining();
                jQuery('.result2').show();
            }
        } else if (jQuery('#pivotal-lagcs').is(':checked') || jQuery('#pivotal-lh').is(':checked') || jQuery('#echelon2').is(':checked') || jQuery('#alcanza').is(':checked') || jQuery('#aethera').is(':checked')) {
            if (jQuery('#input1').val() > 0) {
                populateRemaining();
            }
        }
    });

});
