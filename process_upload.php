<?php
// Check if a file was uploaded
if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    header("Location: form.php?error=upload");
    exit;
}

// Get the selected model
if (!isset($_POST['model'])) {
    header("Location: form.php?error=nomodel");
    exit;
}

$selected_model = $_POST['model'];
$valid_models = ['SVM', 'KNN', 'DT', 'NN'];

if (!in_array($selected_model, $valid_models)) {
    header("Location: form.php?error=invalidmodel");
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
    header("Location: form.php?error=move");
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
        header("Location: form.php?error=columns&missing=" . urlencode($missing));
        exit;
    }
    
    // Read the data to store for display later
    $csv_data = [];
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        if (count($data) == count($header)) {
            $row = [];
            for ($i = 0; $i < count($header); $i++) {
                $row[$header[$i]] = $data[$i];
            }
            $csv_data[] = $row;
        }
    }
    
    fclose($handle);
} else {
    unlink($upload_file); // Delete the file if we can't open it
    header("Location: form.php?error=read");
    exit;
}

// Call the Python script to make predictions
$prediction_result = null;
$output = null;
$return_var = null;

// Execute the Python script with the model parameter
exec("python scripts/predict.py --csv $upload_file --model $selected_model 2>&1", $output, $return_var);

// Check if the execution was successful
if ($return_var !== 0) {
    // Something went wrong with the Python script
    unlink($upload_file); // Clean up
    $error_message = implode("<br>", $output);
    header("Location: form.php?error=prediction&message=" . urlencode($error_message));
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
$_SESSION['csv_data'] = $csv_data;
$_SESSION['selected_model'] = $selected_model;

// Redirect to the results page
header("Location: predict.php");
exit;
?>