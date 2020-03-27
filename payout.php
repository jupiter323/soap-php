<?php 
	// Create connection
	$conn = new mysqli("localhost","root","yuri","payment");
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 

	$sessionId = str_replace("}","",str_replace("{","",$_GET['sessionId']));
	$sql = "SELECT * FROM payout_scanning WHERE sessionId='".$sessionId."'";	

	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {	     
	        $sql = "UPDATE payout_scanning SET scanned=true WHERE payout_scanning_id=".$row["payout_scanning_id"];
		    if ($conn->query($sql) === TRUE) {
			    echo "UUID: ".$sessionId." is scanned";
			} else {
			    echo "Error: " . $sql . "<br>" . $conn->error;
			}		        
	    }
	} else {
		$sql = "INSERT INTO payout_scanning (sessionId, scanned) VALUES ('".$sessionId."', true)";	
		if ($conn->query($sql) === TRUE) {
		    echo "UUID: ".$sessionId." is scanned";
		} else {
		    echo "Error: " . $sql . "<br>" . $conn->error;
		}	
	    
	}			

	$conn->close();
	
 ?>