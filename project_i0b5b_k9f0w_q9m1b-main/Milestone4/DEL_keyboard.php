<?php
    session_start();
?>

<html>

<head>
    <title>Deletion of KEYBOARD_CONTAINS</title>
</head>

<body>
    <h2>Delete Keyboard</h2>
    <p>Please indicate the KeyboardName you wish to delete.</h2>



    <form method="POST" action="DEL_keyboard.php">
        <input type="hidden" id="DELkeyboardREQ" name="DELkeyboardREQ">
        KeyboardName: <input type="text" name="KeyboardName" required="required"> <br><br>
        <p><input type="submit" value="Delete" name="deleteKeyboard"></p>
    </form>
    <hr />

    <a href="keyboard_view.php"><button>View All Tables</button></a>
    <a href="atk-template.php"><button>Go Back to Main Page</button></a>
<br><br>
    

    <?php 
    $db_conn = NULL;	// login credentials are used in connectToDB()

    $success = true;	// keep track of errors so page redirects only if there are no errors
    
    $show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

    function handleDeleteKeyboardReq()
	{
		global $db_conn;
		global $success;

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $_POST['KeyboardName'])) {
            echo "<p style='color: red;'>Invalid keyboardname, only alphanumeric characters and underscores are allowed, please try again</p>";
            return;
        }


		$kb = $_POST['KeyboardName'];
        $ruid = executePlainSQL("SELECT COUNT(*) FROM KEYBOARD_CONTAINS WHERE KeyboardName = '$kb'");
		$row_uid = oci_fetch_row($ruid);
        if ($row_uid[0] == 0) {
            // $feedback_message = "Error: KeyboardName does not exist - nothing deleted.";
            echo "<p style='color: red;'>Error: KeyboardName does not exist - nothing deleted.</p>";
            return;
        }

        $logintuple = array (
            "bind1" => htmlspecialchars($_POST['KeyboardName']),
        );

        // $before_deletion_count = executePlainSQL("SELECT COUNT(*) FROM KEYBOARD_CONTAINS");
        // $row = pg_fetch_assoc($result);
        // $count = $row['COUNT(*)'];

        $loginAlltuples = array ($logintuple);
        executeBoundSQL("delete from KEYBOARD_CONTAINS where KeyboardName = (:bind1)", $loginAlltuples);
        OCICommit($db_conn);
        echo "<p style='color: green;'>Deletion successful!</p>";
      

        // $after_deletion_count = executePlainSQL("SELECT COUNT(*) FROM KEYBOARD_CONTAINS");
        // $affectedRows = $before_deletion_count - $after_deletion_count;
        // if ($after_deletion_count = 0) {
        //     echo ("<p style='color: red;'>Deletion failed: womp womp</p>");
        // }

            $query = "SELECT * FROM KEYBOARD_CONTAINS";
            $result1 = executePlainSQL($query);
            printAllResult($result1);

            $query = "SELECT * FROM MECHANICAL";
            $result2 = executePlainSQL($query);
            printAllResult($result2);

            $query = "SELECT * FROM GAMING";
            $result3 = executePlainSQL($query);
            printAllResult($result3);

            $query = "SELECT * FROM MADE_BY";
            $result4 = executePlainSQL($query);
            printAllResult($result4);

            $query = "SELECT * FROM RATING_HAS_FEEDBACK";
            $result5 = executePlainSQL($query);
            printAllResult($result5);

            $query = "SELECT * FROM ACCESSORY_INCLUDED_ON";
            $result6 = executePlainSQL($query);
            printAllResult($result6);

            $query = "SELECT * FROM BOARD_HAS_LAYOUT";
            $result7 = executePlainSQL($query);
            printAllResult($result7);

            $rowsAffected = OCIRowCount($db_conn);
            echo "Rows affected: " . $rowsAffected . "<br>";
        
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



function printAllResult($result) {
    echo "<table>";
  
    $columnNames = array();
    $isFirstRow = true;
  
    while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
      if ($isFirstRow) {
        $numColumns = oci_num_fields($result);
        for ($i = 1; $i <= $numColumns; $i++) {
          $columnNames[] = oci_field_name($result, $i);
        }
        
        echo "<tr>"; 
        foreach ($columnNames as $columnName) {
          echo "<th>" . $columnName . "</th>";
        }
        echo "</tr>"; 
        $isFirstRow = false; 
      }
  
      echo "<tr>";
      foreach ($row as $value) {
        echo "<td>" . $value . "</td>";
      }
      echo "</tr>"; 
    }
  
    echo "</table>";
  }

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

	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('DELkeyboardREQ', $_POST)) {
				handleDeleteKeyboardReq();
			} 

			disconnectFromDB();
		}
	}

    if (isset($_POST['deleteKeyboard'])) {
		handlePOSTRequest();
	} 
    
    ?>
    </body>
    </html>