<?php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Create necessary tables
$create_tables = "
CREATE TABLE IF NOT EXISTS admin_roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    FOREIGN KEY (role_id) REFERENCES admin_roles(id)
);

CREATE TABLE IF NOT EXISTS user_activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    activity_type VARCHAR(50),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES admin_users(id)
);
";

$conn->multi_query($create_tables);
while ($conn->next_result()) {;
} // clear multi_query

// Insert default roles if none exist
$check_roles = $conn->query("SELECT COUNT(*) as count FROM admin_roles");
if ($check_roles->fetch_assoc()['count'] == 0) {
    $insert_roles = "INSERT INTO admin_roles (role_name, description) VALUES
        ('super_admin', 'Full system access'),
        ('admin', 'Administrative access with some restrictions'),
        ('editor', 'Can manage content only')";
    $conn->query($insert_roles);
}

// Get user statistics
$stats_query = "SELECT 
    COUNT(*) as total_users,
    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
    SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_users,
    COUNT(DISTINCT role_id) as total_roles
FROM admin_users";
$stats = $conn->query($stats_query)->fetch_assoc();

// Get all users with roles
$users_query = "SELECT 
    au.*, 
    ar.role_name,
    (SELECT COUNT(*) FROM user_activity_log WHERE user_id = au.id) as login_count
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
                                            <td><?php echo $user['last_login'] ? date('M d, Y', strtotime($user['last_login'])) : 'Never'; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-sm btn-primary btn-edit"
                                                        data-id="<?php echo $user['id']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <?php if ($user['id'] != $_SESSION['admin_id']): ?>
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
                                        <td colspan="6" class="text-center py-3">No users found</td>
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
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="modalTitle" aria-modal="true" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required
                            minlength="3" pattern="^[a-zA-Z0-9_-]*$" autocomplete="username">
                        <div class="invalid-feedback">
                            Please enter a valid username (at least 3 characters, letters, numbers, dash and underscore only)
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required
                            autocomplete="email">
                        <div class="invalid-feedback">
                            Please enter a valid email address
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password"
                            minlength="8" autocomplete="new-password">
                        <div class="invalid-feedback">
                            Password must be at least 8 characters long
                        </div>
                        <small class="text-muted">Leave blank to keep existing password when editing</small>
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <?php
                            $roles->data_seek(0);
                            while ($role = $roles->fetch_assoc()):
                            ?>
                                <option value="<?php echo $role['id']; ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $role['role_name'])); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a role
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a status
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
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

    .btn-group {
        gap: 0.25rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userModal = new bootstrap.Modal(document.getElementById('userModal'), {
            backdrop: 'static',
            keyboard: true
        });
        const userForm = document.getElementById('userForm');
        const modalTitle = document.getElementById('modalTitle');

        // Add New User
        document.getElementById('addUserBtn').addEventListener('click', function() {
            modalTitle.textContent = 'Add New User';
            userForm.reset();
            document.getElementById('user_id').value = '';
            document.getElementById('password').required = true;
            userModal.show();
        });

        // Edit User
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', async function() {
                const userId = this.dataset.id;
                modalTitle.textContent = 'Edit User';
                document.getElementById('password').required = false;

                try {
                    const response = await fetch(`get_user.php?id=${userId}`);
                    const data = await response.json();

                    if (data.success) {
                        const user = data.user;
                        document.getElementById('user_id').value = user.id;
                        document.getElementById('username').value = user.username;
                        document.getElementById('email').value = user.email;
                        document.getElementById('role_id').value = user.role_id;
                        document.getElementById('status').value = user.status;
                        document.getElementById('password').value = '';

                        userModal.show();
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Error loading user data', 'error');
                }
            });
        });

        // Delete User
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (confirm('Are you sure you want to delete this user?')) {
                    const userId = this.dataset.id;
                    try {
                        const response = await fetch('delete_user.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                user_id: userId
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showToast('User deleted successfully', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToast(data.message, 'error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        showToast('Error deleting user', 'error');
                    }
                }
            });
        });

        // Save User
        userForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Form validation
            if (!this.checkValidity()) {
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            const formData = new FormData(this);

            try {
                const response = await fetch('save_user.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    userModal.hide();
                    showToast(result.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(result.message || 'Error saving user', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Error saving user. Please try again.', 'error');
            }
        });

        // Modal focus management
        const modalElement = document.getElementById('userModal');
        modalElement.addEventListener('shown.bs.modal', function() {
            document.getElementById('username').focus();
        });

        // Toast notification function
        function showToast(message, type = 'success') {
            const toastContainer = document.querySelector('.toast-container');
            const toast = document.createElement('div');
            toast.className = `toast align-items-center border-0 bg-${type} text-white fade show`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            const toastBody = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

            toast.innerHTML = toastBody;
            toastContainer.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast, {
                delay: 3000
            });

            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
    });
</script>

<?php include 'includes/footer.php'; ?>