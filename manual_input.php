


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Input - Obesity Prediction System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h2>Enter Your Data</h2>
                <p>Fill in all fields to get an obesity prediction.</p>
            </div>
            <div class="card-body">
                <form action="process_manual.php" method="post" id="predictionForm">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Personal Information</h4>
                            
                            <div class="form-group">
                                <label for="Gender">Gender</label>
                                <select class="form-control" id="Gender" name="Gender" required>
                                    <option value="">Select...</option>
                                    <option value="Female">Female</option>
                                    <option value="Male">Male</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="Age">Age</label>
                                <input type="number" class="form-control" id="Age" name="Age" min="0" max="100" step="1" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="Height">Height (meters)</label>
                                <input type="number" class="form-control" id="Height" name="Height" min="0.5" max="2.5" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="Weight">Weight (kg)</label>
                                <input type="number" class="form-control" id="Weight" name="Weight" min="20" max="300" step="0.1" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="family_history_of_overweight">Family History of Overweight</label>
                                <select class="form-control" id="family_history_of_overweight" name="family_history_of_overweight" required>
                                    <option value="">Select...</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Eating Habits</h4>
                            
                            <div class="form-group">
                                <label for="FAVC">Do you eat high caloric food frequently?</label>
                                <select class="form-control" id="FAVC" name="FAVC" required>
                                    <option value="">Select...</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="FCVC">Frequency of consumption of vegetables (1-3)</label>
                                <input type="number" class="form-control" id="FCVC" name="FCVC" min="1" max="3" step="0.01" required>
                                <small class="form-text text-muted">1: Never, 2: Sometimes, 3: Always</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="NCP">Number of main meals daily (1-4)</label>
                                <input type="number" class="form-control" id="NCP" name="NCP" min="1" max="4" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="CAEC">Do you eat food between meals?</label>
                                <select class="form-control" id="CAEC" name="CAEC" required>
                                    <option value="">Select...</option>
                                    <option value="no">No</option>
                                    <option value="Sometimes">Sometimes</option>
                                    <option value="Frequently">Frequently</option>
                                    <option value="Always">Always</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="SMOKE">Do you smoke?</label>
                                <select class="form-control" id="SMOKE" name="SMOKE" required>
                                    <option value="">Select...</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h4>Lifestyle</h4>
                            
                            <div class="form-group">
                                <label for="CH2O">Daily water consumption (1-3 liters)</label>
                                <input type="number" class="form-control" id="CH2O" name="CH2O" min="1" max="3" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="SCC">Do you monitor calorie consumption?</label>
                                <select class="form-control" id="SCC" name="SCC" required>
                                    <option value="">Select...</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="FAF">Physical activity frequency (0-3 days/week)</label>
                                <input type="number" class="form-control" id="FAF" name="FAF" min="0" max="3" step="0.01" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h4>Additional Information</h4>
                            
                            <div class="form-group">
                                <label for="TUE">Time using technology devices (0-2 hours/day)</label>
                                <input type="number" class="form-control" id="TUE" name="TUE" min="0" max="2" step="0.01" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="CALC">Alcohol consumption</label>
                                <select class="form-control" id="CALC" name="CALC" required>
                                    <option value="">Select...</option>
                                    <option value="no">No</option>
                                    <option value="Sometimes">Sometimes</option>
                                    <option value="Frequently">Frequently</option>
                                    <option value="Always">Always</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="MTRANS">Mode of transportation</label>
                                <select class="form-control" id="MTRANS" name="MTRANS" required>
                                    <option value="">Select...</option>
                                    <option value="Automobile">Automobile</option>
                                    <option value="Bike">Bike</option>
                                    <option value="Motorbike">Motorbike</option>
                                    <option value="Public_Transportation">Public Transportation</option>
                                    <option value="Walking">Walking</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mt-4 text-center">
                        <button type="submit" class="btn btn-primary btn-lg">Get Prediction</button>
                        <a href="index.php" class="btn btn-secondary btn-lg ml-2">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>