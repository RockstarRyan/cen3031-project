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

					<table class="table table-bordered" style="width: 100%; ">
					<thead class="thead-dark">
					<colgroup>
						<col style="width: 10%;">
						<col style="width: 25%;">
						<col style="width: 15%;">
						<col style="width: 15%;">
						<col style="width: 10%;">

					</colgroup>
						<tr>
						  <th scope="col">Picture</th>
						  <th scope="col">Medication Name</th>
						  <th scope="col">Dosage</th>
						  <th scope="col">Notes</th>
						  <th scope="col">Intakes</th>
						 
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
							echo "<td class=\"text-center\"><button 
							class=\"btn btn-link text-decoration-none\" 
							style=\"padding: calc(0.50 rem + 0.05vw)\"
							data-bs-toggle=\"modal\" 
							data-bs-target=\"#intakeModal\"
							data-medication=\"" . $med['MedicationID'] . "\"
							data-dosage=\"" . $med['PrescriptionDosage'] . " " . $med['PrescriptionUnit'] . "\"
							data-notes=\"Fill with notes\"
							>Add Intake</button>
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
											<div class="col-4 text-start"><?php
												// FILL IN: Replace $med_id with actual medication ID
												$med_id = 5;
												$medication = $db->selectRowsCustom('medications','*',[['MedicationID',$med_id]])[0];
												echo "<select name='MedicationID' disabled><option value='".$medication[0]."' selected>".$medication[1].", ".$medication[2]."</option></select>";
											?></div>
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
													<select class="dynamicDropdown" id="PrescriptionUnit" style="width: auto; font-size: calc(0.40rem + 0.60vw);" onchange="resizeDropdown(this)" required>
														<option value="" disabled selected>Select an amount</option>
														<option value="1">mg</option>
														<option value="2">ml</option>
														<option value="3">capsules</option>
														<option value="4">tablets</option>
													</select>
												</div>
												<label style="width: auto; font-size: calc(0.40rem + 0.60vw);" for="UserPassword"><b>per </b></label>
												<div class="form-group">
													<select class="dynamicDropdown" id="secondDropdown" style="width: auto; font-size: 0.9vw;"onchange="toggleThirdDropdown(); resizeDropdown(this);" required>
														<option value="" disabled selected>Select a frequency</option>
														<option value="1">day</option>
														<option value="2">week</option>
														<option value="3">month</option>
													</select>
												</div>
												<div class="form-group" id="DayDropDown" style="dispaly: none; width: auto; font-size: calc(0.40rem + 0.60vw);" >
													<label style="font-size: 1vw;"><b> on </b></label>
													<select class="dynamicDropdown" id="thirdDropdownSelect" onchange="resizeDropdown(this)">
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
												<input type="text" placeholder="Hr" name="hourValue" style="width: calc(2rem + 0.40vw); font-size: calc(0.40rem + 0.60vw);" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
												<label style="font-size: 1vw;"><b>:</b></label>
												<input type="text" placeholder="Min" name="minuteValue" style="width: calc(2rem + 0.40vw); font-size: calc(0.40rem + 0.60vw);" oninput="this.style.width = (this.value.length + 2) + 'ch'" required></input>	
												<div class="form-group">
													<select class="dynamicDropdown" id="fourthDropdwon" style="width: calc(2rem + 0.40vw); font-size: calc(0.40rem + 0.60vw);"onchange="toggleThirdDropdown(); resizeDropdown(this);" required>
														<option value="1">AM</option>
														<option value="2">PM</option>
													</select>
												</div>
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
														<select class="dynamicDropdown" id="PrescriptionUnit" style="width: auto; font-size: calc(0.40rem + 0.60vw);" onchange="resizeDropdown(this)" required>
															<option value="" disabled selected>Select an amount</option>
															<option value="1">mg</option>
															<option value="2">ml</option>
															<option value="3">capsules</option>
															<option value="4">tablets</option>
														</select>
													</div>
													<!--
													<label style="font-size: 1rem;"><b>per </b></label>
													<div class="form-group">
														<select class="dynamicDropdown" id="secondDropdown" style="width: auto; font-size: 0.9vw;"onchange="toggleThirdDropdown(); resizeDropdown(this);" required>
															<option value="" disabled selected>Select a frequency</option>
															<option value="1">day</option>
															<option value="2">week</option>
															<option value="3">month</option>
														</select>
													</div>
													<div class="form-group" id="DayDropDownContainer" style="font-size: 0.9vw;" >
														<label style="font-size: 1rem;"><b> on </b></label>
														<select class="dynamicDropdown" id="DayDropDown" style="display: none;" onchange="resizeDropdown(this)" required>
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
													-->
												</div>
											</div>
											<!--
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
											-->
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
		
		// Show second dropdown if the specific option is selected
		if (secondDropdown.value == "2") {
			DayDropDown.style.display = "block";
		} else {
			DayDropDown.style.display = "none";
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
        const customMedicationButton = document.getElementById("customMedication");
        
        // Ensure event listener is added only once
        customMedicationButton.removeEventListener("click", toggleCustomMed); // Remove any existing listener
        customMedicationButton.addEventListener("click", toggleCustomMed);
    });
</script>
</body>
</html>
