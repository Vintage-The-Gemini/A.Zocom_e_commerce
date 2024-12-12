<?php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Create necessary tables if they don't exist
$create_tables = "
CREATE TABLE IF NOT EXISTS admin_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS user_logins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent TEXT,
    FOREIGN KEY (user_id) REFERENCES admin_users(id)
);
";

$conn->multi_query($create_tables);
while ($conn->next_result()) {;
} // clear multi_query

// Insert default roles if none exist
$check_roles = $conn->query("SELECT COUNT(*) as count FROM admin_roles");
$roles_count = $check_roles->fetch_assoc()['count'];

if ($roles_count == 0) {
    $insert_roles = "INSERT INTO admin_roles (role_name, description) VALUES
        ('super_admin', 'Full system access'),
        ('admin', 'Administrative access with some restrictions'),
        ('editor', 'Can manage content only')";
    $conn->query($insert_roles);
}

// Modify admin_users table if needed
$alter_users = "
ALTER TABLE admin_users 
ADD COLUMN IF NOT EXISTS role_id INT,
ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive') DEFAULT 'active',
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL";

$conn->query($alter_users);

// Get user statistics
$stats_query = "SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users,
    COUNT(DISTINCT role_id) as total_roles
FROM admin_users";

$stats_result = $conn->query($stats_query);
$stats = $stats_result ? $stats_result->fetch_assoc() : [
    'total_users' => 0,
    'active_users' => 0,
    'inactive_users' => 0,
    'total_roles' => 0
];

// Get all users with roles and last login
$users_query = "SELECT 
    au.*, 
    ar.role_name,
    (SELECT COUNT(*) FROM user_logins WHERE user_id = au.id) as login_count,
    (SELECT login_time FROM user_logins WHERE user_id = au.id ORDER BY login_time DESC LIMIT 1) as last_login
FROM admin_users au 
LEFT JOIN admin_roles ar ON au.role_id = ar.id 
ORDER BY au.created_at DESC";

$users = $conn->query($users_query);

// Get roles for dropdown
$roles_query = "SELECT * FROM admin_roles ORDER BY role_name";
$roles = $conn->query($roles_query);

$page_title = "User Management";
include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 main-content">
            <!-- Header with Stats -->
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3">
                <h1>User Management</h1>
                <button type="button" class="btn btn-primary" id="addUserBtn">
                    <i class="fas fa-user-plus"></i> Add New User
                </button>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">Total Users</h6>
                                    <h3 class="card-title mb-0"><?php echo $stats['total_users']; ?></h3>
                                </div>
                                <i class="fas fa-users fa-2x text-primary opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">Active Users</h6>
                                    <h3 class="card-title mb-0"><?php echo $stats['active_users']; ?></h3>
                                </div>
                                <i class="fas fa-user-check fa-2x text-success opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">Inactive Users</h6>
                                    <h3 class="card-title mb-0"><?php echo $stats['inactive_users']; ?></h3>
                                </div>
                                <i class="fas fa-user-times fa-2x text-danger opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">User Roles</h6>
                                    <h3 class="card-title mb-0"><?php echo $stats['total_roles']; ?></h3>
                                </div>
                                <i class="fas fa-user-tag fa-2x text-info opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Login Count</th>
                                    <th>Created Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($users && $users->num_rows > 0): ?>
                                    <?php while ($user = $users->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2 bg-primary text-white">
                                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                                    </div>
                                                    <div>
                                                        <div><?php echo htmlspecialchars($user['username']); ?></div>
                                                        <small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo ucwords(str_replace('_', ' ', $user['role_name'] ?? 'No Role')); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                    <?php echo ucfirst($user['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($user['last_login']): ?>
                                                    <div data-bs-toggle="tooltip" title="<?php echo date('Y-m-d H:i:s', strtotime($user['last_login'])); ?>">
                                                        <?php echo date('M d, Y', strtotime($user['last_login'])); ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-muted">Never</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    <?php echo $user['login_count']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary btn-edit"
                                                        data-id="<?php echo $user['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                                        <button type="button" class="btn btn-sm btn-warning btn-reset-password"
                                                            data-id="<?php echo $user['id']; ?>"
                                                            data-bs-toggle="tooltip"
                                                            title="Reset Password">
                                                            <i class="fas fa-key"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                            data-id="<?php echo $user['id']; ?>">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-3">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="userForm">
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <?php
                            if ($roles) {
                                $roles->data_seek(0);
                                while ($role = $roles->fetch_assoc()):
                            ?>
                                    <option value="<?php echo $role['id']; ?>">
                                        <?php echo ucwords(str_replace('_', ' ', $role['role_name'])); ?>
                                    </option>
                            <?php
                                endwhile;
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="text-muted">Leave blank to keep existing password when editing</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3"></div>

<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
        margin-bottom: 1rem;
    }

    .btn-group {
        gap: 0.25rem;
    }

    .tooltip {
        font-size: 0.75rem;
    }
</style>

<script src="assets/js/users.js"></script>
<?php include 'includes/footer.php'; ?>