<?php
ini_set('display_errors', 'On');

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "sibailaj-db", "j1nl10en0wr49WVv", "sibailaj-db");

if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
	echo "Connection worked!<br>";
}

//var_dump(isset($_POST['name']) && isset($_POST['category']) && isset($_POST['length']));
//var_dump($_POST);

if (isset($_POST['name']) && isset($_POST['category']) && isset($_POST['length'])) {
	if (!empty($_POST['name']) && !empty($_POST['category']) && !empty($_POST['length']) && (ctype_digit($_POST['length']) && is_numeric($_POST['length']) && $_POST['length'] > 0)) {
		$length = (int)$_POST['length'];
		if($mysqli->query("INSERT INTO video_inventory (name, category, length, rented) VALUES ('$_POST[name]', 
			'$_POST[category]', $length, 0);") === true) {
			echo "Video Successfully Added.<br>";
		} else {
			echo "Error: Video not added.<br>";
		}
	} else {
		if (empty($_POST['name'])) {
			echo "Error: Name cannot be empty.<br>";
		}
		if (empty($_POST['category'])) {
			echo "Error: Category cannot be empty.<br>";
		}
		if (empty($_POST['length'])) {
			echo "Error: Length cannot be empty.<br>";
		}
		if (!is_numeric(($_POST['length'])) || $_POST['length'] <= 0 || !ctype_digit($_POST['length'])) {
			echo "Error: Length must be an integer greater than 0.<br>";
		}
	}
}
?>
<html>
  <head>
    <title>Video DB</title>
  </head>
  <body>
    <form action="" method = "post">
   	  Name:
   	  <input type="text" name="name">
   	  <br>
      Category:
      <input type="text" name="category">
      <br>
      Length:
      <input type="text" name="length">
      <br>
   	  <button type="submit">Add</button>
   	</form>
  </body>
</html>