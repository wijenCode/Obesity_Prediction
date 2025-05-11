<?php
// Check if a file was uploaded
if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    header("Location: index.php?error=upload");
    exit;
}

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Generate a unique file name
$upload_file = 'uploads/' . uniqid() . '.csv';

// Move the uploaded file to our directory
if (!move_uploaded_file($_FILES['csvFile']['tmp_name'], $upload_file)) {
    header("Location: index.php?error=move");
    exit;
}

// Validate CSV format and required columns
if (($handle = fopen($upload_file, "r")) !== FALSE) {
    // Read the header row
    $header = fgetcsv($handle, 1000, ",");
    
    // Check if all required columns are present
    $required_columns = [
        "Gender", "Age", "Height", "Weight", "family_history_of_overweight", 
        "FAVC", "FCVC", "NCP", "CAEC", "SMOKE", "CH2O", "SCC", 
        "FAF", "TUE", "CALC", "MTRANS"
    ];
    
    $missing_columns = array_diff($required_columns, $header);
    
    if (!empty($missing_columns)) {
        unlink($upload_file); // Delete the invalid file
        $missing = implode(", ", $missing_columns);
        header("Location: index.php?error=columns&missing=" . urlencode($missing));
        exit;
    }
    
    fclose($handle);
} else {
    unlink($upload_file); // Delete the file if we can't open it
    header("Location: index.php?error=read");
    exit;
}

// Call the Python script to make predictions
$prediction_result = null;
$output = null;
$return_var = null;

// Execute the Python script
exec("python scripts/predict.py --csv $upload_file 2>&1", $output, $return_var);

// Check if the execution was successful
if ($return_var !== 0) {
    // Something went wrong with the Python script
    unlink($upload_file); // Clean up
    $error_message = implode("<br>", $output);
    header("Location: index.php?error=prediction&message=" . urlencode($error_message));
    exit;
}

// Get the result from the last line of output
$prediction_result = end($output);

// Clean up - delete the temporary file
unlink($upload_file);

// Store prediction result in session for display
session_start();
$_SESSION['prediction_result'] = $prediction_result;
$_SESSION['prediction_type'] = 'csv';

// Redirect to the results page
header("Location: predict.php");
exit;
?>