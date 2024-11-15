<?php

include_once("includes/site_construct.inc");


//Inserts prescriptions

// $PrescriptionID = 4; 
// $UserID = 7;
// $MedicationID = 2;
// $PrescriptionTime = '2024-11-04 12:00:00';
// $PrescriptionUnit = 'ml';
// $PrescriptionDosage = 25;


// $insertion = $db->prepare("INSERT INTO prescriptions (PrescriptionID, UserID, MedicationID, PrescriptionTime, PrescriptionUnit, PrescriptionDosage) VALUES (?, ?, ?, ?, ?, ?)");
// $insertion->bind_param("iiissi", $PrescriptionID, $UserID, $MedicationID, $PrescriptionTime, $PrescriptionUnit, $PrescriptionDosage);

// // Execute the statement
// if ($insertion->execute()) {
//     echo "New record added successfully";
// } else {
//     echo "Error: " . $insertion->error;
// }

// // Close the statement and connection
// $insertion->close();



//Inserts Intakes


// $IntakeID = 3; 
// $PrescriptionID = 3;
// $IntakeTime = '2024-11-03 11:00:00';


// $insertion = $db->prepare("INSERT INTO intakes (IntakeID, PrescriptionID, IntakeTime) VALUES (?, ?, ?)");
// $insertion->bind_param("iis", $IntakeID, $PrescriptionID, $IntakeTime);

// // Execute the statement
// if ($insertion->execute()) {
//     echo "New record added successfully";
// } else {
//     echo "Error: " . $insertion->error;
// }

// // Close the statement and connection
// $insertion->close();

//Inserts Medications


// $MedicationID = 3; 
// $MedicationBrand = 'Big Pharma';
// $MedicationName = 'Name';


// $insertion = $db->prepare("INSERT INTO medications (MedicationID, MedicationBrand, MedicationName) VALUES (?, ?, ?)");
// $insertion->bind_param("iss", $MedicationID, $MedicationBrand, $MedicationName);

// // Execute the statement
// if ($insertion->execute()) {
//     echo "New record added successfully";
// } else {
//     echo "Error: " . $insertion->error;
// }

// // Close the statement and connection
// $insertion->close();


//Prints out all values of a particular table


// $sql = "SELECT * FROM intakes";
// $result = $db->query($sql);
// if (!$result) {
//     echo "its returning nothing";
// }

// while ($row = $result->fetch_assoc()) {
//     var_dump($row); // Print each row's data
// }

// // Check if there are results and print them
// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         var_dump($row); // Print each row's data
//     }
// } else {
//     echo "No medications exist for this user";
// }


?>