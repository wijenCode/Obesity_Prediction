<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obesity Prediction System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="jumbotron">
            <h1 class="display-4">Obesity Prediction System</h1>
            <p class="lead">This system predicts obesity levels based on lifestyle and personal data.</p>
            <hr class="my-4">
            <p>Choose one of the following options to get started:</p>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Manual Input</h5>
                            <p class="card-text">Enter your personal data manually through a form.</p>
                            <a href="manual_input.php" class="btn btn-primary">Go to Form</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">CSV Upload</h5>
                            <p class="card-text">Upload a CSV file with your data for prediction.</p>
                            <form action="process_upload.php" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="file" class="form-control-file" id="csvFile" name="csvFile" accept=".csv" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload and Predict</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <h4>CSV File Format</h4>
                <p>Your CSV file should include the following columns in this order:</p>
                <ul>
                    <li>Gender (Female/Male)</li>
                    <li>Age (number)</li>
                    <li>Height (meters, e.g., 1.75)</li>
                    <li>Weight (kg)</li>
                    <li>family_history_of_overweight (yes/no)</li>
                    <li>FAVC (yes/no) - Frequent consumption of high caloric food</li>
                    <li>FCVC (1-3) - Frequency of consumption of vegetables</li>
                    <li>NCP (1-4) - Number of main meals</li>
                    <li>CAEC (no/Sometimes/Frequently/Always) - Consumption of food between meals</li>
                    <li>SMOKE (yes/no)</li>
                    <li>CH2O (1-3) - Consumption of water daily</li>
                    <li>SCC (yes/no) - Calories consumption monitoring</li>
                    <li>FAF (0-3) - Physical activity frequency</li>
                    <li>TUE (0-2) - Time using technology devices</li>
                    <li>CALC (no/Sometimes/Frequently/Always) - Consumption of alcohol</li>
                    <li>MTRANS (Automobile/Bike/Motorbike/Public_Transportation/Walking) - Transportation used</li>
                </ul>
                <p>Download a <a href="assets/sample.csv">sample CSV file</a>.</p>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>