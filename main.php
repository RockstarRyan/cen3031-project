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
 <header class="main-header py-3 shadow-sm">
	<div class="row">
		<div class="col-2 text-start ps-3">
		<div class = "settings-icon mb-4 pb-3">
			<a href="settings.php">
				<img src="images/cog.png" alt="Settings" class="invert settings-icon top-0 end-0 m-3" style="width: 3vw; height: 3vw;">
			</a>
			</div>
	    </div>
		<div class="col-8 text-center">
			<p class="mainFont2">Welcome, Partner!</p>
		</div>
		<div class="col-2 text-end pe-3">
			
			<div class = "logout-button p-3">
				<a href="logout.php" class="btn btn-outline-custom">Log Out</a>
			</div>
		</div>
	</div>
 </header>

  <main class="container my-5 text-align-center">
	<div class="container-fluid">
		<div class="container-fluid">
			<div class="row flex-nowrap">
				<div class="col-3 d-flex flex-column align-items-center" style="padding-right: calc(8rem + 5vw);">
					<img src="images/PillPartner.png" alt="PillPartner Avatar" class="pill-partner text-start text m-3" style="width: calc(25rem + 10vw); height: calc(25rem + 10vw);">
					<!-- Button to Open Modal -->
					<button type="button" class="btn btn-primary med-button" data-bs-toggle="modal" data-bs-target="#addModal">
						Add New Prescription
					</button>
				</div>
				<div class="col-9 table-background pt-3 pe-4 ps-4 pb-2">
					<p><strong>Logged in as:</strong> <?php echo $_SESSION['UserFirstName']." ".$_SESSION['UserLastName']. " (".$_SESSION['UserName'].")"; ?></p>

					<h2>Your Medications:</h2>

					<div class="table-responsive">
						<table class="table table-bordered" style="width: 100%; font-size: calc(0.5rem + 0.35vw);">
							<thead class="thead-dark">
								<colgroup>
									<col style="width: 10%;">
									<col style="width: 15%;">
									<col style="width: 5%;">
									<col style="width: 15%;">
									<col style="width: 10%;">
									<col style="width: 5%;">
								</colgroup>
								<tr>
									<th scope="col">Picture</th>
									<th scope="col">Medication Name</th>
									<th scope="col">Dosage</th>
									<th scope="col">Notes</th>
									<th scope="col">Intakes</th>
									<th scope="col">Delete</th>
								</tr>
							 </thead>
							  <tbody>
							  <?php
								foreach ($user_meds as $med) {
									echo "<tr>";
									echo "<td><img src=\"images/medication_".$med["MedicationID"].".jpg\" alt=\"{$med['MedicationID']}\" class=\"pill1\" style=\"width: 10vw; height: 10vw;\"></td>";
									echo "<td class=\"text-wrap\">{$med['MedicationName']}</td>";
									echo "<td class=\"text-wrap\">{$med['PrescriptionDosage']} {$med['PrescriptionUnit']}</td>";
									echo "<td class=\"text-wrap\">".$med["PrescriptionNotes"]."</td>";
									echo "<td class=\"text-wrap\">
									<div class=\"text-center\">
									<button class=\"btn btn-custom\"
									data-bs-toggle=\"modal\" 
									data-bs-target=\"#intakeModal\"
									data-medication=\"" . $med['MedicationBrand'] . ", " . $med['MedicationName'] . "\"
									>Add Intake</button>
									</div>
									<!-- the lines under this before <\td> is the temp for intake format + delete button for each, needs to be in a for loop per intake, user_meds needs to populate intake data so that IntakeTime can be used-->
									<!-- replace the div here with the for loop logic, it is just keeping the format for now-->
									<div>
										<label class=\"text-wrap d-inline\">Take [PrescriptionDosage] per [IntakeTime]</label>
										<button class=\"btn btn-delete d-inline\" data-intake-id=\"deleteIntake\" onclick=\"deleteIntake(this)\">
											<img src=\"images/xmark.png\" alt=\"Delete\" style=\"width: 12px; height: 12px;\">
										</button> 
									</div>
										</td>";
									echo"<td style=\" text-align: center; vertical-align: middle;\">
									<button class=\"btn btn-delete\" data-intake-id=\"deleteMedication\" onclick=\"deleteMedication(this)\">
										<img src=\"images/xmark.png\" alt=\"Delete\" style=\"width: 24px; height: 24px;\">
									</button> 
										</td>";
									echo "</tr>";
								}
								?>
							  </tbody>
						</table>
					</div>
				</div>
			</div>
		<div class="row">
			<div class="col-8 text-center" style="margin-top: auto;">
				<div class="modal fade" id="intakeModal" tabindex="-1" aria-labelledby="intakeModalLabel" aria-hidden="true">
					<div class="modal-dialog model-dialog-centered modal-xl">
						<div class="modal-content">
							<form>
								<div class="modal-header">
									<h1 class="modal-title" id="intakeModalLabel">Add Intake</h2>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<div class="container-fluid">
										<div class="row mb-3">
											<div class="col-4 text-end">
												<label style="font-size: calc(1rem + 1vw);" for="MedicationID"><b>Medication:</b></label>
											</div>
											<div class="col-4 text-start d-flex flex-column justify-content-center align-items-start">
												<input type="text" class="form-control" id = "medicationName" name="medicationName" style="font-size: calc(0.40rem + 0.60vw);" disabled></input>
												</div>
										</div>
										<div class="row mb-3">
											<div class="col-4 text-end">
												<label style="font-size: calc(1rem + 1vw);" for="UserName"><b>Medication dosage: </b></label>
											</div>
											<div class="col-8 d-flex flex-wrap" style="display: flex; gap: 10px;">
												<label style="font-size: calc(0.40rem + 0.60vw);"><b>Take </b></label>
												<div class="form-group">
													<input type="text" placeholder="Type..." name="medicationAmount" style="width:  calc(3rem + 0.60vw); font-size: calc(0.40rem + 0.60vw);" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
													</div>
													<div class="form-group">
													<select class="dynamicDropdown" id="PrescriptionUnit" name="PrescriptionUnit" style="width: auto; font-size: calc(0.40rem + 0.60vw);" onchange="resizeDropdown(this)" required>
														<option value="" disabled selected>Select an amount</option>
														<option value="mg">mg</option>
														<option value="ml">ml</option>
														<option value="capsules">capsules</option>
														<option value="tablets">tablets</option>
													</select>
												</div>
												<label style="width: auto; font-size: calc(0.40rem + 0.60vw);" for="UserPassword"><b>per </b></label>
												<div class="form-group">
													<select class="dynamicDropdown" id="secondDropdown" name="timeframe" style="width: auto; font-size: 0.9vw;"onchange="toggleThirdDropdown(); resizeDropdown(this);" required>
														<option value="" disabled selected>Select a frequency</option>
														<option value="1">day</option>
														<option value="2">week</option>
														<option value="3">month</option>
													</select>
												</div>
												<div class="form-group" id="DayDropDown" style="display: none; width: auto; font-size: calc(0.40rem + 0.60vw);" >
													<label style="font-size: 1vw;"><b> on </b></label>
													<select class="dynamicDropdown" id="thirdDropdownSelect" name="weekday" onchange="resizeDropdown(this)">
														<option value="M">Monday</option>
														<option value="T">Tuesday</option>
														<option value="W">Wednesday</option>
														<option value="R">Thursday</option>
														<option value="F">Friday</option>
														<option value="Sat">Saturday</option>
														<option value="S">Sunday</option>
													</select>
												</div>	
												<div class="form-group" id="MonthDropDown" style="display: none; width: auto; font-size: calc(0.40rem + 0.60vw);" >
													<label style="font-size: 1vw;"><b> on the </b></label>
													<select class="dynamicDropdown" id="fourthDropdownSelect" name="monthDay" onchange="resizeDropdown(this)">
														<?php
															for ($i = 1; $i <= 31; $i++) {
																// Determine suffix
																if ($i % 10 == 1 && $i != 11) {
																	$suffix = "st";
																} elseif ($i % 10 == 2 && $i != 12) {
																	$suffix = "nd";
																} elseif ($i % 10 == 3 && $i != 13) {
																	$suffix = "rd";
																} else {
																	$suffix = "th";
																}
															
																echo "<option value=\"$i\">$i$suffix</option>\n";
																}
														?>
													</select>
													<label style="font-size: 1vw;"><b> day, </b></label>

												</div>	
												<label style="font-size: 1vw;"><b> at </b></label>
												<input type="text" placeholder="Hr" name="hourValue" style="width: calc(2rem + 0.40vw); height: calc(2rem + 0.40vw); font-size: calc(0.40rem + 0.60vw);" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
												<label style="font-size: 1vw;"><b>:</b></label>
												<input type="text" placeholder="Min" name="minuteValue" style="width: calc(3rem + 0.60vw); height: calc(2rem + 0.40vw); font-size: calc(0.40rem + 0.60vw);" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
												<div class="form-group">
													<select class="dynamicDropdown" id="fourthDropdwon" name="ampm" style="width: calc(3rem + 1vw); font-size: calc(0.40rem + 0.60vw);"onchange="toggleThirdDropdown(); resizeDropdown(this);" required>
														<option value="AM">AM</option>
														<option value="PM">PM</option>
													</select>
												</div>
											</div>
										</div>
					
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary">Save Intake</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
				<!-- Add Med Modal Structure -->
				<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
					<div class="modal-dialog model-dialog-centered modal-xl">
						<div class="modal-content">
							<form id="saveMed" method="post" action="dbInsert.php">
								<div class="modal-header">
									<h1 class="modal-title" id="addModalLabel">Add New Perscription</h2>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<div class="container-fluid">
											<div class="row mb-3">
												<div class="col-4 text-end">
													<label style="font-size: calc(1rem + 1vw);" for="UserName"><b>Medication Details: </b></label>
												</div>
												<div class="col-8 text-start">
													<div class="form-group" id="databaseMedication" style="display: block;">
														<select class="dynamicDropdown" id="MedicationNameDatabase" style="width: auto; font-size: calc(0.40rem + 0.60vw); margin: 0.20rem;" onchange="resizeDropdown(this)" name='MedicationID' required>
															<option value="" disabled selected>Select Medication Name...</option><?php 
															$medications = $db->selectRowsFrom('medications');
															foreach ($medications as $i=>$med) {
																echo "<option value='".$med[0],"'>".$med[1].", ".$med[2]." [".$med[0]."]</option>";
															}?>
														</select>
													</div>
													<div class="form-group" id="customMedication" style="display: none;">
														<div class="form-group">
																<input type="text" placeholder="Type Medication Name..." name="MedicationNameCustom" style="width: auto; font-size: calc(0.40rem + 0.60vw); margin: 0.20rem;" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
																<input type="text" placeholder="Type Medication Brand..." name="MedicationBrandCustom" style="width: auto; font-size: calc(0.40rem + 0.60vw); margin: 0.20rem; " oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
														</div>
													</div>
													<button type="button" class="btn btn-primary custom-button" id="medicationButton" onclick="toggleCustomMed()" style="font-size: 1rem;">
															Custom Medication
													</button>
												</div>
											</div>
											<div class="row mb-3">
												<div class="col-4 text-end">
												<label style="font-size: calc(1rem + 1vw);" for="PrescriptionDosage"><b>Dosage: </b></label>
												</div>
												<div class="col-8 d-flex flex-wrap" style="display: flex; gap: 10px;">
													<div class="form-group">
														<input type="text" placeholder="Type..." name="medicationAmount" style="width: auto; font-size: calc(0.40rem + 0.60vw);" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
													</div>
													<div class="form-group">
														<select class="dynamicDropdown" id="PrescriptionUnit" name="PrescriptionUnit" style="width: auto; font-size: calc(0.40rem + 0.60vw);" onchange="resizeDropdown(this)" required>
															<option value="" disabled selected>Select an amount</option>
															<option value="mg">mg</option>
															<option value="ml">ml</option>
															<option value="capsules">capsules</option>
															<option value="tablets">tablets</option>
														</select>
													</div>
													
												</div>
											</div>
											<div class="row mb-3">
												<div class="col-4 text-end text-wrap">
													<label style="font-size: calc(1rem + 1vw);" ><b>Notes:</b></label><br>
													<label style="font-size: calc(0.50rem + 0.50vw);" ><b>(optional)</b></label>
												</div>
												<div class="col-4 text-wrap">
													<input type="text" placeholder="Type here..." name="PrescriptionNotes" style="width: calc(22rem + 5vw); height: calc(5rem + 2vw); font-size: calc(0.40rem + 0.60vw); padding-top: 0.5vw;" ></input>	
												</div>
											</div>	
										</div>
									</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
									<button type="submit" class="btn btn-primary" onclick="document.getElementById('saveMed').submit()">Save Perscription</button>
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
  </main>

  <footer class="main-footer text-white py-3 mt-5">
    <div class="container text-center">
      <p class="mb-0">CEN3031 Project by Team Small Pharma</p>
    </div>
  </footer>

	
