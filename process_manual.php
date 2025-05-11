<?php
// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: manual_input.php");
    exit;
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Collect all form data
$formData = [
    'Gender' => $_POST['Gender'] ?? '',
    'Age' => $_POST['Age'] ?? '',
    'Height' => $_POST['Height'] ?? '',
    'Weight' => $_POST['Weight'] ?? '',
    'family_history_of_overweight' => $_POST['family_history_of_overweight'] ?? '',
    'FAVC' => $_POST['FAVC'] ?? '',
    'FCVC' => $_POST['FCVC'] ?? '',
    'NCP' => $_POST['NCP'] ?? '',
    'CAEC' => $_POST['CAEC'] ?? '',
    'SMOKE' => $_POST['SMOKE'] ?? '',
    'CH2O' => $_POST['CH2O'] ?? '',
    'SCC' => $_POST['SCC'] ?? '',
    'FAF' => $_POST['FAF'] ?? '',
    'TUE' => $_POST['TUE'] ?? '',
    'CALC' => $_POST['CALC'] ?? '',
    'MTRANS' => $_POST['MTRANS'] ?? ''
];

// Validate that all required fields are present
foreach ($formData as $key => $value) {
    if (empty($value)) {
        header("Location: manual_input.php?error=missing&field=" . $key);
        exit;
    }
}

// Create a temporary CSV file from the form data
$temp_file = 'uploads/' . uniqid() . '.csv';
$handle = fopen($temp_file, 'w');

// Write the header
fputcsv($handle, array_keys($formData));

// Write the data
fputcsv($handle, array_values($formData));

// Close the file
fclose($handle);

// Call the Python script for prediction
$output = null;
$return_var = null;

// Execute the Python script
exec("python scripts/predict.py --csv $temp_file 2>&1", $output, $return_var);

// Check if the execution was successful
if ($return_var !== 0) {
    // Something went wrong with the Python script
    unlink($temp_file); // Clean up
    $error_message = implode("<br>", $output);
    header("Location: manual_input.php?error=prediction&message=" . urlencode($error_message));
    exit;
}

// Get the result from the last line of output
$prediction_result = end($output);

// Clean up - delete the temporary file
unlink($temp_file);

// Store prediction result in session for display
session_start();
$_SESSION['prediction_result'] = $prediction_result;
$_SESSION['prediction_type'] = 'manual';
$_SESSION['form_data'] = $formData;

// Redirect to the results page
header("Location: predict.php");
exit;
?>