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
		prescriptions.PrescriptionNotes,
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
		<div class="col-4 d-flex flex-column align-items-center">
		</div>
		<div class="col-7">
			<p class="mainFont2">Welcome, Partner!</p>
		</div>
		<div class="col-1">
		<a href="settings.php">
			<img src="images/cog.png" alt="Settings" class="settings-icon position-absolute top-0 end-0 m-3" style="width: 3vw; height: 3vw;">
		</a>
		<a href='logout.php'>Log Out</a>
		</div>
	</div>
	<div class="container-fluid">
	<!-- set up bootstrap grid system to make page format responsive -->
	<div class="row flex-nowrap">
		<div class="col-3 d-flex flex-column align-items-center">
			<img src="images/PillPartner.png" alt="PillPartner Avatar" class="pill-partner text-start text m-3" style="width: 30vw; height: 30vw">
			<!-- Button to Open Modal -->
				<button type="button" class="btn btn-primary med-button " data-bs-toggle="modal" data-bs-target="#addModal">
					Add New Perscription
				</button>
			
		</div>
		<div class="col-8" style="background-color: lightblue; border-width: 5px; border-style: solid; border-color: white; border-radius: 10px; margin: 1rem; padding-right: 1rem;">
			<p><strong>Logged in as:</strong> <?php echo $_SESSION['UserFirstName']." ".$_SESSION['UserLastName']. " (".$_SESSION['UserName'].")"; ?></p>
			<table class="table table-bordered" style="width: 100%; ">
			<thead class="thead-dark">
			<colgroup>
				<col style="width: 9vw;">
				<col style="width: 25%;">
				<col style="width: 15%;">
				<col style="width: 20%;">
				<col style="width: 5%;">
				<col style="width: 5%;">

			</colgroup>
				<tr>
				  <th scope="col">Picture</th>
				  <th scope="col">Medication</th>
				  <th scope="col">Dosage</th>
				  <th scope="col">Notes</th>
				  <th scope="col">Taken</th>
				  <th scope="col">Edit</th>
				</tr>
			  </thead>
			  <tbody>
			  <?php
                foreach ($user_meds as $med) {
                    echo "<tr>";
                    echo "<td><img src=\"images/medication_".$med["MedicationID"].".jpg\" alt=\"{$med['MedicationID']}\" class=\"pill1\" style=\"width: 10vw; height: 10vw;\"></td>";
                    echo "<td class=\"text-nowrap\">{$med['MedicationName']}</td>";
					echo "<td class=\"text-nowrap\">{$med['PrescriptionDosage']} {$med['PrescriptionUnit']}</td>";
					echo "<td class=\"text-nowrap\">".$med["PrescriptionNotes"]."</td>";
                    echo "<td class=\"text-center\">TBD</td>";
					echo "<td class=\"text-center\"><button 
					class=\"btn btn-link text-decoration-none\" 
					style=\"padding: calc(1 rem + 0.05vw)\"
					data-bs-toggle=\"modal\" 
					data-bs-target=\"#editModal\"
					data-medication=\"" . $med['MedicationName'] . "\"
					data-dosage=\"" . $med['PrescriptionDosage'] . " " . $med['PrescriptionUnit'] . "\"
					data-notes=\"Fill with notes\"
					>...</button>
						</td>";
                    echo "</tr>";
                }
                ?>
			  </tbody>
			</table>
		</div>
		
	</div>
	<div class="row">
		<div class="col-8 text-center" style="margin-top: auto;">
			<!-- Edit Med Modal Structure -->
			<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
				<div class="modal-dialog model-dialog-centered modal-xl">
					<div class="modal-content">
						<form>
							<div class="modal-header">
								<h1 class="modal-title" id="editModalLabel">Edit Medication</h2>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<div class="container-fluid">
									<div class="row mb-3">
										<div class="col-4 text-end">
											<label style="font-size: 2vw;" for="UserName"><b>Medication name: </b></label>
											</div>
											<div class="col-4 text-start">
											<input type="text" class="form-control" id = "medicationName" name="medicationName" style="width: 10vw; height: 2vw; font-size: 1vw;" required></input>
										</div>
									</div>
									<div class="row mb-3">
										<div class="col-4 text-end">
											<label style="font-size: 2vw;" for="UserName"><b>Medication dosage: </b></label>
										</div>
										<div class="col-8 d-flex flex-wrap" style="display: flex; gap: 10px;">
											<label style="font-size: 1vw;"><b>Take </b></label>
											<div class="form-group">
												<input type="text" placeholder="Type..." name="medicationAmount" style="width: 4vw; height: 1.50vw; font-size: 1vw;" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
												</div>
												<div class="form-group">
												<select class="dynamicDropdown" id="PrescriptionUnit" style="width: auto; font-size: 0.9vw;" onchange="resizeDropdown(this)" required>
													<option value="" disabled selected>Select an amount</option>
													<option value="1">mg</option>
													<option value="2">ml</option>
													<option value="3">capsules</option>
													<option value="4">tablets</option>
												</select>
											</div>
											<label style="font-size: 1vw;" for="UserPassword"><b>per </b></label>
											<div class="form-group">
												<select class="dynamicDropdown" id="secondDropdown" style="width: auto; font-size: 0.9vw;"onchange="toggleThirdDropdown(); resizeDropdown(this);" required>
													<option value="" disabled selected>Select a frequency</option>
													<option value="1">day</option>
													<option value="2">week</option>
													<option value="3">month</option>
												</select>
											</div>
											<div class="form-group" id="thirdDropdown" style="display: none; font-size: 0.9vw;" >
												<label style="font-size: 1vw;"><b> on </b></label>
												<select class="dynamicDropdown" id="thirdDropdownSelect" onchange="resizeDropdown(this)" required>
													<option value="option1">Monday</option>
													<option value="option2">Tuesday</option>
													<option value="option2">Wednesday</option>
													<option value="option1">Thursday</option>
													<option value="option2">Friday</option>
													<option value="option2">Saturday</option>
													<option value="option2">Sunday</option>
												</select>
											</div>	
											<label style="font-size: 1vw;"><b> at </b></label>
											<input type="text" placeholder="Hr" name="hourValue" style="width: 2.50vw; height: 1.50vw; font-size: 1vw;" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
											<label style="font-size: 1vw;"><b>:</b></label>
											<input type="text" placeholder="Min" name="minuteValue" style="width: 2.50vw; height: 1.50vw; font-size: 1vw;" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
											<div class="form-group">
												<select class="dynamicDropdown" id="fourthDropdwon" style="width: auto; font-size: 0.9vw;"onchange="toggleThirdDropdown(); resizeDropdown(this);" required>
													<option value="1">AM</option>
													<option value="2">PM</option>
												</select>
											</div>
										</div>
									</div>
									<div class="row mb-3">
										<div class="col-4 text-end text-wrap">
											<label style="font-size: 2vw;" for="UserPassword"><b>Image of medication:</b></label> 
										</div>
										<div class="col-4 text-start text-wrap">
											<div class="form-group">
												<label style="font-size: 1vw;" for="imageUpload">Select image to upload:</label>
												<input type="file" class="form-control-file" id="imageUpload" style="font-size: .8vw;" accept="image/*">
											</div>
											<button type="button" class="btn btn-primary" style="font-size: 1vw">Upload</button>
										</div>
									</div>
									<div class="row mb-3">
										<div class="col-4 text-end text-wrap">
											<label style="font-size: 2vw;" for="UserPassword"><b>Notes:</b></label>
										</div>
										<div class="col-4 text-wrap">
											<input type="text" class="form-control" id = "medicationName" name="medicationName" style="width: 10vw; height: 2vw; font-size: 1vw;" required></input>
										</div>
									</div>	
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary">Save Medication</button>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- Add Med Modal Structure -->
			<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
				<div class="modal-dialog model-dialog-centered modal-xl">
					<div class="modal-content">
						<form>
							<div class="modal-header">
								<h1 class="modal-title" id="addModalLabel">Add New Perscription</h2>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<div class="container-fluid">
										<div class="row mb-3">
											<div class="col-4 text-end">
												<label style="font-size: 2rem;" for="UserName"><b>Medication name: </b></label>
											</div>
											<div class="col-4 text-start">
												<div class="form-group">
														<select class="medicationDropdown" id="PrescriptionUnit" style="width: auto; font-size: 0.9vw;" onchange="resizeDropdown(this)" required>
															<option value="" disabled selected>Select an amount</option>
															<option value="1">med1</option>
															<option value="2">med2</option>
															<option value="3">med3</option>
															<option value="4">med4</option>
														</select>
												</div>
												<button type="button" class="btn btn-primary custom-button" onclick="toggleCustomMed()">
													Add New Medication
												</button>
												<div class="form-group" id="customMedication" style="display: none;">
													<label style="font-size: 1rem;"><b>Take </b></label>
												</div>
											</div>
										</div>
										<div class="row mb-3">
											<div class="col-4 text-end">
											<label style="font-size: 2rem;" for="PerscirptionDosage"><b>Medication dosage: </b></label>
											</div>
											<div class="col-8 d-flex flex-wrap" style="display: flex; gap: 10px;">
											<label style="font-size: 1rem;"><b>Take </b></label>
													<div class="form-group">
														<input type="text" placeholder="Type..." name="medicationAmount" style="width: 4vw; height: 1.50vw; font-size: 1vw;" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
													</div>
													<div class="form-group">
														<select class="dynamicDropdown" id="PrescriptionUnit" style="width: auto; font-size: 0.9vw;" onchange="resizeDropdown(this)" required>
															<option value="" disabled selected>Select an amount</option>
															<option value="1">mg</option>
															<option value="2">ml</option>
															<option value="3">capsules</option>
															<option value="4">tablets</option>
														</select>
													</div>
													<label style="font-size: 1rem;"><b>per </b></label>
													<div class="form-group">
														<select class="dynamicDropdown" id="secondDropdown" style="width: auto; font-size: 0.9vw;"onchange="toggleThirdDropdown(); resizeDropdown(this);" required>
															<option value="" disabled selected>Select a frequency</option>
															<option value="1">day</option>
															<option value="2">week</option>
															<option value="3">month</option>
														</select>
													</div>
													<div class="form-group" id="DayDropDown" style="display: none; font-size: 0.9vw;" >
														<label style="font-size: 1rem;"><b> on </b></label>
														<select class="dynamicDropdown" id="thirdDropdownSelect" onchange="resizeDropdown(this)" required>
															<option value="option1">Monday</option>
															<option value="option2">Tuesday</option>
															<option value="option2">Wednesday</option>
															<option value="option1">Thursday</option>
															<option value="option2">Friday</option>
															<option value="option2">Saturday</option>
															<option value="option2">Sunday</option>
														</select>
													</div>	
													<label style="font-size: 1rem;"><b> at </b></label>
													<input type="text" style="width: 2.50vw; height: 1.50vw; font-size: 1vw; color: black;" placeholder="Hr" name="hourValue"  oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
													<label style="font-size: 1rem;"><b>:</b></label>
													<input type="text" placeholder="Min" name="minuteValue" style="width: 2.50vw; height: 1.50vw; font-size: 1vw;" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
													<div class="form-group">
														<select class="dynamicDropdown" id="fourthDropdwon" style="width: auto; font-size: 0.9vw;"onchange="toggleThirdDropdown(); resizeDropdown(this);" required>
															<option value="1">AM</option>
															<option value="2">PM</option>
														</select>
													</div>
													
											</div>
										</div>
										<div class="row mb-3">
											<div class="col-4 text-end text-wrap">
												<label style="font-size: 2rem;" for="UserPassword"><b>Image of medication:</b></label> 
											</div>
											<div class="col-4 text-start text-wrap">
												<div class="form-group">
													<label style="font-size: 1rem;" for="imageUpload">Select image to upload:</label>
													<input type="file" class="form-control-file" id="imageUpload" style="font-size: .8vw;" accept="image/*">
												</div>
												<button type="button" class="btn btn-primary" style="font-size: 1vw">Upload</button>
											</div>
										</div>
										<div class="row mb-3">
											<div class="col-4 text-end text-wrap">
												<label style="font-size: 2rem;" for="UserPassword"><b>Notes:</b></label>
											</div>
											<div class="col-4 text-wrap">
												<input type="text" placeholder="Type here..." name="UserPassword" style="width: 30vw; height: 10vw; font-size: 1vw; padding-top: 0.5vw;"></input>	
											</div>
										</div>	
									</div>
								</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
								<button type="submit" class="btn btn-primary">Save Medication</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
		<div class="col-7">
		  
			
		</div>
		
		
	</div>
