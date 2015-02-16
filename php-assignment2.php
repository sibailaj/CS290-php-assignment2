<?php
ini_set('display_errors', 'On');

$mysqli = new mysqli("oniddb.cws.oregonstate.edu", "sibailaj-db", "j1nl10en0wr49WVv", "sibailaj-db");

if ($mysqli->connect_errno) {
	echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
} else {
	//echo "Connection worked!<br>";
}

//Delete Button
if (isset($_POST['deleteButton'])) {
	$statement = $mysqli->prepare("DELETE FROM video_inventory WHERE name = ?;");
	$statement->bind_param("s", $_POST['deleteButton']);
	$statement->execute();
	$statement->close();
	//$mysqli->query("DELETE FROM video_inventory WHERE name = '$_POST[deleteButton]';");

}

//Check In/Out Button
if (isset($_POST['checkinButton'])) {
	$rented = 0;
	//$statement = $mysqli->prepare("SELECT rented FROM video_inventory WHERE name = ?;");
	//$statement->bind_param("s", $_POST['checkinButton']);
	//$statement->execute();
	//$statement->bind_result($rentedResult);
	//echo "checkin button is called<br>";
	$rentedQuery = $mysqli->query("SELECT rented FROM video_inventory WHERE name = '$_POST[checkinButton]';");
	//var_dump($rentedQuery);
	$buttonValue = mysqli_fetch_array($rentedQuery, MYSQLI_ASSOC);
	//var_dump($buttonValue);

	if ($buttonValue['rented'] == 1) {
		$rented = 0;
	} else {
		$rented = 1;
	}

	$mysqli->query("UPDATE video_inventory SET rented = $rented WHERE name = '$_POST[checkinButton]';");
}

//Delete All Button
if (isset($_POST['deleteAllButton'])) {
	$query = $mysqli->query("SELECT name, category, length, rented FROM video_inventory;");
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$name = $row['name'];
		$mysqli->query("DELETE FROM video_inventory WHERE name = '$name';");
	}
}

//var_dump(isset($_POST['name']) && isset($_POST['category']) && isset($_POST['length']));
//var_dump($_POST);

if (isset($_POST['name']) && isset($_POST['category']) && isset($_POST['length'])) {
	if (!empty($_POST['name']) && !empty($_POST['category']) && !empty($_POST['length']) && (ctype_digit($_POST['length']) && is_numeric($_POST['length']) && $_POST['length'] > 0)) {
		$length = (int)$_POST['length'];
		if($mysqli->query("INSERT INTO video_inventory (name, category, length, rented) VALUES ('$_POST[name]', 
			'$_POST[category]', $length, 0);") === true) {
			echo "<font color='green'>Video Successfully Added.</font><br>";
		} else {
			echo "<font color='red'>Error: Video not added.</font><br>";
		}
	} else {
		if (empty($_POST['name'])) {
			echo "<font color='red'>Error: Name cannot be empty.</font><br>";
		}
		if (empty($_POST['category'])) {
			echo "<font color='red'>Error: Category cannot be empty.</font><br>";
		}
		if (empty($_POST['length'])) {
			echo "<font color='red'>Error: Length cannot be empty.</font><br>";
		}
		if (!empty($_POST['length']) && (!is_numeric(($_POST['length'])) || $_POST['length'] <= 0 || !ctype_digit($_POST['length']))) {
			echo "<font color='red'>Error: Length must be an integer greater than 0.</font><br>";
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
  	<form action="" method="post">
<?php
$query = $mysqli->query("SELECT name, category, length, rented FROM video_inventory;");
$uniqueCategory = $mysqli->query("SELECT DISTINCT (category) FROM video_inventory;");
$index = 0;
echo "<select name='select'>";
echo "<option value='allMovies'>All Movies</option>";
while ($unique = mysqli_fetch_array($uniqueCategory, MYSQLI_ASSOC)) {
	echo "<option value='$unique[category]'>$unique[category]</option>";
}
echo "</select>  <button type='submit' name='filter'>Filter</button>";
echo "<br><br>";


echo "<table border=1>";
echo "<tr><td><td><b>Name</b><td><b>Category</b><td><b>Length</b><td><b>Rented</b><td>";

if(isset($_POST['select']) && $_POST['select'] != "allMovies") {
	$filteredQuery = $mysqli->query("SELECT name, category, length, rented FROM video_inventory 
		WHERE category = '$_POST[select]';");
	while ($row = mysqli_fetch_array($filteredQuery, MYSQLI_ASSOC)) {
		$name = $row['name'];
		$category = $row['category'];
		$length = $row['length'];
		$rented = "";
		if ($row['rented'] == 0) {
			$rented = "Available";
		} else {
			$rented = "Checked Out";
		}
		echo "<tr><td><button type='submit' name='deleteButton' value='" . $name . "'>Delete</button><td>" . $name . 
		"<td>" . $category . "<td>" . $length . "<td>" . $rented . "<td><button type='submit' name='checkinButton' value='" . 
		$name . "'>Check In/Out</button>";
		$index++;
	}

} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$name = $row['name'];
		$category = $row['category'];
		$length = $row['length'];
		$rented = "";
		if ($row['rented'] == 0) {
			$rented = "Available";
		} else {
			$rented = "Checked Out";
		}
		//$rented = $row['rented'];
		echo "<tr><td><button type='submit' name='deleteButton' value='" . $name . "'>Delete</button><td>" . $name . 
		"<td>" . $category . "<td>" . $length . "<td>" . $rented . "<td><button type='submit' name='checkinButton' value='" . 
		$name . "'>Check In/Out</button>";
		$index++;
	}
}
echo "</table>";

?>
  	</form>
  	<form action="" method="post">
  	<button type="submit" name="deleteAllButton" value="1">Delete All Videos</button>
  	</form>
  </body>
</html>