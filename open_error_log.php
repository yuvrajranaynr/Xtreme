<?php
// Open the PHP error log file in Visual Studio Code
$logFilePath = 'C:\\xampp\\php\\logs\\php_error_log';

if (file_exists($logFilePath)) {
    echo "<p>To open the error log in Visual Studio Code, run the following command in your terminal:</p>";
    echo "<code>code $logFilePath</code>";
} else {
    echo "<p>Error log file not found at: $logFilePath</p>";
}
?>