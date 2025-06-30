<?php
// Ensure this file is named "promo-banner.php"
$category_link = "shop.php#head-protection";

// Use the specific motorcycle helmet image path from your files
$helmet_image = "Head__images/MOTORCYCLE RIDERS HELMET  Copy.webp";
?>
<div class="promo-banner">
    <div class="promo-banner__container">
        <div class="promo-banner__icon">
            <!-- Using the actual helmet image file with bigger size -->
            <img src="<?php echo $helmet_image; ?>" alt="Motorcycle Riding Helmet" class="promo-banner__image">
        </div>
        <div class="promo-banner__text">
            <strong>Exciting News!</strong> Grab the Best Deals and Offers on selected Riding Helmets. Call us today for more information.
        </div>
        <a href="<?php echo $category_link; ?>" class="promo-banner__button">Shop Now</a>
        <button class="promo-banner__close">×</button>
    </div>
</div>

<style>
    /* Promo Banner Styles - with bigger helmet image */
    .promo-banner {
        width: 100%;
        background: linear-gradient(90deg, #1e3c72, #2a5298, #e94e92);
        color: white;
        padding: 10px 0;
        font-family: 'Poppins', sans-serif;
    }

    .promo-banner__container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
    }

    /* Enlarged helmet icon container */
    .promo-banner__icon {
        flex-shrink: 0;
        width: 45px;
        /* Increased from 30px */
        height: 45px;
        /* Increased from 30px */
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        overflow: hidden;
        background-color: white;
        padding: 3px;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, 0.3);
        /* Added subtle glow */
        margin-right: 5px;
    }

    .promo-banner__image {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .promo-banner__text {
        flex: 1;
        font-size: 16px;
        line-height: 1.4;
    }

    .promo-banner__button {
        background: white;
        color: #2a5298;
        padding: 6px 18px;
        /* Slightly bigger button */
        border-radius: 20px;
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        white-space: nowrap;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        /* Added subtle shadow */
    }

    .promo-banner__button:hover {
        background: rgba(255, 255, 255, 0.9);
        transform: scale(1.05);
        color: #2a5298;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.15);
        /* Enhanced shadow on hover */
    }

    .promo-banner__close {
        background: none;
        border: none;
        color: white;
        font-size: 24px;
        cursor: pointer;
        padding: 0;
        opacity: 0.7;
        line-height: 1;
        transition: opacity 0.3s ease;
        flex-shrink: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .promo-banner__close:hover {
        opacity: 1;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .promo-banner__text {
            font-size: 14px;
        }

        .promo-banner__button {
            font-size: 12px;
            padding: 5px 15px;
        }

        /* Keep helmet image big even on tablets */
        .promo-banner__icon {
            width: 40px;
            height: 40px;
        }
    }

    @media (max-width: 576px) {
        .promo-banner__container {
            flex-wrap: wrap;
            justify-content: center;
            padding: 10px 15px;
        }

        .promo-banner__text {
            width: 100%;
            order: 1;
            text-align: center;
            margin-bottom: 5px;
            font-size: 13px;
        }

        .promo-banner__icon {
            order: 0;
            width: 35px;
            /* Still bigger than original but fits mobile better */
            height: 35px;
            margin-bottom: 5px;
        }

        .promo-banner__button {
            order: 2;
            margin-top: 5px;
        }

        .promo-banner__close {
            position: absolute;
            top: 5px;
            right: 5px;
        }
    }
</style>

<script>
    // Banner JavaScript - reappears after 5 minutes
    document.addEventListener('DOMContentLoaded', function() {
        const banner = document.querySelector('.promo-banner');
        const closeBtn = document.querySelector('.promo-banner__close');

        if (!banner || !closeBtn) return;

        // Time in milliseconds before banner reappears (5 minutes = 300000 ms)
        const reappearTime = 300000;

        // Check if banner was recently closed
        const lastClosedTime = localStorage.getItem('promo_banner_closed_time');
        const currentTime = new Date().getTime();

        if (lastClosedTime) {
            const elapsedTime = currentTime - parseInt(lastClosedTime);

            // If less than reappearTime has passed, hide the banner
            if (elapsedTime < reappearTime) {
                banner.style.display = 'none';

                // Set a timeout to show the banner again after the remaining time
                const remainingTime = reappearTime - elapsedTime;
                setTimeout(function() {
                    banner.style.display = 'block';
                }, remainingTime);
            }
        }

        // Handle close button click
        closeBtn.addEventListener('click', function() {
            banner.style.display = 'none';

            // Store the time when the banner was closed
            localStorage.setItem('promo_banner_closed_time', new Date().getTime());

            // Set a timeout to show the banner again after reappearTime
            setTimeout(function() {
                banner.style.display = 'block';
            }, reappearTime);
        });

        // If the image fails to load, replace with an SVG backup
        const img = document.querySelector('.promo-banner__image');
        if (img) {
            img.onerror = function() {
                const iconContainer = document.querySelector('.promo-banner__icon');
                if (iconContainer) {
                    // Replace with SVG if the image fails to load
                    iconContainer.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#2a5298" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2a8 8 0 0 0-8 8v2h16v-2a8 8 0 0 0-8-8z"/>
                        <path d="M4 10v4c0 4.4 3.6 8 8 8s8-3.6 8-8v-4"/>
                    </svg>
                `;
                }
            };
        }
    });
</script>