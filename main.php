<?php

include_once("includes/site_construct.inc");

// User is not logged in
if ((!isset($_SESSION['UserName'])) || (!isset($_SESSION['UserPassword']))) {
	redirect('login.php');
}


//prints only a specific users medications, implement after adding prescriptions to users
$user_id = (int)$_SESSION['UserID'];

$user_meds = $db->query(
	"SELECT 
        prescriptions.PrescriptionID,
        prescriptions.PrescriptionTime,
        prescriptions.PrescriptionUnit,
        prescriptions.PrescriptionDosage,
    	medications.MedicationID,
        medications.MedicationBrand,
        medications.MedicationName
    FROM 
        prescriptions
    INNER JOIN 
        medications ON prescriptions.MedicationID = medications.MedicationID
    WHERE 
        prescriptions.UserID = $user_id
")->fetch_all(MYSQLI_ASSOC);


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Page</title>
	<!-- Bootstrap CSS (CDN) -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
	<!-- Custon css-->
    <link rel="stylesheet" href="css/mainStyle.css">
</head>
<body>
<div class="container-fluid">
	<!-- set up bootstrap grid system to make page format responsive -->
	<div class="row">
		<div class="col-4">
			<img src="images/PillPartner.png" alt="PillPartner Avatar" class="pill-partner position-absolute top-0 end-1 m-3" style="width: 30vw; height: 30vw">
		</div>
		<div class="col-7">
			<p class="mainFont2">Welcome Partner!</p>
			<p><strong>Logged in as:</strong> <?php echo $_SESSION['UserFirstName']." ".$_SESSION['UserLastName']. " (".$_SESSION['UserName'].")"; ?></p>
		</div>
		<div class="col-1">
			<a href="settings.php">
			<img src="images/cog.png" alt="Settings" class="settings-icon position-absolute top-0 end-0 m-3" style="width: 3vw; height: 3vw;">
			</a>
			<a href='logout.php'>Log Out</a>
		</div>
		<div class="w-100"></div>
		<div class="col-4 text-center" style="margin-top: auto;">
		 <!-- Button to Open Modal -->
			<button type="button" class="btn btn-primary med-button " data-bs-toggle="modal" data-bs-target="#myModal">
				Add Medication
			</button>
			<!-- Modal Structure -->
			<div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="myModalLabel">Add New Medication</h5>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							This is the content inside the popup.
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
							<button type="button" class="btn btn-primary">Save Medication</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-7">
		  <table class="table table-bordered custom-table">
			<thead class="thead-dark">
			<colgroup>
				<col style="width: 20%;">
				<col style="width: 40%;">
				<col style="width: 30%;">
				<col style="width: 10%;">
			</colgroup>
				<tr>
				  <th scope="col">Picture</th>
				  <th scope="col">Medication</th>
				  <th scope="col">Dosage</th>
				  <th scope="col">Taken</th>
				</tr>
			  </thead>
			  <tbody>
			  <?php
                foreach ($user_meds as $med) {
                    echo "<tr>";
                    echo "<td><img src=\"images/DummyPill1.jpg\" alt=\"{$med['MedicationID']}\" class=\"pill1\" style=\"width: 10vw; height: 10vw;\"></td>";
                    echo "<td class=\"text-nowrap\">{$med['MedicationName']}</td>";
					//echo "<td class=\"text-nowrap\">{$med['PrescriptionDosage']}</td>";
                    echo "<td class=\"text-nowrap\">Fill with dosage</td>";
                    echo "<td>yes</td>";
                    echo "</tr>";
                }
                ?>
			  </tbody>
			</table>
		</div>
		<div class="w-100"></div>
		<div class="col-1"></div>
		<div class="col-4 p-0">
			
		</div>
		
		
	</div>
</div>


	<!-- Bootstrap JavaScript (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Custom JavaScript -->
    <script src="js/scripts.js"></script>
</body>
</html>
