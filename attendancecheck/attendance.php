<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'student_attendance';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action_type'])) {
        $actionType = $_POST['action_type'];
        $studentNumber = $_POST['student_number'];
        $studentName = $_POST['student_name'];
        $studentType = $_POST['student_type'];

        if ($actionType === 'add') {
            $stmt = $conn->prepare("INSERT INTO students (student_number, student_name, student_type) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $studentNumber, $studentName, $studentType);
            $stmt->execute();
        } elseif ($actionType === 'edit' && isset($_POST['student_id'])) {
            $studentId = $_POST['student_id'];
            $stmt = $conn->prepare("UPDATE students SET student_number = ?, student_name = ?, student_type = ? WHERE id = ?");
            $stmt->bind_param("sssi", $studentNumber, $studentName, $studentType, $studentId);
            $stmt->execute();
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Students</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script>
        function updateTotalAttendance(rowIndex) {
            let totalAttendance = 0;
            const checkboxes = document.querySelectorAll(`#row-${rowIndex} input[type="checkbox"]`);
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    totalAttendance++;
                }
            });
            const attendancePercentage = (totalAttendance / 12) * 100;
            document.getElementById(`total-${rowIndex}`).textContent = totalAttendance;
            document.getElementById(`percentage-${rowIndex}`).textContent = `${attendancePercentage.toFixed(2)}%`;
        }

        function updateDateTime() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', hour12: true };
            const dateTimeSting = now.toLocaleDateString('en-US', options);
            document.getElementById('current-time').textContent = dateTimeSting;
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();

        function showStudentModal(actionType, rowIndex = null) {
            const modal = document.getElementById('student-modal');
            const studentNumberInput = document.getElementById('student-number');
            const studentNameInput = document.getElementById('student-name');
            const studentTypeInput = document.getElementById('student-type');
            const actionTypeInput = document.getElementById('action-type');

            if (actionType === 'edit' && rowIndex !== null) {
                const row = document.getElementById(`row-${rowIndex}`);
                studentNumberInput.value = row.children[0].textContent;
                studentNameInput.value = row.children[1].textContent;
                studentTypeInput.value = row.children[1].dataset.type;
                actionTypeInput.value = 'edit';
                document.getElementById('edit-row-index').value = rowIndex;
            } else {
                studentNumberInput.value = '';
                studentNameInput.value = '';
                studentTypeInput.value = 'regular';
                actionTypeInput.value = 'add';
                document.getElementById('edit-row-index').value = '';
            }
            modal.classList.remove('hidden');
        }

        function handleStudentAction() {
            const actionType = document.getElementById('action-type').value;
            const studentNumber = document.getElementById('student-number').value;
            const studentName = document.getElementById('student-name').value;
            const studentType = document.getElementById('student-type').value;
            const rowIndex = document.getElementById('edit-row-index').value;

            if (!studentNumber || !studentName) {
                alert("Please fill in all fields.");
                return;
            }

            const form = document.getElementById('student-form');
            form.action = actionType === 'add' ? 'add_student.php' : 'edit_student.php';
            form.submit();
        }

        function closeStudentModal() {
            document.getElementById('student-modal').classList.add('hidden');
        }

        function removeStudent(rowIndex) {
            const row = document.getElementById(`row-${rowIndex}`);
            row.remove();
        }

        function showDateModal(action) {
            const dateModal = document.getElementById('date-modal');
            const modalActionText = document.getElementById('modal-action');
            const modalDateTime = document.getElementById('modal-date-time');

            modalActionText.textContent = action === 'save' ? 'Save' : 'Report';
            const currentDateTime = new Date().toLocaleString();
            modalDateTime.textContent = `Current Date and Time: ${currentDateTime}`;

            document.getElementById('modal-date-picker').value = new Date().toISOString().split('T')[0];
            dateModal.classList.remove('hidden');
        }

        function handleDateAction() {
            const selectedDate = document.getElementById('modal-date-picker').value;
            if (!selectedDate) {
                alert('Please select a date!');
                return;
            }

            const action = document.getElementById('modal-action').textContent.toLowerCase();
            alert(`${action.charAt(0).toUpperCase() + action.slice(1)} successful on ${selectedDate}`);
            closeDateModal();
        }

        function closeDateModal() {
            document.getElementById('date-modal').classList.add('hidden');
        }
    </script>
</head>

