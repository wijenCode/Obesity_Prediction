<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual Input - Obesity Prediction System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'header-blue': '#4169e1'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-900 min-h-screen">
    <div class="container mx-auto px-4 py-10 max-w-6xl">
        <div class="bg-form-bg rounded-lg shadow-xl overflow-hidden">
            <div class="bg-header-blue text-white px-8 py-10">
                <h2 class="text-4xl font-bold">Enter Your Data</h2>
                <p class="mt-2 text-lg">Fill in all fields to get an obesity prediction.</p>
            </div>
            <div class="p-8 bg-gray-800">
                <form action="process_manual.php" method="post" id="predictionForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-2xl font-bold mb-6 text-white">Personal Information</h4>
                            
                            <div class="mb-6">
                                <label for="Gender" class="block mb-2 text-base font-medium text-gray-300">Gender</label>
                                <select class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="Gender" name="Gender" required>
                                    <option value="">Select...</option>
                                    <option value="Female">Female</option>
                                    <option value="Male">Male</option>
                                </select>
                            </div>
                            
                            <div class="mb-6">
                                <label for="Age" class="block mb-2 text-base font-medium text-gray-300">Age (1-100 tahun)</label>
                                <input type="number" class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="Age" name="Age" min="0" max="100" step="1" required>
                            </div>
                            
                            <div class="mb-6">
                                <label for="Height" class="block mb-2 text-base font-medium text-gray-300">Height (meters ex: 1,75)</label>
                                <input type="number" class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="Height" name="Height" min="0.5" max="2.5" step="0.01" required>
                            </div>
                            
                            <div class="mb-6">
                                <label for="Weight" class="block mb-2 text-base font-medium text-gray-300">Weight (kg) (Min: 20Kg, Maks: 300Kg)</label>
                                <input type="number" class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="Weight" name="Weight" min="20" max="300" step="0.1" required>
                            </div>
                            
                            <div class="mb-6">
                                <label for="family_history_of_overweight" class="block mb-2 text-base font-medium text-gray-300">Family History of Overweight</label>
                                <select class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="family_history_of_overweight" name="family_history_of_overweight" required>
                                    <option value="">Select...</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-2xl font-bold mb-6 text-white">Eating Habits</h4>
                            
                            <div class="mb-6">
                                <label for="FAVC" class="block mb-2 text-base font-medium text-gray-300">Do you eat high caloric food frequently?</label>
                                <select class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="FAVC" name="FAVC" required>
                                    <option value="">Select...</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            
                            <div class="mb-6">
                                <label for="FCVC" class="block mb-2 text-base font-medium text-gray-300">Frequency of consumption of vegetables (1-3)</label>
                                <input type="number" class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="FCVC" name="FCVC" min="1" max="3" step="0.01" required>
                                <p class="mt-1 text-sm text-gray-400">1: Never, 2: Sometimes, 3: Always</p>
                            </div>
                            
                            <div class="mb-6">
                                <label for="NCP" class="block mb-2 text-base font-medium text-gray-300">Number of main meals daily (1-4)</label>
                                <input type="number" class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="NCP" name="NCP" min="1" max="4" step="0.01" required>
                            </div>
                            
                            <div class="mb-6">
                                <label for="CAEC" class="block mb-2 text-base font-medium text-gray-300">Do you eat food between meals?</label>
                                <select class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="CAEC" name="CAEC" required>
                                    <option value="">Select...</option>
                                    <option value="no">No</option>
                                    <option value="Sometimes">Sometimes</option>
                                    <option value="Frequently">Frequently</option>
                                    <option value="Always">Always</option>
                                </select>
                            </div>
                            
                            <div class="mb-6">
                                <label for="SMOKE" class="block mb-2 text-base font-medium text-gray-300">Do you smoke?</label>
                                <select class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="SMOKE" name="SMOKE" required>
                                    <option value="">Select...</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                        <div>
                            <h4 class="text-2xl font-bold mb-6 text-white">Lifestyle</h4>
                            
                            <div class="mb-6">
                                <label for="CH2O" class="block mb-2 text-base font-medium text-gray-300">Daily water consumption (1-3 liters)</label>
                                <input type="number" class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="CH2O" name="CH2O" min="1" max="3" step="0.01" required>
                            </div>
                            
                            <div class="mb-6">
                                <label for="SCC" class="block mb-2 text-base font-medium text-gray-300">Do you monitor calorie consumption?</label>
                                <select class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="SCC" name="SCC" required>
                                    <option value="">Select...</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </div>
                            
                            <div class="mb-6">
                                <label for="FAF" class="block mb-2 text-base font-medium text-gray-300">Physical activity frequency (0-3 days/week)</label>
                                <input type="number" class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="FAF" name="FAF" min="0" max="3" step="0.01" required>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-2xl font-bold mb-6 text-white">Additional Information</h4>
                            
                            <div class="mb-6">
                                <label for="TUE" class="block mb-2 text-base font-medium text-gray-300">Time using technology devices (0-2 hours/day)</label>
                                <input type="number" class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="TUE" name="TUE" min="0" max="2" step="0.01" required>
                            </div>
                            
                            <div class="mb-6">
                                <label for="CALC" class="block mb-2 text-base font-medium text-gray-300">Alcohol consumption</label>
                                <select class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="CALC" name="CALC" required>
                                    <option value="">Select...</option>
                                    <option value="no">No</option>
                                    <option value="Sometimes">Sometimes</option>
                                    <option value="Frequently">Frequently</option>
                                    <option value="Always">Always</option>
                                </select>
                            </div>
                            
                            <div class="mb-6">
                                <label for="MTRANS" class="block mb-2 text-base font-medium text-gray-300">Mode of transportation</label>
                                <select class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="MTRANS" name="MTRANS" required>
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
                    
                    <div class="mt-8">
                        <label for="model" class="block mb-2 text-xl font-bold text-white">Select Prediction Model:</label>
                        <select class="w-full bg-gray-700 border border-gray-700 text-white rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500" id="model" name="model" required>
                            <option value="SVM">Support Vector Machine (SVM)</option>
                            <option value="KNN">K-Nearest Neighbors (KNN)</option>
                            <option value="DT">Decision Tree</option>
                            <option value="NN">Neural Network</option>
                        </select>
                    </div>
                    
                    <div class="mt-10 text-center">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-8 rounded-lg text-lg transition duration-300 transform hover:scale-105">Get Prediction</button>
                        <a href="index.php" class="bg-gray-700 hover:bg-gray-800 text-white font-bold py-4 px-8 rounded-lg ml-4 text-lg transition duration-300">Go Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>