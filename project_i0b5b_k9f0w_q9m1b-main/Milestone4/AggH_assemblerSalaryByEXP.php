<?php
    session_start();
?>

<html>

<head>
    <title>Summarize Salary of Keyboard Assemblers by Experience Level on KEYBOARD_ASSEMBLER_NORMALIZED</title>
</head>

<body>
    <h2>Salary Summary of Keyboard Assemblers by Experience Level</summary></h2>

    <p>Determine the max, min, and average salaries of keyboard assemblers as well as the number of them, by their experience level.</p>
    <form method="GET" action="AggH_assemblerSalaryByEXP.php">
        <input type="hidden" id="AGGHassemblerREQ" name="AGGHassemblerREQ">
        <p><input type="submit" value="View Salary Summary" name="AGGHassembler"></p>
    </form>
    <hr />
    
    <a href="keyboard_view.php"><button>View All Tables</button></a>
    <a href="atk-template.php"><button>Go Back to Main Page</button></a>
<br><br>

    <?php 
    $db_conn = NULL;	// login credentials are used in connectToDB()
    $success = true;	// keep track of errors so page redirects only if there are no errors
    $show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())


function handleAGGHassemblerREQ()  {
		global $db_conn;

        $result = executePlainSQL("SELECT Experience_level, MAX(Salary), MIN(Salary), AVG(Salary), Count(*)
         FROM KEYBOARD_ASSEMBLER_NORMALIZED GROUP BY Experience_level 
         HAVING MIN(Number_of_completed_keyboards) >= 1");
        OCICommit($db_conn);
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

	function handleGETRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('AGGHassemblerREQ', $_GET)) {
				handleAGGHassemblerREQ();
			} 

			disconnectFromDB();
		}
	}

    if (isset($_GET['AGGHassembler'])) {
		handleGETRequest();
	} 
    
    ?>
    </body>
    </html>