<?php
// include Settings
require __DIR__ . '/setting.php';
// Include Functions
require __DIR__ . '/functions.php';

// Check if paypal request or response
if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])) {
    if (isset($_POST['accountid'])) {
        // PayPal settings. Change these to your account details and the relevant URLs
        // for your site.
        $paypalConfig = [
            'email' => $_POST['accountid'],
            'return_url' => $success_url,
            'cancel_url' => $cancel_url,
            'notify_url' => $notify_url,
        ];

        $paypalUrl = $enableSandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

        // Grab the post data so that we can set up the query string for PayPal.
        // Ideally we'd use a whitelist here to check nothing is being injected into
        // our post data.
        $data = [];
        foreach ($_POST as $key => $value) {
            if($key != 'accountid') {
                $data[$key] = stripslashes($value);
            }
        }

        // Set the PayPal account.
        $data['business'] = $paypalConfig['email'];

        // Set the PayPal return addresses.
        $data['return'] = stripslashes($paypalConfig['return_url']);
        $data['cancel_return'] = stripslashes($paypalConfig['cancel_url']);
        $data['notify_url'] = stripslashes($paypalConfig['notify_url']);

        // and currency so that these aren't overridden by the form data.
        $data['currency_code'] = $currency_code;

        // Add any custom fields for the query string.
        //$data['custom'] = USERID;

        // Build the query string from the data.
        $queryString = http_build_query($data);

        // Redirect to paypal IPN
        header('location:' . $paypalUrl . '?' . $queryString);
        exit();
    } else {
        echo 'Business Account not set';
    }
} else {
    // Handle the PayPal response.

    // Create a connection to the database.
    $db = new mysqli($dbConfig['host'], $dbConfig['username'], $dbConfig['password'], $dbConfig['name']);

    // Assign posted variables to local data array.
    $data = [
        'item_name' => $_POST['item_name'],
        'item_number' => $_POST['item_number'],
        'payment_status' => $_POST['payment_status'],
        'payment_amount' => $_POST['mc_gross'],
        'payment_currency' => $_POST['mc_currency'],
        'txn_id' => $_POST['txn_id'],
        'receiver_email' => $_POST['receiver_email'],
        'payer_email' => $_POST['payer_email'],
        'custom' => $_POST['custom'],
    ];

    // We need to verify the transaction comes from PayPal and check we've not
    // already processed the transaction before adding the payment to our
    // database.
    if (verifyTransaction($_POST) && checkTxnid($data['txn_id'])) {
        if (addPayment($data) !== false) {
            // Payment successfully added.
        }
    }
}