</div>


	<!-- Bootstrap JavaScript (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Custom JavaScript -->
    <script src="js/scripts.js"></script>
	<script> 
	function toggleThirdDropdown() {
		var secondDropdown = document.getElementById("secondDropdown");
		var DayDropDown = document.getElementById("DayDropDown");

		
		// Show second dropdown if the specific option is selected
		if (DayDropDown.style.display === "none" || DayDropDown.style.display === "") {
			DayDropDown.style.display = "block";
		} else {
			DayDropDown.style.display = "none";
		}
	}
</script>
<script>
	function resizeDropdown(dropdown){
		let tempSpan = document.createElement("span");
		tempSpan.style.visibility = "hidden";
		tempSpan.style.position = "absolute";
		tempSpan.style.whiteSpace = "nowrap";
		tempSpan.innerText = dropdown.options[dropdown.selectedIndex].text;
		document.body.appendChild(tempSpan);
		
		dropdown.style.width = `${tempSpan.offsetWidth + 30}px`; 
		document.body.removeChild(tempSpan);
	}
</script>
<script>
	function resizeField(field){
		let tempSpan = document.createElement("span");
		tempSpan.style.visibility = "hidden";
		tempSpan.style.position = "absolute";
		tempSpan.style.whiteSpace = "nowrap";
		tempSpan.innerText = field.options[field.selectedIndex].text;
		document.body.appendChild(tempSpan);
		
		field.style.width = `${tempSpan.offsetWidth + 30}px`; 
		document.body.removeChild(tempSpan);
	}
</script>
<script>
	function toggleCustomMed() {
		var dropdown = document.getElementById("toggleCustomMed");

		// Toggle between hiding and showing the dropdown menu
		if (dropdown.style.display === "none" || dropdown.style.display === "") {
			dropdown.style.display = "block";
		} else {
			dropdown.style.display = "none";
		}
	}
</script>
<script>
	const editModal = document.getElementById('editModal');
	editModal.addEventListener('show.bs.modal', function (event) {
		const button = event.relatedTarget; // Button that triggered the modal
		const medication = button.getAttribute('data-medication');
		const dosage = button.getAttribute('data-dosage');
		const notes = button.getAttribute('data-notes');
		
		// Populate the modal fields
		document.getElementById('medicationName').value = medication;
		document.getElementById('dosage').value = dosage;
		document.getElementById('notes').value = notes;
	});
	const addModal = document.getElementById('addModal');
	addModal.addEventListener('show.bs.modal', function (event) {			
		document.getElementById("customMedication").addEventListener("click", toggleCustomMed);

	});
</script>
</body>
</html>
