<?php
// prints e.g. 'Current PHP version: 4.1.1'
echo 'Current PHP version: ' . phpversion() . "\n";

// prints e.g. '2.0' or nothing if the extension isn't enabled
echo phpversion('tidy');


/**
 * This code will benchmark your server to determine how high of a cost you can
 * afford. You want to set the highest cost that you can without slowing down
 * you server too much. 8-10 is a good baseline, and more is good if your servers
 * are fast enough (UoB server probably are). The code below aims for ≤ 50 milliseconds stretching time,
 * which is a good baseline for systems handling interactive logins.
 * Source: PHP API for password_hash() as at 7th of June 2017
 */

$timeTarget = 0.05; // 50 milliseconds

$cost = 8;
do {
    $cost++;
    $start = microtime(true);
    password_hash("test", PASSWORD_BCRYPT, ["cost" => $cost]);
    $end = microtime(true);
} while (($end - $start) < $timeTarget);

echo "Appropriate Cost For Password Hashing: " . $cost . "\n";
echo "Update Password Hashing Accordingly";

?>
