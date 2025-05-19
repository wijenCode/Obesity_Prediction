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
                        <p class="mb-3">Unggah file CSV dengan data Anda untuk prediksi.</p>
                        <form action="process_upload.php" method="post" enctype="multipart/form-data">
                            <div class="mb-4">
                                <input type="file" class="border border-gray-600 p-2 rounded-lg w-full text-gray-700" id="csvFile" name="csvFile" accept=".csv" required>
                            </div>
                            <div class="mb-6">
                                <label for="model" class="block mb-2">Pilih Model Prediksi:</label>
                                <select class="border border-gray-600 p-2 rounded-lg w-full text-gray-700" id="model" name="model" required>
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
                            <p class="mb-4">Your CSV file should include the following columns in this order:</p>
                            <ul class="list-disc pl-5">
                                <li>Gender (Female/Male)</li>
                                <li>Age (number)</li>
                                <li>Height (meters, e.g., 1.75)</li>
                                <li>Weight (kg)</li>
                                <li>Family History of Overweight (yes/no)</li>
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
                            <p class="mt-8">Download a <a href="assets/sample.csv" class="text-blue-400 hover:underline">sample CSV file</a>.</p>
                        </div>
                    </div>
                </div>
            </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
