<?php
// Check if this is a POST request or initial page load
$is_post_request = ($_SERVER['REQUEST_METHOD'] === 'POST');

// Initialize variables to handle default visualization
$visualization_type = isset($_POST['visualization_type']) ? $_POST['visualization_type'] : 'column_distribution';
$selected_column = isset($_POST['selected_column']) ? $_POST['selected_column'] : 'Age';
$correlation_type = isset($_POST['correlation_type']) ? $_POST['correlation_type'] : 'heatmap';

// Get available column names from dataset (do this regardless of request type)
$columns_command = "python get_columns.py";
$columns_output = shell_exec($columns_command);
$columns = json_decode($columns_output, true);

if (!is_array($columns)) {
    $columns = ['Gender', 'Age', 'Height', 'Weight', 'family_history_of_overweight', 'FAVC', 'FCVC', 'NCP', 'CAEC', 'SMOKE', 'CH2O', 'SCC', 'FAF', 'TUE', 'CALC', 'MTRANS', 'Obesity'];
}

// Execute Python script to generate visualizations only if this is a POST request
// or if the image doesn't exist yet (first load)
$image_path = "";
$should_generate = $is_post_request || 
                  (!file_exists("images/column_visualization.png") && 
                   !file_exists("images/correlation_visualization.png"));

