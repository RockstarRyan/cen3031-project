<?php

include_once("includes/site_construct.inc");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['IntakeID'])) {
    // Set prescriptionID based on which row was clicked for delete
    $IntakeID = intval($_POST['IntakeID']);

    try {
        // Turn off autocommit to begin the transaction
        $db->autocommit(false);

        // Step 1: Delete all intakes associated with the PrescriptionID
        $sql1 = "DELETE FROM intakes WHERE IntakeID = ?";
        $stmt1 = $db->prepare($sql1);
        $stmt1->bind_param('i', $IntakeID);
        $stmt1->execute();

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
