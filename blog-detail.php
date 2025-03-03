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
$related_query = "SELECT id, title, excerpt, featured_image, created_at 
                 FROM blogs 
                 WHERE category = '" . mysqli_real_escape_string($conn, $blog['category']) . "'
                 AND id != '$blog_id' 
                 AND status = 'published' 
                 ORDER BY created_at DESC 
                 LIMIT 3";
$related_result = mysqli_query($conn, $related_query);

// Get estimated reading time
function getReadingTime($content)
{
    $word_count = str_word_count(strip_tags($content));
    $minutes = floor($word_count / 200); // Average reading speed: 200 words per minute
    if ($minutes < 1) {
        return "Less than a minute";
    } elseif ($minutes == 1) {
        return "1 minute";
    } else {
        return $minutes . " minutes";
    }
}

$reading_time = getReadingTime($blog['content']);

// Format the content with proper HTML structure if it's not already formatted
// This function cleans up the content and ensures proper paragraph and list formatting
function formatBlogContent($content)
{
    // If content already has proper HTML structure, return as is
    if (strpos($content, '<p>') !== false || strpos($content, '<h2>') !== false) {
        return $content;
    }

    // Split content by double newlines to identify paragraphs
    $paragraphs = preg_split('/\n\s*\n/', $content, -1, PREG_SPLIT_NO_EMPTY);

    $formatted = '';
    foreach ($paragraphs as $paragraph) {
        $paragraph = trim($paragraph);

        // Check if it's a heading (starts with # or ##)
        if (preg_match('/^##\s+(.+)$/m', $paragraph, $matches)) {
            $formatted .= '<h3>' . $matches[1] . '</h3>';
        } elseif (preg_match('/^#\s+(.+)$/m', $paragraph, $matches)) {
            $formatted .= '<h2>' . $matches[1] . '</h2>';
        }
        // Check if it's a bullet list (lines starting with * or -)
        elseif (preg_match('/^[\*\-]\s+/m', $paragraph)) {
            $lines = explode("\n", $paragraph);
            $formatted .= '<ul>';
            foreach ($lines as $line) {
                if (preg_match('/^[\*\-]\s+(.+)$/', $line, $matches)) {
                    $formatted .= '<li>' . $matches[1] . '</li>';
                }
            }
            $formatted .= '</ul>';
        }
        // Check if it's a numbered list (lines starting with 1., 2., etc)
        elseif (preg_match('/^\d+\.\s+/m', $paragraph)) {
            $lines = explode("\n", $paragraph);
            $formatted .= '<ol>';
            foreach ($lines as $line) {
                if (preg_match('/^\d+\.\s+(.+)$/', $line, $matches)) {
                    $formatted .= '<li>' . $matches[1] . '</li>';
                }
            }
            $formatted .= '</ol>';
        }
        // Check if it's a blockquote (starts with >)
        elseif (preg_match('/^>\s+(.+)$/m', $paragraph, $matches)) {
            $formatted .= '<blockquote>' . $matches[1] . '</blockquote>';
        }
        // Otherwise it's a regular paragraph
        else {
            $formatted .= '<p>' . nl2br($paragraph) . '</p>';
        }
    }

    return $formatted;
}

