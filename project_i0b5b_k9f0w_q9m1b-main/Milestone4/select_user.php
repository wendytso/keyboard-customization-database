<?php
// Start the session
session_start();
?>
<html>
<head>
    <title>Keyboard</title>
</head>

<body>
<h2>Select User(s)</h2>
<p>Find all Users and their relating information by filtering on the given conditions.</p>
<form method="GET" action="select_user.php">
    <input type="hidden" id="selectUserQueryRequest" name="selectUserQueryRequest">
    

    <select name="attribute1" id="attribute1">
        <option value="None">None</option>
        <option value="UserID">UserID</option>
        <option value="email">Email</option>
        <option value="experience_level">Experience Level</option>
    </select>
    = <input type="text" name="val1" disabled>
    <br><br>
    
    <select name="logicalOperator1" id="logicalOperator1">
        <option value="AND">AND</option>
        <option value="OR">OR</option>
    </select>
    <br><br>

    <div id="second" style="display: none;">
        <select name="attribute2" id="attribute2">
            <option value="None">None</option>
            <option value="UserID">UserID</option>
            <option value="email">Email</option>
            <option value="experience_level">Experience Level</option>
        </select>
        =
        <input type="text" name="val2" disabled>
        <br><br>
    </div>
    
    <div id="secondLogicalOperator" style="display: none;">
        <select name="logicalOperator2" id="logicalOperator2">
            <option value="AND">AND</option>
            <option value="OR">OR</option>
        </select>
        <br><br>
    </div>

    <div id="third" style="display: none;">
        <select name="attribute3" id="attribute3">
            <option value="None">None</option>
            <option value="UserID">UserID</option>
            <option value="email">Email</option>
            <option value="experience_level">Experience Level</option>
        </select>
        =
        <input type="text" name="val3" disabled>
        <br><br>
    </div>
    
    <input type="submit" value="Select User(s)" name="selectSubmit">
</form>
<a href="atk-template.php"><button>Go Back to Main Page</button></a>
<br><br>

<script>
    function toggleVisibilityAndInput(attribute, operator, input, div) {
    var selectedAttribute = attribute.value;
    if (selectedAttribute === 'None') {
        if (operator) {
            operator.style.display = 'none';
        }
        input.disabled = true;
        if (div) {
            div.style.display = 'none';
        }
    } else {
        if (operator) {
            operator.style.display = 'inline-block';
        }
        input.disabled = false;
        if (div) {
            div.style.display = 'block';
        }
    }
}

function handleAttributeChange(attribute, operator, input, div) {
    toggleVisibilityAndInput(attribute, operator, input, div);
}

attribute1.addEventListener('change', function () {
    var operator = document.getElementById('logicalOperator1');
    var input = document.getElementsByName('val1')[0];
    var div = document.getElementById('second');
    var divOperator = document.getElementById('secondLogicalOperator'); 
    handleAttributeChange(attribute1, operator, input, div);
    handleAttributeChange(attribute1, operator, null, divOperator); 
});

attribute2.addEventListener('change', function () {
    var operator = document.getElementById('logicalOperator2');
    var input = document.getElementsByName('val2')[0];
    var div = document.getElementById('third');
    var divOperator = document.getElementById('secondLogicalOperator'); 
    handleAttributeChange(attribute2, operator, input, div);
    if (div.style.display !== 'none') {
        divOperator.style.display = 'inline-block';
    } else {
        divOperator.style.display = 'none';
    }
});

attribute3.addEventListener('change', function () {
    var input = document.getElementsByName('val3')[0];
    var div = null;
    handleAttributeChange(attribute3, null, input, div);
});



</script>





<?php
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()
$show_debug_alert_messages = False; // set to True if you want alerts to show you which methods are being triggered (see how it is used in debugAlertMessage())

function debugAlertMessage($message) {
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr) { //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $cmdstr);
    //There are a set of comments at the end of the file that describe some of the OCI specific functions and how they work

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For OCIParse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For OCIExecute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }

    return $statement;
}

function connectToDB() {
    global $db_conn;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    $db_conn = OCILogon("ora_wendytso", "a34159368", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For OCILogon errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    OCILogoff($db_conn);
}

function handleselectRequest() {
    global $db_conn;

    $attribute1 = $_GET['attribute1'];
    $val1 = htmlspecialchars($_GET['val1']);
    $attribute2 = $_GET['attribute2'];
    $val2 = htmlspecialchars($_GET['val2']);
    $attribute3 = $_GET['attribute3'];
    $val3 = htmlspecialchars($_GET['val3']);

    $logicalOperator1 = isset($_GET['logicalOperator1']) ? $_GET['logicalOperator1'] : '';
    $logicalOperator2 = isset($_GET['logicalOperator2']) ? $_GET['logicalOperator2'] : '';

    $query = "SELECT * FROM USER_NORMALIZED WHERE ";

    // Add first condition
    if ($attribute1 != 'None' && !empty($val1)) {
        $query .= "$attribute1 = '$val1'";
    }

    // Add second condition with logical operator
    if ($attribute2 != 'None' && !empty($val2)) {
        $query .= " $logicalOperator1 $attribute2 = '$val2'";
    }

    // Add third condition with logical operator
    if ($attribute3 != 'None' && !empty($val3)) {
        $query .= " $logicalOperator2 $attribute3 = '$val3'";
    }

    $result = executePlainSQL($query);

    echo "<table border='1'><tr>";
    echo "<th>UserID</th>";
    echo "<th>Email Address</th>";
    echo "<th>Experience Level</th>";
    echo "</tr><tr>";

    $rowsFetched = false;

    while ($row = oci_fetch_array($result, OCI_ASSOC)) {
        $rowsFetched = true;
        foreach ($row as $column) {
            echo "<td>$column</td>";
        }
        echo "</tr>";
    }

    echo "</table>";

    if (!$rowsFetched) {
        echo "<p style='color: red;'>No user found.</p>";
    }
}

if (isset($_GET['selectSubmit'])) {
    if (connectToDB()) {
        if (array_key_exists('selectUserQueryRequest', $_GET)) {
            handleselectRequest();
        }

        disconnectFromDB();
    }
}
?>
</body>
</html>
