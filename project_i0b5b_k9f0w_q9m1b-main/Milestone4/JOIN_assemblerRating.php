<?php
    session_start();
?>

<html>

<head>
    <title>Find Keyboard Assembler by Rating</title>
</head>

<body>
    <h2>Find Keyboard Assembler by Rating</h2>
    <p>Finds the names of the Keyboard Assemblers whom have made keyboards with input Ratings</p>
	<p><b>Please note that keyboard ratings are on a scale of 1-10. </b></p>

    <form method="POST" action="JOIN_assemblerRating.php">
			<input type="hidden" id="joinSearch" name="joinSearch">
			Rating: <input type="text" name="rate"> <br /><br />
			<input type="submit" value="Search" name="joinSearchSubmit"></p>
		</form>
    <hr />
    
    <a href="keyboard_view.php"><button>View All Tables</button></a>
    <a href="atk-template.php"><button>Go Back to Main Page</button></a>
<br><br>

    <?php 
    $db_conn = NULL;	// login credentials are used in connectToDB()
    $success = true;	// keep track of errors so page redirects only if there are no errors
    $show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())


function handleJoinSearchRequest()
	{
		if (empty($_POST['rate'])) {
			// $feedback_message = "Error: Please fill in the rating field.";
			// echo $feedback_message;
            echo "<p style='color: red;'>Error: Please fill in the rating field.</p>";

			return;
		}


        if (($_POST['rate']) > 10 || ($_POST['rate']) < 1) {
			// $feedback_message = "Error: Please fill in the rating field.";
			// echo $feedback_message;
            echo "<p style='color: red;'>Error: Rating must be an integer (1-10).</p>";

			return;
		}
        if (!preg_match('/^[0-9]+$/', $_POST['rate'])) {
            echo "<p style='color: red;'>Error: rating must be an integer</p>";
            return;
        }

		$query = $_POST['rate'];

		global $db_conn;

		$sql = "SELECT k.KeyboardName, r.Rate, b.AssemblerID, an.Name, an.Experience_level
			FROM RATING_HAS_FEEDBACK r, MADE_BY b, KEYBOARD_CONTAINS k, KEYBOARD_ASSEMBLER_NORMALIZED an
			WHERE r.KeyboardName = k.KeyboardName AND r.KeyboardName = b.KeyboardName AND k.KeyboardName = b.KeyboardName AND an.AssemblerId = b.AssemblerId
			AND r.Rate = $query";

		$result = executePlainSQL($sql);

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
			if (array_key_exists('joinSearch', $_POST)) {
				handleJoinSearchRequest();
			}

			disconnectFromDB();
		}
	}

    if (isset($_POST['joinSearchSubmit'])) {
		handlePOSTRequest();
	}
    
    ?>
    </body>
    </html>