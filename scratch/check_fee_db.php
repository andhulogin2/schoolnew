<?php
$m = new mysqli('localhost', 'root', '', 'db_school');
$res = $m->query("SHOW TABLES LIKE '%fee%'");
echo "Fee tables in db_school:\n";
while($r = $res->fetch_row()) {
    echo "- " . $r[0] . "\n";
}

foreach (['tbl_fee_heads', 'tbl_fee_structures', 'tbl_student_fees', 'tbl_fee_payments'] as $t) {
    echo "\nColumns for {$t}:\n";
    $c = $m->query("DESCRIBE {$t}");
    if ($c) {
        while($cr = $c->fetch_assoc()) {
            echo "  {$cr['Field']} | {$cr['Type']} | {$cr['Null']} | {$cr['Key']} | {$cr['Default']}\n";
        }
    } else {
        echo "  Table {$t} does not exist\n";
    }
}
