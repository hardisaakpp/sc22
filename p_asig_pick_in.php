<?php

echo $barcode = $_POST['barcode'];
$asignaciones = json_decode($_POST['asignaciones_json'], true);

echo '<pre>';
print_r($asignaciones);
echo '</pre>';

?>