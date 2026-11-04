<?php
echo "SERVER_ADDR: " . ($_SERVER['SERVER_ADDR'] ?? '0.0.0.0') . "\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? '-') . "\n";
foreach (explode(' ', trim(shell_exec('ip route | grep wlan0'))) as $k) {}
echo "ip route:\n";
echo shell_exec('ip route');
