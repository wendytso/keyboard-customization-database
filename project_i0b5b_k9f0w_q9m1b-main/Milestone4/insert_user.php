<?php
// Start the session
session_start();
?>

<html>
<head>
    <title>Insert User</title>
</head>

<body>
<h2>Insert User</h2>
<form method="POST" action="">
    UserID* <input type="text" name="userid" required="required"> <br><br>
    Email <input type="email" name="email" required="required"> <br><br>
    Name <input type="text" name="name" required="required"> <br><br> 
    Experience Level
    <select name="experienceLevel" required="required">
        <option disabled selected value> -- select an option -- </option>
        <option value="novice">Novice</option>
        <option value="beginner">Beginner</option>
        <option value="intermediate">Intermediate</option>
        <option value="advanced">Advanced</option>
        <option value="expert">Expert</option>
    </select>
    <input type="submit" value="Insert User" name="insertSubmit">
</form>

<hr>

<?php
$success = True; //keep track of errors so it redirects the page only if there are no errors
$db_conn = NULL; // edit the login credentials in connectToDB()

function executePlainSQL($cmdstr) {
    global $db_conn, $success;

    $statement = OCIParse($db_conn, $cmdstr);

    if (!$statement) {
        $success = False;
    }

    $r = OCIExecute($statement, OCI_DEFAULT);
    if (!$r) {
        $success = False;
    }

    return $statement;
}

function connectToDB() {
    global $db_conn;

    $db_conn = OCILogon("ora_wendytso", "a34159368", "dbhost.students.cs.ubc.ca:1522/stu");

    if ($db_conn) {
        return true;
    } else {
        return false;
    }
}

function disconnectFromDB() {
    global $db_conn;

    OCILogoff($db_conn);
}

function displayUserTable() {
    global $db_conn;

    $query = "SELECT * FROM USER_NORMALIZED";
    $result = executePlainSQL($query);

    if (!$result) {
        return false;
    }

    echo "<h3>USER_NORMALIZED Table</h3>";
    echo "<table border='1'><tr>";
    echo "<th>UserID</th>";
    echo "<th>Email</th>";
    echo "<th>Experience Level</th>";
    echo "</tr>";

    while ($row = oci_fetch_array($result, OCI_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['USERID'] . "</td>";
        echo "<td>" . $row['EMAIL'] . "</td>";
        echo "<td>" . $row['EXPERIENCE_LEVEL'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    return true;
}

function displayEmailDeterminesNameTable() {
    global $db_conn;

    $query = "SELECT * FROM EMAIL_DETERMINES_NAME";
    $result = executePlainSQL($query);

    if (!$result) {
        return false;
    }

    echo "<h3>EMAIL_DETERMINES_NAME Table</h3>";
    echo "<table border='1'><tr>";
    echo "<th>Email</th>";
    echo "<th>Name</th>";
    echo "</tr>";

    while ($row = oci_fetch_array($result, OCI_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['EMAIL'] . "</td>";
        echo "<td>" . $row['NAME'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    return true;
}

if (isset($_POST['insertSubmit'])) {
    if (connectToDB()) {
        $userid = htmlspecialchars($_POST['userid']);
        $email = htmlspecialchars($_POST['email']);
        $name = htmlspecialchars($_POST['name']);
        $experienceLevel = htmlspecialchars($_POST['experienceLevel']);
        
        // Insert user into USER_NORMALIZED table
        $query = "INSERT INTO USER_NORMALIZED (USERID, EMAIL, EXPERIENCE_LEVEL) 
                  VALUES ('$userid', '$email', '$experienceLevel')";
        $result_user_normalized = executePlainSQL($query);

        if (!$result_user_normalized) {
            echo "<p style='color: red;'>Error: Failed to insert user into USER_NORMALIZED table.</p>";
        } else {
            echo "<p style='color: green;'>User inserted successfully into USER_NORMALIZED table.</p>";
        }

        // Insert user into EMAIL_DETERMINES_NAME table
        $query_email_determines_name = "INSERT INTO EMAIL_DETERMINES_NAME (EMAIL, NAME) 
                                        VALUES ('$email', '$name')";
        $result_email_determines_name = executePlainSQL($query_email_determines_name);

        if (!$result_email_determines_name) {
            echo "<p style='color: red;'>Error: Failed to insert user into EMAIL_DETERMINES_NAME table.</p>";
        } else {
            echo "<p style='color: green;'>User inserted successfully into EMAIL_DETERMINES_NAME table.</p>";
        }

        disconnectFromDB();
    } else {
        echo "<p style='color: red;'>Error: Could not connect to the database.</p>";
    }
}

// Display the updated tables
if (connectToDB()) {
    if (!displayUserTable()) {
        echo "<p style='color: red;'>Error: Failed to fetch user data from USER_NORMALIZED table.</p>";
    }
    if (!displayEmailDeterminesNameTable()) {
        echo "<p style='color: red;'>Error: Failed to fetch email determines name data from EMAIL_DETERMINES_NAME table.</p>";
    }
    disconnectFromDB();
}
?>

<br>
<form action="keyboard.php">
    <input type="submit" value="Go Back to Main Page">
</form>

</body>
</html>
