<?php

include_once("includes/site_construct.inc");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'savePrescription':
            handleSavePrescription($db);
            break;

        // case 'deletePrescription':
        //     // Code to delete a prescription
        //     handleDeletePrescription($db);
        //     break;

        case 'addIntake':
            // Code to add an intake
            handleAddIntake($db);
            break;

        default:
            // Redirect or handle invalid actions
            redirect('main.php');
            break;
    }

}

function handleSavePrescription($db) {
    //Instantiating for potential changing depending on whether or not it is a custom medication
    $MedicationID = 0;
    $isCustom = false;

    // References the highest existing PrescriptionID to avoid conflicts
    $table_name = "prescriptions";
    $sql1 = "SELECT MAX(PrescriptionID) AS max_pID FROM $table_name";
    $result = $db->query($sql1);
    $max_pID = $result->fetch_assoc()['max_pID'];

    // References the highest existing MedicationID to avoid conflicts
    $table_name = "medications";
    $sql1 = "SELECT MAX(MedicationID) AS max_mID FROM $table_name";
    $result = $db->query($sql1);
    $max_mID = $result->fetch_assoc()['max_mID'];


    //Ensures all parameters are set in order to correctly add to the database
    if ((isset($_POST['MedicationID']) || (isset($_POST['MedicationNameCustom']) 
    && isset($_POST['MedicationBrandCustom']))) 
    && (isset($_POST['medicationAmount'])) 
    && (isset($_POST['PrescriptionUnit']))) {
        
        //Checks if a custom medication is being added and adds that first if so
        if (isset($_POST['MedicationNameCustom']) && isset($_POST['MedicationBrandCustom'])
        && (!empty($_POST['MedicationNameCustom']) && !empty($_POST['MedicationBrandCustom']))){
            $isCustom = true;
            $MedicationID = $max_mID !== null ? $max_mID + 1 : 1;
            $MedicationBrand = $_POST['MedicationBrandCustom'];
            $MedicationName = $_POST['MedicationNameCustom'];

            $insertion = $db->prepare("INSERT INTO medications (MedicationID, MedicationBrand, MedicationName) VALUES (?, ?, ?)");
            $insertion->bind_param("iss", $MedicationID, $MedicationBrand, $MedicationName);

            // Execute the statement
            if ($insertion->execute()) {
                $yay = 'yay';
            } else {
                echo "Error: " . $insertion->error;
            }

            // Close the statement and connection
            $insertion->close();
        }


        //Adds prescription to the database using either selected medication or the created custom one
        $PrescriptionID = $max_pID !== null ? $max_pID + 1 : 1; // Start at 1 if no rows exist 
        $UserID = (int)$_SESSION['UserID'];
        //May have to adjust for custom med
        if (!$isCustom){
            $MedicationID = $_POST['MedicationID'];
        }
        $PrescriptionUnit = $_POST['PrescriptionUnit'];
        $PrescriptionDosage = $_POST['medicationAmount'];
        $PrescriptionNotes = $_POST['PrescriptionNotes'];


        $insertion = $db->prepare("INSERT INTO prescriptions (PrescriptionID, UserID, MedicationID, PrescriptionUnit, PrescriptionDosage, PrescriptionNotes) VALUES (?, ?, ?, ?, ?, ?)");
        $insertion->bind_param("iiisis", $PrescriptionID, $UserID, $MedicationID, $PrescriptionUnit, $PrescriptionDosage, $PrescriptionNotes);

        if ($insertion->execute()) {
            $yay = 'yay';
        } else {
            echo "Error: " . $insertion->error;
        }

        // Close the statement and connection
        $insertion->close();
        

        //Redirects to main.php which then refreshes and should display the newly added prescription
        redirect('main.php');
    }
    else {
        //Fix later please or ensure that if user does not have all of those variables set that this page will never be called
        redirect('main.php');
    }
}


function handleAddIntake($db) {

 // References the highest existing IntakeID to avoid conflicts
 $table_name = "intakes";
 $sql1 = "SELECT MAX(IntakeID) AS max_iID FROM $table_name";
 $result = $db->query($sql1);
 $max_iID = $result->fetch_assoc()['max_iID'];

//Insertion queries for Add intake
$IntakeID = $max_iID !== null ? $max_iID + 1 : 1; // Start at 1 if no rows exist
$PrescriptionID = $_POST['PrescriptionID'];
$IntakeTime = "deafault text here";

// defining variables for IntakeTime text construction
$hourValue = $_POST['hourValue'];
$minuteValue = $_POST['minuteValue'];
$ampm = $_POST['ampm'];

if($_POST['timeframe'] == "1"){
    $IntakeTime = "Everyday at {$hourValue}:{$minuteValue} $ampm";
}
if($_POST['timeframe'] == "2"){
    $weekday = $_POST['weekday'];
    $IntakeTime = "$weekday at {$hourValue}:{$minuteValue} $ampm";
}
if($_POST['timeframe'] == "3"){
    $IntakeTime = "Every month at {$hourValue}:{$minuteValue} $ampm";
}

$insertion = $db->prepare("INSERT INTO intakes (IntakeID, PrescriptionID, IntakeTime) VALUES (?, ?, ?)");
$insertion->bind_param("iis", $IntakeID, $PrescriptionID, $IntakeTime);

// Execute the statement
if ($insertion->execute()) {
    $yay = 'yay';
} else {
    echo "Error: " . $insertion->error;
}

// Close the statement and connection
$insertion->close();

redirect('main.php');

}






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