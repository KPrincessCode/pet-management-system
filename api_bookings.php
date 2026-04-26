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
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
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
                    <th>Admin Notes</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($bookings)): ?>
                    <?php foreach ($bookings as $booking): ?>
                        <?php
                            $bookingId = $booking['id'] ?? '';
                            $paymentStatus = $booking['payment_status'] ?? '';
                            $bookingStatus = $booking['status'] ?? 'Pending';
                            $adminNotes = $booking['admin_notes'] ?? '';
                        ?>

                        <tr>
                            <td><?= htmlspecialchars($bookingId) ?></td>
                            <td><?= htmlspecialchars($booking['owner_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($booking['pet_type'] ?? '') ?></td>
                            <td><?= htmlspecialchars($booking['service_type'] ?? '') ?></td>
                            <td><?= htmlspecialchars($booking['medicine_needed'] ?? '') ?></td>
                            <td><?= htmlspecialchars($booking['injection_status'] ?? '') ?></td>
                            <td><?= htmlspecialchars($booking['check_in_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($booking['check_out_date'] ?? '') ?></td>
                            <td><?= htmlspecialchars($booking['payment_amount'] ?? '') ?></td>

                            <td>
                                <?php if ($paymentStatus === 'paid'): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php elseif ($paymentStatus === 'unpaid'): ?>
                                    <span class="badge bg-warning text-dark">Unpaid</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Not Set</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($bookingStatus === 'Approved'): ?>
                                    <span class="badge bg-success">Approved</span>
                                <?php elseif ($bookingStatus === 'Rejected'): ?>
                                    <span class="badge bg-danger">Rejected</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php
                                $hasRemark = false;

                                if (($booking['injection_status'] ?? '') === 'No') {
                                    echo '<span class="badge bg-warning text-dark mb-1">Vaccination Needed</span> ';
                                    $hasRemark = true;
                                }

                                if (($booking['medicine_needed'] ?? '') === 'Yes') {
                                    echo '<span class="badge bg-danger mb-1">Medicine Needed</span>';
                                    $hasRemark = true;
                                }

                                if (!$hasRemark) {
                                    echo '<span class="badge bg-success">No Issues</span>';
                                }
                                ?>
                            </td>

                            <td style="min-width: 220px;">
                                <?php if (!empty($adminNotes)): ?>
                                    <?= htmlspecialchars($adminNotes) ?>
                                <?php else: ?>
                                    <span class="text-muted">No admin notes yet</span>
                                <?php endif; ?>
                            </td>

                            <td style="min-width: 260px;">
                                <?php if ($bookingStatus === 'Pending' && $paymentStatus === 'paid'): ?>

                                    <form method="POST" action="update_booking_status.php" class="mb-2">
                                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($bookingId) ?>">
                                        <input type="hidden" name="status" value="Approved">

                                        <textarea
                                            name="admin_notes"
                                            class="form-control form-control-sm mb-1"
                                            rows="2"
                                            placeholder="Approval notes"
                                            required>Bring vaccination card during check-in.</textarea>

                                        <button
                                            type="submit"
                                            class="btn btn-success btn-sm w-100"
                                            onclick="return confirm('Approve this booking with the admin note?');"
                                        >
                                            Approve
                                        </button>
                                    </form>

                                    <form method="POST" action="update_booking_status.php">
                                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($bookingId) ?>">
                                        <input type="hidden" name="status" value="Rejected">

                                        <textarea
                                            name="admin_notes"
                                            class="form-control form-control-sm mb-1"
                                            rows="2"
                                            placeholder="Rejection reason"
                                            required>Booking rejected. Please contact the clinic for more information.</textarea>

                                        <button
                                            type="submit"
                                            class="btn btn-danger btn-sm w-100"
                                            onclick="return confirm('Reject this booking with the admin note?');"
                                        >
                                            Reject
                                        </button>
                                    </form>

                                <?php elseif ($bookingStatus === 'Pending' && $paymentStatus !== 'paid'): ?>
                                    <span class="badge bg-warning text-dark">Payment Required</span>

                                <?php else: ?>
                                    <?php if ($bookingStatus === 'Approved'): ?>
                                        <span class="badge bg-success">Accepted</span>
                                    <?php elseif ($bookingStatus === 'Rejected'): ?>
                                        <span class="badge bg-danger">Rejected</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Processed</span>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="14" class="text-center">No bookings found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "includes/footer.php"; ?>