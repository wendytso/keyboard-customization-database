<!-- Oracle file for UBC CPSC304
Originally:
  Created by Jiemin Zhang
  Modified by Simona Radu
  Modified by Jessica Wong (2018-06-22)
  Modified by Jason Hall (23-09-20)
  - And additionally modfied by Antonia Tykei, Wendy Li and Wendy Tso (March 2024)
  -->

<?php
// The preceding tag tells the web server to parse the following text as PHP
// rather than HTML (the default)

// The following 3 lines allow PHP errors to be displayed along with the page
// content. Delete or comment out this block when it's no longer needed.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set some parameters

// Database access configuration
$config["dbuser"] = "ora_wendytso";			// change "cwl" to your own CWL
$config["dbpassword"] = "a34159368";	// change to 'a' + your student number
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;	// login credentials are used in connectToDB()

$success = true;	// keep track of errors so page redirects only if there are no errors

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

?>
<html>
<head>
    <title>View Keyboards</title>
</head>
<body>
<h2>Display Selected Attributes from Selected Table</h2>
<?php
if (connectToDB()) {
    global $db_conn;
    $tables = executePlainSQL("SELECT table_name FROM all_tables WHERE owner = 'ORA_WENDYTSO'");

    disconnectFromDB();
}

if (isset($_GET['table'])) {
    $selectedTable = $_GET['table'];

    if (connectToDB()) {
        $attributesResult = executePlainSQL("SELECT column_name FROM all_tab_columns WHERE table_name = '$selectedTable' AND owner = 'ORA_WENDYTSO'");
        $tableAttributes = [];

        while ($row = oci_fetch_array($attributesResult, OCI_ASSOC)) {
            $tableAttributes[] = $row['COLUMN_NAME'];
        }

        disconnectFromDB();

        if (count($tableAttributes) > 0) {
            echo "<form method='get' action='keyboard_view.php'>";
            echo "<input type='hidden' name='selectedTable' value='$selectedTable'>";
            echo "<label for='attributes[]'>Choose attribute(s) for $selectedTable:</label><br>";
            foreach ($tableAttributes as $attribute) {
                echo "<input type='checkbox' id='$attribute' name='attributes[]' value='$attribute'>";
                echo "<label for='$attribute'>$attribute</label><br>";
            }
            echo "<br><input type='submit' value='Submit' name='searchSubmit'>";
            echo "</form>";

            echo "<form method='get' action='keyboard_view.php'>";
            echo "<input type='submit' value='Go back to select table'>";
            echo "</form>";
        } else {
            echo "<p>Selected table does not exist or has no attributes.</p>";
        }
    }
} else { // if no table selected, display drop down
    ?>
    <form method="get" action="keyboard_view.php">
        <label for="table">Choose a table:</label>
        <select id="table" name="table">
            <?php
            while ($row = oci_fetch_array($tables, OCI_ASSOC)) {
                foreach ($row as $column) {
                    echo "<option value=\"$column\">$column</option>";
                }
            }
            ?>

        </select>
        <br><br>
        <input type="submit" value="Submit">
    </form>
    <?php
}
?>
<a href="atk-template.php"><button>Go Back to Main Page</button></a>
<br><br>


<?php
// The following code will be parsed as PHP

function debugAlertMessage($message)
{
    global $show_debug_alert_messages;

    if ($show_debug_alert_messages) {
        echo "<script type='text/javascript'>alert('" . $message . "');</script>";
    }
}

function executePlainSQL($cmdstr)
{ //takes a plain (no bound variables) SQL command and executes it
    //echo "<br>running ".$cmdstr."<br>";
    global $db_conn, $success;

    $statement = oci_parse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn); // For oci_parse errors pass the connection handle
        echo htmlentities($e['message']);
        $success = False;
    }

    $r = oci_execute($statement, OCI_DEFAULT);
    if (!$r) {
        echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
        $e = oci_error($statement); // For oci_execute errors pass the statementhandle
        echo htmlentities($e['message']);
        $success = False;
    }


    return $statement;
}

function executeBoundSQL($cmdstr, $list)
{
    global $db_conn, $success;
    $statement = oci_parse($db_conn, $cmdstr);

    if (!$statement) {
        echo "<br>Cannot parse the following command: " . $cmdstr . "<br>";
        $e = OCI_Error($db_conn);
        echo htmlentities($e['message']);
        $success = False;
    }

    foreach ($list as $tuple) {
        foreach ($tuple as $bind => $val) {
            //echo $val;
            //echo "<br>".$bind."<br>";
            oci_bind_by_name($statement, $bind, $val);
            unset($val); //make sure you do not remove this. Otherwise $val will remain in an array object wrapper which will not be recognized by Oracle as a proper datatype
        }

        $r = oci_execute($statement, OCI_DEFAULT);
        if (!$r) {
            echo "<br>Cannot execute the following command: " . $cmdstr . "<br>";
            $e = OCI_Error($statement); // For oci_execute errors, pass the statementhandle
            echo htmlentities($e['message']);
            echo "<br>";
            $success = False;
        }
    }
}


function connectToDB()
{
    global $db_conn;
    global $config;

    // Your username is ora_(CWL_ID) and the password is a(student number). For example,
    // ora_platypus is the username and a12345678 is the password.
    // $db_conn = oci_connect("ora_cwl", "a12345678", "dbhost.students.cs.ubc.ca:1522/stu");
    $db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);

    if ($db_conn) {
        debugAlertMessage("Database is Connected");
        return true;
    } else {
        debugAlertMessage("Cannot connect to Database");
        $e = OCI_Error(); // For oci_connect errors pass no handle
        echo htmlentities($e['message']);
        return false;
    }
}

function disconnectFromDB()
{
    global $db_conn;

    debugAlertMessage("Disconnect from Database");
    oci_close($db_conn);
}

function handleSelectRequest() {
    global $db_conn;

    if (!isset($_GET["attributes"]) || count($_GET["attributes"]) === 0) {
        echo ("<p style='color: red;'>No attribute selected.</p>");
        return;
    }
    $attributes = implode(", ", $_GET['attributes']);

    $result = executePlainSQL("SELECT ". $attributes . " FROM ". $_GET["selectedTable"]);

    // generate table to display result
    echo "<table border='1'><tr>";

    foreach ($_GET['attributes'] as $attribute) {
        echo "<th>$attribute</th>";
    }

    echo "</tr>";

    $rowsFetched = false;


    while ($row = oci_fetch_array($result, OCI_ASSOC)) {

        $rowsFetched = true;
        echo "<tr>";
        foreach ($_GET['attributes'] as $attribute){
            $cellValue = $row["$attribute"] ?? 'N/A';
            echo "<td>$cellValue</td>";
        }
        echo "</tr>";
    }

    echo "</table>";

    if (!$rowsFetched) {
        echo "<p style='color: blue;'>No tuple found.</p>";
    }
}

if (isset($_GET['searchSubmit'])) {
    if (connectToDB()) {
        if (array_key_exists('selectedTable', $_GET)) {
            handleSelectRequest();
        }

        disconnectFromDB();
    }
}
?>
</body>
</html>
