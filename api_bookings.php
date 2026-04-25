<?php
include "includes/header.php";

$apiUrl = "http://127.0.0.1:8000/api/bookings";

$response = @file_get_contents($apiUrl);

if ($response === false) {
    $bookings = [];
    $error = "Cannot connect to Laravel API. Make sure php artisan serve is running.";
} else {
    $bookings = json_decode($response, true);
    $error = null;
}
?>

<div class="container mt-4">
    <h1>Pet Hostel Bookings</h1>
    <p class="text-muted">Bookings submitted from PET_HOSTEL</p>

    <?php if ($error): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Owner</th>
                <th>Pet Type</th>
                <th>Service Type</th>
                <th>Medicine</th>
                <th>Injection</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Payment</th>
                <th>Payment Status</th>
                <th>Booking Status</th>
                <th>Remarks</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            <?php if (!empty($bookings)): ?>
                <?php foreach ($bookings as $booking): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($booking['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['owner_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['pet_type'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['service_type'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['medicine_needed'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['injection_status'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['check_in_date'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['check_out_date'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['payment_amount'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($booking['payment_status'] ?? ''); ?></td>

                        <td>
                            <?php if (($booking['status'] ?? '') == 'Approved'): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php elseif (($booking['status'] ?? '') == 'Rejected'): ?>
                                <span class="badge bg-danger">Rejected</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php
                            $hasRemark = false;

                            if (($booking['injection_status'] ?? '') == 'No') {
                                echo '<span class="badge bg-warning text-dark">Vaccination Needed</span> ';
                                $hasRemark = true;
                            }

                            if (($booking['medicine_needed'] ?? '') == 'Yes') {
                                echo '<span class="badge bg-danger">Medicine Needed</span>';
                                $hasRemark = true;
                            }

                            if (!$hasRemark) {
                                echo '<span class="badge bg-success">No Issues</span>';
                            }
                            ?>
                        </td>

                        <td>
                            <?php if (($booking['status'] ?? '') == 'Pending'): ?>
                                <form method="POST" action="update_booking_status.php" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                                    <input type="hidden" name="status" value="Approved">
                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                </form>

                                <form method="POST" action="update_booking_status.php" style="display:inline;">
                                    <input type="hidden" name="booking_id" value="<?php echo htmlspecialchars($booking['id']); ?>">
                                    <input type="hidden" name="status" value="Rejected">
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">No action</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
           <?php else: ?>
    <?php if (($booking['status'] ?? '') == 'Approved'): ?>
        <span class="badge bg-success">Accepted</span>
    <?php elseif (($booking['status'] ?? '') == 'Rejected'): ?>
        <span class="badge bg-danger">Rejected</span>
    <?php else: ?>
        <span class="badge bg-secondary">Processed</span>
    <?php endif; ?>
<?php endif; ?>
        </tbody>
    </table>
</div>

<?php include "includes/footer.php"; ?>