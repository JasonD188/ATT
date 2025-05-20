<?php

$servername = "localhost"; 
$username = "root";      
$password = "";            
$dbname = "your_database";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT name FROM subjects";  
$result = $conn->query($sql);


if ($result->num_rows > 0) {

    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row['name'];
    }
} else {
    $subjects = []; 
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subjects</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        #loadingBar {
            width: 0;
            height: 4px;
            background-color: #4CAF50;
            transition: width 2s ease-in-out;
        }
    </style>
</head>
<body class="flex items-center justify-center h-screen bg-cover bg-center" style="background-image: url('pictures/background.jpg');">
    <div class="bg-white shadow-lg rounded-lg p-8 max-w-md w-full">
  
        <div class="mb-4 text-center">
            <button><i class="fas fa-home text-blue-600 text-4xl"></i></button>
        </div>
  

        <h2 class="text-2xl font-bold mb-4 text-center text-blue-600">SUBJECTS</h2>
        <div id="loadingBar" class="mb-4"></div>
        <div class="space-y-4">
            <?php
 
            foreach ($subjects as $subject) {
                echo '<button onclick="startLoadingBar(\'indexsection.html\')" class="w-full bg-blue-500 text-white font-semibold py-2 rounded hover:bg-blue-600 transition duration-200 hover:underline">' . htmlspecialchars($subject) . '</button>';
            }
            ?>
        </div>

  
        <div class="mt-4">
            <button onclick="window.history.back()" class="w-full bg-gray-500 text-white font-semibold py-2 rounded hover:bg-gray-600 transition duration-200">
                Back
            </button>
        </div>
    </div>

    <script>
        function startLoadingBar(url) {
            var loadingBar = document.getElementById('loadingBar');
            loadingBar.style.width = '100%';

            setTimeout(function() {
                window.location.href = url;
            }, 2000);
        }
    </script>
</body>
</html>
