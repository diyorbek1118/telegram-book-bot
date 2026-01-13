<?php
require 'vendor/autoload.php';

use Core\Database;

$db = Database::connect();
echo "Database connected successfully\n";
