<?php
require __DIR__ . '/vendor/autoload.php';
$s = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $s->getActiveSheet();
echo get_class($sheet) . PHP_EOL;
$methods = get_class_methods($sheet);
// print only methods that contain 'setCell' to be concise
$filtered = array_filter($methods, function ($m) {
    return stripos($m, 'setcell') !== false || stripos($m, 'setCell') !== false;
});
print_r($filtered);
echo "\nTotal methods: " . count($methods) . "\n";
