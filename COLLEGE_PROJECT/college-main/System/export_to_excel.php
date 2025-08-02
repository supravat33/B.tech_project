<?php
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=registrations.xls");

$conn = new mysqli("localhost", "root", "", "event_registration");

$result = $conn->query("SELECT * FROM registrations");

echo "ID\tFull Name\tEmail\tPhone\tCollege\tDepartment\tYear\tEvent\tComments\tRegistered At\n";

while ($row = $result->fetch_assoc()) {
    echo "{$row['id']}\t{$row['fullname']}\t{$row['email']}\t{$row['phone']}\t{$row['college']}\t{$row['department']}\t{$row['year']}\t{$row['event']}\t{$row['comments']}\t{$row['registered_at']}\n";
}
?>