<body class="p-10 flex items-center justify-center h-screen bg-cover bg-center" style="background-image: url('pictures/background.jpg');">
    <div class="bg-white shadow-lg rounded-lg overflow-hidden w-full max-w-4xl">
        <div class="bg-blue-600 text-white text-2xl py-4 flex items-center">
            <span id="current-time" class="absolute text-white text-sm ml-4 text-yellow-500"></span>
            <span class="flex items-center flex-grow justify-center text-center">LIST OF STUDENTS</span>
        </div>

        <table class="min-w-full border-collapse border border-gray-300">
            <thead>
                <tr class="bg-blue-200">
                    <th class="border border-gray-300 px-4 py-2">Student no.</th>
                    <th class="border border-gray-300 px-4 py-2">Name of Students</th>
                    <th class="border border-gray-300 px-4 py-2">Student Type</th>
                    <th class="border border-gray-300 px-4 py-2">M</th>
                    <th class="border border-gray-300 px-4 py-2">T</th>
                    <th class="border border-gray-300 px-4 py-2">W</th>
                    <th class="border border-gray-300 px-4 py-2">Th</th>
                    <th class="border border-gray-300 px-4 py-2">F</th>
                    <th class="border border-gray-300 px-4 py-2">Total Attendance</th>
                    <th class="border border-gray-300 px-4 py-2">Percentage</th>
                    <th class="border border-gray-300 px-4 py-2">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-gray-100" id="row-1">
                    <td class="border border-gray-300 px-4 py-2">232323</td>
                    <td class="border border-gray-300 px-4 py-2" data-type="regular">Luyas</td>
                    <td class="border border-gray-300 px-4 py-2">Regular</td>
                    <td class="border border-gray-300 px-4 py-2"><input type="checkbox" onchange="updateTotalAttendance(1)"></td>
                    <td class="border border-gray-300 px-4 py-2"><input type="checkbox" onchange="updateTotalAttendance(1)"></td>
                    <td class="border border-gray-300 px-4 py-2"><input type="checkbox" onchange="updateTotalAttendance(1)"></td>
                    <td class="border border-gray-300 px-4 py-2"><input type="checkbox" onchange="updateTotalAttendance(1)"></td>
                    <td class="border border-gray-300 px-4 py-2"><input type="checkbox" onchange="updateTotalAttendance(1)"></td>
                    <td class="border border-gray-300 px-4 py-2" id="total-1">0</td>
                    <td class="border border-gray-300 px-4 py-2" id="percentage-1">0.00%</td>
                    <td class="border border-gray-300 px-4 py-2">
                        <button class="text-blue-500" onclick="showStudentModal('edit', 1)">Edit</button>
                        <button class="text-red-500" onclick="removeStudent(1)">Remove</button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="flex justify-center space-x-4 p-4">
            <button class="w-full bg-gray-500 text-white font-semibold py-2 rounded hover:bg-gray-600 transition duration-200" onclick="window.history.back()">BACK</button>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="showStudentModal('add')">Add Student</button>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="showDateModal('save')">SAVE</button>
            <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="showDateModal('report')">REPORT</button>
        </div>
    </div>

    <!-- Student Modal -->
    <div id="student-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h3 class="text-xl mb-4">Add/Edit Student</h3>
            <form id="student-form" method="POST">
                <input type="text" id="student-number" name="student_number" class="border border-gray-300 p-2 w-full mb-4" placeholder="Student Number">
                <input type="text" id="student-name" name="student_name" class="border border-gray-300 p-2 w-full mb-4" placeholder="Student Name">
                <select id="student-type" name="student_type" class="border border-gray-300 p-2 w-full mb-4">
                    <option value="regular">Regular</option>
                    <option value="irregular">Irregular</option>
                </select>
                <input type="hidden" id="action-type" name="action_type">
                <input type="hidden" id="edit-row-index" name="student_id">
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeStudentModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
                    <button type="submit" onclick="handleStudentAction()" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Date Modal -->
    <div id="date-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-50 flex justify-center items-center">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h3 class="text-xl mb-4" id="modal-action"></h3>
            <input type="date" id="modal-date-picker" class="border border-gray-300 p-2 w-full mb-4">
            <p class="text-lg mb-4" id="modal-date-time"></p>
            <div class="flex justify-end">
                <button onclick="closeDateModal()" class="bg-gray-500 text-white px-4 py-2 rounded">Close</button>
                <button onclick="handleDateAction()" class="bg-blue-500 text-white px-4 py-2 rounded">Save</button>
            </div>
        </div>
    </div>
</body>

</html>