// Format blog content for better readability
$formatted_content = formatBlogContent($blog['content']);
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
    <!-- End Google Tag Manager -->

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Favicon -->
    <link rel="icon" href="images/zocom no bg logo.png" type="image/png">
    <link rel="apple-touch-icon" href="images/zocom no bg logo.png">

    <!-- Primary Meta Tags -->
    <meta name="title" content="<?php echo htmlspecialchars($blog['title']); ?> - Zocom Limited Blog">
    <meta name="description" content="<?php echo htmlspecialchars($blog['excerpt']); ?>">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($blog['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($blog['excerpt']); ?>">
    <?php if ($blog['featured_image']): ?>
        <meta property="og:image" content="<?php echo "https://$_SERVER[HTTP_HOST]/" . $blog['featured_image']; ?>">
    <?php endif; ?>

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
    <meta property="twitter:title" content="<?php echo htmlspecialchars($blog['title']); ?>">
    <meta property="twitter:description" content="<?php echo htmlspecialchars($blog['excerpt']); ?>">
    <?php if ($blog['featured_image']): ?>
        <meta property="twitter:image" content="<?php echo "https://$_SERVER[HTTP_HOST]/" . $blog['featured_image']; ?>">
    <?php endif; ?>

    <title><?php echo htmlspecialchars($blog['title']); ?> - Zocom Limited Blog</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&family=Merriweather:wght@300;400;700&display=swap" rel="stylesheet">

    <!-- Stylesheets -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/blogs.css">
    <link rel="stylesheet" href="assets/css/blog-detail.css">
</head>

<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K9SX33NJ"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

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
                <div class="blog-hero__meta-item">
                    <i class="far fa-clock"></i>
                    <span><?php echo $reading_time; ?> read</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Info Bar -->
    <div class="blog-info-bar">
        <div class="blog-info-container">
            <div class="blog-info__meta">
                <div class="blog-info__item">
                    <i class="far fa-calendar-alt"></i>
                    <span>Published: <?php echo date('F j, Y', strtotime($blog['created_at'])); ?></span>
                </div>
                <div class="blog-info__item">
                    <i class="far fa-folder"></i>
                    <span>Category: <?php echo htmlspecialchars($blog['category']); ?></span>
                </div>
                <div class="blog-info__item">
                    <i class="far fa-clock"></i>
                    <span><?php echo $reading_time; ?> read</span>
                </div>
            </div>
            <div class="blog-info__share">
                <span class="blog-info__share-text">Share:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>"
                    class="social-icon social-icon--facebook" target="_blank" aria-label="Share on Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>&text=<?php echo urlencode($blog['title']); ?>"
                    class="social-icon social-icon--twitter" target="_blank" aria-label="Share on Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>"
                    class="social-icon social-icon--linkedin" target="_blank" aria-label="Share on LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="https://wa.me/?text=<?php echo urlencode($blog['title'] . ' ' . "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"); ?>"
                    class="social-icon social-icon--whatsapp" target="_blank" aria-label="Share on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="blog-detail">
        <div class="blog-detail__container">
            <article class="blog-detail__main">
                <!-- Table of Contents - Generated via JavaScript -->
                <div class="toc-container" id="tableOfContents">
                    <h3 class="toc-title"><i class="fas fa-list"></i> Table of Contents</h3>
                    <ul class="toc-links" id="tocLinks"></ul>
                </div>

                <!-- Blog Content -->
                <div class="blog-detail__content">
                    <?php echo $formatted_content; ?>
                </div>

                <!-- Author Box -->
                <div class="author-box">
                    <div class="author-avatar">
                        <?php echo strtoupper(substr($blog['author'], 0, 1)); ?>
                    </div>
                    <div class="author-info">
                        <h3><?php echo htmlspecialchars($blog['author']); ?></h3>
                        <p>Content writer at Zocom Limited with expertise in safety equipment and workplace safety practices.</p>
                    </div>
                </div>
            </article>

            <aside class="blog-detail__sidebar">
                <!-- CTA Widget -->
                <div class="cta-widget">
                    <div class="cta-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h3 class="cta-title">Need Safety Equipment?</h3>
                    <p class="cta-text">Contact us to discuss your safety equipment needs. Our team is ready to assist you.</p>
                    <a href="contacts.php" class="cta-btn">Contact Us</a>
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
                                        <div class="related-post__date">
                                            <i class="far fa-calendar-alt"></i>
                                            <span><?php echo date('M d, Y', strtotime($related['created_at'])); ?></span>
                                        </div>
                                    </div>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Categories Widget -->
                <div class="sidebar-widget">
                    <h3 class="sidebar-widget__title">Categories</h3>
                    <ul class="category-list">
                        <?php
                        $categories_query = "SELECT DISTINCT category FROM blogs WHERE status = 'published' AND category IS NOT NULL AND category != ''";
                        $categories_result = mysqli_query($conn, $categories_query);
                        while ($category = mysqli_fetch_assoc($categories_result)): ?>
                            <li class="category-item">
                                <a href="blogs.php?category=<?php echo urlencode($category['category']); ?>">
                                    <?php echo htmlspecialchars($category['category']); ?>
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
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
            const articleContent = document.querySelector('.blog-detail__content');
            const tableOfContents = document.getElementById('tableOfContents');
            const tocLinks = document.getElementById('tocLinks');

            // Generate Table of Contents
            function generateTableOfContents() {
                // Get all h2 and h3 elements in the content
                const headings = articleContent.querySelectorAll('h2, h3');

                // If there are fewer than 3 headings, hide the TOC
                if (headings.length < 3) {
                    tableOfContents.style.display = 'none';
                    return;
                }

                // Add ID to each heading and create TOC links
                headings.forEach((heading, index) => {
                    const id = 'heading-' + index;
                    heading.id = id;

                    const listItem = document.createElement('li');
                    listItem.className = 'toc-link';

                    // Add additional padding for h3 elements
                    if (heading.tagName === 'H3') {
                        listItem.style.paddingLeft = '2rem';
                    }

                    const link = document.createElement('a');
                    link.href = '#' + id;
                    link.textContent = heading.textContent;

                    listItem.appendChild(link);
                    tocLinks.appendChild(listItem);
                });
            }

            // Make images zoomable
            function makeImagesZoomable() {
                const images = articleContent.querySelectorAll('img');
                images.forEach(img => {
                    img.classList.add('zoomable');
                    img.addEventListener('click', function() {
                        this.classList.toggle('zoomed');
                    });
                });
            }

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

            // Smooth scroll for TOC links and handle active states
            document.addEventListener('click', function(e) {
                const target = e.target;
                if (target.tagName === 'A' && target.parentNode.classList.contains('toc-link')) {
                    e.preventDefault();
                    const targetId = target.getAttribute('href').substring(1);
                    const targetElement = document.getElementById(targetId);

                    if (targetElement) {
                        const headerOffset = 100; // Adjust for fixed header
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });

                        // Update URL without reloading
                        history.pushState(null, null, '#' + targetId);
                    }
                }
            });

            // Handle social sharing
            document.querySelectorAll('.social-icon').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.open(this.href, '_blank', 'width=600,height=400');
                });
            });

            // Initialize
            generateTableOfContents();
            makeImagesZoomable();
            updateReadingProgress();

            // Update progress on scroll
            window.addEventListener('scroll', updateReadingProgress);

            // Update progress when images load (can change document height)
            document.querySelectorAll('img').forEach(img => {
                img.addEventListener('load', updateReadingProgress);
            });
        });
    </script>
</body>

</html>