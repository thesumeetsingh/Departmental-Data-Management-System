<?php
include 'suppressError.php';
session_start();// Start PHP session

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['username'];
$userEmail = $_SESSION['useremail'];
$logdept = $_SESSION['dept'];

if ($logdept !== 'ADMIN') {
    echo "<script>
            alert('You are not an admin');
            window.location.href = 'index.php';
          </script>";
    exit();
}

// Check if the request method is POST (for AJAX requests)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the JSON data sent from the frontend
    $requestData = json_decode(file_get_contents('php://input'), true);
    $fromDate = $requestData['fromDate'];
    $toDate = $requestData['toDate'];
    $selectedDept = $requestData['selectedDept'];
    $selectedLocation= $requestData['selectedLocation'];

    // Validate the input data
    if (!$fromDate || !$toDate || !$selectedDept || !$selectedLocation) {
        echo json_encode(['error' => 'Invalid input data']);
        exit();
    }

    // Map departments to their corresponding table names
    $tableMapping = [
        'SMS' => 'SMS',
        'SPM' => 'SPM',
        'NSPL' => 'NSPL',
        'RAILMILL' => 'RAILMILL',
        'PLATEMILL' => 'PLATEMILL',
        'JLDC' => 'JLDC',
        'ALL' => 'power_table'
    ];

    if (!isset($tableMapping[$selectedDept])) {
        echo json_encode(['error' => 'Invalid department selected']);
        exit();
    }

    $tableName = $tableMapping[$selectedDept];

    // Connect to MySQL database
    include 'connection.php';

    $updationQuery = "UPDATE power_table
        SET 
            LOAD_SECH_SMS_TOTAL = LOAD_SECH_SMS2 + LOAD_SECH_SMS3,
            TOTAL = LOAD_SECH_SMS2 + LOAD_SECH_SMS3 + LOAD_SECH_RAILMILL + LOAD_SECH_PLATEMILL + LOAD_SECH_SPM + LOAD_SECH_NSPL";
    if (!$conn->query($updationQuery)) {
        echo json_encode(['error' => 'Failed to update totals: ' . $conn->error]);
        exit();
    }

    // Determine the columns to query based on department
    if ($selectedDept === 'ALL') {
        $columns = ['TIME', 'DATE', 'POWER_GENERATION', 'LOAD_SECH_SMS2', 'LOAD_SECH_SMS3', 'LOAD_SECH_SMS_TOTAL', 'LOAD_SECH_RAILMILL', 'LOAD_SECH_PLATEMILL', 'LOAD_SECH_SPM', 'LOAD_SECH_NSPL', 'TOTAL','LOCATION'];
    } elseif ($selectedDept === 'JLDC') {
        $columns = ['TIME', 'DATE', 'POWER_GENERATION', 'UPDATEDBY', 'UPDATED_ON', 'LOCATION'];
    } elseif ($selectedDept === 'SMS') {
        $columns = ['TIME', 'DATE', 'LOADSECH_SMS2', 'LOADSECH_SMS3', 'UPDATEDBY', 'UPDATED_ON', 'LOCATION'];
    } else {
        $columns = ['TIME', 'DATE', 'LOADSECH', 'UPDATEDBY', 'UPDATED_ON', 'LOCATION'];
    }

    // Prepare and execute the query
    $columnString = implode(", ", $columns);
    $stmt = $conn->prepare("SELECT $columnString FROM $tableName WHERE DATE BETWEEN ? AND ? AND LOCATION = ? ORDER BY DATE,TIME ASC");
    if ($stmt === false) {
        echo json_encode(['error' => 'Failed to prepare statement: ' . $conn->error]);
        exit();
    }

    $stmt->bind_param("sss", $fromDate, $toDate, $selectedLocation);
    if (!$stmt->execute()) {
        echo json_encode(['error' => 'Failed to execute query: ' . $stmt->error]);
        exit();
    }


    $result = $stmt->get_result();
    if ($result === false) {
        echo json_encode(['error' => 'Failed to get result: ' . $stmt->error]);
        exit();
    }

    // Fetch data and send it back as JSON
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);

    // Close database connection
    $stmt->close();
    $conn->close();
    exit(); // Stop further execution after handling the AJAX request
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Portal | Admin</title>
    <link rel="icon" type="image/x-icon" href="/images/favicon.png">
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

        #myTable {
            position: relative;
            height: 60vh; /* Adjust the height as needed */
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
            white-space: nowrap; /* Prevents text wrapping */
        }
        tbody tr {
    background-color: #fff; /* Ensures the rows have a background */
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
            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <a class="nav-link" href="#"><i class="fa-solid fa-user"></i> <?php echo $userEmail; ?></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </li>
    </ul>
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
                        <button type="button" class="btn btn-dark" id="clearSelection">Clear Selection</button>
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
                        <option value="SPM">SPM</option>
                        <option value="NSPL">NSPL</option>
                        <option value="RAILMILL">RAILMILL</option>
                        <option value="PLATEMILL">PLATEMILL</option>
                        <option value="JLDC">JLDC</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="selectLocation" class="form-label"><b>Location:</b></label>
                    <select name="selectLocation" id="selectLocation" class="form-control " required>
                        <option value="">Select Location</option>
                        <option value="RAIGARH">RAIGARH</option>
                        <option value="ANGUL">ANGUL</option>
                        <option value="PATRATU">PATRATU</option>
                        <option value="TAMNAR">TAMNAR</option>
                        <option value="NSPL">NSPL</option>
                    </select>
                </div>
                <!-- <div class="col-md-2"></div> -->
                <div class="btn-group col-md-4">
                    <button type="button" class="btn btn-primary" style=" width:20px;" id="searchBtn"><i class="fa fa-search"></i></button>
                    <button type="button" class="btn btn-dark" style=" width:200px;"id="exportBtn"> <i class="fa fa-file-excel-o" aria-hidden="true"></i>Export to Excel </button>
                </div>
                <div class="card" id="myTable" style=" height:61vh; overflow-y: auto; margin-top:20px;">
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
    var selectedLocation = document.getElementById('selectLocation').value;

    if (!fromDate || !toDate) {
        alert('Select both From Date and To Date.');
        return;
    }
    if (fromDate > toDate) {
        alert('From Date should be before To Date.');
        return;
    }
    if (!selectedLocation) {
        alert('Please select a location.');
        return;
    }

    fetch('admin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ fromDate: fromDate, toDate: toDate, selectedDept: selectedDept, selectedLocation: selectedLocation })
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
            columns = ['Sr No.', 'TIME', 'DATE', 'POWER_GENERATION', 'LOAD_SECH_SMS2', 'LOAD_SECH_SMS3', 'LOAD_SECH_SMS_TOTAL', 'LOAD_SECH_RAILMILL', 'LOAD_SECH_PLATEMILL', 'LOAD_SECH_SPM', 'LOAD_SECH_NSPL', 'TOTAL', 'LOCATION'];
        } else if (selectedDept === 'JLDC') {
            columns = ['Sr No.', 'TIME', 'DATE', 'POWER_GENERATION', 'UPDATEDBY', 'UPDATED_ON', 'LOCATION'];
        } else if (selectedDept === 'SMS') {
            columns = ['Sr No.', 'TIME', 'DATE', 'LOADSECH_SMS2', 'LOADSECH_SMS3', 'UPDATEDBY', 'UPDATED_ON', 'LOCATION'];
        } else {
            columns = ['Sr No.', 'TIME', 'DATE', 'LOADSECH', 'UPDATEDBY', 'UPDATED_ON', 'LOCATION'];
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
        data.forEach((row, index) => {
            var tr = document.createElement('tr');
            var serialNumberCell = document.createElement('td');
            serialNumberCell.textContent = index + 1; // Serial number starts from 1
            tr.appendChild(serialNumberCell);
            columns.slice(1).forEach(col => {
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

document.getElementById('clearSelection').addEventListener('click', function() {
            var inputs = document.querySelectorAll('input');
            inputs.forEach(function(input) {
                input.value = '';
            });
            var clearDept=document.querySelector('#selectedDept');
            clearDept.value="ALL";
            var clearLoc=document.querySelector('#selectLocation');
            clearLoc.value="";
            var tableContainer = document.getElementById('myTable');
            tableContainer.innerHTML = '';
        });

    </script>    
</body>

</html>
