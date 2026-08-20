<?php
$m = new mysqli('localhost', 'root', '', 'db_school');
$r = $m->query("SELECT * FROM tbl_certificates");
echo "Count: " . $r->num_rows . "\n";
while($row = $r->fetch_assoc()) {
    print_r($row);
}
