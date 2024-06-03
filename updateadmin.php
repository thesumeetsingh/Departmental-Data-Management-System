<?php
include 'suppressError.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$userName = $_SESSION['username'];
$userEmail = $_SESSION['useremail'];
$userDepartment = $_SESSION['dept'];

if ($userDepartment !== 'ADMIN') {
    echo "<script>
            alert('You are not an admin');
            window.location.href = 'index.php';
          </script>";
    exit();
}

date_default_timezone_set('Asia/Kolkata');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data'])) {
    include 'connection.php';

    $data = json_decode($_POST['data'], true);
    $sheetDate = $data['sheetDate'];
    $location = $data['location'];
    $updatingUser = $_SESSION['username'];
    $currentDateTime = date("Y-m-d H:i:s");
    $rows = $data['rows'];
    function sanitizeValue($value) {
        return is_numeric($value) ? $value : 0;
    }
    try {
        foreach ($rows as $row) {
            $time = $row['A'];
            $date = $sheetDate;
            $powerGeneration = sanitizeValue($row['B']);
            $loadSechSMS2 = sanitizeValue($row['C']);
            $loadSechSMS3 = sanitizeValue($row['D']);
            $loadSechRailMill = sanitizeValue($row['F']);
            $loadSechPlateMill = sanitizeValue($row['G']);
            $loadSechSPM = sanitizeValue($row['H']);
            $loadSechNSPL = sanitizeValue($row['I']);

            $loadSechSMSTotal = $loadSechSMS2 + $loadSechSMS3;
            $total = $loadSechSMS2 + $loadSechSMS3 + $loadSechRailMill + $loadSechPlateMill + $loadSechSPM + $loadSechNSPL;

            $checkQuery = "SELECT * FROM power_table WHERE DATE = '$date' AND TIME = '$time' and LOCATION='$location'";
            $checkResult = $conn->query($checkQuery);

            if ($checkResult->num_rows > 0) {
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
                                WHERE DATE = '$date' AND TIME = '$time' AND LOCATION='$location'";
                if ($conn->query($updateQuery) !== TRUE) {
                    echo "Error updating record: " . $conn->error;
                }
            } else {
                $insertQuery = "INSERT INTO power_table (TIME, DATE, POWER_GENERATION, LOAD_SECH_SMS2, LOAD_SECH_SMS3, LOAD_SECH_SMS_TOTAL, LOAD_SECH_RAILMILL, LOAD_SECH_PLATEMILL, LOAD_SECH_SPM, LOAD_SECH_NSPL, TOTAL, UPDATEDBY, UPDATED_ON, LOCATION) 
                                VALUES ('$time', '$date', '$powerGeneration', '$loadSechSMS2', '$loadSechSMS3', '$loadSechSMSTotal', '$loadSechRailMill', '$loadSechPlateMill', '$loadSechSPM', '$loadSechNSPL', '$total', '$updatingUser', '$currentDateTime', '$location')";
                if ($conn->query($insertQuery) !== TRUE) {
                    echo "Error inserting record: " . $conn->error;
                }
            }

            $tables = ['JLDC', 'NSPL', 'SMS', 'SPM', 'RAILMILL', 'PLATEMILL'];
            foreach ($tables as $table) {
                if ($table == 'SMS') {
                    $checkTableQuery = "SELECT * FROM $table WHERE DATE = '$date' AND TIME = '$time' AND LOCATION='$location'";
                    $checkTableResult = $conn->query($checkTableQuery);

                    if ($checkTableResult->num_rows > 0) {
                        $updateTableQuery = "UPDATE $table SET 
                                        LOADSECH_SMS2 = '$loadSechSMS2',
                                        LOADSECH_SMS3 = '$loadSechSMS3',
                                        UPDATEDBY = '$updatingUser',
                                        UPDATED_ON = '$currentDateTime'
                                        WHERE DATE = '$date' AND TIME = '$time' AND LOCATION='$location'";
                        if ($conn->query($updateTableQuery) !== TRUE) {
                            echo "Error updating record: " . $conn->error;
                        }
                    } else {
                        $insertQuery = "INSERT INTO $table (TIME, DATE, LOADSECH_SMS2, LOADSECH_SMS3, UPDATEDBY, UPDATED_ON, LOCATION) 
                                        VALUES ('$time', '$date', '$loadSechSMS2', '$loadSechSMS3', '$updatingUser', '$currentDateTime', '$location')";
                        if ($conn->query($insertQuery) !== TRUE) {
                            echo "Error inserting record: " . $conn->error;
                        }
                    }
                } else {
                    $loadSechColumn = ($table == 'JLDC') ? 'POWER_GENERATION' : 'LOADSECH';
                    $deptValue = ($table == 'JLDC') ? $powerGeneration : (($table == 'NSPL') ? $loadSechNSPL : (($table == 'SPM') ? $loadSechSPM : (($table == 'RAILMILL') ? $loadSechRailMill : $loadSechPlateMill)));

                    $checkTableQuery = "SELECT * FROM $table WHERE DATE = '$date' AND TIME = '$time' AND LOCATION='$location'";
                    $checkTableResult = $conn->query($checkTableQuery);

                    if ($checkTableResult->num_rows > 0) {
                        $updateTableQuery = "UPDATE $table SET 
                                        $loadSechColumn = '$deptValue',
                                        UPDATEDBY = '$updatingUser',
                                        UPDATED_ON = '$currentDateTime'
                                        WHERE DATE = '$date' AND TIME = '$time' AND LOCATION='$location'";
                        if ($conn->query($updateTableQuery) !== TRUE) {
                            echo "Error updating record: " . $conn->error;
                        }
                    } else {
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
    <title>Web Portal | Admin</title>
<link rel="icon" type="image/x-icon" href="/images/favicon.png">
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
            text-align:center;
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
        tr:nth-child(-n+2) {
            background-color: #d0a4a4;
            font-weight: bold;
        }
        tr:not(:nth-child(-n+2)):hover {
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

			<button class='btn btn-dark' id="downloadSample">Download Sample</button>
                        <!-- Export to Excel button -->
                        <button type="button" class="btn btn-dark" id="toViewDB">View Database</button>


                    </div>
                </div>
                <div class="container-fluid mb-3">
                    <div class="row align-items-end">
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
                        <div class="col-auto mt-3 mt-md-0">
                            <label for="fileInput" class="btn btn-dark">Choose File</label>
                            <input type="file" id="fileInput" class="custom-file-input" accept=".xlsx,.xls" style="display: none;">
                            <button class="btn btn-dark" id="updateDatabaseBtn"><i class="fa fa-upload"></i> Update</button>
                        </div>
                        <div class="col-auto">

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
document.getElementById('toViewDB').addEventListener('click', function() {
    window.location.href = 'admin.php';
});

document.getElementById('fileInput').addEventListener('change', function(e) {
    var file = e.target.files[0];
    var reader = new FileReader();
    reader.readAsBinaryString(file);
    reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, { type: 'binary' });
        var sheetName = workbook.SheetNames[0];
        var sheet = workbook.Sheets[sheetName];
        // var htmlTable = XLSX.utils.sheet_to_html(sheet);
        // document.getElementById('myTable').innerHTML = htmlTable;
        var firstRow = XLSX.utils.sheet_to_json(sheet, { header: 1 })[0];
        var valid = validateColumns(firstRow);
        if(valid){
        var htmlTable = XLSX.utils.sheet_to_html(sheet);
        document.getElementById('myTable').innerHTML = htmlTable;
        }
        else{
                    alert('Invalid columns for the admin upload.');
                    document.getElementById('tableContainer').innerHTML = '';
                }

        // Calculate and update column 6 and column 11 values
        updateCalculatedValues();
    };
});
function validateColumns(columns) {
            var requiredColumns = ['TIME (HRS)', 'POWER GENERATION', 'LOAD SECHDULE SMS (MW)', 'LOAD SECHDULE RAILMILL (MW)', 'LOAD SECHDULE PLATEMILL (MW)', 'LOAD SECHDULE SPM (MW)', 'LOAD SECHDULE NSPL (MW)'];
            return requiredColumns.every(col => columns.includes(col));
        }

document.getElementById('updateDatabaseBtn').addEventListener('click', function() {
    var sheetDate = document.getElementById('sheetDate').value;
    var location = document.getElementById('locationSelect').value;
    if (!location || location === "") {
        alert('Please select a location.');
        return;
    }
    if (!sheetDate) {
        alert('Please select a sheet date.');
        return;
    }
    var fileInput = document.getElementById('fileInput').files[0];
    if (!fileInput) {
        alert('Please select a file first.');
        return;
    }
    var reader = new FileReader();
    reader.readAsBinaryString(fileInput);
    reader.onload = function(e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, { type: 'binary' });
        var sheetName = workbook.SheetNames[0];
        var sheet = workbook.Sheets[sheetName];
        var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });
        var rows = [];
        for (var i = 2; i < jsonData.length; i++) {
            var row = {};
            row.A = jsonData[i][0] || ''; // Assuming A is the time column
            row.B = jsonData[i][1] || ''; // Assuming C is the power generation column
            row.C = jsonData[i][2] || ''; // Assuming D is the load sech sms 2 column
            row.D = jsonData[i][3] || ''; // Assuming E is the load sech sms 3 column
            row.F = jsonData[i][5] || ''; // Assuming G is the load sech rail mill column
            row.G = jsonData[i][6] || ''; // Assuming H is the load sech plate mill column
            row.H = jsonData[i][7] || ''; // Assuming I is the load sech spm column
            row.I = jsonData[i][8] || ''; // Assuming J is the load sech nspl column
            rows.push(row);
        }
        var formData = new FormData();
        formData.append('data', JSON.stringify({ sheetDate: sheetDate, location: location, rows: rows }));

        fetch('updateadmin.php', {
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
    };
});

// Function to update calculated values in the table
function updateCalculatedValues() {
    var table = document.getElementById('myTable');
    var rows = table.rows;

    // Start from row 3 as the first two rows are header rows
    for (var i = 2; i < rows.length; i++) {
        var row = rows[i];
        var col3 = parseInt(row.cells[2].textContent) || 0; // Value in column 4
        var col4 = parseInt(row.cells[3].textContent) || 0; // Value in column 5
        var col6 = parseInt(row.cells[4].textContent) || 0; // Value in column 7
        var col7 = parseInt(row.cells[6].textContent) || 0; // Value in column 8
        var col8 = parseInt(row.cells[7].textContent) || 0; // Value in column 9
        var col9 = parseInt(row.cells[8].textContent) || 0; // Value in column 10

        // Calculate values for column 6 and column 11
        var col5 = col3 + col4;
        var col10 = col3 + col4 + col6 + col7 + col8 + col9;

        // Update the cells in the row with calculated values
        row.cells[4].textContent = col5;
        row.cells[9].textContent = col10;
    }
}
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
<script>
        document.getElementById('downloadSample').addEventListener('click', function() {
            var fileName = 'admin.xlsx';
            var filePath = 'samplesheets/' + fileName;

            var link = document.createElement('a');
            link.href = filePath;
            link.download = fileName;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });
    </script>
</body>

</html>
