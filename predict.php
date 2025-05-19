<?php
session_start();

// Check if prediction result exists in session
if (!isset($_SESSION['prediction_result'])) {
    header("Location: form.php");
    exit;
}

$prediction_result = $_SESSION['prediction_result'];
$prediction_type = $_SESSION['prediction_type'] ?? 'unknown';
$form_data = $_SESSION['form_data'] ?? [];
$csv_data = $_SESSION['csv_data'] ?? [];
$selected_model = $_SESSION['selected_model'] ?? 'SVM';

// Mapping of prediction codes to labels
$label_map = [
    '0' => 'Insufficient Weight',
    '1' => 'Normal Weight',
    '2' => 'Overweight Level I',
    '3' => 'Overweight Level II',
    '4' => 'Obesity Type I',
    '5' => 'Obesity Type II',
    '6' => 'Obesity Type III'
];

// Process the prediction result
$predictions = [];
$raw_predictions = json_decode($prediction_result, true);

if (is_array($raw_predictions)) {
    foreach ($raw_predictions as $pred) {
        $predictions[] = isset($label_map["$pred"]) ? $label_map["$pred"] : "Unknown ($pred)";
    }
} else {
    // If we couldn't parse JSON, show the raw output
    $predictions = ["Error: $prediction_result"];
}

// Clear the session data after use
unset($_SESSION['prediction_result']);
unset($_SESSION['prediction_type']);
unset($_SESSION['form_data']);
// Don't unset csv_data yet as we need it for display
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Results - Obesity Prediction System</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS CDN -->
    <style>
        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }
    </style>
</head>
<body class="bg-[#0d1017] text-white">

    <div class="container mx-auto mt-10 p-6">
        <div class="bg-gray-800 rounded-lg shadow-lg">
            <div class="bg-gray-700 p-4 rounded-t-lg">
                <h2 class="text-2xl mb-6 font-semibold">Prediction Results</h2>
                <?php if ($prediction_type === 'csv'): ?>
                <p class="mb-0">Using model: <strong><?php echo htmlspecialchars($selected_model); ?></strong></p>
                <?php endif; ?>
            </div>
            <div class="p-6">
                <?php if (count($predictions) === 1 && $prediction_type === 'manual'): ?>
                    <div class="bg-blue-500 text-white p-4 rounded-md">
                        <h4>Your Obesity Category:</h4>
                        <h2 class="text-center mt-3 mb-3 text-3xl"><?php echo htmlspecialchars($predictions[0]); ?></h2>
                    </div>
                    
                    <?php if (!empty($form_data)): ?>
                        <h4 class="text-xl font-semibold mt-6">Your Input Data:</h4>
                        <table class="table-auto w-full text-gray-100 mt-4 border-collapse border border-gray-500 border-opacity-50">
                            <thead>
                                <tr class="bg-gray-700">
                                    <th class="px-4 py-2">Feature</th>
                                    <th class="px-4 py-2">Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($form_data as $key => $value): ?>
                                    <tr class="bg-gray-800">
                                        <td class="px-4 py-2 border border-gray-500"><?php echo htmlspecialchars($key); ?></td>
                                        <td class="px-4 py-2 border border-gray-500"><?php echo htmlspecialchars($value); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php elseif ($prediction_type === 'csv'): ?>
                    <div class="bg-gray-700 text-white p-4 rounded-md">
                        <h4>Prediction Results for CSV Data:</h4>
                        <p>Total records: <?php echo count($predictions); ?></p>
                    </div>
                    
                    <?php if (!empty($csv_data) && !empty($predictions)): ?>
                        <div class="table-responsive mt-4">
                            <table class="table-auto w-full text-gray-100 border-collapse border border-gray-500 border-opacity-50">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 border border-gray-500">#</th>
                                        <?php foreach (array_keys($csv_data[0]) as $header): ?>
                                            <th class="px-4 py-2 border border-gray-500"><?php echo htmlspecialchars($header); ?></th>
                                        <?php endforeach; ?>
                                        <th class="px-4 py-2 bg-info text-white border border-gray-500 w-1/4 whitespace-nowrap">Prediction</th> <!-- Make this column wider and prevent wrapping -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < count($csv_data); $i++): ?>
                                        <tr class="bg-gray-800">
                                            <td class="px-4 py-2 border border-gray-500"><?php echo $i + 1; ?></td>
                                            <?php foreach ($csv_data[$i] as $value): ?>
                                                <td class="px-4 py-2 border border-gray-500"><?php echo htmlspecialchars($value); ?></td>
                                            <?php endforeach; ?>
                                            <td class="px-4 py-2 bg-gray-600 font-semibold text-white border border-gray-500 whitespace-nowrap">
                                                <?php echo isset($predictions[$i]) ? htmlspecialchars($predictions[$i]) : "N/A"; ?>
                                            </td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php elseif (!empty($predictions)): ?>
                        <div class="bg-yellow-500 text-gray-800 p-4 rounded-md mt-4">
                            <h5>Predictions only (original data not available):</h5>
                            <ul>
                                <?php foreach ($predictions as $index => $pred): ?>
                                    <li>Record <?php echo $index + 1; ?>: <?php echo htmlspecialchars($pred); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="bg-red-500 text-white p-4 rounded-md mt-4">
                        <h4>Prediction Result:</h4>
                        <p><?php echo htmlspecialchars($prediction_result); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="form.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg text-lg">Back to Home</a>
                    <?php if ($prediction_type === 'manual'): ?>
                        <a href="manual_input.php" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-6 rounded-lg text-lg ml-4">New Prediction</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    
    <?php
    // Clear the session data after display
    unset($_SESSION['csv_data']);
    unset($_SESSION['selected_model']);
    ?>
</body>
</html>