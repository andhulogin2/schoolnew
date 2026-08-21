<?php
require_once __DIR__ . '/debug_post.php';
if (preg_match('/<!-- Flash Messages -->(.*?)<!-- Header/s', $html2, $m)) {
    echo "Flash container:\n" . $m[1] . "\n";
} else {
    echo "Flash container not matched. Looking for material-symbols-outlined:\n";
    preg_match_all('/<span class="material-symbols-outlined[^"]*">([^<]+)<\/span>\s*([^<\n]+)/', $html2, $all_m, PREG_SET_ORDER);
    foreach ($all_m as $item) {
        echo "Icon: {$item[1]} | Text: {$item[2]}\n";
    }
}
