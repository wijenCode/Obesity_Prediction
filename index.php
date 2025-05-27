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
            <a href="index.php" class="text-lg text-gray-800 dark:text-gray-200 border-b-2 bg-color-white border-blue-500 mx-1.5 sm:mx-6">home</a>

            <a href="visualisasi.php" class="text-lg border-b-2 border-transparent hover:text-gray-800 dark:hover:text-gray-200 hover:border-blue-500 mx-1.5 sm:mx-6">Data Visualisation</a>

            <a href="form.php" class="text-lg border-b-2 border-transparent hover:text-gray-800 dark:hover:text-gray-200 hover:border-blue-500 mx-1.5 sm:mx-6">Prediction</a>
        </div>
    </nav>

    <!-- Home Page (Dataset Overview) -->
    <div class="container mx-auto mt-12 px-16">
        <div class="bg-[#0d1017] text-white rounded-lg p-8 shadow-lg">
            <h1 class="text-4xl font-bold mb-4">Obesity Prediction App</h1>
            <p class="text-lg mb-4">This app predicts obesity levels based on lifestyle and personal data.</p>
            <hr class="my-4 border-gray-600">
            <p class="mt-16 mb-4">
                Obesity Prediction App adalah sebuah website yang dirancang untuk memprediksi kemungkinan seseorang mengalami obesitas berdasarkan berbagai fitur yang dimasukkan. Website ini menggunakan dataset obesitas yang mencakup faktor-faktor seperti informasi pribadi dan gaya hidup, seperti jenis kelamin, usia, tinggi badan, berat badan, kebiasaan makan, tingkat aktivitas fisik, dan kebiasaan lainnya. Dengan memasukkan fitur-fitur ini, website dapat memberikan prediksi yang cukup akurat mengenai tingkat obesitas seseorang, apakah termasuk berat badan normal, berlebih, atau bahkan obesitas.
            </p>
            <p class="mb-4">
                Website ini sangat bermanfaat bagi individu yang ingin mengetahui apakah mereka berisiko mengalami obesitas, sehingga mereka dapat melakukan langkah-langkah preventif seperti mengatur pola makan dan meningkatkan aktivitas fisik mereka untuk menjaga berat badan yang sehat. Dengan adanya prediksi ini, pengguna dapat lebih sadar akan gaya hidup mereka dan membuat keputusan yang lebih baik dalam upaya mencegah obesitas serta masalah kesehatan terkait.
            </p>

            <h4 class="text-2xl font-semibold text-blue-400 mt-12 mb-4">Dataset</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto border-collapse text-gray-100">
                    <thead class="bg-gray-700">
                        <tr>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">Gender</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">Age</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">Height</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">Weight</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">Family History</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">FAVC</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">FCVC</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">NCP</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">CAEC</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">SMOKE</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">CH2O</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">SCC</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">FAF</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">TUE</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">CALC</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">MTRANS</th>
                            <th class="px-4 py-2 border border-gray-500 border-opacity-50">Obesity</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800">
                        <?php
                            // Array of data
                            $data = [
                                ["Female", 21, 1.62, 64.0, "yes", "no", 2.0, 3.0, "Sometimes", "no", 2.0, "no", 0.0, 1.0, "no", "Public_Transportation", "Normal_Weight"],
                                ["Female", 21, 1.52, 56.0, "yes", "no", 3.0, 3.0, "Sometimes", "yes", 3.0, "yes", 3.0, 0.0, "Sometimes", "Public_Transportation", "Normal_Weight"],
                                ["Male", 23, 1.80, 77.0, "yes", "no", 2.0, 3.0, "Sometimes", "no", 2.0, "no", 1.0, 1.0, "no", "Public_Transportation", "Normal_Weight"],
                                ["Male", 27, 1.80, 87.0, "no", "no", 3.0, 3.0, "Sometimes", "no", 2.0, "no", 1.0, 1.0, "yes", "Walking", "Overweight_Level_I"],
                                ["Male", 22, 1.78, 89.8, "no", "no", 2.0, 1.0, "Sometimes", "no", 2.0, "yes", 2.0, 0.0, "Sometimes", "Public_Transportation", "Overweight_Level_II"]
                            ];

                            // Loop through the data array to generate table rows
                            foreach ($data as $row) {
                                echo "<tr>";
                                foreach ($row as $cell) {
                                    echo "<td class='px-4 py-2 border border-gray-500 border-opacity-50'>$cell</td>";
                                }
                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <p class="pt-8">
                Dataset ini diambil dari <a href="https://www.kaggle.com/datasets/ruchikakumbhar/obesity-prediction/data" target="_blank" class="text-blue-500 hover:underline">Kaggle</a>, dan disediakan oleh <strong>Ruchika Kumbhar</strong>.  
            </p>


            <div class="pt-12">
                <h2 class="text-2xl font-semibold text-blue-400 mb-4">Features</h2>
                    <ul class="list-disc space-y-2">
                        <li><span class="font-bold">Gender:</span> The gender of the individual (e.g., Male, Female).</li>
                        <li><span class="font-bold">Age:</span> Age of the individual (in years).</li>
                        <li><span class="font-bold">Height:</span> Height of the individual (in meters).</li>
                        <li><span class="font-bold">Weight:</span> Weight of the individual (in kilograms).</li>
                        <li><span class="font-bold">Family_History:</span> Indicates whether the individual has a family member who has suffered or suffers from being overweight (Yes/No).</li>
                        <li><span class="font-bold">FAVC:</span> Frequency of consuming high-caloric food (Yes/No).</li>
                        <li><span class="font-bold">FCVC:</span> Frequency of consuming vegetables during meals (e.g., Always, Sometimes, Never).</li>
                        <li><span class="font-bold">NCP:</span> Number of main meals consumed daily (e.g., 1, 2, 3, or more).</li>
                        <li><span class="font-bold">CAEC:</span> Frequency of eating food between meals (e.g., no, Sometimes, Frequently, Always).</li>
                        <li><span class="font-bold">SMOKE:</span> Smoking habit of the individual (Yes/No).</li>
                        <li><span class="font-bold">CH2O:</span> Daily water intake (e.g., &lt;1 liter, 1-2 liters, &gt;2 liters).</li>
                        <li><span class="font-bold">SCC:</span> Whether the individual monitors their daily calorie intake (Yes/No).</li>
                        <li><span class="font-bold">FAF:</span> Frequency of physical activity per week (e.g., None, 1-2 days, 3-4 days, Daily).</li>
                        <li><span class="font-bold">TUE:</span> Time spent using technological devices daily (e.g., in hours).</li>
                        <li><span class="font-bold">CALC:</span> Frequency of alcohol consumption (e.g., no, Sometimes, Frequently, Always).</li>
                        <li><span class="font-bold">MTRANS:</span> Main mode of transportation used by the individual (e.g., Public_Transportation, Walking, Automobile, Motorbike, Bike).</li>
                    </ul>

                    <h2 class="text-2xl font-semibold text-blue-400 mt-8 mb-4">Target Variable</h2>
                    <p class="font-bold text-lg">Obesity:</p>
                    <ul class="list-disc pl-6">
                        <li class="font-bold">Insufficient_Weight</li>
                        <li class="font-bold">Normal_Weight</li>
                        <li class="font-bold">Overweight_Level_I</li>
                        <li class="font-bold">Overweight_Level_II</li>
                        <li class="font-bold">Obesity_Type_I</li>
                        <li class="font-bold">Obesity_Type_II</li>
                        <li class="font-bold">Obesity_Type_III</li>
                    </ul>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>
