<?php

require_once __DIR__ . '/paths-header.php'; //Now update this path for file system updates
require_once FILE_PATH . GOOGLE_SERVICES_HEADER_PATH;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$error_message;
switch (filter_input(INPUT_GET, 'e', FILTER_SANITIZE_STRING)) {
    case "invalid_token":
        $error_message = 'Token was invalid or has already been used.';
        break;
    case "token_expired":
        $error_message = 'Access token has expired.';
        break;
    case "invalid_event":
        $error_message = 'Event was invalid or has been removed.';
        break;
    case "invalid_email":
        $error_message = 'Email found was invalid. This is due to an unknown error. Please try the link again.';
        break;
    case "missing_token":
        $error_message = 'URL does not include access token.';
        break;
    case "missing_event":
        $error_message = 'URL does not include event id.';
        break;
    case "sql_connection":
        $error_message = 'SQL database connection error.';
        break;
    case "sql_insertion":
        $error_message = 'SQL database insertion error, no lines changed.';
        break;
    case "sql_charset":
        $error_message = 'SQL database error: Error loading character set utf8';
        break;
    default:
        $error_message = 'Unknown error, please try again';
        break;
}
session_destroy();
session_start();
$_SESSION['calendarsaved'] = array("messagetype" => "errors", "message" => $error_message);
redirect_local("../index.php");

