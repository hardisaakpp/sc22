<?php
include_once "bd_StoreControl.php";


$data = json_decode(file_get_contents("php://input"), true);

foreach ($data as $item) {
     $stmt = $db->prepare("UPDATE [dbo].[ced_grouprecol] 
         SET [Scan] = :scan 
         WHERE [ID] = :id");

     $stmt->execute([
     ':scan' => $item['scan'],
     ':id' => $item['id']
     ]);
}

echo json_encode(["status" => "success"]);

