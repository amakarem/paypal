<?php
// For test payments we want to enable the sandbox mode. If you want to put live
// payments through then this setting needs changing to `false`.
$enableSandbox = true;
$currency_code = 'USD';

// Database settings. Change these for your database configuration.
$dbConfig = [
    'host' => 'localhost',
    'username' => 'user',
    'password' => 'secret',
    'name' => 'example_database',
];

// Callback settings. Change these relevant URLs for your site.
$success_url = 'http://example.com/payment-successful.html';
$cancel_url = 'http://example.com/payment-cancelled.html';
$notify_url = 'http://example.com/payments.php';
?>