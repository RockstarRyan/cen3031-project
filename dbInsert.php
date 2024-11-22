<?php

include_once("includes/site_construct.inc");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Instantiating for potential changing depending on whether or not it is a custom medication
    $MedicationID = 0;
    $isCustom = false;
    
    // References prescription count in order to allow new prescriptions to fill the next available id spot
    $table_name = "prescriptions";
    $sql1 = "SELECT COUNT(*) AS pID_count FROM $table_name";
    $pID_count = $db->query($sql1)->fetch_assoc()['pID_count'];

    // References medication count in order to allow new medications to fill the next available id spot
    $table_name = "medications";
    $sql1 = "SELECT COUNT(*) AS mID_count FROM $table_name";
    $mID_count = $db->query($sql1)->fetch_assoc()['mID_count'];

    // References medication count in order to allow new medications to fill the next available id spot
    $table_name = "intakes";
    $sql1 = "SELECT COUNT(*) AS iID_count FROM $table_name";
    $iID_count = $db->query($sql1)->fetch_assoc()['iID_count'];

    //Ensures all parameters are set in order to correctly add to the database
    if ((isset($_POST['MedicationID']) || (isset($_POST['MedicationNameCustom']) 
    && isset($_POST['MedicationBrandCustom']))) 
    && (isset($_POST['medicationAmount'])) 
    && (isset($_POST['PrescriptionUnit']))) {
        
        //Checks if a custom medication is being added and adds that first if so
        if (isset($_POST['MedicationNameCustom']) && isset($_POST['MedicationBrandCustom'])){
            $isCustom = true;
            $MedicationID = $mID_count + 2;
            $MedicationBrand = $_POST['MedicationNameCustom'];
            $MedicationName = $_POST['MedicationBrandCustom'];

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
        $PrescriptionID = $pID_count + 2; 
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



//Insertion queries for Add intake


// $IntakeID = $iID_count + 2;
// $PrescriptionID = REFERENCE THE ROW THE INTAKE IS BEING ADDED TO;
// $IntakeTime = INSERT FROM USER INPUT;


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



//Refresh command to update table for user viewing

//header("Refresh: 0");


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