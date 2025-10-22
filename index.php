<?php 
include 'header.php'; 

// Fetch all active slides for the HERO slider
$hero_slides = [];
$sql_hero = "SELECT * FROM hero_slider WHERE is_active = 1 ORDER BY sort_order ASC";
$result_hero = $mysqli->query($sql_hero);
if ($result_hero && $result_hero->num_rows > 0) {
    while ($row = $result_hero->fetch_assoc()) {
        $hero_slides[] = $row;
    }
}

// Fetch all active slides for the POPUP slider
$popup_slides = [];
$popup_result = $mysqli->query("SELECT * FROM popup_slides WHERE is_active = 1 ORDER BY sort_order ASC");
if ($popup_result && $popup_result->num_rows > 0) {
    while ($row = $popup_result->fetch_assoc()) {
        $popup_slides[] = $row;
    }
}
?>

<div class="container mx-auto px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <div class="text-center md:text-left animate-on-scroll">
            <h1 class="text-4xl md:text-6xl font-extrabold text-gray-900 leading-tight mb-6">Elevate Your Digital World.</h1>
            <p class="text-lg md:text-xl text-gray-600 mb-8">Discover premium electronic accessories that blend cutting-edge technology with minimalist design.</p>
            <a href="products.php" class="btn btn-primary text-lg">Shop The Collection</a>
        </div>
        <div class="w-full h-96 card p-2 animate-on-scroll">
            <div class="swiper-container hero-product-slider h-full rounded-lg">
                <div class="swiper-wrapper">
                    <?php if (!empty($hero_slides)): ?>
                        <?php foreach ($hero_slides as $slide): ?>
                            <div class="swiper-slide" <?php if ($slide['type'] == 'image') echo 'style="background-image: url(\'' . htmlspecialchars($slide['media_path']) . '\');"'; ?>>
                                <?php if ($slide['type'] == 'video'): ?>
                                    <video class="absolute top-0 left-0 w-full h-full object-cover" autoplay muted loop playsinline><source src="<?php echo htmlspecialchars($slide['media_path']); ?>" type="video/mp4"></video>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide" style="background-image: url('https://images.unsplash.com/photo-1505740420928-5e560c06d30e?q=80&w=2070&auto=format&fit=crop');"></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-surface py-16">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
            <div class="animate-on-scroll"><h3 class="text-xl font-bold mb-2">Premium Quality</h3><p class="text-gray-600">Only the best materials, ensuring durability and performance.</p></div>
            <div class="animate-on-scroll" style="transition-delay: 200ms;"><h3 class="text-xl font-bold mb-2">Fast Shipping</h3><p class="text-gray-600">Get your new gear delivered to your door, quickly and safely.</p></div>
            <div class="animate-on-scroll" style="transition-delay: 400ms;"><h3 class="text-xl font-bold mb-2">24/7 Support</h3><p class="text-gray-600">Our team is always here to help you with any questions.</p></div>
        </div>
    </div>
</div>

<div class="container mx-auto px-6 py-16">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-12 animate-on-scroll">New Arrivals</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php
        $sql_products = "SELECT id, name, price, thumbnail_image FROM products ORDER BY id DESC LIMIT 4";
        $result_products = $mysqli->query($sql_products);
        if ($result_products && $result_products->num_rows > 0) {
            while ($product = $result_products->fetch_assoc()) {
                echo '<div class="card flex flex-col justify-between animate-on-scroll">';
                echo '  <div>';
                echo '    <a href="product-detail.php?id=' . $product['id'] . '" class="block p-4"><img src="' . htmlspecialchars($product['thumbnail_image']) . '" alt="' . htmlspecialchars($product['name']) . '" class="w-full h-48 object-contain"></a>';
                echo '    <div class="p-4 text-center">';
                echo '      <h3 class="font-semibold text-lg text-gray-800 h-14">' . htmlspecialchars($product['name']) . '</h3>';
                echo '      <p class="text-gray-500 mt-2">৳' . number_format($product['price'], 2) . '</p>';
                echo '    </div>';
                echo '  </div>';
                echo '  <div class="p-4 border-t border-gray-200">';
                echo '    <form action="add_to_cart.php" method="POST" class="space-y-2">';
                echo '      <input type="hidden" name="product_id" value="' . $product['id'] . '">';
                echo '      <input type="hidden" name="quantity" value="1">';
                echo '      <button type="submit" name="add_to_cart" class="w-full btn btn-primary py-2 text-sm">Add to Cart</button>';
                echo '      <button type="submit" name="buy_now" class="w-full btn bg-gray-700 hover:bg-gray-800 text-white py-2 text-sm">Buy Now</button>';
                echo '    </form>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo "<p class='col-span-4 text-center text-gray-500'>No new products found. Please add products in the admin panel.</p>";
        }
        ?>
    </div>
</div>

<?php if (!empty($popup_slides)): ?>
<div id="announcement-popup" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="card p-4 max-w-lg w-full relative">
        <button id="close-popup-btn" class="absolute top-3 right-3 text-gray-800 hover:text-black z-20 text-3xl font-bold">&times;</button>
        <div class="swiper-container popup-slider rounded-lg">
            <div class="swiper-wrapper">
                <?php foreach ($popup_slides as $slide): ?>
                <div class="swiper-slide" style="background-image: url('<?php echo htmlspecialchars($slide['media_path']); ?>');">
                    <div class="absolute inset-0 bg-black/50"></div>
                    <div class="relative z-10 p-8 text-center text-white">
                        <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($slide['heading']); ?></h2>
                        <p class="mt-2 text-gray-200"><?php echo htmlspecialchars($slide['subheading']); ?></p>
                        <a href="<?php echo htmlspecialchars($slide['button_link']); ?>" class="mt-4 inline-block btn btn-primary !py-2 !px-5 text-sm"><?php echo htmlspecialchars($slide['button_text']); ?></a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>

<script>
    const popup = document.getElementById('announcement-popup');
    const closeBtn = document.getElementById('close-popup-btn');

    if (popup) {
        setTimeout(() => {
            popup.classList.remove('hidden');
            new Swiper('.popup-slider', {
                loop: true,
                autoplay: { delay: 5000 },
                pagination: { el: '.swiper-pagination', clickable: true },
            });
        }, 2000);

        const closePopup = () => { popup.classList.add('hidden'); };
        if (closeBtn) { closeBtn.addEventListener('click', closePopup); }
        popup.addEventListener('click', (event) => { if (event.target === popup) { closePopup(); } });
    }
</script>