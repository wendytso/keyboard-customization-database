<?php
    session_start();
?>

<html>

<head>
    <title>Insert New Order for Existing User</title>
</head>

<body>
    <h2>Insert New Order for Existing User</h2>
    <p>Inserts new order information for existing userID</p>

    <form method="POST" action="INSERT_order.php">
		<input type="hidden" id="insertQueryRequest" name="insertQueryRequest">
		OrderId: <input type="text" name="insOid"> <br /><br />
		UserId: <input type="text" name="insUid"> <br /><br />
		Date: <input type="text" name="insD"> <br /><br />
		Cost: <input type="text" name="insC"> <br /><br />
		<input type="submit" value="Insert" name="insertSubmit"></p>
	</form>
    <hr />
    
    <a href="keyboard_view.php"><button>View All Tables</button></a>
    <a href="atk-template.php"><button>Go Back to Main Page</button></a>
<br><br>

    <?php 
    $db_conn = NULL;	// login credentials are used in connectToDB()
    $success = true;	// keep track of errors so page redirects only if there are no errors
    $show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())


    function handleInsertRequest()
	{
        
        
		if (empty($_POST['insOid']) || empty($_POST['insUid']) || empty($_POST['insD']) || empty($_POST['insC'])) {
			// $feedback_message = "Error: Please fill in all fields.";
			// echo $feedback_message;
            echo "<p style='color: red;'>Error: Please fill in all fields.</p>";
			return;
		}

        $date = $_POST['insD'];
        if (!strtotime($date)) {
            // $feedback_message = "Error: Please enter a valid date as (e.g 10-MAR-24).";
            // echo $feedback_message;
            echo "<p style='color: red;'>Error: Please enter a valid date as (e.g 10-MAR-24).</p>";

            return;
        }

        $cost = $_POST['insC'];
        if (!is_numeric($cost)) {
            // $feedback_message = "Error: Total cost must be a number (e.g 200.20).";
            // echo $feedback_message;
            echo "<p style='color: red;'>Error: Total cost must be a number (e.g 200.20).</p>";

            return;
        }

        if ((!preg_match('/^[a-zA-Z0-9_]+$/', $_POST['insOid'])) || (!preg_match('/^[a-zA-Z0-9_]+$/', $_POST['insUid']))) {
            echo "<p style='color: red;'>Invalid orderId and/or userId, only alphanumeric characters and underscores are allowed, please try again</p>";
            return;
        }

		$uid = $_POST['insUid'];
        $ruid = executePlainSQL("SELECT COUNT(*) FROM USER_NORMALIZED WHERE USERID = '$uid'");
		$row_uid = oci_fetch_row($ruid);
        if ($row_uid[0] == 0) {
            // $feedback_message = "Error: Foreign key value does not exist in the referred table.";
            // echo $feedback_message;
            echo "<p style='color: red;'>Error: Foreign key value does not exist in the referred table.</p>";

            return;
        }

		$oid = $_POST['insOid'];
        $roid = executePlainSQL("SELECT COUNT(*) FROM PLACES_ORDER WHERE ORDERID = '$oid'");
		$row_oid = oci_fetch_row($roid);
        if ($row_oid[0] != 0) {
            // $feedback_message = "Error: OrderId must be unique.";
            // echo $feedback_message;
            echo "<p style='color: red;'>Error: Duplicate OrderId; OrderId must be unique (i.e. cannot exist in current table).</p>";
            return;
        }
	
	try {
		global $db_conn;

		//Getting the values from user and insert data into the table
		$tuple = array(
			":bind1" => $_POST['insOid'],
			":bind2" => $_POST['insUid'],
			":bind3" => $_POST['insD'],
			":bind4" => $_POST['insC']
		);

		$alltuples = array(
			$tuple
		);

		executeBoundSQL("insert into places_order values (:bind1, :bind2, :bind3, :bind4)", $alltuples);
		oci_commit($db_conn);
		// $feedback_message = "Insertion successful!";
		// echo $feedback_message;
        echo "<p style='color: green;'>Insertion successful!</p>";


	
	} catch (Exception $e) {
		// $feedback_message = "Cannot insert - Foreign Key - UserId does not exist";
		// echo $feedback_message;
        echo "<p style='color: red;'>Cannot insert - Foreign Key - UserId does not exist</p>";

	}

    $result = executePlainSQL("SELECT * FROM PLACES_ORDER");
    printAllResult($result);
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
            echo "<th style='padding: 8px; border: 1px solid black;'>" . $columnName . "</th>";			
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
			if (array_key_exists('insertQueryRequest', $_POST)) {
				handleInsertRequest();
			} 

			disconnectFromDB();
		}
	}

    if (isset($_POST['insertSubmit'])) {
		handlePOSTRequest();
	}
    
    ?>
    </body>
    </html>