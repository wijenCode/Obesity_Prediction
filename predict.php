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
$is_labeled = $_SESSION['is_labeled'] ?? false;
$accuracy = $_SESSION['accuracy'] ?? null;
$actual_labels = $_SESSION['actual_labels'] ?? [];
$results_csv_path = $_SESSION['results_csv_path'] ?? null;

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
$raw_predictions = [];

// First, try to decode as JSON
$decoded_result = json_decode($prediction_result, true);

if (is_array($decoded_result)) {
    // For CSV predictions, the result is a JSON object with predictions array
    if (isset($decoded_result['predictions']) && is_array($decoded_result['predictions'])) {
        $raw_predictions = $decoded_result['predictions'];
        foreach ($raw_predictions as $pred) {
            $predictions[] = isset($label_map["$pred"]) ? $label_map["$pred"] : "Unknown ($pred)";
        }
    } else {
        // If it's just an array of predictions
        $raw_predictions = $decoded_result;
        foreach ($raw_predictions as $pred) {
            $predictions[] = isset($label_map["$pred"]) ? $label_map["$pred"] : "Unknown ($pred)";
        }
    }
} else {
    // For manual predictions, it might be a single numeric value or a simple JSON array
    // Try to parse as a simple array first
    $simple_decode = json_decode($prediction_result, true);
    
    if (is_array($simple_decode)) {
        $raw_predictions = $simple_decode;
        foreach ($raw_predictions as $pred) {
            $predictions[] = isset($label_map["$pred"]) ? $label_map["$pred"] : "Unknown ($pred)";
        }
    } else {
        // If it's a single numeric value (for manual prediction)
        $pred_value = trim($prediction_result);
        
        // Check if it's a numeric value
        if (is_numeric($pred_value)) {
            $raw_predictions = [intval($pred_value)];
            $predictions[] = isset($label_map[$pred_value]) ? $label_map[$pred_value] : "Unknown ($pred_value)";
        } else {
            // If we couldn't parse anything, show the raw output
            $predictions = ["Error: $prediction_result"];
        }
    }
}

// Prepare chart data for labeled predictions
$chart_data = [];
$overall_correct = 0;
$overall_total = 0;
$label_stats = [];

if ($is_labeled && !empty($actual_labels) && !empty($raw_predictions)) {
    $label_to_code = [
        'Insufficient_Weight' => 0,
        'Normal_Weight' => 1,
        'Overweight_Level_I' => 2,
        'Overweight_Level_II' => 3,
        'Obesity_Type_I' => 4,
        'Obesity_Type_II' => 5,
        'Obesity_Type_III' => 6
    ];
    
    // Initialize label stats
    foreach ($label_map as $code => $label) {
        $label_stats[$label] = ['correct' => 0, 'incorrect' => 0, 'total' => 0];
    }
    
    // Calculate statistics
    for ($i = 0; $i < count($actual_labels); $i++) {
        $actual_code = $label_to_code[$actual_labels[$i]] ?? -1;
        $predicted_code = $raw_predictions[$i] ?? -1;
        
        if ($actual_code >= 0 && isset($label_map["$actual_code"])) {
            $actual_label = $label_map["$actual_code"];
            $label_stats[$actual_label]['total']++;
            
            if ($actual_code == $predicted_code) {
                $label_stats[$actual_label]['correct']++;
                $overall_correct++;
            } else {
                $label_stats[$actual_label]['incorrect']++;
            }
            $overall_total++;
        }
    }
}

// Clear the session data after use
unset($_SESSION['prediction_result']);
unset($_SESSION['prediction_type']);
unset($_SESSION['form_data']);
// Don't unset csv_data yet as we need it for display

