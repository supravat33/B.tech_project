<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "event_registration";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle reset
if (isset($_GET['reset_db']) && $_GET['reset_db'] == 'true' && $_GET['confirm'] == 'yes') {
    $conn->query("DELETE FROM registrations");
    $conn->query("ALTER TABLE registrations AUTO_INCREMENT = 1");
    header("Location: view_registrations.php");
    exit();
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $conn->query("DELETE FROM registrations WHERE id = $deleteId");
    header("Location: view_registrations.php");
    exit();
}

// Handle edit
$editMode = false;
if (isset($_GET['edit_id'])) {
    $editId = intval($_GET['edit_id']);
    $result = $conn->query("SELECT * FROM registrations WHERE id = $editId");
    $registration = $result->fetch_assoc();
    $editMode = true;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $college = $_POST['college'];
        $department = $_POST['department'];
        $year = $_POST['year'];
        $event = $_POST['event'];
        $comments = $_POST['comments'];

        $updateSql = "UPDATE registrations SET fullname = ?, email = ?, phone = ?, college = ?, department = ?, year = ?, event = ?, comments = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssssssssi", $fullname, $email, $phone, $college, $department, $year, $event, $comments, $editId);

        if ($stmt->execute()) {
            header("Location: view_registrations.php");
            exit();
        } else {
            echo "Error updating record: " . $stmt->error;
        }
    }
}

// Handle search
$search = "";
if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $sql = "SELECT * FROM registrations WHERE 
        fullname LIKE '%$search%' OR 
        email LIKE '%$search%' OR 
        phone LIKE '%$search%' OR 
        college LIKE '%$search%' OR 
        department LIKE '%$search%' OR 
        year LIKE '%$search%' OR 
        event LIKE '%$search%'
        ORDER BY id ASC";
} else {
    $sql = "SELECT * FROM registrations ORDER BY id ASC";
}
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Event Registrations</title>
    <style>
        body { font-family: Arial; background: #f4f4f4; padding: 20px; }
        table { border-collapse: collapse; width: 100%; background: white; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background: #2c3e50; color: white; }
        .btn { padding: 6px 12px; border-radius: 4px; color: white; text-decoration: none; margin: 2px; }
        .edit-btn { background: #3498db; }
        .delete-btn { background: #e74c3c; }
        .export-btn { background: #2ecc71; display: inline-block; margin: 10px 0; }
        .reset-btn { background: #c0392b; margin: 10px 0; display: inline-block; }
        h1 { text-align: center; }
        form input, textarea { display: block; width: 100%; margin-bottom: 10px; padding: 8px; }
        form label { margin-top: 10px; font-weight: bold; }
    </style>
</head>
<body>

<h1>Event Registrations</h1>

<div style="text-align:center; margin: 20px 0;">
    <div style="display: inline-block;">
        <!-- Search Form with placeholder to the left of the search button -->
        <form method="GET" style="display: inline-block;">
            <div style="display: inline-block; vertical-align: middle;">
                <input type="text" name="search" placeholder="Search by name, email, etc..." value="<?= htmlspecialchars($search) ?>" style="padding: 6px; width: 300px; border-radius: 4px; border: 1px solid #ccc; vertical-align: middle;">
            </div>
            <div style="display: inline-block; vertical-align: middle; margin-left: 10px;">
                <button type="submit" class="btn export-btn">Search</button>
            </div>
        </form>
    </div>
    <a class="btn export-btn" href="export_to_excel.php" target="_blank">Export to Excel</a>
    <a class="btn reset-btn" href="?reset_db=true&confirm=yes" onclick="return confirm('Are you sure? This will delete all data and reset IDs to 1.')">Reset Database</a>
</div>

<?php if ($editMode): ?>
    <h2>Edit Registration</h2>
    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="fullname" value="<?= htmlspecialchars($registration['fullname']) ?>" required>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($registration['email']) ?>" required>
        <label>Phone</label>
        <input type="text" name="phone" value="<?= htmlspecialchars($registration['phone']) ?>" required>
        <label>College</label>
        <input type="text" name="college" value="<?= htmlspecialchars($registration['college']) ?>" required>
        <label>Department</label>
        <input type="text" name="department" value="<?= htmlspecialchars($registration['department']) ?>" required>
        <label>Year</label>
        <input type="text" name="year" value="<?= htmlspecialchars($registration['year']) ?>" required>
        <label>Event</label>
        <input type="text" name="event" value="<?= htmlspecialchars($registration['event']) ?>" required>
        <label>Comments</label>
        <textarea name="comments" required><?= htmlspecialchars($registration['comments']) ?></textarea>
        <button type="submit">Update</button>
    </form>
<?php else: ?>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>College</th><th>Department</th><th>Year</th><th>Event</th><th>Comments</th><th>Actions</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['fullname']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['college']) ?></td>
                    <td><?= htmlspecialchars($row['department']) ?></td>
                    <td><?= htmlspecialchars($row['year']) ?></td>
                    <td><?= htmlspecialchars($row['event']) ?></td>
                    <td><?= htmlspecialchars($row['comments']) ?></td>
                    <td>
                        <a class="btn edit-btn" href="?edit_id=<?= $row['id'] ?>">Edit</a>
                        <a class="btn delete-btn" href="?delete_id=<?= $row['id'] ?>" onclick="return confirm('Delete this entry?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="10" style="text-align:center;">No records found.</td></tr>
        <?php endif; ?>
    </table>
<?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>











