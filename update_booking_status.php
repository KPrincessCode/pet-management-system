<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: api_bookings.php");
    exit;
}

$bookingId = $_POST['booking_id'] ?? null;
$status = $_POST['status'] ?? null;

if (!$bookingId || !$status) {
    header("Location: api_bookings.php");
    exit;
}

$apiUrl = "http://127.0.0.1:8000/api/bookings/" . $bookingId;

$data = http_build_query([
    'status' => $status
]);

$options = [
    'http' => [
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'method' => 'PUT',
        'content' => $data,
        'ignore_errors' => true,
    ]
];

$context = stream_context_create($options);
file_get_contents($apiUrl, false, $context);

header("Location: api_bookings.php");
exit;