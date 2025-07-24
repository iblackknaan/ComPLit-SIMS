<?php
header('Content-Type: text/plain');
echo "=== Active Log ===" . PHP_EOL;
readfile(__DIR__.'/logs/errors_'.date('Y-m-d').'.log');

echo PHP_EOL . "=== Flag File ===" . PHP_EOL;
var_dump(file_exists(__DIR__.'/logs/dev_warning.flag'));