// Form validation script for manual input form

document.addEventListener('DOMContentLoaded', function() {
    const predictionForm = document.getElementById('predictionForm');
    
    if (predictionForm) {
        predictionForm.addEventListener('submit', function(e) {
            // Get all input fields
            const numberInputs = document.querySelectorAll('input[type="number"]');
            const selectInputs = document.querySelectorAll('select');
            let isValid = true;
            
            // Validate number inputs
            numberInputs.forEach(input => {
                // Remove any existing error messages
                const existingError = input.nextElementSibling;
                if (existingError && existingError.classList.contains('text-danger')) {
                    existingError.remove();
                }
                
                const value = parseFloat(input.value);
                const min = parseFloat(input.getAttribute('min'));
                const max = parseFloat(input.getAttribute('max'));
                
                if (isNaN(value) || value < min || value > max) {
                    e.preventDefault();
                    isValid = false;
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'text-danger small mt-1';
                    errorMsg.textContent = `Value must be between ${min} and ${max}`;
                    input.after(errorMsg);
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }
            });
            
            // Validate select inputs
            selectInputs.forEach(select => {
                // Remove any existing error messages
                const existingError = select.nextElementSibling;
                if (existingError && existingError.classList.contains('text-danger')) {
                    existingError.remove();
                }
                
                if (select.value === '') {
                    e.preventDefault();
                    isValid = false;
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'text-danger small mt-1';
                    errorMsg.textContent = 'Please select an option';
                    select.after(errorMsg);
                    select.classList.add('is-invalid');
                } else {
                    select.classList.remove('is-invalid');
                    select.classList.add('is-valid');
                }
            });
            
            // Show global error message if needed
            if (!isValid) {
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger mt-3';
                alertDiv.textContent = 'Please correct the errors in the form before submitting.';
                
                // Remove any existing alert
                const existingAlert = document.querySelector('.alert-danger');
                if (existingAlert) {
                    existingAlert.remove();
                }
                
                predictionForm.prepend(alertDiv);
                
                // Scroll to top of the form
                window.scrollTo(0, predictionForm.offsetTop - 20);
            }
        });
    }
    
    // Handle URL parameters for error messages
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('error')) {
        const errorType = urlParams.get('error');
        let errorMessage = '';
        
        switch(errorType) {
            case 'upload':
                errorMessage = 'Error uploading file. Please try again.';
                break;
            case 'move':
                errorMessage = 'Error moving uploaded file. Please try again.';
                break;
            case 'columns':
                const missing = urlParams.get('missing');
                errorMessage = `CSV is missing required columns: ${missing}`;
                break;
            case 'read':
                errorMessage = 'Error reading CSV file. Please check the format.';
                break;
            case 'prediction':
                const message = urlParams.get('message');
                errorMessage = `Prediction error: ${message || 'Unknown error occurred'}`;
                break;
            case 'missing':
                const field = urlParams.get('field');
                errorMessage = `Missing required field: ${field}`;
                break;
            default:
                errorMessage = 'An error occurred. Please try again.';
        }
        
        // Display error message
        const container = document.querySelector('.container');
        if (container) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger mt-3';
            alertDiv.textContent = errorMessage;
            container.prepend(alertDiv);
        }
    }

    let currentPage = window.location.pathname.split("/").pop();

    // Set the active class based on the current page
    if (currentPage === "index.html") {
        document.getElementById("home").classList.add("bg-blue-600", "text-white");
    } else if (currentPage === "data_visualisation.html") {
        document.getElementById("visualisation").classList.add("bg-blue-600", "text-white");
    } else if (currentPage === "prediction.html") {
        document.getElementById("prediction").classList.add("bg-blue-600", "text-white");
    }
});