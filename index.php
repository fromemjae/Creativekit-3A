<?php
// ============================================================
// FULLY DYNAMIC LOOP-DRIVEN HOMEPAGE
// index.php
// ============================================================
$pageTitle = "Home | CreativeKit 3A";
include 'includes/header.php'; 
require_once 'includes/db.php'; 

// ============================================================
// LOUD UPDATE COMMENTS: TRUE CONDITIONAL VISIBILITY LOOP
// Extracts ONLY active products out of the MySQL database. 
// Uses a loop container to dynamically draw product cards on screen.
// ============================================================
/* */
$live_products_query = mysqli_query($conn, "SELECT id, name, price, image, slug FROM products WHERE status = 'active' ORDER BY id DESC LIMIT 8");
/* */
?>

<main>
  <section class="hero-section" aria-label="Hero banner">
    <div class="hero-carousel">

      <div class="hero-slide active">
        <div class="hero-slide-img">
          <img data-src="" src="assets/email.png" alt="SOUVENIR GIFT SETS">
        </div>
        <div class="hero-slide-content">
          <span class="eyebrow">Customize Your</span>
          <h1>SOUVENIR GIFT SETS</h1>
          <p>We specialize in custom item personalization, made quick and easy.</p>
          <a href="accessories.php?item=bamboo-tumbler" class="btn btn-white btn-large">BROWSE CATALOG</a>
        </div>
      </div>

      <div class="hero-slide">
        <div class="hero-slide-img">
          <img data-src="" src="assets/hero2.png" alt="FOLDABLE TOTE BAGS">
        </div>
        <div class="hero-slide-content">
          <span class="eyebrow">Showcase Your Brand with</span>
          <h1>FOLDABLE TOTE BAGS</h1>
          <p>Stylish and durable custom foldable tote bags for events, recognition, and giveaways.</p>
          <a href="accessories.php" class="btn btn-white btn-large">BROWSE CATALOG</a>
        </div>
      </div>

      <div class="hero-slide">
        <div class="hero-slide-img">
          <img data-src=""
               src="assets/hero3.png" alt="Custom Logo Tote Bags">
        </div>
        <div class="hero-slide-content">
          <span class="eyebrow">ELEVATE YOUR BRAND IDENTITY</span>
          <h1>EMPLOYEE ONBOARDING GIFT SETS</h1>
          <p>Premium custom employee onboarding gift sets tailored for your company's needs.</p>
          <a href="accessories.php" class="btn btn-white btn-large">BROWSE CATALOG</a>
        </div>
      </div>

      <button class="carousel-btn prev" aria-label="Previous slide"><i class="fas fa-chevron-left" aria-hidden="true"></i></button>
      <button class="carousel-btn next" aria-label="Next slide"><i class="fas fa-chevron-right" aria-hidden="true"></i></button>

      <div class="carousel-dots" role="tablist" aria-label="Slide indicators">
        <button class="carousel-dot active" role="tab" aria-label="Slide 1" aria-selected="true"></button>
        <button class="carousel-dot" role="tab" aria-label="Slide 2" aria-selected="false"></button>
        <button class="carousel-dot" role="tab" aria-label="Slide 3" aria-selected="false"></button>
      </div>
    </div>
  </section>

  <section class="products-section" aria-labelledby="products-heading">
    <div class="container">
      <div class="section-header">
        <h2 class="section-title" id="products-heading">OUR PRODUCTS</h2>
        <a href="accessories.php" class="view-all-link">View All <i class="fas fa-arrow-right" aria-hidden="true"></i></a>
      </div>

      <div class="product-grid">
       
        <?php if ($live_products_query && mysqli_num_rows($live_products_query) > 0): ?>
          <?php while ($product = mysqli_fetch_assoc($live_products_query)): ?>
            <?php 
              $displayImg = !empty($product['image']) ? htmlspecialchars($product['image']) : 'assets/placeholder.png';
              $productUrl = "accessories.php?item=" . urlencode($product['slug']);
            ?>
            <div class="product-card" data-product-id="<?php echo $product['id']; ?>" data-url="<?php echo $productUrl; ?>">
              <div class="product-card-img">
                <img src="<?php echo $displayImg; ?>?t=<?php echo time(); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
              </div>
              <div class="product-card-body">
                <p class="product-card-title"><?php echo htmlspecialchars($product['name']); ?></p>
                <p class="product-card-price">As low as ₱<?php echo number_format($product['price'], 2); ?> ea</p>
              </div>
              <div class="product-card-footer">
                <span class="customize-btn"><i class="fas fa-chevron-right" aria-hidden="true"></i> CUSTOMIZE</span>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p style="text-align: center; color: #64748b; font-style: italic; grid-column: 1 / -1; padding: 40px 0;">No featured products are currently active.</p>
        <?php endif; ?>
        
      </div>

    </div>
  </section>

  <section class="corporate-section" aria-labelledby="corporate-heading">
    <div class="container">
      <div class="corporate-inner">
        <div class="corporate-img"><img src="assets/hero.jpg" alt="Custom Corporate Items"></div>
        <div class="corporate-text">
          <h3>CreativeKit 3A — Your Trusted Partner for Custom Corporate Giveaways</h3>
          <p>Discover premium customized giveaways with CreativeKit 3A, your reliable source for personalized wristbands, promotional items, and branded accessories.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="contact-section" aria-labelledby="contact-heading">
    <div class="container">
      <h6 id="contact-heading">CONTACT US</h6>
      <div class="contact-numbers">
        <a href="tel:+639177142774"><i class="fas fa-mobile-alt" aria-hidden="true"></i> +63 917 714 2774</a>
        <a href="tel:+639339927410"><i class="fas fa-phone" aria-hidden="true"></i> +63 933 992 7410</a>
      </div>
      <p class="contact-email">Email us at <a href="mailto:salesandmarketing@creativekit3a.com">salesandmarketing@creativekit3a.com</a></p>
      <a href="/contact-us/" class="btn btn-primary btn-large">Contact Us</a>
    </div>
  </section>
</main>

<?php include 'includes/footer.php'; ?>