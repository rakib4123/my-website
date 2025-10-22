</main>
<footer class="bg-surface border-t border-gray-200 mt-24">
    <div class="container mx-auto px-6 py-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-500">&copy; <?php echo date("Y"); ?> Electro Beats BD. All Rights Reserved.</p>
            <div class="flex space-x-6 mt-4 md:mt-0">
                <a href="products.php" class="text-gray-500 hover:text-blue-600 transition-colors">Shop</a>
                <a href="#" class="text-gray-500 hover:text-blue-600 transition-colors">Privacy Policy</a>
            </div>
        </div>
    </div>
</footer>

<?php
// --- Fetch all active popup slides from the database ---
$popup_slides = [];
$popup_result = $mysqli->query("SELECT * FROM popup_slides WHERE is_active = 1 ORDER BY sort_order ASC");
if ($popup_result && $popup_result->num_rows > 0) {
    while ($row = $popup_result->fetch_assoc()) {
        $popup_slides[] = $row;
    }
}
?>

<?php if (!empty($popup_slides)): ?>
<div id="announcement-popup" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="card p-4 max-w-lg w-full relative animate-on-scroll">
        <button id="close-popup-btn" class="absolute top-2 right-3 text-gray-400 hover:text-gray-800 z-20 text-3xl font-bold">&times;</button>
        
        <div class="swiper-container popup-slider rounded-lg">
            <div class="swiper-wrapper">
                <?php foreach ($popup_slides as $slide): ?>
                <div class="swiper-slide" style="background-image: url('<?php echo htmlspecialchars($slide['media_path']); ?>');">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="relative z-10 p-8 text-center text-white">
                        <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($slide['heading']); ?></h2>
                        <?php if(!empty($slide['subheading'])): ?>
                            <p class="mt-2 text-gray-200"><?php echo htmlspecialchars($slide['subheading']); ?></p>
                        <?php endif; ?>
                        <a href="<?php echo htmlspecialchars($slide['button_link']); ?>" class="mt-4 inline-block btn btn-primary !py-2 !px-5 text-sm"><?php echo htmlspecialchars($slide['button_text']); ?></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>

    </div>
</div>
<?php endif; ?>

<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<script>
    // --- Scroll Animations ---
    const scrollElements = document.querySelectorAll(".animate-on-scroll");
    const elementInView = (el) => {
        const elementTop = el.getBoundingClientRect().top;
        return elementTop <= (window.innerHeight || document.documentElement.clientHeight) - 50;
    };
    const displayScrollElement = (element) => element.classList.add("is-visible");
    const handleScrollAnimation = () => {
        scrollElements.forEach((el) => { if (elementInView(el)) { displayScrollElement(el); } });
    }
    window.addEventListener("scroll", handleScrollAnimation);
    handleScrollAnimation();

    // --- Homepage Hero Slider ---
    if (document.querySelector('.hero-product-slider')) {
        new Swiper('.hero-product-slider', {
            loop: true,
            autoplay: { delay: 4000, disableOnInteraction: false },
            effect: 'fade',
            fadeEffect: { crossFade: true },
        });
    }

    // --- Announcement Popup Slider Logic ---
    const popup = document.getElementById('announcement-popup');
    const closeBtn = document.getElementById('close-popup-btn');

    if (popup) {
        if (!sessionStorage.getItem('announcementShown')) {
            setTimeout(() => {
                popup.classList.remove('hidden');
                popup.querySelector('.animate-on-scroll').classList.add('is-visible');
                
                // Initialize the popup slider AFTER the popup is visible
                new Swiper('.popup-slider', {
                    loop: true,
                    autoplay: { delay: 5000, disableOnInteraction: false },
                    pagination: { el: '.swiper-pagination', clickable: true },
                    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                });

            }, 1500);
        }

        const closePopup = () => {
            popup.classList.add('hidden');
            sessionStorage.setItem('announcementShown', 'true');
        };

        if (closeBtn) {
            closeBtn.addEventListener('click', closePopup);
        }
        
        popup.addEventListener('click', (event) => {
            if (event.target === popup) {
                closePopup();
            }
        });
    }
</script>

</body>
</html>