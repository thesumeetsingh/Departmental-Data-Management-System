<?php
include 'suppressError.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if($_SESSION['dept']=='ADMIN'){
    echo "<script>
            alert('You are an admin');
            window.location.href = 'admin.php';
          </script>";
}

$userName = $_SESSION['username'];
$department = $_SESSION['dept'];
$deptTitle=strtoupper($department);
$userEmail = $_SESSION['useremail'];
$userLocation = $_SESSION['userLocation'];
include 'connection.php';
date_default_timezone_set('Asia/Kolkata');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['data']) && isset($_POST['sheetDate']) && isset($_POST['location'])) {
        $data = json_decode($_POST['data'], true);
        $sheetDate = $_POST['sheetDate'];
        $location = $_POST['location'];
        $updatinguser = $userName;
        $currentDateTime = date("Y-m-d H:i:s");
        // Function to sanitize values
        function sanitizeValue($value) {
            return (isset($value) && is_numeric($value)) ? $value : 0;
        }
        foreach ($data as $row) {
            $time = $row[0];
            $departmentValue = sanitizeValue($row[1]);

            $loadsechColumn = ($department === 'JLDC') ? 'POWER_GENERATION' : 'LOADSECH';
	
            $checkDeptSql = "SELECT * FROM $department WHERE DATE='$sheetDate' AND TIME='$time' AND LOCATION='$location'";
            $checkDeptResult = $conn->query($checkDeptSql);
	
            if ($checkDeptResult->num_rows > 0) {
                if ($department === 'SMS') {
                    $departmentValue2 = sanitizeValue($row[2]);
                    $deptSql = "UPDATE $department SET LOADSECH_SMS2='$departmentValue', LOADSECH_SMS3='$departmentValue2', UPDATEDBY='$updatinguser', UPDATED_ON='$currentDateTime' WHERE DATE='$sheetDate' AND TIME='$time' AND LOCATION='$location' ";
                } else {
                    $deptSql = "UPDATE $department SET $loadsechColumn='$departmentValue', UPDATEDBY='$updatinguser', UPDATED_ON='$currentDateTime' WHERE DATE='$sheetDate' AND TIME='$time' AND LOCATION='$location' ";
                }
            } else {
                if ($department === 'SMS') {
                    $departmentValue2 = sanitizeValue($row[2]);
                    $deptSql = "INSERT INTO $department (TIME, DATE, LOADSECH_SMS2, LOADSECH_SMS3, UPDATEDBY, UPDATED_ON, LOCATION) 
                                VALUES ('$time', '$sheetDate', '$departmentValue', '$departmentValue2', '$updatinguser', '$currentDateTime', '$location')";
                } else {
                    $deptSql = "INSERT INTO $department (TIME, DATE, $loadsechColumn, UPDATEDBY, UPDATED_ON, LOCATION) 
                                VALUES ('$time', '$sheetDate', '$departmentValue', '$updatinguser', '$currentDateTime', '$location')";
                }
            }

            if ($conn->query($deptSql) !== TRUE) {
                echo "Error: " . $deptSql . "<br>" . $conn->error;
            }

            if ($department === 'SMS') {
                $checkSqlSMS = "SELECT * FROM power_table WHERE DATE='$sheetDate' AND TIME='$time' AND LOCATION='$location' ";
                $checkResultSMS = $conn->query($checkSqlSMS);
                if ($checkResultSMS->num_rows > 0) {
                    $updateSqlSMS = "UPDATE power_table SET LOAD_SECH_SMS2='$departmentValue', LOAD_SECH_SMS3='$departmentValue2' WHERE DATE='$sheetDate' AND TIME='$time' AND LOCATION='$location' ";
                    if ($conn->query($updateSqlSMS) !== TRUE) {
                        echo "Error updating power_table for SMS: " . $conn->error;
                    }
                } else {
                    $insertSqlSMS = "INSERT INTO power_table (DATE, TIME, LOAD_SECH_SMS2, LOAD_SECH_SMS3, LOCATION) VALUES ('$sheetDate', '$time', '$departmentValue', '$departmentValue2','$location')";
                    if ($conn->query($insertSqlSMS) !== TRUE) {
                        echo "Error inserting into power_table for SMS: " . $conn->error;
                    }
                }
            }

            $powerColumn = '';
            switch (strtolower($department)) {
                case 'railmill':
                    $powerColumn = 'LOAD_SECH_RAILMILL';
                    break;
                case 'platemill':
                    $powerColumn = 'LOAD_SECH_PLATEMILL';
                    break;
                case 'spm':
                    $powerColumn = 'LOAD_SECH_SPM';
                    break;
                case 'nspl':
                    $powerColumn = 'LOAD_SECH_NSPL';
                    break;
                case 'jldc':
                    $powerColumn = 'POWER_GENERATION';
                    break;
                default:
                    break;
            }

            if (!empty($powerColumn)) {
                $checkSql = "SELECT * FROM power_table WHERE DATE='$sheetDate' AND TIME='$time' AND LOCATION='$location'";
                $checkResult = $conn->query($checkSql);
                if ($checkResult->num_rows > 0) {
                    $updateSql = "UPDATE power_table SET $powerColumn='$departmentValue' WHERE DATE='$sheetDate' AND TIME='$time' AND LOCATION='$location' ";
                    if ($conn->query($updateSql) !== TRUE) {
                        echo "Error updating power_table: " . $conn->error;
                    }
                } else {
                    $insertSql = "INSERT INTO power_table (DATE, TIME, $powerColumn, LOCATION) VALUES ('$sheetDate', '$time', '$departmentValue', '$location')";
                    if ($conn->query($insertSql) !== TRUE) {
                        echo "Error inserting into power_table: " . $conn->error;
                    }
                }
            }
        }

        echo "Data inserted successfully";
	exit();
    } else {
        echo "Invalid request";
    }

    $conn->close();
} else {
    //echo "Invalid request method";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Portal | <?php echo $deptTitle ?></title>
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

                        <button class='btn btn-dark' id="clearSelection">Clear Selection</button>
                        <button class='btn btn-dark' id="downloadSample">Download Sample</button>
                        <button class='btn btn-dark' id="toViewDeptTable">View Database</button>
                    </div>
                </div>
                <div class="container-fluid mb-3">
    <div class="row align-items-end">
        <div class="col-auto">
            <label for="sheetDate" class="form-label"><b>Select Sheet Date:</b></label>
            <input type="date" class="form-control" id="sheetDate">
        </div>

        <div class="col-auto mt-3 mt-md-0"> <!-- Add margin top here for small screens -->
            <label for="fileInput" class="btn btn-dark">Choose File</label>
            <input type="file" id="fileInput" class="custom-file-input" accept=".xlsx,.xls" style="display: none;">
            <!-- Update Database button -->
            <button class="btn btn-dark" id="updateDatabaseBtn"><i class="fa fa-upload"></i>Update</button>
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
        document.getElementById('toViewDeptTable').addEventListener('click', function() {
            // Redirect to updateadmin.php page
            window.location.href = 'depttable.php';
        });
        
        document.getElementById('fileInput').addEventListener('change', function(e) {
    var file = e.target.files[0];
    var reader = new FileReader();
    reader.onload = function(e) {
        var data = new Uint8Array(e.target.result);
        var workbook = XLSX.read(data, { type: 'array' });
        var sheetName = workbook.SheetNames[0];
        var sheet = workbook.Sheets[sheetName];
        var htmlTable = XLSX.utils.sheet_to_html(sheet);
        document.getElementById('myTable').innerHTML = htmlTable;

        //validate columns
        var firstRow = XLSX.utils.sheet_to_json(sheet, { header: 1 })[0];
        var department = "<?php echo $department; ?>";  
        var valid = validateColumns(firstRow, department); 
        if (!valid) {
                    alert('Invalid columns for the selected department.');
                    document.getElementById('myTable').innerHTML = '';
                }  
    };
    reader.readAsArrayBuffer(file);
});
function validateColumns(columns, department) {
            var validColumns = {
                'SMS': ['TIME (HRS)', 'LOAD SECHDULE SMS2 (MW)', 'LOAD SECHDULE SMS3 (MW)'],
                'NSPL': ['TIME (HRS)', 'LOAD SECHDULE NSPL (MW)'],
                'JLDC': ['TIME (HRS)', 'POWER GENERATION (JLDC)',],
                'SPM': ['TIME (HRS)', 'LOAD SECHDULE SPM (MW)'],
                'RAILMILL': ['TIME (HRS)', 'LOAD SECHDULE RAILMILL (MW)'],
                'PLATEMILL': ['TIME (HRS)', 'LOAD SECHDULE PLATEMILL (MW)'],
            };
            var requiredColumns = validColumns[department.toUpperCase()];
            return requiredColumns.every(col => columns.includes(col));
        }
document.getElementById('updateDatabaseBtn').addEventListener('click', function() {
    var sheetDate = document.getElementById('sheetDate').value;
    var location = "<?php echo $userLocation; ?>";
    if (!sheetDate) {
        alert('Please select a sheet date.');
        return;
    }
    var workbookData = [];
    var table = document.querySelector('#myTable table');
    for (var i = 1, row; row = table.rows[i]; i++) {
        var rowData = [];
        for (var j = 0, col; col = row.cells[j]; j++) {
            rowData.push(col.innerText);
        }
        workbookData.push(rowData);
    }
    var formData = new FormData();
    formData.append('data', JSON.stringify(workbookData));
    formData.append('sheetDate', sheetDate);
    formData.append('location', location);

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating database!');
    });
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
<script>
        // Getting the department variable from PHP
        var department = "<?php echo $department; ?>";

        // Mapping of department to file names
        var fileMapping = {
            'SMS': 'sms.xlsx',
            'NSPL': 'nspl.xlsx',
            'SPM': 'spm.xlsx',
            'RAILMILL': 'railmill.xlsx',
            'PLATEMILL': 'platemill.xlsx',
            'JLDC': 'jldc.xlsx',
            'ADMIN': 'admin.xlsx'
        };

        document.getElementById('downloadSample').addEventListener('click', function() {
            var fileName = fileMapping[department.toUpperCase()];

            if (fileName) {
                var link = document.createElement('a');
                link.href = 'samplesheets/' + fileName;
                link.download = fileName;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            } else {
                alert('No sample sheet available for the selected department.');
            }
        });
        document.getElementById('clearSelection').addEventListener('click', function() {
            var inputs = document.querySelectorAll('input');
            var tableContainer = document.getElementById('myTable');
            tableContainer.innerHTML = '';
            inputs.forEach(function(input) {
                input.value = '';
            });
});

    </script>
</body>

</html>
