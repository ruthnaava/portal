<?php
include_once('main.php');
include_once('includes/topbar.php');
include_once('includes/sidebar.php');
$classid = isset($_GET['classid']) ? $_GET['classid'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Course Units by Class</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../source/CSS/style.css">
</head>
<body>
    <div class="main-content" id="mainContent">
        <div class="page-header">
            <h1 class="page-title">My Course Units by Class</h1>
        </div>
        <div class="dashboard-grid">
            <div class="stat-card">
                <table class="modern-table" border="1" width="100%">
                    <thead>
                        <tr>
                            <th>Course ID</th>
                            <th>Course Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($classid) {
                            $stmt = $mysqli->prepare("SELECT id, name FROM course WHERE teacherid = ? AND classid = ?");
                            $stmt->bind_param("ss", $check, $classid);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                echo '<tr>';
                                echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                                echo '<td>' . htmlspecialchars($row['name']) . '</td>';
                                echo '</tr>';
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
