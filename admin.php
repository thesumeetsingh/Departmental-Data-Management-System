<?php
include 'supressError.php';
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the user's email from the session
$userName = $_SESSION['username'];
$userEmail = $_SESSION['useremail'];
$userDepartment = $_SESSION['dept'];

// Check if the user is not an admin
if ($userDepartment !== 'ADMIN') {
    // Redirect to index.php with an alert prompt
    echo "<script>
            alert('You are not an admin');
            window.location.href = 'index.php';
          </script>";
    exit();
}

// Set the timezone to Kolkata/Chennai
date_default_timezone_set('Asia/Kolkata');

// Check if data is posted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Connect to MySQL database
    include 'connection.php';

    // Get posted data
    $data = json_decode(file_get_contents('php://input'), true);

    // Extract the data
    $sheetDate = $data['sheetDate'];
    $location = $data['location'];
    $updatingUser = $_SESSION['username'];
    $currentDateTime = date("Y-m-d H:i:s");

    try {
        foreach ($data['rows'] as $row) {
            $time = $row['A'];
            $date = $sheetDate; // Use the selected sheet date
            $powerGeneration = $row['C'];
            $loadSechSMS2 = $row['D'];
            $loadSechSMS3 = $row['E'];
            $loadSechSMSTotal = $loadSechSMS2 + $loadSechSMS3;
            $loadSechRailMill = $row['G'];
            $loadSechPlateMill = $row['H'];
            $loadSechSPM = $row['I'];
            $loadSechNSPL = $row['J'];
            $total = $loadSechSMSTotal + $loadSechRailMill + $loadSechPlateMill + $loadSechSPM + $loadSechNSPL;

            // Check if a row with the same DATE and TIME already exists
            $checkQuery = "SELECT * FROM power_table WHERE DATE = '$date' AND TIME = '$time'";
            $checkResult = $conn->query($checkQuery);

            if ($checkResult->num_rows > 0) {
                // Update the existing row
                $updateQuery = "UPDATE power_table SET 
                                POWER_GENERATION = '$powerGeneration',
                                LOAD_SECH_SMS2 = '$loadSechSMS2',
                                LOAD_SECH_SMS3 = '$loadSechSMS3',
                                LOAD_SECH_SMS_TOTAL = '$loadSechSMSTotal',
                                LOAD_SECH_RAILMILL = '$loadSechRailMill',
                                LOAD_SECH_PLATEMILL = '$loadSechPlateMill',
                                LOAD_SECH_SPM = '$loadSechSPM',
                                LOAD_SECH_NSPL = '$loadSechNSPL',
                                TOTAL = '$total',
                                UPDATEDBY = '$updatingUser',
                                UPDATED_ON = '$currentDateTime',
                                LOCATION = '$location'
                                WHERE DATE = '$date' AND TIME = '$time'";
                if ($conn->query($updateQuery) !== TRUE) {
                    echo "Error updating record: " . $conn->error;
                }
            } else {
                // Insert a new row
                $insertQuery = "INSERT INTO power_table (TIME, DATE, POWER_GENERATION, LOAD_SECH_SMS2, LOAD_SECH_SMS3, LOAD_SECH_SMS_TOTAL, LOAD_SECH_RAILMILL, LOAD_SECH_PLATEMILL, LOAD_SECH_SPM, LOAD_SECH_NSPL, TOTAL, UPDATEDBY, UPDATED_ON, LOCATION) 
                                VALUES ('$time', '$date', '$powerGeneration', '$loadSechSMS2', '$loadSechSMS3', '$loadSechSMSTotal', '$loadSechRailMill', '$loadSechPlateMill', '$loadSechSPM', '$loadSechNSPL', '$total', '$updatingUser', '$currentDateTime', '$location')";
                if ($conn->query($insertQuery) !== TRUE) {
                    echo "Error inserting record: " . $conn->error;
                }
            }

            // Update or insert data into the specific tables based on the department
            $tables = ['jldc', 'nspl', 'sms', 'spm', 'railmill', 'platemill'];
            foreach ($tables as $table) {
                $loadSechColumn = '';
                $deptValue = '';
                switch ($table) {
                    case 'jldc':
                        $loadSechColumn = 'POWER_GENERATION';
                        $deptValue = $powerGeneration;
                        break;
                    case 'nspl':
                        $loadSechColumn = 'LOADSECH';
                        $deptValue = $loadSechNSPL;
                        break;
                    case 'sms':
                        $loadSechColumn = 'LOADSECH_SMS2';
                        $deptValue = $loadSechSMS2;
                        $loadSechColumn2 = 'LOADSECH_SMS3';
                        $deptValue2 = $loadSechSMS3;
                        break;
                    case 'spm':
                        $loadSechColumn = 'LOADSECH';
                        $deptValue = $loadSechSPM;
                        break;
                    case 'railmill':
                        $loadSechColumn = 'LOADSECH';
                        $deptValue = $loadSechRailMill;
                        break;
                    case 'platemill':
                        $loadSechColumn = 'LOADSECH';
                        $deptValue = $loadSechPlateMill;
                        break;
                }

                if ($table == 'sms') {
                    $checkTableQuery = "SELECT * FROM $table WHERE DATE = '$date' AND TIME = '$time'";
                    $checkTableResult = $conn->query($checkTableQuery);

                    if ($checkTableResult->num_rows > 0) {
                        // Update the existing row
                        $updateTableQuery = "UPDATE $table SET 
                                        LOADSECH_SMS2 = '$loadSechSMS2',
                                        LOADSECH_SMS3 = '$loadSechSMS3',
                                        UPDATEDBY = '$updatingUser',
                                        UPDATED_ON = '$currentDateTime',
                                        LOCATION = '$location'
                                        WHERE DATE = '$date' AND TIME = '$time'";
                        if ($conn->query($updateTableQuery) !== TRUE) {
                            echo "Error updating record: " . $conn->error;
                        }
                    } else {
                        // Insert a new row
                        $insertQuery = "INSERT INTO $table (TIME, DATE, LOADSECH_SMS2, LOADSECH_SMS3, UPDATEDBY, UPDATED_ON, LOCATION) 
                                        VALUES ('$time', '$date', '$loadSechSMS2', '$loadSechSMS3', '$updatingUser', '$currentDateTime', '$location')";
                        if ($conn->query($insertQuery) !== TRUE) {
                            echo "Error inserting record: " . $conn->error;
                        }
                    }
                } else {
                    $checkTableQuery = "SELECT * FROM $table WHERE DATE = '$date' AND TIME = '$time'";
                    $checkTableResult = $conn->query($checkTableQuery);

                    if ($checkTableResult->num_rows > 0) {
                        // Update the existing row
                        $updateTableQuery = "UPDATE $table SET 
                                        $loadSechColumn = '$deptValue',
                                        UPDATEDBY = '$updatingUser',
                                        UPDATED_ON = '$currentDateTime',
                                        LOCATION = '$location'
                                        WHERE DATE = '$date' AND TIME = '$time'";
                        if ($conn->query($updateTableQuery) !== TRUE) {
                            echo "Error updating record: " . $conn->error;
                        }
                    } else {
                        // Insert a new row
                        $insertQuery = "INSERT INTO $table (TIME, DATE, $loadSechColumn, UPDATEDBY, UPDATED_ON, LOCATION) 
                                        VALUES ('$time', '$date', '$deptValue', '$updatingUser', '$currentDateTime', '$location')";
                        if ($conn->query($insertQuery) !== TRUE) {
                            echo "Error inserting record: " . $conn->error;
                        }
                    }
                }
            }
        }
        echo "Data updated successfully!";
        exit();
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
    // Close database connection
    $conn->close();
} else {
    // Redirect the user back to the same page without any warning 
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha384-KyZXEAg3QhqLMpG8r+Knujsl5/5hb7x6f5n2l5+4vpe5y5T1GZfgwS7L5R5Aq8s5" crossorigin="anonymous">

    <style>
        .form-group {
            display: flex;
            align-items: center;
        }
        .custom-file-input {
            visibility: hidden;
            width: 0;
        }
        #myTable{
            text-align:center;
        }
        th, td {
            border: 2px solid #e8e4e4;
            padding: 8px;
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
                        <!-- Option 2 Button -->
                        <button type="button" class="btn btn-dark" id="option2Btn">Update Database</button>
                    </div>
                </div>
            </div>
            <!-- Date selection and search button for Option 1 -->
            <div class="row align-items-end" id="option1Section">
                <div class="col-md-2">
                    <label for="fromDate" class="form-label"><b>From Date:</b></label>
                    <input type="date" class="form-control" id="fromDate" required>
                </div>
                <div class="col-md-2">
                    <label for="toDate" class="form-label"><b>To Date:</b></label>
                    <input type="date" class="form-control" id="toDate" required>
                </div>
                <div class="col-md-2">
                    <label for="selectedDept" class="form-label"><b>Department:</b></label>
                    <select name="department" id="selectedDept" class="form-control " required>
                        <option value="ALL">All Departments</option>
                        <option value="SMS">SMS</option>
                        <option value="SMS2">SMS2</option>
                        <option value="SMS3">SMS3</option>
                        <option value="SPM">SPM</option>
                        <option value="NSPL">NSPL</option>
                        <option value="RAILMILL">RAILMILL</option>
                        <option value="PLATEMILL">PLATEMILL</option>
                        <option value="JLDC">JLDC</option>
                    </select>
                </div>
                <div class="col-md-2"></div>
                <div class="btn-group col-md-4">
                    <button type="button" class="btn btn-primary" style=" width:20px;" id="searchBtn"><i class="fa fa-search"></i></button>
                    <button type="button" class="btn btn-dark" style=" width:200px;"id="exportBtn"> <i class="fa fa-file-excel-o" aria-hidden="true"></i>Export to Excel </button>
                </div>
                <div class="card" id="myTable" style="padding: 20px; height:61vh; overflow-y: auto; margin-top:20px;">
                    <!-- Table to display results will be inserted here -->
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Handle click on Option 2 button
        document.getElementById('option2Btn').addEventListener('click', function() {
            // Redirect to updateadmin.php page
            window.location.href = 'updateadmin.php';
        });

        // Handle click on Export to Excel button
        document.getElementById('exportBtn').addEventListener('click', function() {
            var table = document.getElementById('myTable').querySelector('table');

            if (!table) {
                alert('No data to export. Please import table first by clicking Search button.');
                return;
            }

            var ws = XLSX.utils.table_to_sheet(table);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
            XLSX.writeFile(wb, 'exported_data.xlsx');
        });

        // Handle click on Search button
        document.getElementById('searchBtn').addEventListener('click', function() {
    var fromDate = document.getElementById('fromDate').value;
    var toDate = document.getElementById('toDate').value;
    var selectedDept = document.getElementById('selectedDept').value;

    if (!fromDate || !toDate) {
        alert('Select both From Date and To Date.');
        return;
    }
    if (fromDate > toDate) {
        alert('From Date should be before To Date.');
        return;
    }

    fetch('admin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ fromDate: fromDate, toDate: toDate, selectedDept: selectedDept })
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            alert(data.error);
            return;
        }

        // Create a table to display the data
        var table = document.createElement('table');
        table.classList.add('table', 'table-striped');

        // Create table headers
        var thead = document.createElement('thead');
        var headerRow = document.createElement('tr');
        var columns;
        if (selectedDept === 'ALL') {
            columns = ['TIME', 'DATE', 'POWER_GENERATION', 'LOAD_SECH_SMS2', 'LOAD_SECH_SMS3', 'LOAD_SECH_SMS_TOTAL', 'LOAD_SECH_RAILMILL', 'LOAD_SECH_PLATEMILL', 'LOAD_SECH_SPM', 'LOAD_SECH_NSPL', 'TOTAL'];
        } else if (selectedDept === 'JLDC') {
            columns = ['TIME', 'DATE', 'POWER_GENERATION', 'UPDATEDBY', 'UPDATED_ON', 'LOCATION'];
        } else if (selectedDept === 'SMS') {
            columns = ['TIME', 'DATE', 'LOADSECH_SMS2', 'LOADSECH_SMS3', 'UPDATEDBY', 'UPDATED_ON', 'LOCATION'];
        } else {
            columns = ['TIME', 'DATE', 'LOADSECH', 'UPDATEDBY', 'UPDATED_ON', 'LOCATION'];
        }

        columns.forEach(col => {
            var th = document.createElement('th');
            th.textContent = col;
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);
        table.appendChild(thead);

        // Create table body
        var tbody = document.createElement('tbody');
        data.forEach(row => {
            var tr = document.createElement('tr');
            columns.forEach(col => {
                var td = document.createElement('td');
                td.textContent = row[col];
                tr.appendChild(td);
            });
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);

        // Clear previous table data and append new table
        var tableContainer = document.getElementById('myTable');
        tableContainer.innerHTML = '';
        tableContainer.appendChild(table);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error fetching data from database!');
    });
});

    </script>
</body>

</html>
