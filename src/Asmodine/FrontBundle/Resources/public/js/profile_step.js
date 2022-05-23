function nextProfileStep(event, current_step) {
    if (!current_step) {
        return false;
    }

    if (!checkStepValue(current_step)) {
        return false;
    }

    var next_id = current_step.getAttribute(next_gender());
    //var next_step_input = document.querySelector('.profile-form [name="physical_profile_form[step_'+ next_id +']"]');
    var next_step_input = jQuery('.profile-form [data-step="' + next_id + '"]');

    if (!next_step_input) {
        return false;
    }
    var next_step = next_step_input.findParent('profile-form');

    if (!next_step_input) {
        return false;
    }

    next_step.classList.add('active');
    var current_step_number = document.querySelector('.step-number .current-step');
    current_step_number.innerHTML = parseInt(current_step_number.innerHTML, 10) + 1;
    current_step.findParent('profile-form').classList.remove('active');

    // On cache le couton suivant lors de la validation
    if (next_step_input.getAttribute('type').toUpperCase() == 'SUBMIT') {
        event.target.classList.add('hide');
    } else {
        next_step_input.focus();
    }

}

function next_gender() {
    var input_gender = jQuery(document).querySelector('#physical_profile_form_step_gender');
    if (!input_gender) {
        return false;
    }

    if (input_gender.value === 'female') {
        return 'data-next_female';
    }

    return 'data-next_male';
}

/**
 * Si une valeur est présente on continue
 * @param current_step
 * @returns {*}
 */
function checkStepValue(current_step) {
    var input_value = current_step.value;
    if (!input_value) {
        var checked = current_step.querySelector('input:checked');
        if (checked !== null) {
            input_value = checked.value;
        }
    }
    return (current_step && input_value);
}

jQuery(document).on('ready', function () {

    jQuery('#morphoprofileModal').modal('show');
    /*jQuery(document).on('click', '.next-step', function (event) {
        event.preventDefault();
        var current_step = document.querySelector('.profile-form.active .form-control:not(label)');
        nextProfileStep(event, current_step);
    });

    // Ajoute le bouton entrée pour faire suivant et focus le next input
    jQuery(document).keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (event.target.findParent('profile-form') && keycode == '13') {
            event.preventDefault();
            // Focus next input
            jQuery(document).querySelector('.next-step').click();
            // the new input is visible now
            var new_input = jQuery(document).querySelector('.profile-form.active input');
            if (new_input) {
                new_input.focus();
            }
        }
    });*/
});

