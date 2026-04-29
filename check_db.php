<?php
$conn = new mysqli('localhost', 'root', '', 'prestark');
$res = $conn->query("SELECT vendor_name FROM vendors");
$data = [];
while($row = $res->fetch_assoc()) $data[] = $row['vendor_name'];
echo json_encode($data);
?>
