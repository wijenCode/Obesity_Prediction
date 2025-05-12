<?php
session_start();

// Check if prediction result exists in session
if (!isset($_SESSION['prediction_result'])) {
    header("Location: index.php");
    exit;
}

$prediction_result = $_SESSION['prediction_result'];
$prediction_type = $_SESSION['prediction_type'] ?? 'unknown';
$form_data = $_SESSION['form_data'] ?? [];

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Results - Obesity Prediction System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h2>Prediction Results</h2>
            </div>
            <div class="card-body">
                <?php if (count($predictions) === 1 && $prediction_type === 'manual'): ?>
                    <div class="alert alert-info">
                        <h4>Your Obesity Category:</h4>
                        <h2 class="text-center mt-3 mb-3"><?php echo htmlspecialchars($predictions[0]); ?></h2>
                    </div>
                    
                    <?php if (!empty($form_data)): ?>
                        <h4>Your Input Data:</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Feature</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($form_data as $key => $value): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($key); ?></td>
                                        <td><?php echo htmlspecialchars($value); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php elseif ($prediction_type === 'csv'): ?>
                    <div class="alert alert-info">
                        <h4>Prediction Results for CSV Data:</h4>
                        <?php if (count($predictions) > 10): ?>
                            <p>Total predictions: <?php echo count($predictions); ?></p>
                            <p>First 10 predictions shown below:</p>
                            <ul>
                                <?php for ($i = 0; $i < 10; $i++): ?>
                                    <li><?php echo htmlspecialchars($predictions[$i]); ?></li>
                                <?php endfor; ?>
                            </ul>
                        <?php else: ?>
                            <ul>
                                <?php foreach ($predictions as $pred): ?>
                                    <li><?php echo htmlspecialchars($pred); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <h4>Prediction Result:</h4>
                        <p><?php echo htmlspecialchars($prediction_result); ?></p>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-primary btn-lg">Back to Home</a>
                    <?php if ($prediction_type === 'manual'): ?>
                        <a href="manual_input.php" class="btn btn-secondary btn-lg ml-2">New Prediction</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>