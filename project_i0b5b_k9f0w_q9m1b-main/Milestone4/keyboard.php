<?php
    session_start();
?>

<html>

<head>
	<title>Keyboard Database</title>
</head>

<body>
	<h2>Reset</h2>
	<p>If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>

	<form method="POST" action="keyboard.php">
		<!-- "action" specifies the file or page that will receive the form data for processing. As with this example, it can be this same file. -->
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
        <p><input type="submit" value="Reset" name="reset"></p>
	</form>

    <?php
    $db_conn = NULL;	// login credentials are used in connectToDB()

    $success = true;	// keep track of errors so page redirects only if there are no errors
    
    $show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

    function handleResetRequest()
	{
		global $db_conn;
		global $success;

		$sqlContent = file_get_contents('setup.sql');
        $sqlQueries = explode(';', $sqlContent);

        foreach ($sqlQueries as $sqlQuery) {
            executePlainSQL($sqlQuery);
            OCICommit($db_conn);
        }
        if ($success == True) {
            echo ("<p style='color: blue;'>Successfully resetted.</p>");
        }
	}

        if (isset($_POST['reset'])) {
        handlePOSTRequest();
    }
    ?>

    <hr />
    <h2>View All Tables</h2>
    <a href="keyboard_view.php"><button>View All Tables</button></a>
    <hr />

    <h2>Select User</h2>
    <a href="select_user.php"><button>Select User</button></a>
    <hr />

    <h2>User sign-up</h2>
    <form method="POST" action="keyboard.php">
    <input type="hidden" id="insertUserQueryRequest" name="insertUserQueryRequest">
            Username* <input type="text" name="username" required="required"> <br><br>
            Password* <input type="password" name="password" required="required"> <br><br>
            <label for="userTypeSelect">User Type* </label>
            <select name="userType" id="userTypeSelect" required="required">
                <option disabled selected value> -- select an option -- </option>
                <option value="user" name = "user">User</option>
                <option value="assembler" name = "assembler">Assembler</option>
            </select>
            <br><br>

            UserID* <input type="text" name="userid" required="required"> <br><br>
            Name  <input type="text" name="name" required="required"> <br><br>
            Email <input type="email" name="email" required="required"> <br><br>
            Phone Number (eg. 123-456-7890) <input type="text" name="phone"> <br><br>
            Experience Level 
            <select name="experienceLevel" required="required">
                <option disabled selected value> -- select an option -- </option>
                <option value="novice" name="novice">Novice</option>
                <option value="beginner" name="beginner">Beginner</option>
                <option value="intermediate" name="intermediate">Intermediate</option>
                <option value="advanced" name="advanced">Advanced</option>
                <option value="expert" name="expert">Expert</option>
            </select>

            <div id="assemblerInfo" style="display: none;">
            <label for="assemblerOption">Keyboard Assembler* </label>
            <select name="assemblerOption" id="assemblerOption">
                <option disabled selected value> -- select an option -- </option>
                <option value="existing" name="existing">Use Existing Assembler ID</option>
                <option value="createNew" name="createNew">Create New Assembler</option>
            </select>
            <br><br>
            <div id="existingAssembler" style="display: none;">
                Assembler ID* <input type="text" name="assemblerID"> <br><br>
            </div>
            <div id="newAssembler" style="display: none;">
                New Assembler Info:<br />
                Assembler Name* <input type="text" name="assemblerName"> <br><br>
                Experience Level* 
                <select name="experienceLevel" required="required">
                    <option disabled selected value> -- select an option -- </option>
                    <option value="novice" name="novice">Novice</option>
                    <option value="beginner" name="beginner">Beginner</option>
                    <option value="intermediate" name="intermediate">Intermediate</option>
                    <option value="advanced" name="advanced">Advanced</option>
                    <option value="expert" name="expert">Expert</option>
                </select>
                <br><br>
                Salary* <input type="text" name="salary"> <br><br>
                Number of Completed Keyboards* <input type="text" name="completedKeyboards"> <br><br>
            </div>
        </div>
        <input type="submit" value="Sign Up" name="insertSubmit">
    </form>

    <script>
    const userTypeSelect = document.getElementById('userTypeSelect');
    const assemblerInfoDiv = document.getElementById('assemblerInfo');
    const existingAssemblerDiv = document.getElementById('existingAssembler');
    const newAssemblerDiv = document.getElementById('newAssembler');
    const assemblerOption = document.getElementById('assemblerOption');

    userTypeSelect.addEventListener('change', function () {
        if (userTypeSelect.value === 'user') {
            assemblerInfoDiv.style.display = 'none';
            assemblerOption.required = false;
        } else {
            assemblerInfoDiv.style.display = 'block';
            assemblerOption.required = true;
        }
    });

    assemblerOption.addEventListener('change', function () {
        if (assemblerOption.value === 'existing') {
            existingAssemblerDiv.style.display = 'block';
            newAssemblerDiv.style.display = 'none';
            document.getElementById('assemblerID').required = true;
            document.getElementById('assemblerName').required = false;
        } else if (assemblerOption.value === 'createNew') {
            existingAssemblerDiv.style.display = 'none';
            newAssemblerDiv.style.display = 'block';
            document.getElementById('assemblerID').required = false;
            document.getElementById('assemblerName').required = true;
        }
    });
    </script>

