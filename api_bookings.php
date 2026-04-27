<?php
include "includes/header.php";

/* ================= CHECK IN / CHECK OUT HANDLER ================= */
if (isset($_POST['checkin_id'])) {
    $id = $_POST['checkin_id'];

    $ch = curl_init("http://127.0.0.1:8000/api/bookings/$id/check-in");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    header("Location: api_bookings.php");
    exit;
}

if (isset($_POST['checkout_id'])) {
    $id = $_POST['checkout_id'];

    $ch = curl_init("http://127.0.0.1:8000/api/bookings/$id/check-out");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    header("Location: api_bookings.php");
    exit;
}
/* ================================================================= */

$apiUrl = "http://127.0.0.1:8000/api/bookings";
$response = @file_get_contents($apiUrl);

if ($response === false) {
    $bookings = [];
    $error = "Cannot connect to Laravel API.";
} else {
    $bookings = json_decode($response, true);
    $error = null;
}
?>

<div class="container mt-4">
    <h1>Pet Hostel Bookings</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Owner</th>
                <th>Pet Type</th>
                <th>Service</th>
                <th>Medicine</th>
                <th>Injection</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Payment</th>
                <th>Payment Status</th>
                <th>Booking Status</th>
                <th>Remarks</th>
                <th>Admin Notes</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($bookings as $b): ?>
        <tr>
            <td><?= $b['id'] ?></td>
            <td><?= $b['owner_name'] ?></td>
            <td><?= $b['pet_type'] ?></td>
            <td><?= $b['service_type'] ?></td>
            <td><?= $b['medicine_needed'] ?></td>
            <td><?= $b['injection_status'] ?></td>
            <td><?= $b['check_in_date'] ?></td>
            <td><?= $b['check_out_date'] ?></td>
            <td><?= $b['payment_amount'] ?></td>

            <!-- PAYMENT -->
            <td>
                <?= $b['payment_status'] == 'paid'
                    ? '<span class="badge bg-success">Paid</span>'
                    : '<span class="badge bg-warning">Unpaid</span>' ?>
            </td>

            <!-- BOOKING STATUS (DISPLAY ONLY) -->
            <td>
                <?php
                $status = $b['status'];
                if ($status == 'Pending') echo '<span class="badge bg-warning">Pending</span>';
                elseif ($status == 'Approved') echo '<span class="badge bg-success">Approved</span>';
                elseif ($status == 'Checked In') echo '<span class="badge bg-primary">Checked In</span>';
                elseif ($status == 'Checked Out') echo '<span class="badge bg-secondary">Checked Out</span>';
                elseif ($status == 'Rejected') echo '<span class="badge bg-danger">Rejected</span>';
                ?>
            </td>

            <!-- REMARKS -->
            <td>
                <?php
                if ($b['medicine_needed'] == 'Yes') {
                    echo '<span class="badge bg-danger">Medicine Needed</span>';
                } elseif ($b['injection_status'] == 'No') {
                    echo '<span class="badge bg-warning">Vaccination Needed</span>';
                } else {
                    echo '<span class="badge bg-success">No Issues</span>';
                }
                ?>
            </td>

            <!-- ADMIN NOTES -->
            <td><?= $b['admin_notes'] ?? 'None' ?></td>

            <!-- ACTION COLUMN -->
            <td>

                <?php if ($b['status'] == 'Pending' && $b['payment_status'] == 'paid'): ?>

                    <!-- APPROVE -->
                    <form method="POST" action="update_booking_status.php">
                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                        <input type="hidden" name="status" value="Approved">
                        <button class="btn btn-success btn-sm">Approve</button>
                    </form>

                <?php elseif ($b['status'] == 'Approved'): ?>

                    <!-- CHECK IN -->
                    <form method="POST">
                        <input type="hidden" name="checkin_id" value="<?= $b['id'] ?>">
                        <button class="btn btn-primary btn-sm">Check In</button>
                    </form>

                <?php elseif ($b['status'] == 'Checked In'): ?>

                    <!-- CHECK OUT -->
                    <form method="POST">
                        <input type="hidden" name="checkout_id" value="<?= $b['id'] ?>">
                        <button class="btn btn-warning btn-sm">Check Out</button>
                    </form>

                <?php elseif ($b['status'] == 'Checked Out'): ?>

                    <span class="text-muted">Done</span>

                <?php endif; ?>

            </td>

        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include "includes/footer.php"; ?>