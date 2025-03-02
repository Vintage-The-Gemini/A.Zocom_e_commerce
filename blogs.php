<?php
session_start();
require_once 'server/connection.php';


// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get all categories
$categories_query = "SELECT DISTINCT category FROM blogs WHERE status = 'published'";
$categories_result = mysqli_query($conn, $categories_query);

if (!$categories_result) {
    die("Categories query failed: " . mysqli_error($conn));
}

$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    if ($row['category']) {
        $categories[] = $row['category'];
    }
}

// Handle category filter and search
$category_filter = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$search_term = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Build the query
$query = "SELECT * FROM blogs WHERE status = 'published'";
if ($category_filter) {
    $query .= " AND category = '$category_filter'";
}
if ($search_term) {
    $query .= " AND (title LIKE '%$search_term%' OR excerpt LIKE '%$search_term%')";
}
$query .= " ORDER BY created_at DESC";

// Execute query
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Blog query failed: " . mysqli_error($conn));
}

?>
<!DOCTYPE html>
<html lang="en">

<head>


    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-K9SX33NJ');
    </script>
    <!-- End Google Tag Manager -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Favicon -->
    <link rel="icon" href="images/zocom no bg logo.png" type="image/png">
    <link rel="apple-touch-icon" href="images/zocom no bg logo.png">

    <!-- Primary Meta Tags -->
    <meta name="title" content="Blog - Zocom Limited">
    <meta name="description" content="Latest insights on safety equipment and workplace safety from Zocom Limited. Stay updated with safety guidelines and industry news.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://www.zocomlimited.co.ke/blogs">
    <meta property="og:title" content="Safety Blog - Zocom Limited">
    <meta property="og:description" content="Read the latest insights on workplace safety and protective equipment. Expert advice and industry updates from Zocom Limited.">
    <meta property="og:image" content="images/zocom no bg logo.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://www.zocomlimited.co.ke/blogs">
    <meta property="twitter:title" content="Safety Blog - Zocom Limited">
    <meta property="twitter:description" content="Read the latest insights on workplace safety and protective equipment. Expert advice and industry updates from Zocom Limited.">
    <meta property="twitter:image" content="images/zocom no bg logo.png">

    <title>Blog - Zocom Limited</title>

    <!-- Your existing CSS links -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/blogs.css">
</head>

<body>

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K9SX33NJ"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    <?php include 'components/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="blogs-hero">
        <div class="container">
            <h1>Our Latest Insights</h1>
            <p>Stay updated with the latest news, safety guidelines, and industry insights</p>
        </div>
    </section>

    <div class="blogs-container">
        <!-- Search Bar -->
        <div class="blog-search">
            <input type="text" id="searchInput" placeholder="Search blogs..."
                value="<?php echo htmlspecialchars($search_term); ?>">
            <button onclick="searchBlogs()">
                <i class="fas fa-search"></i>
            </button>
        </div>

        <!-- Category Filters -->
        <div class="blog-filters">
            <button class="filter-btn <?php echo !$category_filter ? 'active' : ''; ?>"
                onclick="filterCategory('')">
                All
            </button>
            <?php foreach ($categories as $category): ?>
                <button class="filter-btn <?php echo $category_filter === $category ? 'active' : ''; ?>"
                    onclick="filterCategory('<?php echo htmlspecialchars($category); ?>')">
                    <?php echo htmlspecialchars($category); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Blogs Grid -->
        <div class="blogs-grid">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($blog = mysqli_fetch_assoc($result)): ?>
                    <article class="blog-card">
                        <div class="blog-image">
                            <?php if (!empty($blog['featured_image'])): ?>
                                <img src="<?php echo htmlspecialchars($blog['featured_image']); ?>"
                                    alt="<?php echo htmlspecialchars($blog['title']); ?>">
                            <?php else: ?>
                                <img src="assets/images/default-blog.jpg"
                                    alt="<?php echo htmlspecialchars($blog['title']); ?>">
                            <?php endif; ?>
                            <?php if (!empty($blog['category'])): ?>
                                <span class="blog-category">
                                    <?php echo htmlspecialchars($blog['category']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="blog-content">
                            <h2 class="blog-title">
                                <?php echo htmlspecialchars($blog['title']); ?>
                            </h2>
                            <p class="blog-excerpt">
                                <?php echo htmlspecialchars($blog['excerpt']); ?>
                            </p>
                            <div class="blog-meta">
                                <?php if (!empty($blog['author'])): ?>
                                    <span>
                                        <i class="far fa-user"></i>
                                        <?php echo htmlspecialchars($blog['author']); ?>
                                    </span>
                                <?php endif; ?>
                                <span>
                                    <i class="far fa-calendar"></i>
                                    <?php echo date('M d, Y', strtotime($blog['created_at'])); ?>
                                </span>
                            </div>
                            <a href="blog-detail.php?id=<?php echo $blog['id']; ?>" class="read-more mt-3">
                                Read More <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No blogs found</h3>
                    <p>Try adjusting your search or filter to find what you're looking for.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Blog Modal -->
    <!-- Blog Modal with Enhanced Design -->
    <div class="modal fade" id="blogModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="blog-modal-header">
                        <span class="blog-category"></span>
                        <h5 class="modal-title"></h5>
                        <div class="blog-meta">
                            <span class="author"><i class="far fa-user"></i> </span>
                            <span class="date"><i class="far fa-calendar"></i> </span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="blog-featured-image mb-4"></div>
                    <div class="blog-content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" class="btn btn-primary share-blog">Share</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function filterCategory(category) {
            const searchParams = new URLSearchParams(window.location.search);
            if (category) {
                searchParams.set('category', category);
            } else {
                searchParams.delete('category');
            }
            window.location.search = searchParams.toString();
        }

        function searchBlogs() {
            const searchTerm = document.getElementById('searchInput').value;
            const searchParams = new URLSearchParams(window.location.search);
            if (searchTerm) {
                searchParams.set('search', searchTerm);
            } else {
                searchParams.delete('search');
            }
            window.location.search = searchParams.toString();
        }

        function openBlogModal(blogId) {
            fetch(`fetch_blog.php?id=${blogId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const blog = data.blog;
                        document.querySelector('#blogModal .modal-title').textContent = blog.title;
                        document.querySelector('#blogModal .modal-body').innerHTML = `
                            <div class="blog-modal-content">
                                <div class="blog-modal-meta">
                                    <span><i class="far fa-user"></i> ${blog.author}</span>
                                    <span><i class="far fa-calendar"></i> 
                                        ${new Date(blog.created_at).toLocaleDateString()}
                                    </span>
                                </div>
                                <div class="blog-modal-text">
                                    ${blog.content}
                                </div>
                            </div>
                        `;
                        new bootstrap.Modal(document.getElementById('blogModal')).show();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Handle enter key in search
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchBlogs();
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>