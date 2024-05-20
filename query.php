<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database and Table Management</title>
</head>
<body>

<?php
$dbname = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'connection.php';

    // Handle database creation
    if (isset($_POST['dbname'])) {
        $dbname = $_POST['dbname'];
        $dbCheckQuery = "SHOW DATABASES LIKE '$dbname'";
        $dbCheckResult = $conn->query($dbCheckQuery);

        if ($dbCheckResult->num_rows > 0) {
            echo "Database '$dbname' already exists.<br>";
        } else {
            $createDbQuery = "CREATE DATABASE $dbname";
            if ($conn->query($createDbQuery) === TRUE) {
                echo "Database '$dbname' created successfully.<br>";
            } else {
                echo "Error creating database: " . $conn->error . "<br>";
            }
        }
    }

    // Handle table creation
    if (!empty($dbname) && isset($_POST['tablename'])) {
        $tablename = $_POST['tablename'];
        $conn->select_db($dbname);

        $tableCheckQuery = "SHOW TABLES LIKE '$tablename'";
        $tableCheckResult = $conn->query($tableCheckQuery);

        if ($tableCheckResult->num_rows > 0) {
            echo "Table '$tablename' already exists in database '$dbname'.<br>";
        } else {
            switch ($tablename) {
                case 'user_details':
                    $createTableQuery = "CREATE TABLE user_details (
                        FIRSTNAME VARCHAR(100),
                        LASTNAME VARCHAR(100),
                        USERNAME VARCHAR(100),
                        PASSWORD VARCHAR(50),
                        EMAILADD VARCHAR(500),
                        DEPT VARCHAR(20),
                        PHONENUMBER INT(20),
                        AGE INT(3),
                        GENDER VARCHAR(50)
                    )";
                    break;

                case 'power_table':
                    $createTableQuery = "CREATE TABLE power_table (
                        TIME VARCHAR(100),
                        DATE DATE,
                        POWER_GENERATION INT(5),
                        LOAD_SECH_SMS2 INT(5),
                        LOAD_SECH_SMS3 INT(5),
                        LOAD_SECH_SMS_TOTAL INT(5),
                        LOAD_SECH_RAILMILL INT(5),
                        LOAD_SECH_PLATEMILL INT(5),
                        LOAD_SECH_SPM INT(5),
                        LOAD_SECH_NSPL INT(5),
                        TOTAL INT(5),
                        UPDATEDBY VARCHAR(50),
                        UPDATED_ON DATETIME,
                        LOCATION VARCHAR(100)
                    )";
                    break;

                case 'jldc':
                    $createTableQuery = "CREATE TABLE jldc (
                        TIME VARCHAR(100),
                        DATE DATE,
                        POWER_GENERATION INT(5),
                        LOADSECH INT(5),
                        UPDATEDBY VARCHAR(50),
                        UPDATED_ON DATETIME,
                        LOCATION VARCHAR(100)
                    )";
                    break;
                case 'nspl':
                case 'NSPL':
                    $createTableQuery = "CREATE TABLE NSPL (
                        TIME VARCHAR(100),
                        DATE DATE,
                        LOADSECH INT(5),
                        UPDATEDBY VARCHAR(50),
                        UPDATED_ON DATETIME,
                        LOCATION VARCHAR(100)
                    )";
                    break;
                case 'SMS2':
                case 'sms2':
                    $createTableQuery = "CREATE TABLE sms2 (
                        TIME VARCHAR(100),
                        DATE DATE,
                        LOADSECH INT(5),
                        UPDATEDBY VARCHAR(50),
                        UPDATED_ON DATETIME,
                        LOCATION VARCHAR(100)
                    )";
                    break;
                case 'sms3':
                case 'SMS3':
                    $createTableQuery = "CREATE TABLE sms3 (
                        TIME VARCHAR(100),
                        DATE DATE,
                        LOADSECH INT(5),
                        UPDATEDBY VARCHAR(50),
                        UPDATED_ON DATETIME,
                        LOCATION VARCHAR(100)
                    )";
                    break;
                case 'railmill':
                case 'RAILMILL':
                    $createTableQuery = "CREATE TABLE railmill (
                        TIME VARCHAR(100),
                        DATE DATE,
                        LOADSECH INT(5),
                        UPDATEDBY VARCHAR(50),
                        UPDATED_ON DATETIME,
                        LOCATION VARCHAR(100)
                    )";
                    break;
                case 'platemill':
                case 'PLATEMILL':
                    $createTableQuery = "CREATE TABLE platemill (
                        TIME VARCHAR(100),
                        DATE DATE,
                        LOADSECH INT(5),
                        UPDATEDBY VARCHAR(50),
                        UPDATED_ON DATETIME,
                        LOCATION VARCHAR(100)
                    )";
                    break;
                case 'SPM':
                case 'spm':
                    $createTableQuery = "CREATE TABLE spm (
                        TIME VARCHAR(100),
                        DATE DATE,
                        LOADSECH INT(5),
                        UPDATEDBY VARCHAR(50),
                        UPDATED_ON DATETIME,
                        LOCATION VARCHAR(100)
                    )";
                    break;
                case 'SMS':
                case 'sms':
                    $createTableQuery = "CREATE TABLE sms (
                        TIME VARCHAR(100),
                        DATE DATE,
                        LOADSECH_SMS2 INT(5),
                        LOADSECH_SMS3 INT(5),
                        UPDATEDBY VARCHAR(50),
                        UPDATED_ON DATETIME,
                        LOCATION VARCHAR(100)
                    )";
                    break;
                default:
                    echo "Table '$tablename' is not defined.<br>";
                    $createTableQuery = '';
                    break;
            }

            if ($createTableQuery && $conn->query($createTableQuery) === TRUE) {
                echo "Table '$tablename' created successfully in database '$dbname'.<br>";
            } elseif ($createTableQuery) {
                echo "Error creating table '$tablename': " . $conn->error . "<br>";
            }
        }
    }

    $conn->close();
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="dbname">Enter Database Name:</label>
    <input type="text" id="dbname" name="dbname" required><br><br>

    <label for="tablename">Enter Table Name:</label>
    <input type="text" id="tablename" name="tablename" required><br><br>

    <input type="submit" value="Submit">
