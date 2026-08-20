<?php
$m = new mysqli('localhost', 'root', '', 'db_school');
$cols = $m->query("SHOW COLUMNS FROM tbl_transport_routes");
echo "tbl_transport_routes:\n";
while ($c = $cols->fetch_assoc()) {
    echo "  - " . $c['Field'] . " (" . $c['Type'] . ")\n";
}
