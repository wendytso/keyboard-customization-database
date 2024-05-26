<?php
    session_start();
?>

<html>

<head>
    <title>Update existing order</title>
</head>

<body>
    <h2>Update existing order</h2>
    <p>The values are case sensitive and if you enter in the wrong case, the update statement will not do anything.</p>
	<p><b>To update an order, you must specify the orderId. Blank fields will not be updated and will remain as their initial value. </b></p>

    <p><b>If you're unsure of which order to update, you can first view the orders by clicking the 'View Orders' button below:</b></p>
    <form method="GET" action="UPDATE_order.php">
		<input type="hidden" id="viewOrderRequest" name="viewOrderRequest">
		<input type="submit" value="View Orders" name="viewOrders"></p>
	</form>

    <p><b>You must fill in at least ONE blank in additional to the OrderID.</b></p>

    <form method="POST" action="UPDATE_order.php">
			<input type="hidden" id="updateQueryRequest" name="updateQueryRequest">
			OrderId: <input type="text" name="insOid"> <br /><br />
			UserId: <input type="text" name="insUid"> <br /><br />
			Date: <input type="text" name="insD"> <br /><br />
			Cost: <input type="text" name="insC"> <br /><br />
			<input type="submit" value="Update Order" name="updateSubmit"></p>
		</form>
    <hr />
    
    <a href="keyboard_view.php"><button>View All Tables</button></a>
    <a href="atk-template.php"><button>Go Back to Main Page</button></a>
<br><br>

    <?php 
    $db_conn = NULL;	// login credentials are used in connectToDB()
    $success = true;	// keep track of errors so page redirects only if there are no errors
    $show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())


    function handleUpdateRequest()
    {
        global $db_conn;
    
        if (empty($_POST['insOid'])) {
            // $feedback_message = "Error: Please specify which order you would like to update";
            // echo $feedback_message;
            echo "<p style='color: red;'>Error: Please specify which order you would like to update</p>";
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $_POST['insOid'])) {
            echo "<p style='color: red;'>Invalid orderId, only alphanumeric characters and underscores are allowed, please try again</p>";
            return;
        }

        if (!empty($_POST['insOid']) && empty($_POST['insUid']) && empty($_POST['insD']) && empty($_POST['insC'])) {
            // $feedback_message = "Error: Please specify an attribute to update";
            // echo $feedback_message;
            echo "<p style='color: red;'>Error: Please specify an attribute to update</p>";

            return;
        }
    
        $old_oid = $_POST['insOid'];
    
        $roid = executePlainSQL("SELECT COUNT(*) FROM PLACES_ORDER WHERE ORDERID = '$old_oid'");
        $row_oid = oci_fetch_row($roid);
        if ($row_oid[0] == 0) {
            // $feedback_message = "Error: The order you wish to update does not exist.";
            // echo $feedback_message;
            echo "<p style='color: red;'>Error: The order you wish to update does not exist.</p>";
            return;
        }
    
        $user = !empty($_POST['insUid']);
        $td = !empty($_POST['insD']);
        $tc = !empty($_POST['insC']);
    
        $sql = "UPDATE PLACES_ORDER SET ";
    
        $updates = array();
    
        if ($user) {
            $new_uid = $_POST['insUid'];
            $ruid = executePlainSQL("SELECT COUNT(*) FROM USER_NORMALIZED WHERE USERID = '$new_uid'");
            $row_uid = oci_fetch_row($ruid);
            if ($row_uid[0] == 0) {
                // $feedback_message = "Error: Foreign key value does not exist in the referred table - this update is prohibited.";
                // echo $feedback_message;
                echo "<p style='color: red;'>Error: Foreign key (userId) value does not exist in the referred table - this update is prohibited.</p>";
                return;
            }
            $updates[] = "USERID = '$new_uid'";
        }
    
        if ($td) {
            $new_td = $_POST['insD'];
            $updates[] = "TodayDate = '$new_td'";
        }
    
        if ($tc) {
            $new_tc = $_POST['insC'];
            $updates[] = "Total_cost = '$new_tc'";
        }
    
        $sql .= implode(", ", $updates);
    
        $sql .= " WHERE OrderID = '$old_oid'";
    
        executePlainSQL($sql);
        oci_commit($db_conn);
    
        echo "<p style='color: green;'>Order updated successfully.</p>";
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
			if (array_key_exists('updateQueryRequest', $_POST)) {
				handleUpdateRequest();
			}

			disconnectFromDB();
		}
	}

    function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('viewOrders', $_GET)) {
				global $db_conn;
				$result = executePlainSQL("SELECT * FROM PLACES_ORDER");
				printAllResult($result);
			}

			disconnectFromDB();
			}
		}

  

    if (isset($_POST['updateSubmit'])) {
		handlePOSTRequest();
	} else if (isset($_GET['viewOrderRequest'])) {
        handleGETRequest();
    }
    
    ?>
    </body>
    </html>