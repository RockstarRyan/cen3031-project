<?php

include_once("includes/site_construct.inc");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['PrescriptionID'])) {
    // Set prescriptionID based on which row was clicked for delete
    $prescriptionID = intval($_POST['PrescriptionID']);

    try {
        // Turn off autocommit to begin the transaction
        $db->autocommit(false);

        // Step 1: Delete all intakes associated with the PrescriptionID
        $sql1 = "DELETE FROM intakes WHERE PrescriptionID = ?";
        $stmt1 = $db->prepare($sql1);
        $stmt1->bind_param('i', $prescriptionID);
        $stmt1->execute();

        // Step 2: Delete the prescription from the prescriptions table
        $sql2 = "DELETE FROM prescriptions WHERE PrescriptionID = ?";
        $stmt2 = $db->prepare($sql2);
        $stmt2->bind_param('i', $prescriptionID);
        $stmt2->execute();

        // Commit the transaction
        $db->commit();

        redirect('main.php');

    } catch (Exception $e) {
        // Rollback the transaction if an error occurs
        $db->rollback();
        redirect('main.php');
    } finally {
        // Turn autocommit back on
        $db->autocommit(true);
    }
}
?>