// Create a mapping between form field names and descriptive labels
$feature_descriptions = [
    "Gender" => "Gender (Female/Male)",
    "Age" => "Age (number)",
    "Height" => "Height (meters, e.g., 1.75)",
    "Weight" => "Weight (kg)",
    "family_history_of_overweight" => "Family History of Overweight (yes/no)",
    "FAVC" => "FAVC (yes/no) - Frequent consumption of high caloric food",
    "FCVC" => "FCVC (1-3) - Frequency of consumption of vegetables",
    "NCP" => "NCP (1-4) - Number of main meals",
    "CAEC" => "CAEC (no/Sometimes/Frequently/Always) - Consumption of food between meals",
    "SMOKE" => "SMOKE (yes/no)",
    "CH2O" => "CH2O (1-3) - Consumption of water daily",
    "SCC" => "SCC (yes/no) - Calories consumption monitoring",
    "FAF" => "FAF (0-3) - Physical activity frequency",
    "TUE" => "TUE (0-2) - Time using technology devices",
    "CALC" => "CALC (no/Sometimes/Frequently/Always) - Consumption of alcohol",
    "MTRANS" => "MTRANS (Automobile/Bike/Motorbike/Public_Transportation/Walking) - Transportation used"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Results - Obesity Prediction System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .table-responsive {
            max-height: 500px;
            overflow-y: auto;
        }
        .chart-container {
            position: relative;
            height: 400px;
        }
        .chart-small {
            height: 300px;
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
                                        <td class="px-4 py-2 border border-gray-500">
                                            <?php echo htmlspecialchars(isset($feature_descriptions[$key]) ? $feature_descriptions[$key] : $key); ?>
                                        </td>
                                        <td class="px-4 py-2 border border-gray-500"><?php echo htmlspecialchars($value); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php elseif ($prediction_type === 'csv'): ?>
                    <div class="bg-gray-700 text-white p-4 rounded-md mb-6">
                        <h4 class="text-lg font-semibold mb-2">Prediction Summary:</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p><span class="font-semibold">Total records:</span> <?php echo count($predictions); ?></p>
                            </div>
                            <?php if ($is_labeled && $accuracy !== null): ?>
                            <div>
                                <p><span class="font-semibold">Accuracy:</span> 
                                    <span class="text-green-400 text-lg font-bold">
                                        <?php echo number_format($accuracy * 100, 2); ?>%
                                    </span>
                                </p>
                            </div>
                            <div>
                                <p><span class="font-semibold">Correct predictions:</span> 
                                    <?php echo $overall_correct . ' / ' . $overall_total; ?>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($is_labeled && $overall_total > 0): ?>
                    <!-- Charts Section -->
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-6 text-center">Prediction Analysis Charts</h3>
                        
                        <!-- Overall Accuracy Chart -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-lg font-semibold mb-4 text-center">Overall Accuracy</h4>
                                <div class="chart-container chart-small">
                                    <canvas id="overallChart"></canvas>
                                </div>
                            </div>
                            
                            <!-- Accuracy by Label Chart -->
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <h4 class="text-lg font-semibold mb-4 text-center">Accuracy by Obesity Category</h4>
                                <div class="chart-container chart-small">
                                    <canvas id="accuracyByLabelChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Detailed Results by Category -->
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <h4 class="text-lg font-semibold mb-4 text-center">Detailed Results by Category</h4>
                            <div class="chart-container">
                                <canvas id="detailedChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Download Options -->
                    <?php if ($results_csv_path && file_exists($results_csv_path)): ?>
                    <div class="bg-green-600 text-white p-4 rounded-md mb-6">
                        <h4 class="text-lg font-semibold mb-3">Download Results:</h4>
                        <div class="flex flex-wrap gap-3">
                            <a href="download.php?file=<?php echo urlencode(basename($results_csv_path)); ?>&type=csv" 
                               class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                Download CSV
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($csv_data) && !empty($predictions)): ?>
                        <div class="table-responsive mt-4">
                            <table class="table-auto w-full text-gray-100 border-collapse border border-gray-500 border-opacity-50">
                                <thead class="bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 border border-gray-500">#</th>
                                        <?php foreach (array_keys($csv_data[0]) as $header): ?>
                                            <?php if ($header !== 'Obesity'): // Don't show the label column ?>
                                                <th class="px-4 py-2 border border-gray-500"><?php echo htmlspecialchars($header); ?></th>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                        <?php if ($is_labeled): ?>
                                            <th class="px-4 py-2 bg-yellow-600 text-white border border-gray-500">Actual Label</th>
                                        <?php endif; ?>
                                        <th class="px-4 py-2 bg-blue-600 text-white border border-gray-500 w-1/4 whitespace-nowrap">Predicted Label</th>
                                        <?php if ($is_labeled): ?>
                                            <th class="px-4 py-2 bg-green-600 text-white border border-gray-500">Correct?</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for ($i = 0; $i < count($csv_data); $i++): ?>
                                        <?php 
                                        $is_correct = false;
                                        if ($is_labeled && !empty($actual_labels)) {
                                            $label_to_code = [
                                                'Insufficient_Weight' => 0,
                                                'Normal_Weight' => 1,
                                                'Overweight_Level_I' => 2,
                                                'Overweight_Level_II' => 3,
                                                'Obesity_Type_I' => 4,
                                                'Obesity_Type_II' => 5,
                                                'Obesity_Type_III' => 6
                                            ];
                                            $actual_code = $label_to_code[$actual_labels[$i]] ?? -1;
                                            $is_correct = ($actual_code == $raw_predictions[$i]);
                                        }
                                        $row_class = $is_correct ? "bg-green-800" : ($is_labeled ? "bg-red-800" : "bg-gray-800");
                                        ?>
                                        <tr class="<?php echo $row_class; ?>">
                                            <td class="px-4 py-2 border border-gray-500"><?php echo $i + 1; ?></td>
                                            <?php foreach ($csv_data[$i] as $key => $value): ?>
                                                <?php if ($key !== 'Obesity'): // Don't show the label column ?>
                                                    <td class="px-4 py-2 border border-gray-500"><?php echo htmlspecialchars($value); ?></td>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php if ($is_labeled): ?>
                                                <td class="px-4 py-2 bg-yellow-700 font-semibold text-white border border-gray-500">
                                                    <?php echo isset($actual_labels[$i]) ? htmlspecialchars(str_replace('_', ' ', $actual_labels[$i])) : "N/A"; ?>
                                                </td>
                                            <?php endif; ?>
                                            <td class="px-4 py-2 bg-blue-700 font-semibold text-white border border-gray-500 whitespace-nowrap">
                                                <?php echo isset($predictions[$i]) ? htmlspecialchars($predictions[$i]) : "N/A"; ?>
                                            </td>
                                            <?php if ($is_labeled): ?>
                                                <td class="px-4 py-2 <?php echo $is_correct ? 'bg-green-600' : 'bg-red-600'; ?> font-semibold text-white border border-gray-500 text-center">
                                                    <?php echo $is_correct ? '✓' : '✗'; ?>
                                                </td>
                                            <?php endif; ?>
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
                
                <div class="text-center mt-6">
                    <a href="form.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg text-lg">Back to Home</a>
                    <?php if ($prediction_type === 'manual'): ?>
                        <a href="manual_input.php" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-6 rounded-lg text-lg ml-4">New Prediction</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($is_labeled && $overall_total > 0): ?>
    <script>
        // Chart.js configuration
        Chart.defaults.color = '#ffffff';
        Chart.defaults.borderColor = '#374151';
        
        // Overall Accuracy Pie Chart
        const overallCtx = document.getElementById('overallChart').getContext('2d');
        const overallChart = new Chart(overallCtx, {
            type: 'doughnut',
            data: {
                labels: ['Correct Predictions', 'Incorrect Predictions'],
                datasets: [{
                    data: [<?php echo $overall_correct; ?>, <?php echo $overall_total - $overall_correct; ?>],
                    backgroundColor: ['#10B981', '#EF4444'],
                    borderColor: ['#059669', '#DC2626'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            color: '#ffffff'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed * 100) / total).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Accuracy by Label Bar Chart
        const accuracyByLabelCtx = document.getElementById('accuracyByLabelChart').getContext('2d');
        const labelAccuracyData = {
            labels: [
                <?php 
                foreach ($label_stats as $label => $stats) {
                    if ($stats['total'] > 0) {
                        echo "'" . addslashes($label) . "',";
                    }
                }
                ?>
            ],
            datasets: [{
                label: 'Accuracy %',
                data: [
                    <?php 
                    foreach ($label_stats as $label => $stats) {
                        if ($stats['total'] > 0) {
                            $accuracy_percent = ($stats['correct'] / $stats['total']) * 100;
                            echo number_format($accuracy_percent, 1) . ",";
                        }
                    }
                    ?>
                ],
                backgroundColor: '#3B82F6',
                borderColor: '#2563EB',
                borderWidth: 1
            }]
        };

        const accuracyByLabelChart = new Chart(accuracyByLabelCtx, {
            type: 'bar',
            data: labelAccuracyData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            },
                            color: '#ffffff'
                        },
                        grid: {
                            color: '#374151'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#ffffff',
                            maxRotation: 45
                        },
                        grid: {
                            color: '#374151'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Accuracy: ' + context.parsed.y.toFixed(1) + '%';
                            }
                        }
                    }
                }
            }
        });

        // Detailed Chart - Correct vs Incorrect by Category
        const detailedCtx = document.getElementById('detailedChart').getContext('2d');
        const detailedChart = new Chart(detailedCtx, {
            type: 'bar',
            data: {
                labels: [
                    <?php 
                    foreach ($label_stats as $label => $stats) {
                        if ($stats['total'] > 0) {
                            echo "'" . addslashes($label) . "',";
                        }
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Correct',
                    data: [
                        <?php 
                        foreach ($label_stats as $label => $stats) {
                            if ($stats['total'] > 0) {
                                echo $stats['correct'] . ",";
                            }
                        }
                        ?>
                    ],
                    backgroundColor: '#10B981',
                    borderColor: '#059669',
                    borderWidth: 1
                }, {
                    label: 'Incorrect',
                    data: [
                        <?php 
                        foreach ($label_stats as $label => $stats) {
                            if ($stats['total'] > 0) {
                                echo $stats['incorrect'] . ",";
                            }
                        }
                        ?>
                    ],
                    backgroundColor: '#EF4444',
                    borderColor: '#DC2626',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        stacked: false,
                        ticks: {
                            color: '#ffffff',
                            stepSize: 1
                        },
                        grid: {
                            color: '#374151'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#ffffff',
                            maxRotation: 45
                        },
                        grid: {
                            color: '#374151'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#ffffff'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            afterLabel: function(context) {
                                const datasetIndex = context.datasetIndex;
                                const dataIndex = context.dataIndex;
                                const correct = detailedChart.data.datasets[0].data[dataIndex];
                                const incorrect = detailedChart.data.datasets[1].data[dataIndex];
                                const total = correct + incorrect;
                                const percentage = total > 0 ? ((correct / total) * 100).toFixed(1) : 0;
                                return 'Total: ' + total + ' (Accuracy: ' + percentage + '%)';
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false
                }
            }
        });
    </script>
    <?php endif; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    
    <?php
    // Clear the session data after display
    unset($_SESSION['csv_data']);
    unset($_SESSION['selected_model']);
    unset($_SESSION['is_labeled']);
    unset($_SESSION['accuracy']);
    unset($_SESSION['actual_labels']);
    unset($_SESSION['results_csv_path']);
    ?>
</body>
</html>