if ($should_generate) {
    if ($visualization_type == 'column_distribution') {
        $command = "python generate_column_visualization.py \"$selected_column\"";
        $output = shell_exec($command);
        $image_path = "images/column_visualization.png";
    } else {
        $command = "python generate_correlation.py \"$correlation_type\"";
        $output = shell_exec($command);
        $image_path = "images/correlation_visualization.png";
    }
} else {
    // Set the image path based on the visualization type even if we didn't generate a new image
    if ($visualization_type == 'column_distribution') {
        $image_path = "images/column_visualization.png";
    } else {
        $image_path = "images/correlation_visualization.png";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Visualization - Obesity Prediction</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function updateVisualization() {
            // Set a flag in session storage to indicate this is not the first load
            sessionStorage.setItem('formSubmitted', 'true');
            document.getElementById("visualization-form").submit();
        }

        // Check if page was just loaded or redirected from form submission
        window.onload = function() {
            // Only auto-submit if this is the first page load (not after a form submission)
            if (!window.location.search && !document.referrer.includes('visualisasi.php')) {
                setTimeout(function() {
                    document.getElementById("visualization-form").submit();
                }, 100);
            }
        };
    </script>
</head>
<body class="bg-[#0d1017] text-gray-100">
    <!-- Navbar -->
    <nav class="bg-white sticky top-0 shadow dark:bg-gray-800">
        <div class="container flex items-center justify-center p-6 mx-auto text-gray-600 capitalize dark:text-gray-300">
            <a href="index.php" class="text-lg border-b-2 border-transparent hover:text-gray-800 dark:hover:text-gray-200 hover:border-blue-500 mx-1.5 sm:mx-6">Beranda</a>
            <a href="visualisasi.php" class="text-lg text-gray-800 dark:text-gray-200 border-b-2 bg-color-white border-blue-500 mx-1.5 sm:mx-6">Visualisasi Data</a>
            <a href="form.php" class="text-lg border-b-2 border-transparent hover:text-gray-800 dark:hover:text-gray-200 hover:border-blue-500 mx-1.5 sm:mx-6">Prediksi</a>
        </div>
    </nav>

    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-8 text-center">Visualisasi Dataset Obesity</h1>
        
        <div class="bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
            <form id="visualization-form" method="POST" action="" class="">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="mb-4">
                        <label for="visualization_type" class="block text-lg font-medium mb-2">Visualization Type:</label>
                        <select id="visualization_type" name="visualization_type" onchange="updateVisualization()" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="column_distribution" <?php echo ($visualization_type == 'column_distribution') ? 'selected' : ''; ?>>Column Distribution</option>
                            <option value="correlation" <?php echo ($visualization_type == 'correlation') ? 'selected' : ''; ?>>Correlation Analysis</option>
                        </select>
                    </div>
                    
                    <div id="column-selector" class="mb-4" style="<?php echo ($visualization_type == 'column_distribution') ? '' : 'display: none;'; ?>">
                        <label for="selected_column" class="block text-lg font-medium mb-2">Select Column:</label>
                        <select id="selected_column" name="selected_column" onchange="updateVisualization()" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <?php foreach ($columns as $column): ?>
                                <option value="<?php echo $column; ?>" <?php echo ($selected_column == $column) ? 'selected' : ''; ?>><?php echo $column; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="correlation-selector" class="mb-4" style="<?php echo ($visualization_type == 'correlation') ? '' : 'display: none;'; ?>">
                        <label for="correlation_type" class="block text-lg font-medium mb-2">Correlation Type:</label>
                        <select id="correlation_type" name="correlation_type" onchange="updateVisualization()" class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="heatmap" <?php echo ($correlation_type == 'heatmap') ? 'selected' : ''; ?>>Correlation Heatmap</option>
                            <option value="pairplot" <?php echo ($correlation_type == 'pairplot') ? 'selected' : ''; ?>>Pairwise Correlation</option>
                            <option value="target_correlation" <?php echo ($correlation_type == 'target_correlation') ? 'selected' : ''; ?>>Correlation with Target</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="bg-gray-800 rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-4">
                <?php if ($visualization_type == 'column_distribution'): ?>
                    Distribution of <?php echo $selected_column; ?>
                <?php else: ?>
                    <?php 
                    if ($correlation_type == 'heatmap') echo 'Correlation Heatmap';
                    else if ($correlation_type == 'pairplot') echo 'Pairwise Feature Correlation';
                    else echo 'Features Correlation with Target';
                    ?>
                <?php endif; ?>
            </h2>
            
            <div class="flex justify-center">
                <?php if (file_exists($image_path)): ?>
                    <img src="<?php echo $image_path; ?>?v=<?php echo filemtime($image_path); ?>" alt="Visualization" class="max-w-full h-auto rounded-lg shadow-lg">
                <?php else: ?>
                    <div class="bg-gray-700 p-6 rounded-lg">
                        <p>Generating visualization... Please select a visualization type from the dropdown.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-6 text-gray-300">
                <h3 class="text-xl font-semibold mb-2">Insights:</h3>
                <p class="mb-4">
                    <?php if ($visualization_type == 'column_distribution'): ?>
                        Visualisasi ini menunjukkan distribusi fitur <?php echo $selected_column; ?>.
                        Memahami distribusi ini membantu mengidentifikasi pola dan pencilan yang dapat memengaruhi model prediksi.
                    <?php else: ?>
                        <?php if ($correlation_type == 'heatmap'): ?>
                            Peta panas menunjukkan koefisien korelasi antara semua fitur. 
                            Korelasi yang lebih kuat (mendekati 1 atau -1) diwakili oleh warna yang lebih gelap. 
                            Korelasi positif menunjukkan bahwa fitur-fitur meningkat bersama-sama, sedangkan korelasi negatif menunjukkan bahwa ketika satu fitur meningkat, fitur lainnya menurun.
                        <?php elseif ($correlation_type == 'pairplot'): ?>
                            Plot berpasangan menunjukkan hubungan antara beberapa fitur. 
                            Setiap scatterplot menunjukkan bagaimana dua fitur berinteraksi, membantu mengidentifikasi pola dan hubungan.
                        <?php else: ?>
                            Grafik ini menunjukkan kekuatan korelasi antara setiap fitur dan variabel target (Tingkat Obesitas). 
                            Fitur dengan nilai korelasi absolut yang lebih tinggi memiliki hubungan yang lebih kuat dengan target prediksi.
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('visualization_type').addEventListener('change', function() {
            if (this.value === 'column_distribution') {
                document.getElementById('column-selector').style.display = 'block';
                document.getElementById('correlation-selector').style.display = 'none';
            } else {
                document.getElementById('column-selector').style.display = 'none';
                document.getElementById('correlation-selector').style.display = 'block';
            }
        });
    </script>
</body>
</html>