<?php
function handleInsertUserRequest() {
    global $db_conn, $success;

    if (!preg_match('/^[a-zA-Z0-9_]+$/', $_POST['userid'])) {
        echo "<p style='color: red;'>Invalid userid, only alphanumeric characters and underscores are allowed, please try again</p>";
        return;
    }
    if (!preg_match('/^[a-zA-Z\s]+$/', $_POST['name'])) {
        echo "<p style='color: red;'>Invalid name, please try again.</p>";
        return;
    }
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
        echo "<p style='color: red;'>Invalid email, please try again.</p>";
        return;
    }
    if (!empty($_POST['phone']) && !preg_match('/^\d{3}-\d{3}-\d{4}$/', $_POST['phone'])) {
        echo "<p style='color: red;'>Invalid phone number, please try again.</p>";
        return;
    }

    //login info insert
    $logintuple = array (
        ":bind1" => htmlspecialchars($_POST['userid']),
        ":bind2" => password_hash($_POST['password'], PASSWORD_DEFAULT)
    );

    $loginAlltuples = array ($logintuple);
    executeBoundSQL("insert into UserLogInfo values (:bind1, :bind2)", $loginAlltuples);
    OCICommit($db_conn);

    if (!$success) {
        echo ("<p style='color: red;'>Sign up failed: UserId already exists.</p>");
        return;
    }
    
    //user insert
    $userTuple = array(
        ":bind1" => htmlspecialchars($_POST['userid']),
        ":bind2" => htmlspecialchars($_POST['name']),
        ":bind3" => htmlspecialchars($_POST['email']),
        ":bind4" => htmlspecialchars($_POST['phone']),
        ":bind5" => htmlspecialchars($_POST['experienceLevel'])
    );

    $userAlltuples = array($userTuple);
    executeBoundSQL("insert into Users values (:bind1, :bind2, :bind3, :bind4, :bind5)", $userAlltuples);
    OCICommit($db_conn);

    if (!$success) {
        echo ("<p style='color: red;'>Sign up failed: Email already exists.</p>");
        executeBoundSQL("delete from UserLogInfo where UserId = (:bind1)", $loginAlltuples);
        OCICommit($db_conn);
        return;
    }

    if ($_POST['userType'] == "assembler") {
        if ($_POST['assemblerOption'] == "createNew") {
            $assemblerTuple = array(
                ":bind1" => htmlspecialchars($_POST['assemblerName']),
                ":bind2" => htmlspecialchars($_POST['experienceLevel']),
                ":bind3" => htmlspecialchars($_POST['salary']),
                ":bind4" => htmlspecialchars($_POST['completedKeyboards'])
            );

            $assemblerAlltuples = array($assemblerTuple);
            executeBoundSQL("insert into Keyboard_Assembler values (AssemblerID_Sequence.nextval, :bind1, :bind2, :bind3, :bind4)", $assemblerAlltuples);
            OCICommit($db_conn);
            $assemblerID = executePlainSQL("SELECT AssemblerID_Sequence.currval FROM dual");
            $id = oci_fetch_assoc($assemblerID)['CURRVAL'];
            echo "<br> The assembler id is: " . $id . "<br>";
        }
        if ($success) {
            if ($_POST['assemblerOption'] == "existing") {
                $id = $_POST['assemblerID'];
            }
            //user insert
            $assemblerUserTuple = array(
                ":bind1" => $_POST['userid'],
                ":bind2" => $id
            );

            $assemblerUserAlltuples = array($assemblerUserTuple);
            executeBoundSQL("insert into Assembler_Users values (:bind1, :bind2)", $assemblerUserAlltuples);
            OCICommit($db_conn);

            if ($success == FALSE) {
                echo ("<p style='color: red;'>Sign up failed: Invalid assembler ID.</p>");
                executeBoundSQL("delete from UserLogInfo where UserId = (:bind1)", $loginAlltuples);
                executeBoundSQL("delete from Users where UserId = (:bind1)", $userAlltuples);
                executeBoundSQL("delete from Keyboard_Assembler where AssemblerID = (:bind2)", $assemblerUserAlltuples);
                OCICommit($db_conn);
            } else {
                echo ("<p style='color: green;'>Successfully signed up.</p>");
            }
        } else {
            echo ("<p style='color: red;'>Sign up failed: Assembler already exists</p>");
            executeBoundSQL("delete from UserLogInfo where UserId = (:bind1)", $loginAlltuples);
            executeBoundSQL("delete from Users where UserId = (:bind1)", $userAlltuples);
            OCICommit($db_conn);
        }
    }
}

