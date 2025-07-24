<?php
// Include the header
require_once 'header_teacher_profile.php'; // Adjust the path if necessary
?>


<div class="container mt-5">
    <h1 class="mb-4">Notifications</h1>
    <?php if (count($notifications) > 0): ?>
        <ul class="list-group">
            <?php foreach ($notifications as $notification): ?>
                <li class="list-group-item">
                    <strong>From:</strong> <?php echo htmlspecialchars($notification['SenderID']); ?><br>
                    <strong>Date Sent:</strong> <?php echo htmlspecialchars($notification['DateSent']); ?><br>
                    <strong>Message:</strong> <?php echo htmlspecialchars($notification['Message']); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No notifications available.</p>
    <?php endif; ?>
</div>

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
