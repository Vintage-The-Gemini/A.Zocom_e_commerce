```php
<?php
// admin/blogs.php
session_start();
require_once '../server/connection.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

$page_title = 'Blog Management';

// Get blog statistics
$total_blogs = $conn->query("SELECT COUNT(*) as count FROM blogs")->fetch_assoc()['count'];
$published_blogs = $conn->query("SELECT COUNT(*) as count FROM blogs WHERE status = 'published'")->fetch_assoc()['count'];
$draft_blogs = $conn->query("SELECT COUNT(*) as count FROM blogs WHERE status = 'draft'")->fetch_assoc()['count'];

// Get blog categories
$categories_query = "SELECT * FROM blog_categories";
$categories = $conn->query($categories_query);

// Get all blogs with category info
$blogs_query = "SELECT blogs.*, blog_categories.name as category_name 
                FROM blogs 
                LEFT JOIN blog_categories ON blogs.category = blog_categories.id 
                ORDER BY created_at DESC";
$blogs = $conn->query($blogs_query);

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>

        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 main-content">
            <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3">
                <h1>Blog Management</h1>
                <button type="button" class="btn btn-primary" id="addBlogBtn">
                    <i class="fas fa-plus"></i> New Blog Post
                </button>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">Total Posts</h6>
                                    <h3 class="card-title mb-0"><?php echo $total_blogs; ?></h3>
                                </div>
                                <i class="fas fa-blog fa-2x text-primary opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">Published</h6>
                                    <h3 class="card-title mb-0"><?php echo $published_blogs; ?></h3>
                                </div>
                                <i class="fas fa-check-circle fa-2x text-success opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle text-muted">Drafts</h6>
                                    <h3 class="card-title mb-0"><?php echo $draft_blogs; ?></h3>
                                </div>
                                <i class="fas fa-file-alt fa-2x text-warning opacity-25"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blogs Table -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($blog = $blogs->fetch_assoc()): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($blog['featured_image']): ?>
                                                    <img src="../<?php echo htmlspecialchars($blog['featured_image']); ?>"
                                                        class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0"><?php echo htmlspecialchars($blog['title']); ?></h6>
                                                    <small class="text-muted">
                                                        <?php echo substr(htmlspecialchars($blog['excerpt']), 0, 50) . '...'; ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($blog['category_name']); ?></td>
                                        <td><?php echo htmlspecialchars($blog['author']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $blog['status'] === 'published' ? 'success' : 'warning'; ?>">
                                                <?php echo ucfirst($blog['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary btn-edit"
                                                    data-id="<?php echo $blog['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger btn-delete"
                                                    data-id="<?php echo $blog['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Blog Form Modal -->
<div class="modal fade" id="blogModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">New Blog Post</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="blogForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="blog_id" name="blog_id">

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <?php while ($category = $categories->fetch_assoc()): ?>
                                    <option value="<?php echo $category['id']; ?>">
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Excerpt</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="2" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="10" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Featured Image</label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                        <div id="current_image" class="mt-2" style="display: none;">
                            <img src="" alt="Current featured image" class="img-thumbnail" style="height: 100px;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="author" class="form-label">Author</label>
                        <input type="text" class="form-control" id="author" name="author" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Blog Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const blogModal = new bootstrap.Modal(document.getElementById('blogModal'));
        const blogForm = document.getElementById('blogForm');
        const modalTitle = document.getElementById('modalTitle');

        // Add New Blog
        document.getElementById('addBlogBtn').addEventListener('click', function() {
            modalTitle.textContent = 'New Blog Post';
            blogForm.reset();
            document.getElementById('current_image').style.display = 'none';
            document.getElementById('blog_id').value = '';
            blogModal.show();
        });

        // Edit Blog
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', async function() {
                const blogId = this.dataset.id;
                modalTitle.textContent = 'Edit Blog Post';

                try {
                    const response = await fetch(`get_blog.php?id=${blogId}`);
                    const data = await response.json();

                    if (data.success) {
                        const blog = data.blog;
                        document.getElementById('blog_id').value = blog.id;
                        document.getElementById('title').value = blog.title;
                        document.getElementById('excerpt').value = blog.excerpt;
                        document.getElementById('content').value = blog.content;
                        document.getElementById('category').value = blog.category;
                        document.getElementById('status').value = blog.status;
                        document.getElementById('author').value = blog.author;

                        if (blog.featured_image) {
                            const currentImage = document.querySelector('#current_image img');
                            currentImage.src = '../' + blog.featured_image;
                            document.getElementById('current_image').style.display = 'block';
                        }

                        blogModal.show();
                    } else {
                        showToast(data.message, 'error');
                    }
                } catch (error) {
                    showToast('Error loading blog data', 'error');
                }
            });
        });

        // Delete Blog
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (confirm('Are you sure you want to delete this blog post?')) {
                    const blogId = this.dataset.id;
                    try {
                        const response = await fetch('delete_blog.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                blog_id: blogId
                            })
                        });

                        const data = await response.json();

                        if (data.success) {
                            showToast('Blog post deleted successfully', 'success');
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            showToast(data.message, 'error');
                        }
                    } catch (error) {
                        showToast('Error deleting blog post', 'error');
                    }
                }
            });
        });

        // Save Blog
        blogForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('save_blog.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    blogModal.hide();
                    showToast(result.message, 'success');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    showToast(result.message || 'Error saving blog post', 'error');
                }
            } catch (error) {
                showToast('Error saving blog post', 'error');
            }
        });
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
</script>

<?php include 'includes/footer.php'; ?>