</form>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database and Table Management</title>
</head>
<body>

<?php
$dbname = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', '', 3306);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Handle database creation
    if (isset($_POST['dbname'])) {
        $dbname = $_POST['dbname'];
        $dbCheckQuery = "SHOW DATABASES LIKE '$dbname'";
        $dbCheckResult = $conn->query($dbCheckQuery);

        if ($dbCheckResult->num_rows > 0) {
            echo "Database '$dbname' already exists.<br>";
        } else {
            $createDbQuery = "CREATE DATABASE $dbname";
            if ($conn->query($createDbQuery) === TRUE) {
                echo "Database '$dbname' created successfully.<br>";
            } else {
                echo "Error creating database: " . $conn->error . "<br>";
            }
        }
    }

    if (!empty($dbname)) {
        $conn->select_db($dbname);

        if (isset($_POST['action']) && $_POST['action'] == 'create') {
            // Handle table creation
            if (isset($_POST['tablename'])) {
                $tablename = $_POST['tablename'];

                $tableCheckQuery = "SHOW TABLES LIKE '$tablename'";
                $tableCheckResult = $conn->query($tableCheckQuery);

                if ($tableCheckResult->num_rows > 0) {
                    echo "Table '$tablename' already exists in database '$dbname'.<br>";
                } else {
                    switch ($tablename) {
                        case 'user_details':
                        case 'USER_DETAILS':
                            $createTableQuery = "CREATE TABLE user_details (
                                FIRSTNAME VARCHAR(100),
                                LASTNAME VARCHAR(100),
                                USERNAME VARCHAR(100),
                                PASSWORD VARCHAR(50),
                                EMAILADD VARCHAR(500),
                                DEPT VARCHAR(20),
                                PHONENUMBER INT(20),
                                AGE INT(3),
                                GENDER VARCHAR(50)
                            )";
                            break;
        
                        case 'power_table':
                        case 'POWER_TABLE':
                            $createTableQuery = "CREATE TABLE power_table (
                                TIME VARCHAR(100),
                                DATE DATE,
                                POWER_GENERATION INT(5),
                                LOAD_SECH_SMS2 INT(5),
                                LOAD_SECH_SMS3 INT(5),
                                LOAD_SECH_SMS_TOTAL INT(5),
                                LOAD_SECH_RAILMILL INT(5),
                                LOAD_SECH_PLATEMILL INT(5),
                                LOAD_SECH_SPM INT(5),
                                LOAD_SECH_NSPL INT(5),
                                TOTAL INT(5),
                                UPDATEDBY VARCHAR(50),
                                UPDATED_ON DATETIME,
                                LOCATION VARCHAR(100)
                            )";
                            break;
        
                        case 'jldc':
                            $createTableQuery = "CREATE TABLE jldc (
                                TIME VARCHAR(100),
                                DATE DATE,
                                POWER_GENERATION INT(5),
                                LOADSECH INT(5),
                                UPDATEDBY VARCHAR(50),
                                UPDATED_ON DATETIME,
                                LOCATION VARCHAR(100)
                            )";
                            break;
                        case 'nspl':
                        case 'NSPL':
                            $createTableQuery = "CREATE TABLE NSPL (
                                TIME VARCHAR(100),
                                DATE DATE,
                                LOADSECH INT(5),
                                UPDATEDBY VARCHAR(50),
                                UPDATED_ON DATETIME,
                                LOCATION VARCHAR(100)
                            )";
                            break;
                        case 'SMS2':
                        case 'sms2':
                            $createTableQuery = "CREATE TABLE NSPL (
                                TIME VARCHAR(100),
                                DATE DATE,
                                LOADSECH INT(5),
                                UPDATEDBY VARCHAR(50),
                                UPDATED_ON DATETIME,
                                LOCATION VARCHAR(100)
                            )";
                            break;
                        case 'sms3':
                        case 'SMS3':
                            $createTableQuery = "CREATE TABLE NSPL (
                                TIME VARCHAR(100),
                                DATE DATE,
                                LOADSECH INT(5),
                                UPDATEDBY VARCHAR(50),
                                UPDATED_ON DATETIME,
                                LOCATION VARCHAR(100)
                            )";
                            break;
                        case 'railmill':
                        case 'RAILMILL':
                            $createTableQuery = "CREATE TABLE NSPL (
                                TIME VARCHAR(100),
                                DATE DATE,
                                LOADSECH INT(5),
                                UPDATEDBY VARCHAR(50),
                                UPDATED_ON DATETIME,
                                LOCATION VARCHAR(100)
                            )";
                            break;
                        case 'platemill':
                        case 'PLATEMILL':
                            $createTableQuery = "CREATE TABLE NSPL (
                                TIME VARCHAR(100),
                                DATE DATE,
                                LOADSECH INT(5),
                                UPDATEDBY VARCHAR(50),
                                UPDATED_ON DATETIME,
                                LOCATION VARCHAR(100)
                            )";
                            break;
                        case 'SPM':
                        case 'spm':
                            $createTableQuery = "CREATE TABLE NSPL (
                                TIME VARCHAR(100),
                                DATE DATE,
                                LOADSECH INT(5),
                                UPDATEDBY VARCHAR(50),
                                UPDATED_ON DATETIME,
                                LOCATION VARCHAR(100)
                            )";
                            break;
                        case 'SMS':
                        case 'sms':
                            $createTableQuery = "CREATE TABLE NSPL (
                                TIME VARCHAR(100),
                                DATE DATE,
                                LOADSECH_SMS2 INT(5),
                                LOADSECH_SMS3 INT(5),
                                UPDATEDBY VARCHAR(50),
                                UPDATED_ON DATETIME,
                                LOCATION VARCHAR(100)
                            )";
                            break;
                        default:
                            echo "Table '$tablename' is not defined.<br>";
                            $createTableQuery = '';
                            break;
                    }

                    if ($createTableQuery && $conn->query($createTableQuery) === TRUE) {
                        echo "Table '$tablename' created successfully in database '$dbname'.<br>";
                    } elseif ($createTableQuery) {
                        echo "Error creating table '$tablename': " . $conn->error . "<br>";
                    }
                }
            }
        } elseif (isset($_POST['action']) && $_POST['action'] == 'delete') {
            // Handle row deletion
            if (isset($_POST['deletion_target'])) {
                $deletionTarget = $_POST['deletion_target'];

                switch ($deletionTarget) {
                    case 'all':
                        $tables = ['user_details', 'jldc', 'nspl','sms','sms2','sms3','spm','railmill','platemill','power_table'];
                        foreach ($tables as $table) {
                            $deleteQuery = "DELETE FROM $table";
                            if ($conn->query($deleteQuery) === TRUE) {
                                echo "All rows from table '$table' deleted successfully.<br>";
                            } else {
                                echo "Error deleting rows from table '$table': " . $conn->error . "<br>";
                            }
                        }
                        break;

                    case 'user_details':
                    case 'jldc':
                    case 'nspl':
                    case 'sms':
                    case 'sms2':
                    case 'sms3':
                    case 'spm':
                    case 'railmill':
                    case 'platemill':
                        $deleteQuery = "DELETE FROM $deletionTarget";
                        if ($conn->query($deleteQuery) === TRUE) {
                            echo "All rows from table '$deletionTarget' deleted successfully.<br>";
                        } else {
                            echo "Error deleting rows from table '$deletionTarget': " . $conn->error . "<br>";
                        }
                        break;

                    default:
                        echo "Invalid deletion target.<br>";
                        break;
                }
            }
        }
    }

    $conn->close();
}
?>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="dbname">Enter Database Name:</label>
    <input type="text" id="dbname" name="dbname" required><br><br>

    <label>Action:</label>
    <input type="radio" id="create" name="action" value="create" required>
    <label for="create">Create Table</label>
    <input type="radio" id="delete" name="action" value="delete" required>
    <label for="delete">Delete Rows</label><br><br>

    <div id="create-table" style="display: none;">
        <label for="tablename">Enter Table Name:</label>
        <input type="text" id="tablename" name="tablename"><br><br>
    </div>

    <div id="delete-rows" style="display: none;">
        <label for="deletion_target">Select Deletion Target:</label>
        <select id="deletion_target" name="deletion_target">
            <option value="all">All Tables</option>
            <option value="user_details">user_details</option>
            <option value="jldc">jldc</option>
            <option value="nspl">NSPL</option>
            <option value="spm">SPM</option>
            <option value="railmill">RAILMILL</option>
            <option value="platemill">PLATEMILL</option>
            <option value="sms">SMS</option>
            <option value="sms2">SMS2</option>
            <option value="sms3">SMS3</option>
        </select><br><br>
    </div>

    <input type="submit" value="Submit">
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var createRadio = document.getElementById('create');
        var deleteRadio = document.getElementById('delete');
        var createTableDiv = document.getElementById('create-table');
        var deleteRowsDiv = document.getElementById('delete-rows');

        createRadio.addEventListener('change', function () {
            createTableDiv.style.display = 'block';
            deleteRowsDiv.style.display = 'none';
        });

        deleteRadio.addEventListener('change', function () {
            createTableDiv.style.display = 'none';
            deleteRowsDiv.style.display = 'block';
        });
    });
</script>

</body>
</html>
