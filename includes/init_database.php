<?

//initialize dbConnection
function initDB() {
	$host="localhost";
	$db="name_of_your_database";
	$db_user="user_name";
	$db_pass="password";
	$dbh=mysqli_connect($host, $db_user, $db_pass, $db);
	return $dbh;
}

//
?>