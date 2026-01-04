<?php
session_start();
// Database connection settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "turfdb";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed. Please try again later.");
}

$sql = "SELECT * FROM signup WHERE role!='admin'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="userManagement.css">
    <style>
        /* Optional styles for modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            width: 300px;
        }
        .modal button {
            margin: 10px;
            padding: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/turfease logo.png" class="logo">
    </div>
    <div class="user-details-page">
        <h2>User Details</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>USERNAME</th>
                    <th>EMAIL</th>
                    <th>ROLE</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['role']); ?></td>
                            <td>
                                <button class="btn" data-id="<?php echo $row['id']; ?>">Remove</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No user found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for confirmation -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to remove this user?</p>
            <button id="confirmDelete">Yes</button>
            <button id="cancelDelete">No</button>
        </div>
    </div>

    <script>
        const buttons = document.querySelectorAll('.btn');
        const modal = document.getElementById('confirmModal');
        const confirmDelete = document.getElementById('confirmDelete');
        const cancelDelete = document.getElementById('cancelDelete');
        let userId = null;

        // Open modal and set user ID
        buttons.forEach(button => {
            button.addEventListener('click', () => {
                userId = button.getAttribute('data-id');
                modal.style.display = 'flex';
            });
        });

        // Cancel deletion
        cancelDelete.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Confirm deletion
        confirmDelete.addEventListener('click', () => {
            if (userId) {
                fetch('delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${userId}`,
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('User removed successfully');
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert(data.error || 'An error occurred');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An unexpected error occurred.');
                    })
                    .finally(() => {
                        modal.style.display = 'none';
                    });
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