if (isset($_POST['insertSubmit'])) {
    handlePOSTRequest();
}
?>

<hr />

<h2>User Log-in</h2>
<form method="POST" action="login.php">
    <input type="hidden" id="loginQueryRequest" name="loginQueryRequest">
    
    Username: <input type="text" name="username" required="required"> <br><br>
    Password: <input type="password" name="password" required="required"> <br><br>

    <input type="submit" value="Log In" name="loginSubmit">
</form>

<?php
echo "<p style='color: red;'>" . $_SESSION["error_message"] . "</p>";
session_unset(); 
?>

<hr />

<h2>Count Total</h2>
<form method="GET" action="keyboard.php"> <!--refresh page when submitted-->
            <input type="hidden" id="countUserLogInfoTupleRequest" name="countUserLogInfoTupleRequest">
            <input type="submit" name="countUserLogTuples"></p>
        </form>
<h2>Count Users</h2>
<form method="GET" action="keyboard.php"> <!-- refresh page when submitted -->
    <input type="hidden" id="countUserTuplesRequest" name="countUserTuplesRequest">
    <input type="submit" name="countUserTuples"></p>
</form>

<h2>Count Assemblers</h2>
<form method="GET" action="keyboard.php"> <!-- refresh page when submitted -->
    <input type="hidden" id="countAssemblerTuplesRequest" name="countAssemblerTuplesRequest">
    <input type="submit" name="countAssemblerTuples"></p>
</form>

<?php

function debugAlertMessage($message) {
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr) {
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement);
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function executeBoundSQL($cmdstr, $list) {
    global $db_conn, $success;
    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            OCIBindByName($statement, $bind, $val);
            unset($val);
        }

        $r = OCIExecute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement);
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}

function connectToDB() {
    global $db_conn;

    $db_conn = OCILogon("ora_wendytso", "a34159368", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error();
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

function handleCountRequest1() {
    global $db_conn;

    $result = executePlainSQL("SELECT COUNT(*) FROM UserLogInfo");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in UserLogInfo: " . $row[0] . "<br>";
    }
}

function handleCountRequest2() {
    global $db_conn;

    $result = executePlainSQL("SELECT COUNT(*) FROM Assemblers");

    if (($row = oci_fetch_row($result)) != false) {
        echo "<br> The number of tuples in Assemblers: " . $row[0] . "<br>";
    }
}

// HANDLE ALL POST ROUTES
function handlePOSTRequest() {
    if (connectToDB()) {
        if (array_key_exists('resetTablesRequest', $_POST)) {
            handleResetRequest();
        } else if (array_key_exists('insertUserQueryRequest', $_POST)) {
            handleInsertUserRequest();
        } else if (array_key_exists('loginQueryRequest', $_POST)) {
            handleLoginRequest();
        }

        disconnectFromDB();
    }
}

// HANDLE ALL GET ROUTES
function handleGETRequest() {
    if (connectToDB()) {
        if (array_key_exists('countUserLogInfoTupleRequest', $_GET)) {
            handleCountRequest1(); 
        } else if (array_key_exists('countUsersTupleRequest', $_GET)) {
            handleCountRequest2(); 
        } else if (array_key_exists('countAssemblersTupleRequest', $_GET)) {
            handleCountRequest3(); 
        }

        disconnectFromDB();
    }
}

if (isset($_POST['loginSubmit'])) {
    handlePOSTRequest(); // Handle login submission
} else if (isset($_GET['countUserLogTuples']) || isset($_GET['countUserTuples']) || isset($_GET['countAssemblerTuples'])) {
    handleGETRequest(); // Handle count requests
}
?>
</body>
</html>




