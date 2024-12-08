<?php
// blog-filter.php
require_once 'server/connection.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'all';

$query = "SELECT id, title, excerpt, image_url, author, created_at, category 
          FROM blogs 
          WHERE status = 'published'";

if ($category !== 'all') {
    $category = mysqli_real_escape_string($conn, $category);
    $query .= " AND category = '$category'";
}

$query .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    while ($blog = mysqli_fetch_assoc($result)) {
        $date = date('M d, Y', strtotime($blog['created_at']));
?>
        <article class="blog-card">
            <div class="blog-card__image">
                <img src="<?php echo htmlspecialchars($blog['image_url']); ?>"
                    alt="<?php echo htmlspecialchars($blog['title']); ?>"
                    loading="lazy">
                <span class="blog-card__category"><?php echo htmlspecialchars($blog['category']); ?></span>
            </div>
            <div class="blog-card__content">
                <h2 class="blog-card__title"><?php echo htmlspecialchars($blog['title']); ?></h2>
                <p class="blog-card__excerpt"><?php echo htmlspecialchars($blog['excerpt']); ?></p>
                <div class="blog-card__meta">
                    <div class="blog-card__date">
                        <i class="far fa-calendar-alt"></i>
                        <?php echo $date; ?> |
                        <i class="far fa-user"></i>
                        <?php echo htmlspecialchars($blog['author']); ?>
                    </div>
                    <a href="blog-detail.php?id=<?php echo $blog['id']; ?>" class="blog-card__link">
                        Read More <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </article>
<?php
    }
} else {
    echo '<div class="no-blogs">No blog posts found in this category.</div>';
}
?>