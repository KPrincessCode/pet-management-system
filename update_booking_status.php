<?php

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: api_bookings.php");
    exit;
}

$bookingId = $_POST['booking_id'] ?? null;
$status = $_POST['status'] ?? null;
$adminNotes = trim($_POST['admin_notes'] ?? '');

if (!$bookingId || !$status) {
    header("Location: api_bookings.php");
    exit;
}

if ($status === 'Approved') {
    $apiUrl = "http://127.0.0.1:8000/api/bookings/" . $bookingId . "/approve";
} elseif ($status === 'Rejected') {
    $apiUrl = "http://127.0.0.1:8000/api/bookings/" . $bookingId . "/reject";
} else {
    header("Location: api_bookings.php");
    exit;
}

$data = http_build_query([
    'admin_notes' => $adminNotes
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