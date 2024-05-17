
<?php
// Start PHP session
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the user's email from the session
$userName= $_SESSION['username'];
$userEmail = $_SESSION['useremail'];
?>
<?php
 
// Set the timezone to Kolkata/Chennai
date_default_timezone_set('Asia/Kolkata');
// Include PHPExcel classes
require 'PHPExcel/Classes/PHPExcel.php';
require 'PHPExcel/Classes/PHPExcel/IOFactory.php'; // Include IOFactory as well if needed

// Check if file is uploaded
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Connect to MySQL database
    $conn = new mysqli('localhost', 'root', '', 'powerdb', 3306); // Adjust as per your database details

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get uploaded file data
    $file = $_FILES['file']['tmp_name'];
    $userDept = $_SESSION['dept'];
    // Get the selected sheet date and location
    $sheetDate = isset($_POST['sheetDate']) ? $_POST['sheetDate'] : null;
    $location = isset($_POST['location']) ? $_POST['location'] : null;
    $updatinguser=$_SESSION['username'];
    // Get current date and time
    $currentDateTime = date("Y-m-d H:i:s");

    try {
        // Load the uploaded file using PHPExcel
        $objPHPExcel = PHPExcel_IOFactory::load($file);

        // Get the active sheet
        $sheet = $objPHPExcel->getActiveSheet();

        // Get the highest row and column numbers
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // Iterate through each row in the sheet starting from the third row
        for ($row = 2; $row <= $highestRow; $row++) {
            // Get cell values for each column
            $time = $sheet->getCell('A' . $row)->getValue();
            $date = $sheetDate; // Use the selected sheet date
            $departmentValue = $sheet->getCell('C' . $row)->getValue();
            $loadsechColumn = ($userDept === 'JLDC') ? 'POWER_GENERATION' : 'LOADSECH';
            
            // Insert data into department table with current date and time in UPDATED_ON column
            $deptSql = "INSERT INTO $userDept (TIME, DATE,$loadsechColumn, UPDATEDBY, UPDATED_ON, LOCATION) 
                        VALUES ('$time', '$date', '$departmentValue',  '$updatinguser','$currentDateTime', '$location')";
            if ($conn->query($deptSql) !== TRUE) {
                echo "Error: " . $deptSql . "<br>" . $conn->error;
            }
            $powerColumn = '';
            switch ($userDept) {
                case 'sms2':
                case 'SMS2':
                    $powerColumn = 'LOAD_SECH_SMS2';
                    break;
                case 'sms3':
                case 'SMS3':
                    $powerColumn = 'LOAD_SECH_SMS3';
                    break;
                case 'railmill':
                case 'RAILMILL':
                    $powerColumn = 'LOAD_SECH_RAILMILL';
                    break;
                case 'platemill':
                case 'PLATEMILL':
                    $powerColumn = 'LOAD_SECH_PLATEMILL';
                    break;
                case 'spm':
                case 'SPM':
                    $powerColumn = 'LOAD_SECH_SPM';
                    break;
                case 'nspl':
                case 'NSPL':
                    $powerColumn = 'LOAD_SECH_NSPL';
                    break;
                case 'jldc':
                case 'JLDC':
                    $powerColumn = 'POWER_GENERATION';
                    break;
                default:
                    // Handle other cases or errors
                    break;
            }
            if (!empty($powerColumn)) {
                // Check if the date and time combination exists in power_table
                $checkSql = "SELECT * FROM power_table WHERE DATE='$date' AND TIME='$time'";
                $checkResult = $conn->query($checkSql);
                if ($checkResult->num_rows > 0) {
                    // Update existing row
                    $updateSql = "UPDATE power_table SET $powerColumn='$departmentValue' WHERE DATE='$date' AND TIME='$time'";
                    if ($conn->query($updateSql) !== TRUE) {
                        echo "Error updating power_table: " . $conn->error;
                    }
                } else {
                    // Insert new row
                    $insertSql = "INSERT INTO power_table (DATE, TIME, $powerColumn) VALUES ('$date', '$time', '$departmentValue')";
                    if ($conn->query($insertSql) !== TRUE) {
                        echo "Error inserting into power_table: " . $conn->error;
                    }
                }
            }

        }
        echo "Data inserted successfully!";
    } catch (Exception $e) {
        //echo 'Error: ' . $e->getMessage();
    }

    // Close database connection
    $conn->close();
} else {
    // Redirect the user back to the same page without any warning messages
    // header("Location: index.php"); // Comment out this line for debugging
    // exit();
    //echo "Debug: No file uploaded or invalid request."; // Debug statement
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .form-group {
            display: flex;
            align-items: center;
        }
        .custom-file-input {
            visibility: hidden;
            width: 0;
        }
        .custom-file-label::after {
            content: "Choose File";
        }
        #myTable {
            overflow-x: auto; /* Enable horizontal scrolling */
        }
        /* Styling for the table */
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 2px solid #e8e4e4;
            padding: 8px;
            text-align: left;
        }
        th,
        tr:nth-child(-n+1) {
            background-color: #d0a4a4;
            font-weight: bold;
        }
        tr:not(:nth-child(-n+1)):hover {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-white">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="images/Jindal logo Revised.png" width="100" alt=""></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02" aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse " id="navbarTogglerDemo02">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fa-solid fa-user"></i> <?php echo $userEmail; ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row px-3" style="height: 100px; background-image: url('images/banner.jpg'); background-size: cover; position: relative; background-repeat: no-repeat; background-position: top;">
            <h5 class="text-start"></h5>
        </div>
    </div>

    <section class="ftco-section px-3 py-3" style="background: rgb(177,176,160); background: linear-gradient(90deg, rgba(177,176,160,1) 0%, #d0a474 100%); border: none;">
        <div class="container-fluid">
            <div class="row">
                <div class="card mb-2" style="background: rgb(177,176,160); background: linear-gradient(90deg, rgba(177,176,160,1) 0%, rgba(236,177,109,1) 100%); border: none;">
                    <div class="card-body d-flex justify-content-end gap-2">
                        <!-- File upload button -->
                        <label for="fileInput" class="btn btn-success"><i class="fa fa-upload"></i> Upload</label>
                        <input type="file" id="fileInput" class="custom-file-input" accept=".xlsx,.xls" style="display: none;">

                        <!-- Export to Excel button -->
                        <button type="button" class="btn btn-dark" id="exportBtn"> <i class="fa fa-file-excel-o" aria-hidden="true"></i> Export to Excel </button>

                        <!-- Update Database button -->
                        <button class="btn btn-primary" id="updateDatabaseBtn">Update Database</button>
                    </div>
                </div>
                <div class="container-fluid mb-3">
                    <div class="row">
                        <div class="col-auto">
                            <label for="locationSelect" class="form-label">Select Location:</label>
                            <select class="form-select" id="locationSelect">
                                <option value="" selected disabled>Select Location</option>
                                <option value="ANGUL">ANGUL</option>
                                <option value="RAIGARH">RAIGARH</option>
                                <option value="PATRATU">PATRATU</option>
                                <option value="TAMNAR">TAMNAR</option>
                                <option value="NALWA">NALWA</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <label for="sheetDate" class="form-label">Select Sheet Date:</label>
                            <input type="date" class="form-control" id="sheetDate">
                        </div>
                    </div>
                </div>


                <!-- Table container -->
                <div class="card" id="myTable" style="padding: 20px; height:61vh; overflow-y: auto;">
                    <!-- Table content will be dynamically added here -->
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.3/xlsx.full.min.js"></script>

    <script>
        document.getElementById('fileInput').addEventListener('change', function(e) {
            var file = e.target.files[0];
            var reader = new FileReader();
            reader.readAsBinaryString(file);
            reader.onload = function(e) {
                var data = e.target.result;
                var workbook = XLSX.read(data, { type: 'binary' });
                var sheetName = workbook.SheetNames[0];
                var sheet = workbook.Sheets[sheetName];
                var htmlTable = XLSX.utils.sheet_to_html(sheet);

                document.getElementById('myTable').innerHTML = htmlTable;

                // Calculate and update column 6 and column 11 values
                updateCalculatedValues();
            };
        });

        document.getElementById('updateDatabaseBtn').addEventListener('click', function() {
    var sheetDate = document.getElementById('sheetDate').value; // Get selected sheet date
    var location = document.getElementById('locationSelect').value;
    if (!location || location === "") {
        alert('Please select a location.');
        return;
    }
    if (!sheetDate) {
        alert('Please select a sheet date.');
        return;
    }

    var formData = new FormData();
    formData.append('file', document.getElementById('fileInput').files[0]);
    formData.append('sheetDate', sheetDate); // Add sheet date to form data
    formData.append('location', location);

    fetch('dept.php', { // Change the URL to index.php
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data); // Display the server response
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating database!');
    });
});

        document.getElementById('exportBtn').addEventListener('click', function() {
            var table = document.getElementById('myTable');
            if (table.children.length === 0) { // Check if table is empty
                alert('No data to export. Please upload a file first.');
                return;
            }

            var ws = XLSX.utils.table_to_sheet(table);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
            XLSX.writeFile(wb, 'exported_data.xlsx');
        });

    </script>
    <script>
        // Function to get URL parameter by name
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        // Check if there is a message in the URL
        var msg = getUrlParameter('msg');
        if (msg === 'wrong_password') {
            alert("Wrong password. Please try again.");
        } else if (msg === 'username_not_found') {
            alert("Username not found. You can register here.");
        }

        // email from url
        var email = getUrlParameter('email');
        document.getElementById('userEmail').textContent = email;
    </script>
</body>

</html>
