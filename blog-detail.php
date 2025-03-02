<?php
session_start();
require_once 'server/connection.php';

if (!isset($_GET['id'])) {
    header('Location: blogs.php');
    exit();
}

$blog_id = mysqli_real_escape_string($conn, $_GET['id']);

// Get current blog
$query = "SELECT * FROM blogs WHERE id = '$blog_id' AND status = 'published'";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    header('Location: blogs.php');
    exit();
}

$blog = mysqli_fetch_assoc($result);

// Get related blogs
$related_query = "SELECT id, title, featured_image, created_at 
                 FROM blogs 
                 WHERE category = '" . mysqli_real_escape_string($conn, $blog['category']) . "'
                 AND id != '$blog_id' 
                 AND status = 'published' 
                 ORDER BY created_at DESC 
                 LIMIT 3";
$related_result = mysqli_query($conn, $related_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>

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


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Zocom Limited Blog</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/blogs.css">

    <style>
        /* Reading Progress Bar */
        .progress-container {
            position: fixed;
            top: 0;
            z-index: 1000;
            width: 100%;
            height: 4px;
            background: transparent;
        }

        .progress-bar {
            height: 4px;
            background: linear-gradient(to right, #2a5298, #ff0081);
            width: 0%;
            transition: width 0.2s ease;
        }

        /* Hero Section */
        .blog-hero {
            position: relative;
            height: 500px;
            background-color: #0F172A;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .blog-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.95));
        }

        .blog-hero__image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.3;
        }

        .blog-hero__content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 2rem;
        }

        .blog-hero__category {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .blog-hero__title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .blog-hero__meta {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 2rem;
            font-size: 0.95rem;
            opacity: 0.9;
        }

        .blog-hero__meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Main Content */
        .blog-detail {
            padding: 4rem 0;
            background: #f8fafc;
        }

        .blog-detail__container {
            display: grid;
            grid-template-columns: 1fr minmax(200px, 300px);
            gap: 3rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .blog-detail__main {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .blog-detail__content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #334155;
        }

        .blog-detail__content p {
            margin-bottom: 1.5rem;
        }

        .blog-detail__content h2 {
            color: #1e3c72;
            font-size: 1.8rem;
            margin: 2rem 0 1rem;
        }

        .blog-detail__content img {
            max-width: 100%;
            border-radius: 12px;
            margin: 2rem 0;
        }

        /* Sidebar */
        .blog-detail__sidebar {
            position: sticky;
            top: 2rem;
            height: fit-content;
        }

        .sidebar-widget {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-widget__title {
            font-size: 1.3rem;
            color: #1e3c72;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .share-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .share-button {
            flex: 1;
            padding: 0.8rem;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: transform 0.3s ease;
        }

        .share-button:hover {
            transform: translateY(-2px);
            color: white;
        }

        .related-posts {
            display: grid;
            gap: 1.5rem;
        }

        .related-post {
            display: flex;
            gap: 1rem;
            text-decoration: none;
            color: inherit;
            transition: transform 0.3s ease;
        }

        .related-post:hover {
            transform: translateX(5px);
            color: inherit;
        }

        .related-post__image {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            object-fit: cover;
        }

        .related-post__content {
            flex: 1;
        }

        .related-post__title {
            font-size: 1rem;
            font-weight: 500;
            color: #1e3c72;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .related-post__date {
            font-size: 0.85rem;
            color: #64748b;
        }

        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 45px;
            height: 45px;
            background: #2a5298;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
            z-index: 1000;
        }

        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background: #1e3c72;
            color: white;
            transform: translateY(-3px);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .blog-detail__container {
                grid-template-columns: 1fr;
            }

            .blog-detail__sidebar {
                position: static;
                margin-top: 2rem;
            }
        }

        @media (max-width: 768px) {
            .blog-hero {
                height: 400px;
            }

            .blog-detail__main {
                padding: 2rem;
            }

            .blog-hero__meta {
                flex-direction: column;
                gap: 1rem;
            }
        }

        @media (max-width: 576px) {
            .blog-detail {
                padding: 2rem 0;
            }

            .blog-detail__container {
                padding: 0 1rem;
            }

            .blog-detail__main {
                padding: 1.5rem;
            }

            .share-buttons {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>


    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K9SX33NJ"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <?php include 'components/navbar.php'; ?>

    <!-- Reading Progress Bar -->
    <div class="progress-container">
        <div class="progress-bar" id="readingProgress"></div>
    </div>

    <!-- Hero Section -->
    <section class="blog-hero">
        <?php if ($blog['featured_image']): ?>
            <img src="<?php echo htmlspecialchars($blog['featured_image']); ?>"
                alt="<?php echo htmlspecialchars($blog['title']); ?>"
                class="blog-hero__image">
        <?php endif; ?>

        <div class="blog-hero__content">
            <?php if ($blog['category']): ?>
                <span class="blog-hero__category">
                    <?php echo htmlspecialchars($blog['category']); ?>
                </span>
            <?php endif; ?>

            <h1 class="blog-hero__title">
                <?php echo htmlspecialchars($blog['title']); ?>
            </h1>

            <div class="blog-hero__meta">
                <div class="blog-hero__meta-item">
                    <i class="far fa-user"></i>
                    <span><?php echo htmlspecialchars($blog['author']); ?></span>
                </div>
                <div class="blog-hero__meta-item">
                    <i class="far fa-calendar"></i>
                    <span><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="blog-detail">
        <div class="blog-detail__container">
            <article class="blog-detail__main">
                <div class="blog-detail__content">
                    <?php echo $blog['content']; ?>
                </div>
            </article>

            <aside class="blog-detail__sidebar">
                <!-- Share Widget -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">Share this article</h3>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
                            class="share-button" style="background: #1877f2;" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($blog['title']); ?>"
                            class="share-button" style="background: #1da1f2;" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>"
                            class="share-button" style="background: #0a66c2;" target="_blank">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($blog['title'] . ' ' . $_SERVER['REQUEST_URI']); ?>"
                            class="share-button" style="background: #25d366;" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>

                <!-- Related Posts Widget -->
                <?php if (mysqli_num_rows($related_result) > 0): ?>
                    <div class="sidebar-widget">
                        <h3 class="sidebar-widget__title">Related Articles</h3>
                        <div class="related-posts">
                            <?php while ($related = mysqli_fetch_assoc($related_result)): ?>
                                <a href="blog-detail.php?id=<?php echo $related['id']; ?>" class="related-post">
                                    <img src="<?php echo htmlspecialchars($related['featured_image']); ?>"
                                        alt="<?php echo htmlspecialchars($related['title']); ?>"
                                        class="related-post__image">
                                    <div class="related-post__content">
                                        <h4 class="related-post__title">
                                            <?php echo htmlspecialchars($related['title']); ?>
                                        </h4>
                                        <span class="related-post__date">
                                            <?php echo date('M d, Y', strtotime($related['created_at'])); ?>
                                        </span>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </aside>
        </div>
    </main>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <?php include 'components/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const progressBar = document.getElementById('readingProgress');
            const backToTop = document.getElementById('backToTop');

            // Calculate reading progress
            function updateReadingProgress() {
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight - windowHeight;
                const scrolled = window.scrollY;
                const progress = (scrolled / documentHeight) * 100;

                progressBar.style.width = progress + '%';

                // Show/hide back to top button
                if (scrolled > windowHeight * 0.5) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            }

            // Handle back to top click
            backToTop.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Update progress on scroll
            window.addEventListener('scroll', updateReadingProgress);

            // Initialize progress
            updateReadingProgress();

            // Handle image loading
            document.querySelectorAll('img').forEach(img => {
                img.addEventListener('load', updateReadingProgress);
            });

            // Add smooth scrolling to all anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth'
                        });
                    }
                });
            });

            // Initialize share buttons
            const shareButtons = document.querySelectorAll('.share-buttons a');
            shareButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.open(this.href, '_blank', 'width=600,height=400');
                });
            });

            // Add zoom effect to blog images
            const blogImages = document.querySelectorAll('.blog-detail__content img');
            blogImages.forEach(img => {
                img.style.cursor = 'pointer';
                img.addEventListener('click', function() {
                    this.classList.toggle('zoomed');
                });
            });
        });
    </script>

    <!-- Add image zoom styles -->
    <style>
        .blog-detail__content img {
            transition: transform 0.3s ease;
        }

        .blog-detail__content img.zoomed {
            transform: scale(1.5);
            z-index: 1000;
            position: relative;
        }

        /* Add smooth scroll behavior */
        html {
            scroll-behavior: smooth;
        }

        /* Improve article typography */
        .blog-detail__content {
            font-family: var(--font-primary);
        }

        .blog-detail__content h2,
        .blog-detail__content h3,
        .blog-detail__content h4 {
            font-family: var(--font-primary);
            color: #1e3c72;
            margin-top: 2em;
            margin-bottom: 1em;
        }

        .blog-detail__content p {
            margin-bottom: 1.5em;
        }

        .blog-detail__content ul,
        .blog-detail__content ol {
            margin-bottom: 1.5em;
            padding-left: 1.5em;
        }

        .blog-detail__content li {
            margin-bottom: 0.5em;
        }

        .blog-detail__content blockquote {
            margin: 2em 0;
            padding: 1em 1.5em;
            border-left: 4px solid #2a5298;
            background: #f8fafc;
            font-style: italic;
            color: #475569;
        }

        /* Print styles */
        @media print {

            .progress-container,
            .back-to-top,
            .share-buttons,
            .blog-detail__sidebar {
                display: none !important;
            }

            .blog-detail__container {
                grid-template-columns: 1fr !important;
            }

            .blog-detail__main {
                box-shadow: none !important;
                padding: 0 !important;
            }
        }
    </style>
</body>

</html>