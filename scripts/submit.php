<?php
/**
 * This script handles the form processing
 *
 * PHP version 7.2
 *
 * @category Registration
 * @package  Registration
 * @author   Benson Imoh,ST <benson@stbensonimoh.com>
 * @license  GPL https://opensource.org/licenses/gpl-license
 * @link     https://stbensonimoh.com
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
// echo json_encode($_POST);

// Pull in the required files
require '../config.php';
require './DB.php';
require './Notify.php';
require './Newsletter.php';

// Capture the post data coming from the form
$name = htmlspecialchars($_POST['name'], ENT_QUOTES);
$email = $_POST['email'];
$phone = $_POST['full_phone'];
$businessName = htmlspecialchars($_POST['businessName'], ENT_QUOTES);
$businessDescription = htmlspecialchars($_POST['businessDescription'], ENT_QUOTES);
$expectations = htmlspecialchars($_POST['expectations'], ENT_QUOTES);

$details = array(
    "name" => $name,
    "email" => $email,
    "phone" => $phone,
    "businessName" => $businessName,
    "businessDescription" => $businessDescription,
    "expectations" => $expectations
);

$db = new DB($host, $db, $username, $password);

$notify = new Notify($smstoken, $emailHost, $emailUsername, $emailPassword, $SMTPDebug, $SMTPAuth, $SMTPSecure, $Port);

$newsletter = new Newsletter($apiUserId, $apiSecret);

// First check to see if the user is in the Database
if ($db->userExists($email, "iys_participation")) {
    echo json_encode("user_exists");
} else {
    // Insert the user into the database
    $db->getConnection()->beginTransaction();
    $db->insertUser("iys_participation", $details);
        // Send SMS
        $notify->viaSMS("YouthSummit", "Dear {$firstName} {$lastName}, thank you for registering to be a part of AWLO Youth Summit in commemoration of the International Youth Day. We look forward to receiving you. Kindly check your mail for more details. Thank you.", $phone);

        /**
         * Add User to the SendPulse Mail List
         */
        $emails = array(
            array(
                'email'             => $email,
                'variables'         => array(
                    'name'          => $firstName,
                    'middleName'    => $middleName,
                    'lastName'      => $lastName,
                    'phone'         => $phone,
                    'gender'        => $gender,
                    'city'          => $city
                )
            )
        );

        $newsletter->insertIntoList("2414419", $emails);

        $name = $firstName . ' ' . $lastName;
        // Send Email
        require './emails.php';
        // Send Email
        $notify->viaEmail("youthsummit@awlo.org", "AWLO Youth Summit", $email, $name, $emailBody, "AWLO International Youth Summit");

        $db->getConnection()->commit();

        echo json_encode("success");
}