<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Obesity Prediction System</title>
    <script src="https://cdn.tailwindcss.com"></script> <!-- Tailwind CSS CDN -->
</head>
<body class="bg-[#0d1017] text-gray-100">

    <!-- Navbar -->
    <nav class="bg-white sticky top-0 shadow dark:bg-gray-800">
        <div class="container flex items-center justify-center p-6 mx-auto text-gray-600 capitalize dark:text-gray-300">
            <a href="index.php" class="text-lg border-b-2 border-transparent hover:text-gray-800 dark:hover:text-gray-200 hover:border-blue-500 mx-1.5 sm:mx-6">home</a>

            <a href="visualisasi.php" class="text-lg border-b-2 border-transparent hover:text-gray-800 dark:hover:text-gray-200 hover:border-blue-500 mx-1.5 sm:mx-6">Data Visualisation</a>

            <a href="form.php" class="text-lg text-gray-800 dark:text-gray-200 border-b-2 border-blue-500 mx-1.5 sm:mx-6">Prediction</a>
        </div>
    </nav>

    <!-- Error Messages -->
    <?php if (isset($_GET['error'])): ?>
        <div class="container mx-auto mt-4 px-16">
            <div class="bg-red-500 text-white p-4 rounded-lg">
                <?php
                switch ($_GET['error']) {
                    case 'upload':
                        echo "Error uploading file. Please try again.";
                        break;
                    case 'nomodel':
                        echo "Please select a prediction model.";
                        break;
                    case 'invalidmodel':
                        echo "Invalid model selected.";
                        break;
                    case 'move':
                        echo "Error processing uploaded file.";
                        break;
                    case 'columns':
                        $missing = $_GET['missing'] ?? 'unknown';
                        echo "Missing required columns: " . htmlspecialchars($missing);
                        break;
                    case 'read':
                        echo "Error reading CSV file. Please check file format.";
                        break;
                    case 'prediction':
                        $message = $_GET['message'] ?? 'Unknown error';
                        echo "Prediction error: " . htmlspecialchars($message);
                        break;
                    default:
                        echo "An error occurred. Please try again.";
                }
                ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Prediction Page (Manual Input and CSV Upload) -->
    <div class="container mx-auto mt-12 px-16">
        <div class="text-white rounded-lg p-8 shadow-lg">
            <h1 class="text-4xl font-bold mb-4">Prediction</h1>
            <hr class="my-4 border-gray-600">
            <p class="mt-12 mb-4">Pilih bagaimana Anda ingin membuat prediksi:</p>

            <div class="grid gap-12">
                <!-- Manual Input Form -->
                <div>
                    <div class="card bg-gray-800 text-white pb-6 p-4 rounded-lg shadow-md">
                        <h5 class="text-2xl font-semibold mb-3">Manual Input</h5>
                        <p class="mb-6">Masukkan data pribadi dan gaya hidup Anda secara manual melalui formulir.</p>
                        <a href="manual_input.php" class="bg-blue-500 text-white py-2 px-6 rounded-full hover:bg-blue-600">Go to Form</a>
                    </div>
                </div>

                <!-- CSV Upload Form -->
                <div>
                    <div class="card bg-gray-800 text-white p-4 rounded-lg shadow-md">
                        <h5 class="text-2xl font-semibold mb-3">CSV Upload</h5>
                        <p class="mb-3">Unggah file CSV dengan data Anda untuk prediksi batch.</p>
                        
                        <!-- Important Notice -->
                        <div class="bg-blue-600 text-white p-4 rounded-md mb-4">
                            <h6 class="font-semibold mb-2">📊 Fitur Evaluasi Akurasi</h6>
                            <p class="text-sm">
                                Jika file CSV Anda mengandung kolom <strong>"Obesity"</strong> dengan label sebenarnya, 
                                sistem akan secara otomatis menghitung akurasi prediksi dan menyediakan analisis perbandingan!
                            </p>
                        </div>
                        
                        <form action="process_upload.php" method="post" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label for="csvFile" class="block mb-2 font-semibold">Upload CSV File:</label>
                                <input type="file" class="border border-gray-600 p-2 rounded-lg w-full text-white bg-gray-700" id="csvFile" name="csvFile" accept=".csv" required>
                            </div>
                            <div class="mb-6">
                                <label for="model" class="block mb-2 font-semibold">Pilih Model Prediksi:</label>
                                <select class="border border-gray-600 p-2 rounded-lg w-full text-gray-100 bg-gray-700" id="model" name="model" required>
                                    <option value="SVM">Support Vector Machine (SVM)</option>
                                    <option value="KNN">K-Nearest Neighbors (KNN)</option>
                                    <option value="DT">Decision Tree</option>
                                    <option value="NN">Neural Network</option>
                                </select>
                            </div>
                            <button type="submit" class="bg-blue-500 text-white py-2 px-6 rounded-full hover:bg-blue-600">Upload and Predict</button>
                        </form>

                        <div class="mt-16">
                            <h4 class="text-2xl font-semibold mb-4">CSV File Format</h4>
                            
                            <!-- Two types of CSV formats -->
                            <div class="grid md:grid-cols-2 gap-6 mb-6">
                                <!-- Unlabeled CSV -->
                                <div class="bg-gray-700 p-4 rounded-lg">
                                    <h5 class="text-lg font-semibold mb-3 text-green-400">📄 Format 1: Tanpa Label (Untuk Prediksi)</h5>
                                    <p class="text-sm mb-3">File CSV hanya berisi data fitur tanpa kolom label:</p>
                                    <ul class="list-disc pl-5 text-sm space-y-1">
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
                                </div>
                                
                                <!-- Labeled CSV -->
                                <div class="bg-gray-700 p-4 rounded-lg border-2 border-yellow-500">
                                    <h5 class="text-lg font-semibold mb-3 text-yellow-400">🎯 Format 2: Dengan Label (Untuk Evaluasi)</h5>
                                    <p class="text-sm mb-3">File CSV berisi semua fitur + kolom label untuk menghitung akurasi:</p>
                                    <div class="text-sm mb-3">
                                        <p><strong>Semua kolom dari Format 1 +</strong></p>
                                    </div>
                                    <div class="bg-yellow-600 text-gray-900 p-3 rounded">
                                        <p class="font-semibold">Obesity (Label Column):</p>
                                        <ul class="list-disc pl-5 text-xs mt-1">
                                            <li>Insufficient_Weight</li>
                                            <li>Normal_Weight</li>
                                            <li>Overweight_Level_I</li>
                                            <li>Overweight_Level_II</li>
                                            <li>Obesity_Type_I</li>
                                            <li>Obesity_Type_II</li>
                                            <li>Obesity_Type_III</li>
                                        </ul>
                                    </div>
                                    <div class="mt-3 text-xs text-yellow-200">
                                        <p><strong>Keuntungan Format 2:</strong></p>
                                        <ul class="list-disc pl-4">
                                            <li>Menampilkan persentase akurasi</li>
                                            <li>Perbandingan prediksi vs label asli</li>
                                            <li>Download hasil dalam format CSV/Excel</li>
                                            <li>Visualisasi prediksi yang benar/salah</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <h5 class="text-lg font-semibold mb-3">📥 Download Sample Files</h5>
                                <div class="flex flex-wrap gap-3">
                                    <a href="assets/sample.csv" class="bg-green-500 hover:bg-green-600 text-white py-2 px-4 rounded-lg text-sm inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        Sample Unlabeled CSV
                                    </a>
                                    <a href="assets/sample_labeled.csv" class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 py-2 px-4 rounded-lg text-sm inline-flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        Sample Labeled CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>