</div>


	<!-- Bootstrap JavaScript (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<!-- Custom JavaScript -->
    <!--script src="js/scripts.js"></script-->
	<script> 
	function toggleThirdDropdown() {
		var secondDropdown = document.getElementById("secondDropdown");
		var DayDropDown = document.getElementById("DayDropDown");
		var MonthDropDown = document.getElementById("MonthDropDown");

		
		// Show second dropdown if the specific option is selected
		if (secondDropdown.value == "2") {
			DayDropDown.style.display = "block";
			MonthDropDown.style.display = "none";
		} else if(secondDropdown.value == "3"){
			DayDropDown.style.display = "none";
			MonthDropDown.style.display = "block";
		} else {
			DayDropDown.style.display = "none";
			MonthDropDown.style.display = "none";
		}
	}
	</script>
	<script>
		function toggleCustomMed() {
			var dropdown = document.getElementById("databaseMedication");
			var custom = document.getElementById("customMedication");
			var medbutton = document.getElementById("medicationButton");
			var field1 = document.getElementById("MedicationNameDatabase");
			var field2 = document.getElementById("MedicationBrandDatabase");
			var field1 = document.getElementById("MedicationNameCustom");
			var field2 = document.getElementById("MedicationBrandCustom");
			

			// Toggle between hiding and showing the menu
			if (dropdown.style.display === "none" || dropdown.style.display === "") {
				dropdown.setAttribute("required", "false");
				dropdown.style.display = "block";
				custom.style.display = "none";
				medbutton.textContent = "Custom Medication";
				
				field1.required = true;
				field2.required = true;
				field3.required = false;
				field4.required = false;
				
			} else {
				dropdown.disabled = false;
				dropdown.style.display = "none";
				custom.style.display = "block";
				medbutton.textContent = "Database Medication";
				
				field1.required = false
				field2.required = false;
				field3.required = true;
				field4.required = true;
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
	const intakeModal = document.getElementById('intakeModal');
    intakeModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const medication = button.getAttribute('data-medication');
        
        // Populate the modal fields
        document.getElementById('medicationName').value = medication;
		medicationInput.style.width = (medicationInput.value.length + 1) + 'ch';
    });

    const addModal = document.getElementById('addModal');
    addModal.addEventListener('show.bs.modal', function (event) {            
        const customMedicationButton = document.getElementById("customMedication");
        
        // Ensure event listener is added only once
        customMedicationButton.removeEventListener("click", toggleCustomMed); // Remove any existing listener
        customMedicationButton.addEventListener("click", toggleCustomMed);
    });
</script>

</body>
</html>
