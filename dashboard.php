<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get logged in user info
$stmt = $pdo->prepare("SELECT * FROM user WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// --- Search and Pagination ---
$search = $_GET['search'] ?? '';
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit  = 5; // users per page
$offset = ($page - 1) * $limit;

// Count total users (for pagination)
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM user WHERE username LIKE ? OR email LIKE ?");
$countStmt->execute(["%$search%", "%$search%"]);
$totalUsers = $countStmt->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

// Fetch paginated users
$stmtUsers = $pdo->prepare("SELECT * FROM user 
                            WHERE username LIKE ? OR email LIKE ? 
                            ORDER BY created_at DESC 
                            LIMIT ? OFFSET ?");
$stmtUsers->bindValue(1, "%$search%", PDO::PARAM_STR);
$stmtUsers->bindValue(2, "%$search%", PDO::PARAM_STR);
$stmtUsers->bindValue(3, $limit, PDO::PARAM_INT);
$stmtUsers->bindValue(4, $offset, PDO::PARAM_INT);
$stmtUsers->execute();
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebarMenu">
  <!-- Close button -->
  <button id="closeSidebarBtn"><i class="bi bi-x-lg"></i></button>

  <div class="text-center p-3 border-bottom">
    <img src="uploads/<?= htmlspecialchars($user['profile']) ?>" class="profile-img mb-2" width="80" height="80" alt="Profile">
    <h5><?= htmlspecialchars($user['username']) ?></h5>
    <p class="small"><?= htmlspecialchars($user['email']) ?></p>
  </div>
  <nav class="nav flex-column p-2">
    <a href="dashboard.php" class="nav-link"><i class="bi bi-house"></i> Home</a>
    <a href="logout.php" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </nav>
</div>

<!-- Overlay -->
<div class="overlay" id="overlay"></div>

<!-- Content -->
<div class="content-area" id="mainContent">
  <!-- Mobile toggle button -->
  <button class="btn btn-dark d-md-none mb-3" id="sidebarToggle">
    <i class="bi bi-list"></i> Menu
  </button>

  <h2>Welcome, <?= htmlspecialchars($user['username']) ?>!</h2>
  <p>This is your dashboard.</p>

  <!-- Search -->
  <form method="get" class="d-flex mb-3">
    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control me-2" placeholder="Search user...">
    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
  </form>

  <!-- Users Table -->
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Profile</th>
        <th>Username</th>
        <th>Email</th>
        <th>Created At</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($users) > 0): ?>
        <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['user_id'] ?></td>
          <td><img src="uploads/<?= htmlspecialchars($u['profile']) ?>" class="profile-img" width="40" height="40"></td>
          <td><?= htmlspecialchars($u['username']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['created_at'] ?></td>
        </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" class="text-center text-danger">No users found.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>


  <!-- Pagination -->
  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>

  <!-- Footer -->
  <footer>
    <p>&copy; <?= date("Y") ?> My Dashboard. All Rights Reserved.</p>
  </footer>
</div>

<!-- Back to Top Button -->
<button class="btn btn-primary back-to-top"><i class="bi bi-arrow-up"></i></button>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Elements
const sidebar = document.getElementById('sidebarMenu');
const toggleBtn = document.getElementById('sidebarToggle');
const overlay = document.getElementById('overlay');
const closeBtn = document.getElementById('closeSidebarBtn');

// Toggle sidebar on mobile
toggleBtn.addEventListener('click', () => {
  sidebar.classList.toggle('show');
  overlay.classList.toggle('show');
});

// Close sidebar on overlay click
overlay.addEventListener('click', () => {
  sidebar.classList.remove('show');
  overlay.classList.remove('show');
});

// Close sidebar on close button click
closeBtn.addEventListener('click', () => {
  sidebar.classList.remove('show');
  overlay.classList.remove('show');
});

// Back to top button
let backToTop = document.querySelector('.back-to-top');
window.addEventListener('scroll', () => {
  backToTop.style.display = window.scrollY > 200 ? 'block' : 'none';
});
backToTop.addEventListener('click', () => {
  window.scrollTo({ top: 0, behavior: 'smooth' });
});
</script>
</body>
</html>
