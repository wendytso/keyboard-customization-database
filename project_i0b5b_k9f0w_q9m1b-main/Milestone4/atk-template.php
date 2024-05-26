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



$config["dbuser"] = "ora_wendytso";			
$config["dbpassword"] = "a34159368";	
$config["dbserver"] = "dbhost.students.cs.ubc.ca:1522/stu";
$db_conn = NULL;	

$success = true;	

$show_debug_alert_messages = False; // show which methods are being triggered (see debugAlertMessage())

?>

<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>304 Keyboard Database</title>
	<link rel="stylesheet" href="style.css">
</head>


<body>
	<h1>CPSC 304 Mechancial Keyboard Database Project</h1>

	<h3>Start your database</h3>
	<p>Welcome to the mechanical keyboard database!
	<br>
	To begin, please click the reset button to ensure the initial database is loaded. If you wish to reset the table press on the reset button. If this is the first time you're running this page, you MUST use reset</p>
	<form method="POST" action="atk-template.php">
		<input type="hidden" id="resetTablesRequest" name="resetTablesRequest">
		<p><input type="submit" value="Reset" name="reset"></p>
	</form>

	<hr />
	<h3>Insert Order</h3>
	<p>INSERTION: Inserts new order information for existing userID</p>
	<a href="INSERT_order.php"><button>Insert Order</button></a>
	<hr />

	<h3>Update Order</h3>
	<p>UPDATE: Updates Order Information</p>
	<a href="UPDATE_order.php"><button>Update Order</button></a>
	<hr />

	<h3>Delete Keyboard</h3>
	<p>DELETE: Deletes Existing Keyboard</p>
	<a href="DEL_keyboard.php"><button>Delete Keyboard</button></a>
	<hr />

	<h3>Find Keyboard Assembler by Rating</h3>
	<p>JOIN: Find Keyboard Assembler by their Rating</p>
	<a href="JOIN_assemblerRating.php"><button>Find Assembler</button></a>
	<hr />

	<h3>Filter Users</h3>
	<p>SELECT: Find all Users and their relating information by filtering on the given conditions.</p>
	<a href="select_user.php"><button>Filter Users</button></a>
	<hr />

	<h3>View Data</h3>
	<p>PROJECTION: Display any selected data that exists in this Database system.</p>
	<a href="keyboard_view.php"><button>Display Data</button></a>
	<hr />
	
	<h3>Keycap Count Sorted by Brands</h3>
	<p>AGGREGATION WITH GROUP BY: Number of Keycaps Sorted by their Production Brands</p>
	<a href="AggGB_keycapCountByBrand.php"><button>Determine Keycap Brand Counts</button></a>
	<hr />

	<h3>Keyboard Assembler Salary Summary sorted by Experience Level</h3>
	<p>AGGREGATION WITH HAVING: Salary Summary of Keyboard Assemblers by Experience Levels</p>
	<a href="AggH_assemblerSalaryByEXP.php"><button>View Salary Summary</button></a>
	<hr />

	<h3>Keyboard Assembler Salary Discrepency</h3>
	<p>NESTED AGGREGATION WITH GROUP BY: Find Keyboard assemblers whose total price of keyboards they have assembled is less than the average salary of all assemblers.</p>
	<a href="NESTEDagg_assembler.php"><button>Find Pay Discrepancy</button></a>
	<hr />

	<h3>Keyboards that Configures all wave lighting effects</h3>
	<p>DIVISION: Find all keyboards that configure all wave lighting effects</p>
	<a href="DIV_lightingEffects.php"><button>Find Users with all Wave Lighting</button></a>
	<hr />


	<?php

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
		/* Sometimes the same statement will be executed several times with different values for the variables involved in the query.
		In this case you don't need to create the statement several times. Bound variables cause a statement to only be
		parsed once and you can reuse the statement. This is also very useful in protecting against SQL injection.
		See the sample code below for how this function is used */

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

	function printResult($result)
	{ //prints results from a select statement
		echo "<br>Retrieved data from table places_order table:<br>";
		echo "<table>";
		echo "<tr><th>OrderID</th><th>UserID</th><th>TodayDate</th><th>Total_cost</th></tr>";

		while ($row = OCI_Fetch_Array($result, OCI_ASSOC)) {
			echo "<tr><td>" . $row["ORDERID"] . "</td><td>" . $row["USERID"] . "</td><td>" . $row["TODAYDATE"]  . "</td><td>" . $row["TOTAL_COST"] . "</td></tr>"; //or just use "echo $row[0]"
		}

		echo "</table>";
	}

	function connectToDB(){
		global $db_conn;
		global $config;
	
		$db_conn = oci_connect($config["dbuser"], $config["dbpassword"], $config["dbserver"]);
	
		if ($db_conn) {
			debugAlertMessage("Database is Connected");
			return true;
		} else {
			debugAlertMessage("Cannot connect to Database");
			$e = OCI_Error(); // For oci_connect errors pass no handle
			echo htmlentities($e['message']);
			return false;		}
	}

	function disconnectFromDB()
	{
		global $db_conn;

		debugAlertMessage("Disconnect from Database");
		oci_close($db_conn);
	}
	

	function handleResetRequest()
	{
		global $db_conn;
		global $success;

		$sqlContent = file_get_contents('setup.sql');
        $sqlQueries = explode(';', $sqlContent);

        foreach ($sqlQueries as $sqlQuery) {
            executePlainSQL($sqlQuery);
            oci_commit($db_conn);
        }
        if ($success == True) {
            echo ("<p style='color: blue;'>Reset Successful!</p>");
        }
	}

     
    

	function handleCountRequest()
	{
		global $db_conn;

		$result = executePlainSQL("SELECT Count(*) FROM demoTable");

		if (($row = oci_fetch_row($result)) != false) {
			echo "<br> The number of tuples in demoTable: " . $row[0] . "<br>";
		}
	}


	// HANDLE ALL POST ROUTES
	// A better coding practice is to have one method that reroutes your requests accordingly. It will make it easier to add/remove functionality.
	function handlePOSTRequest()
	{
		if (connectToDB()) {
			if (array_key_exists('resetTablesRequest', $_POST)) {
				handleResetRequest();
			}

			disconnectFromDB();
		}
	}

	if (isset($_POST['reset'])) {
		handlePOSTRequest();
	}
	?>
</body>

</html>