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