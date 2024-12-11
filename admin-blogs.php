<?php
session_start();
require_once './server/connection.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit();
}

// Get blog count 
$blog_count_query = "SELECT COUNT(*) as count FROM blogs";
$blog_count_result = mysqli_query($conn, $blog_count_query);
$blog_count = mysqli_fetch_assoc($blog_count_result)['count'];

// Fetch all blogs
$query = "SELECT * FROM blogs ORDER BY created_at DESC";
$blogs = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management - Zocom Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="assets/css/admin-blogs.css">
</head>

<body>
    <div class="toast-container"></div>

    <?php include 'components/admin-nav.php'; ?>
    <?php include 'components/admin-sidebar.php'; ?>

    <main class="admin-main">
        <div class="content-header">
            <h1>Blog Management</h1>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBlogModal">
                <i class="fas fa-plus"></i> Add New Blog
            </button>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($blog = mysqli_fetch_assoc($blogs)): ?>
                        <tr>
                            <td>
                                <?php if ($blog['featured_image']): ?>
                                    <img src="<?php echo $blog['featured_image']; ?>"
                                        alt="<?php echo $blog['title']; ?>"
                                        class="blog-thumbnail">
                                <?php else: ?>
                                    <div class="no-image">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $blog['title']; ?></td>
                            <td><?php echo $blog['category']; ?></td>
                            <td><?php echo $blog['author']; ?></td>
                            <td>
                                <span class="badge <?php echo $blog['status'] === 'published' ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo $blog['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-info me-1" onclick="viewBlog(<?php echo $blog['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-primary me-1" onclick="editBlog(<?php echo $blog['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="deleteBlog(<?php echo $blog['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Add Blog Modal -->
    <div class="modal fade" id="addBlogModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Blog</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addBlogForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Featured Image</label>
                            <input type="file" name="featured_image" class="form-control" accept="image/*">
                            <div class="image-preview mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" required>
                                    <option value="Industry Insights">Industry Insights</option>
                                    <option value="Safety Guidelines">Safety Guidelines</option>
                                    <option value="Compliance">Compliance</option>
                                    <option value="Product Reviews">Product Reviews</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Author</label>
                                <input type="text" name="author" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Excerpt</label>
                            <textarea name="excerpt" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control rich-editor" rows="10" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveBlog()">Save Blog</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Blog Modal -->
    <div class="modal fade" id="editBlogModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Blog</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editBlogForm" enctype="multipart/form-data">
                        <input type="hidden" name="blog_id">
                        <div class="mb-3">
                            <label class="form-label">Current Featured Image</label>
                            <img id="currentBlogImage" src="" alt="" class="blog-thumbnail mb-2 d-block">
                            <input type="file" name="featured_image" class="form-control" accept="image/*">
                            <div class="image-preview mt-2"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-select" required>
                                    <option value="Industry Insights">Industry Insights</option>
                                    <option value="Safety Guidelines">Safety Guidelines</option>
                                    <option value="Compliance">Compliance</option>
                                    <option value="Product Reviews">Product Reviews</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Author</label>
                                <input type="text" name="author" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Excerpt</label>
                            <textarea name="excerpt" class="form-control" rows="2" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Content</label>
                            <textarea name="content" class="form-control rich-editor" rows="10" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="updateBlog()">Update Blog</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/admin.js"></script>
    <script>
        // Toast notification helper
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `custom-toast ${type}-toast`;
            toast.innerHTML = `
        <div class="toast-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;

            const container = document.querySelector('.toast-container');
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Image preview helper
        function setupImagePreview(input, previewElement) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewElement.tagName === 'IMG') {
                        previewElement.src = e.target.result;
                        previewElement.style.display = 'block';
                    } else {
                        previewElement.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 100%; height: auto;">`;
                    }
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Add new blog
        function saveBlog() {
            const form = document.getElementById('addBlogForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            formData.append('action', 'add');

            fetch('server/blog_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Blog post added successfully');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('addBlogModal'));
                        modal.hide();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.error || 'Failed to add blog post', 'error');
                    }
                })
                .catch(error => {
                    console.error('Save error:', error);
                    showToast('An error occurred', 'error');
                });
        }

        // Edit blog
        function editBlog(id) {
            fetch(`server/blog_handler.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateEditForm(data.blog);
                        new bootstrap.Modal(document.getElementById('editBlogModal')).show();
                    } else {
                        showToast(data.error || 'Failed to load blog post', 'error');
                    }
                })
                .catch(error => {
                    console.error('Edit error:', error);
                    showToast('An error occurred', 'error');
                });
        }

        // Populate edit form
        function populateEditForm(blog) {
            const form = document.getElementById('editBlogForm');
            form.blog_id.value = blog.id;
            form.title.value = blog.title;
            form.category.value = blog.category;
            form.author.value = blog.author;
            form.excerpt.value = blog.excerpt;
            form.content.value = blog.content;
            form.status.value = blog.status;

            const currentImage = document.getElementById('currentBlogImage');
            if (blog.featured_image) {
                currentImage.src = blog.featured_image;
                currentImage.style.display = 'block';
            } else {
                currentImage.style.display = 'none';
            }
        }

        // Update blog
        function updateBlog() {
            const form = document.getElementById('editBlogForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const formData = new FormData(form);
            formData.append('action', 'update');

            fetch('server/blog_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Blog post updated successfully');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editBlogModal'));
                        modal.hide();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.error || 'Failed to update blog post', 'error');
                    }
                })
                .catch(error => {
                    console.error('Update error:', error);
                    showToast('An error occurred', 'error');
                });
        }

        // Delete blog
        function deleteBlog(id) {
            if (!confirm('Are you sure you want to delete this blog post?')) {
                return;
            }

            fetch('server/blog_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'delete',
                        id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('Blog post deleted successfully');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showToast(data.error || 'Failed to delete blog post', 'error');
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    showToast('An error occurred', 'error');
                });
        }

        // View blog details
        function viewBlog(id) {
            fetch(`server/blog_handler.php?action=get&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const blog = data.blog;
                        const modalContent = `
                    <div class="row">
                        <div class="col-md-12">
                            ${blog.featured_image ? 
                                `<img src="${blog.featured_image}" alt="${blog.title}" class="img-fluid mb-3">` : 
                                ''}
                            <h3>${blog.title}</h3>
                            <p class="text-muted">
                                <small>
                                    <i class="fas fa-user"></i> ${blog.author} | 
                                    <i class="fas fa-calendar"></i> ${new Date(blog.created_at).toLocaleDateString()} | 
                                    <i class="fas fa-folder"></i> ${blog.category}
                                </small>
                            </p>
                            <p><strong>Excerpt:</strong></p>
                            <p>${blog.excerpt}</p>
                            <p><strong>Content:</strong></p>
                            <div>${blog.content}</div>
                        </div>
                    </div>
                `;

                        document.querySelector('#viewBlogModal .modal-body').innerHTML = modalContent;
                        new bootstrap.Modal(document.getElementById('viewBlogModal')).show();
                    } else {
                        showToast(data.error || 'Failed to load blog post', 'error');
                    }
                })
                .catch(error => {
                    console.error('View error:', error);
                    showToast('An error occurred', 'error');
                });
        }

        // Initialize event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Setup image preview for add form
            const addImageInput = document.querySelector('#addBlogForm input[type="file"]');
            if (addImageInput) {
                addImageInput.addEventListener('change', function() {
                    setupImagePreview(this, this.parentElement.querySelector('.image-preview'));
                });
            }

            // Setup image preview for edit form
            const editImageInput = document.querySelector('#editBlogForm input[type="file"]');
            if (editImageInput) {
                editImageInput.addEventListener('change', function() {
                    setupImagePreview(this, this.parentElement.querySelector('.image-preview'));
                });
            }

            // Reset forms when modals are closed
            ['addBlogModal', 'editBlogModal'].forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('hidden.bs.modal', function() {
                        const form = this.querySelector('form');
                        if (form) {
                            form.reset(); // Reset the form inputs
                            const previewElement = form.querySelector('.image-preview img');
                            if (previewElement) {
                                previewElement.src = ''; // Clear the image preview
                                previewElement.style.display = 'none';
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>