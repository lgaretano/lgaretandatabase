<?php
include_once 'includes/lgaretanlogin.php';
include_once 'includes/lgaretanheader.php';

/*$hn = 'localhost';
$db = 'lgaretan';
$un = 'root';
$pw = '';
*/

#print_r($_POST);
 
# Check if the user entered an order and it isn't empty 
if ((isset($_POST['order']) && !empty($_POST['order'])) ||
	(isset($_POST['family']) && !empty($_POST['family'])) ||
	(isset($_POST['genus']) && !empty($_POST['genus'])) ||
	(isset($_POST['species']) && !empty($_POST['species'])) ||
	(isset($_POST['tissueBox']) && !empty($_POST['tissueBox'])) ||
	(isset($_POST['tissueRack']) && !empty($_POST['tissueRack'])) ||
	(isset($_POST['extractBox']) && !empty($_POST['extractBox'])) ||
	(isset($_POST['extractRack']) && !empty($_POST['extractRack']))
) { 
	# Connect to the db like usual
	$conn = new mysqli($hn, $un, $pw, $db);
	if ($conn->connect_error) die($conn->connect_error);

	# Grab our order we want to search for and make it safe for MySQL
	$order = sanitizeMySQL($conn, $_POST['order']);
	$family = sanitizeMySQL($conn, $_POST['family']);
	$genus = sanitizeMySQL($conn, $_POST['genus']);
	$species = sanitizeMySQL($conn, $_POST['species']);
	$tissueBox = sanitizeMySQL($conn, $_POST['tissueBox']);
	$tissueRack = sanitizeMySQL($conn, $_POST['tissueRack']);
	$extractBox = sanitizeMySQL($conn, $_POST['extractBox']);
	$extractRack = sanitizeMySQL($conn, $_POST['extractRack']);
	
	# array to hold all our pieces of our WHERE statement
	$whereStatement = array();
	
	# Check if they entered something, if so write that piece of the query
	if ($order != "") {
		$whereStatement[] = "orders LIKE \"%$order%\"";
	}
	if ($family != "") {
		$whereStatement[] = "family LIKE \"%$family%\"";
	}
	if ($genus != "") {
		$whereStatement[] = "genus LIKE \"%$genus%\"";
	}
	if ($species != "") {
		$whereStatement[] = "species LIKE \"%$species%\"";
	}
	if ($tissueBox != "") {
		$whereStatement[] = "tissue_box.boxNumber=$tissueBox";
	}
	if ($tissueRack != "") {
		$whereStatement[] = "tissue_racks.rackNumber=$tissueRack";
	}
	
	if ($extractBox != "") {
		$whereStatement[] = "extract_box.boxNumber=$extractBox";
	}
	
	if ($extractRack != "") {
		$whereStatement[] = "extract_racks.rackNumber=$extractRack";
	}
	
	

	#print_r($whereStatement);
	
	$whereString = implode(" AND ", $whereStatement);
	
	#echo $whereString;
	
	# Construct our query (basically the same as before just with a LIKE to add the search piece at the end)
	$query = "SELECT orders,family,genus,species,subspecies,collectorNumber,catalogNumber,institutions.name, 
	tissue_box.boxNumber,tissue_racks.rackNumber,tissues.position,
	extract_box.boxNumber AS ebox,extract_racks.rackNumber AS erack,extracts.position AS eposition FROM specimens
	NATURAL JOIN tissues
	NATURAL JOIN tissue_box
	NATURAL JOIN tissue_racks
	JOIN extracts ON specimens.specimenID=extracts.specimenID
	JOIN extract_box ON extracts.extractBoxID=extract_box.extractBoxID
	JOIN extract_racks ON extract_box.extractRackID=extract_racks.extractRackID
	JOIN institutions ON specimens.institutionID=institutions.institutionID
	WHERE $whereString";

	#echo "<p>OUR FINAL QUERY</p> $query";

	$result = $conn->query($query);
	
	if (!$result) die ("Invalid search term.");
	
	$rows = $result->num_rows;
	
	# If we got no rows then there are no search results
	if ($rows == 0) {
		echo "No items match your search.<br>";
		
	# If we did get rows back then print them out! (Just reusing your code from the main page here to print the table). 
	} else {
		
		echo "<h2>Search Results</h2>";
		echo "<table><tr><th>Order</th><th>Family</th><th>Genus</th><th>Species</th><th>Subspecies</th><th>Institution</th><th>Catalog Number</th>
		<th>Collector Number</th><th>Tissue Rack</th><th>Tissue Box</th><th>Tissue Position</th>
		<th>Extract Rack</th><th>Extract Box</th><th>Extract Position</th> </tr>";
		while ($row = $result->fetch_assoc()) {
			echo '<tr>';
			echo "<td>".$row["orders"]."</td><td>".$row["family"]."</td><td>".$row["genus"]."</td><td>".$row["species"]."</td>";
			echo  "<td>".$row["subspecies"]."</td><td>".$row["name"]."</td><td>".$row["catalogNumber"]."</td><td>".$row["collectorNumber"]."</td>";
			echo "<td>".$row["rackNumber"]."</td><td>".$row["boxNumber"]."</td><td>".$row["position"]."</td>";
			echo  "<td>".$row["erack"]."</td><td>".$row["ebox"]."</td><td>".$row["eposition"]."</td>";
			echo '</tr>';
		}
		echo "</table>";
		
	}
} else {
# Otherwise tell them to enter something	
	echo "<p>**Please enter one or more fields to search**</p>";
#the close is after the form, before the footer

?>	


<form method="post" action="index.php">
Search for...
<br>
<br>
Order: <input type="text" name="order">
<br>
<br>
Family: <input type="text" name="family">
<br>
<br>
Genus: <input type="text" name="genus">
<br>
<br>
Species: <input type="text" name="species">
<br>
<br>
Tissue Box:
<select name="tissueBox">
<option value=""></option>
<?php
for ($i=1;$i<101;$i++) {
	echo '<option value="'.$i.'">'.$i.'</option>';
}	
?>
</select>
Tissue Rack:
<select name="tissueRack">
<option value=""></option>
<?php
for ($i=1;$i<101;$i++) {
	echo '<option value="'.$i.'">'.$i.'</option>';
}	
?>
</select>
<br>
Extract Box:
<select name="extractBox">
<option value=""></option>
<?php
for ($i=1;$i<101;$i++) {
	echo '<option value="'.$i.'">'.$i.'</option>';
}	
?>
</select>
Extract Rack:
<select name="extractRack">
<option value=""></option>
<?php
for ($i=1;$i<101;$i++) {
	echo '<option value="'.$i.'">'.$i.'</option>';
}	
?>
</select>

<br>
<br>
<input type="submit">


</form>



<?php

} #close tag from above so search can be separate from form
# Moving this to the bottom of the page as it's the footer!
include_once 'includes/lgaretanfooter.php';

# Adding our sanitizing functions since we are taking data from the user and sending it to our db
# so we need to make sure it is safe 
function sanitizeString($var)
{
	$var = stripslashes($var);
	$var = strip_tags($var);
	$var = htmlentities($var);
	return $var;
}
function sanitizeMySQL($connection, $var)
{
	$var = $connection->real_escape_string($var);
	$var = sanitizeString($var);
	return $var;
}
?>