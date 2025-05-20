<?php

$host = 'localhost';
$db = 'school';
$user = 'root'; 
$pass = ''; 


$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role']; 
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];

  
    $stmt = $conn->prepare("SELECT * FROM users WHERE role = ? AND identifier = ?");
    $stmt->bind_param("ss", $role, $identifier);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // Validate password
    if ($user && password_verify($password, $user['password'])) {

        session_start();
        $_SESSION['role'] = $user['role'];
        $_SESSION['identifier'] = $user['identifier'];
        header("Location: dashboard.php"); 
        exit;
    } else {
        echo "Invalid login credentials.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
    <title>Login Form</title>
    <style>
        #loadingBar {
            width: 0;
            height: 4px;
            background-color: #4CAF50;
            transition: width 2s ease-in-out;
        }
    </style>
</head>

<body class="bg-gradient-to-r from-blue-500 to-blue-600 min-h-screen flex items-center justify-center bg-cover bg-center"
    style="background-image: url('pictures/background.jpg');">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-md w-full">
        <h2 class="text-2xl font-bold text-center mb-6">LOGIN</h2>

        <div id="loadingBar" class="mb-4"></div>

        <form method="POST" action="login.php" id="loginForm">
            <div class="mb-4">
                <button type="button" id="studentBtn"
                    class="w-full bg-blue-500 text-white py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">STUDENT</button>
            </div>

            <div class="mb-4">
                <button type="button" id="teacherBtn"
                    class="w-full bg-blue-500 text-white py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">TEACHER</button>
            </div>

            <div id="studentFields" class="hidden">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="identifier">Student No.</label>
                    <input type="text" name="identifier" id="identifier" placeholder="Enter your student number"
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password"
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500" required>
                </div>
            </div>

            <div id="teacherFields" class="hidden">
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="identifier">Teacher No.</label>
                    <input type="text" name="identifier" id="identifier" placeholder="Enter your teacher number"
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter your password"
                        class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:border-blue-500" required>
                </div>
            </div>

            <div>
                <button type="submit" name="role" value="student" id="loginBtnStudent"
                    class="w-full bg-blue-500 text-white py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">STUDENT LOGIN</button>
                <button type="submit" name="role" value="teacher" id="loginBtnTeacher"
                    class="w-full bg-blue-500 text-white py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400 mt-2">TEACHER LOGIN</button>
            </div>
        </form>
    </div>

    <script>
        const studentBtn = document.getElementById("studentBtn");
        const teacherBtn = document.getElementById("teacherBtn");
        const studentFields = document.getElementById("studentFields");
        const teacherFields = document.getElementById("teacherFields");

        studentBtn.addEventListener("click", function () {
            studentFields.classList.remove("hidden");
            teacherFields.classList.add("hidden");
            document.getElementById("identifier").placeholder = "Enter your student number";
        });

        teacherBtn.addEventListener("click", function () {
            teacherFields.classList.remove("hidden");
            studentFields.classList.add("hidden");
            document.getElementById("identifier").placeholder = "Enter your teacher number";
        });
    </script>
</body>

</html>
