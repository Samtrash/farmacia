<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "PHP Version: " . phpversion() . "<br>";
echo "MySQLi: " . (extension_loaded('mysqli') ? 'Yes' : 'No') . "<br>";
echo "Intl: " . (extension_loaded('intl') ? 'Yes' : 'No') . "<br>";
echo "Mbstring: " . (extension_loaded('mbstring') ? 'Yes' : 'No') . "<br>";
echo "CURL: " . (extension_loaded('curl') ? 'Yes' : 'No') . "<br>";

try {
    $db = new mysqli('localhost', 'root', '', 'farmacia');
    if ($db->connect_error) {
        echo "DB Error: " . $db->connect_error . "<br>";
    } else {
        echo "DB Connected successfully!<br>";
    }
} catch (Exception $e) {
    echo "DB Exception: " . $e->getMessage() . "<br>";
}

echo "Done.";
