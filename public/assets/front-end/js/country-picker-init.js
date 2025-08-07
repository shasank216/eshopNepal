// "use strict";
// function initializePhoneInput(selector, outputSelector) {
//     const phoneInput = document.querySelector(selector);
//     const phoneNumber = phoneInput.value;
//     const countryCodeMatch = phoneNumber.replace(/[^0-9]/g, '');
//     const initialCountry = countryCodeMatch ? `+${countryCodeMatch}` : $('.system-default-country-code').data('value').toLowerCase();

//     let phoneInputInit = window.intlTelInput(phoneInput, {
//         initialCountry: initialCountry.toLowerCase(),
//         showSelectedDialCode: true,
//     });
//     if (!phoneInputInit.selectedCountryData.dialCode ){
//         phoneInputInit.destroy();
//         phoneInputInit = window.intlTelInput(phoneInput, {
//             initialCountry: $('.system-default-country-code').data('value').toLowerCase(),
//             showSelectedDialCode: true,
//         })
//     }
//     $(outputSelector).val('+' + phoneInputInit.selectedCountryData.dialCode + phoneInput.value.replace(/[^0-9]/g, ''));

//     $(".iti__country").on("click", function() {
//         $(outputSelector).val('+' + $(this).data('dial-code') + phoneInput.value.replace(/[^0-9]/g, ''));
//     });

//     $(selector).on("keyup keypress change", function() {
//         $(outputSelector).val('+' + phoneInputInit.selectedCountryData.dialCode + phoneInput.value.replace(/[^0-9]/g, ''));
//         $(selector).val(phoneInput.value.replace(/[^0-9]/g, ''));
//     });
// }
// $(document).ready(function() {
//     try {
//         initializePhoneInput(".phone-input-with-country-picker", ".country-picker-phone-number");
//     } catch (error) {}
// });

"use strict";
function initializePhoneInput(selector, outputSelector) {
    const phoneInput = document.querySelector(selector);
    const defaultCountryCode = $('.system-default-country-code').data('value')?.toLowerCase() || 'np';

    if (!phoneInput) return;

    // Strip existing country code from the input if duplicated
    const defaultDialCode = '+977'; // adjust or fetch based on defaultCountryCode
    if (phoneInput.value.startsWith(defaultDialCode)) {
        phoneInput.value = phoneInput.value.replace(defaultDialCode, '');
    }

    const iti = window.intlTelInput(phoneInput, {
        separateDialCode: true,
        nationalMode: false,
        initialCountry: defaultCountryCode,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.min.js"
    });

    function updateHiddenField() {
        const fullNumber = iti.getNumber(); // e.g. +9779811018614
        $(outputSelector).val(fullNumber);
    }

    phoneInput.addEventListener('change', updateHiddenField);
    phoneInput.addEventListener('keyup', updateHiddenField);
    phoneInput.addEventListener('blur', updateHiddenField);
    phoneInput.addEventListener('countrychange', updateHiddenField);

    updateHiddenField();
}

$(document).ready(function() {
    try {
        initializePhoneInput(".phone-input-with-country-picker", ".country-picker-phone-number");
    } catch (error) {
        console.error(error);
    }
});



// "use strict";

// function initializePhoneInput(selector, outputSelector) {
//     const phoneInput = document.querySelector(selector);
//     const phoneNumber = phoneInput.value;
//     const cleanedNumber = phoneNumber.replace(/[^0-9]/g, '');
//     console.log(phoneInput);

//     // Get default country from DOM or fallback to 'us'
//     const systemDefaultCountry = $('.system-default-country-code').data('value')?.toLowerCase() || 'us';
//     const initialCountry = cleanedNumber ? systemDefaultCountry : systemDefaultCountry;

//     // Initialize intl-tel-input
//     let phoneInputInit = window.intlTelInput(phoneInput, {
//         initialCountry: initialCountry,
//         showSelectedDialCode: true,
//         separateDialCode: false, // optional: combine dial code with number
//         nationalMode: false      // optional: ensures full intl number
//     });

//     // Fallback if selected country data is not available
//     if (!phoneInputInit.selectedCountryData?.dialCode) {
//         phoneInputInit.destroy();
//         phoneInputInit = window.intlTelInput(phoneInput, {
//             initialCountry: systemDefaultCountry,
//             showSelectedDialCode: true
//         });
//     }

//     // Helper: update the output field
//     const updateOutput = () => {
//         const dialCode = phoneInputInit.selectedCountryData.dialCode;
//         const cleanedInput = phoneInput.value.replace(/[^0-9]/g, '');
//         $(outputSelector).val('+' + dialCode + cleanedInput);
//     };

//     // Initial set
//     updateOutput();

//     // On country change
//     $(".iti__country").on("click", function () {
//         updateOutput();
//     });

//     // On user input
//     $(selector).on("keyup keypress change", function () {
//         phoneInput.value = phoneInput.value.replace(/[^0-9]/g, ''); // clean live input
//         updateOutput();
//     });
// }
