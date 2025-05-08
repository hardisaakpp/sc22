<?php
include_once "db.php";

if (isset($_POST["ids"])) {
    $ids = $_POST["ids"];
    $ids = implode(",", array_map('intval', $ids)); // Sanitiza los IDs

    $query = "DELETE FROM StockScan WHERE id IN ($ids)";
    $db->query($query);
    echo "success";
} else {
    echo "error";
}
?>
