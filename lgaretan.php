<?php
include_once 'includes/lgaretanlogin.php';
include_once 'includes/lgaretanheader.php';



$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die($conn->connect_error);

$query = "SELECT orders,family,genus,species,subspecies,collectorNumber,catalogNumber,institutions.name, 
tissue_box.boxNumber,tissue_racks.rackNumber,tissues.position,
extract_box.boxNumber AS ebox,extract_racks.rackNumber AS erack,extracts.position AS eposition FROM specimens
NATURAL JOIN tissues
NATURAL JOIN tissue_box
NATURAL JOIN tissue_racks
JOIN extracts ON specimens.specimenID=extracts.specimenID
JOIN extract_box ON extracts.extractBoxID=extract_box.extractBoxID
JOIN extract_racks ON extract_box.extractRackID=extract_racks.extractRackID
JOIN institutions ON specimens.institutionID=institutions.institutionID";
$result = $conn->query($query);
if (!$result) die ("Database access failed: " . $conn->error);


echo "<table><tr><th>Order</th><th>Family</th><th>Genus</th><th>Species</th><th>Subspecies</th><th>Institution</th><th>Catalog Number</th>
<th>Collector Number</th><th>Tissue Rack</th><th>Tissue Box</th><th>Tissue Position</th>
<th>Extract Rack</th><th>Extract Box</th><th>Extract Position</th> </tr>";
while ($row = $result->fetch_assoc()) {
#print_r($row);
	echo '<tr>';
	echo "<td>".$row["orders"]."</td><td>".$row["family"]."</td><td>".$row["genus"]."</td><td>".$row["species"]."</td>";
	echo  "<td>".$row["subspecies"]."</td><td>".$row["name"]."</td><td>".$row["catalogNumber"]."</td><td>".$row["collectorNumber"]."</td>";
	echo "<td>".$row["rackNumber"]."</td><td>".$row["boxNumber"]."</td><td>".$row["position"]."</td>";
	echo  "<td>".$row["erack"]."</td><td>".$row["ebox"]."</td><td>".$row["eposition"]."</td>";
	#echo "<td><a href=\"mailto:".$row["email"]."\">".$row["email"]."</a></td>";
	echo '</tr>';
}
echo "</table>";

include_once 'includes/lgaretanfooter.php';

?>