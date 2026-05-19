<?php 
// ============================================================
// MASTER DYNAMIC UNIFIED SYSTEM CATALOG & ROUTER WITH DYNAMIC VISUAL IMAGES
// accessories.php
// ============================================================
$pageTitle = "Products Catalog | CreativeKit3A";
include 'includes/header.php'; 
require_once 'includes/db.php';

// Read dynamic routing URL parameters safely
$currentCatParam  = isset($_GET['cat']) ? trim($_GET['cat']) : null;
$currentItemSlug = isset($_GET['item']) ? trim($_GET['item']) : null;

// Initialize layout processing variables
$viewMode = 'all'; 
$catRecord = null;
$productRecord = null;

// ============================================================
// LOUD UPDATE COMMENTS: MULTI-TIER ROUTING LOGIC PIPELINE
// Evaluates the incoming URL variables against your database tables.
// Handles slugs or structural numeric primary IDs smoothly.
// ============================================================
/* */

// Case A: A specific product details view is requested via 'item' parameter
if ($currentItemSlug) {
    $prodStmt = mysqli_prepare($conn, "SELECT p.*, c.name as cat_name, c.id as cat_id FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? LIMIT 1");
    if ($prodStmt) {
        mysqli_stmt_bind_param($prodStmt, 's', $currentItemSlug);
        mysqli_stmt_execute($prodStmt);
        $res = mysqli_stmt_get_result($prodStmt);
        if ($productRecord = mysqli_fetch_assoc($res)) {
            $viewMode = 'product_detail';
            $pageTitle = htmlspecialchars($productRecord['name']) . " | CreativeKit3A";
        }
        mysqli_stmt_close($prodStmt);
    }
} 
// Case B: A category/sub-category filter view is requested via 'cat' parameter
elseif ($currentCatParam) {
    if (is_numeric($currentCatParam)) {
        $catStmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE id = ? LIMIT 1");
        mysqli_stmt_bind_param($catStmt, 'i', $currentCatParam);
    } else {
        $catStmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE slug = ? LIMIT 1");
        mysqli_stmt_bind_param($catStmt, 's', $currentCatParam);
    }

    if ($catStmt) {
        mysqli_stmt_execute($catStmt);
        $res = mysqli_stmt_get_result($catStmt);
        if ($catRecord = mysqli_fetch_assoc($res)) {
            $viewMode = 'category_filter';
            $pageTitle = htmlspecialchars($catRecord['name']) . " | CreativeKit3A";
        }
        mysqli_stmt_close($catStmt);
    }
}
/* */
?>

<main style="padding: 20px 0 40px 0; min-height: 70vh; background: #fafafa;">
  <div class="container">

    <div class="breadcrumb-container" style="margin-bottom: 25px; padding: 12px 20px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 0.88rem; font-family: sans-serif; display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
      <a href="index.php" style="color: #64748b; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; transition: color 0.15s;" onmouseover="this.style.color='var(--orange-primary, #FF6B00)'" onmouseout="this.style.color='#64748b'"><i class="fas fa-home"></i> Home</a>
      <i class="fas fa-chevron-right" style="color: #cbd5e1; font-size: 0.75rem;"></i>
      <a href="accessories.php" style="color: #64748b; text-decoration: none; font-weight: 500; transition: color 0.15s;" onmouseover="this.style.color='var(--orange-primary, #FF6B00)'" onmouseout="this.style.color='#64748b'">All Products</a>
      
      <?php
      $breadcrumbTrail = [];
      $targetTraceId = 0;

      if ($viewMode === 'product_detail' && $productRecord) {
          $targetTraceId = (int)$productRecord['cat_id'];
      } elseif ($viewMode === 'category_filter' && $catRecord) {
          $targetTraceId = (int)$catRecord['id'];
      }

      while ($targetTraceId > 0) {
          $traceQuery = mysqli_query($conn, "SELECT id, name, slug, parent_id FROM categories WHERE id = $targetTraceId");
          if ($traceQuery && $crumbRow = mysqli_fetch_assoc($traceQuery)) {
              array_unshift($breadcrumbTrail, [
                  'name' => $crumbRow['name'],
                  'url'  => 'accessories.php?cat=' . urlencode($crumbRow['slug'])
              ]);
              $targetTraceId = (int)$crumbRow['parent_id'];
          } else {
              break;
          }
      }

      foreach ($breadcrumbTrail as $index => $crumb) {
          echo '<i class="fas fa-chevron-right" style="color: #cbd5e1; font-size: 0.75rem;"></i>';
          $isLastCategory = ($index === count($breadcrumbTrail) - 1);
          if ($isLastCategory && $viewMode === 'category_filter') {
              echo '<span style="color: var(--orange-primary, #FF6B00); font-weight: 600;">' . htmlspecialchars($crumb['name']) . '</span>';
          } else {
              echo '<a href="' . $crumb['url'] . '" style="color: #64748b; text-decoration: none; font-weight: 500; transition: color 0.15s;" onmouseover="this.style.color=\'var(--orange-primary, #FF6B00)\'" onmouseout="this.style.color=\'#64748b\'">' . htmlspecialchars($crumb['name']) . '</a>';
          }
      }

      if ($viewMode === 'product_detail' && $productRecord) {
          echo '<i class="fas fa-chevron-right" style="color: #cbd5e1; font-size: 0.75rem;"></i>';
          echo '<span style="color: var(--orange-primary, #FF6B00); font-weight: 600; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . htmlspecialchars($productRecord['name']) . '</span>';
      }
      ?>
    </div>


    <?php if ($viewMode === 'product_detail' && $productRecord): ?>
      <?php $displayImg = !empty($productRecord['image']) ? htmlspecialchars($productRecord['image']) : 'assets/placeholder.png'; ?>
      
      <div style="display: flex; flex-wrap: wrap; gap: 40px; margin-top: 20px; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
        
        <div style="flex: 1; min-width: 300px; text-align: center;">
          <img src="<?= $displayImg ?>?t=<?= time() ?>" alt="<?= htmlspecialchars($productRecord['name']) ?>" style="max-width: 100%; max-height: 450px; object-fit: contain; border-radius: 6px; border: 1px solid #e2e8f0; padding: 10px;">
        </div>
        
        <div style="flex: 1; min-width: 300px;">
          <span style="background: #efe3ff; color: #7c3aed; font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">
            <?= htmlspecialchars($productRecord['cat_name'] ?? 'General Corporate Item') ?>
          </span>
          <h1 style="margin: 10px 0 15px 0; font-size: 2.2rem; font-weight: 700; color: #1e293b;"><?= htmlspecialchars($productRecord['name']) ?></h1>
          <p style="font-size: 1.8rem; font-weight: 800; color: #ff6b00; margin: 0 0 20px 0;">₱<?= number_format($productRecord['price'], 2) ?> <span style="font-size: 0.9rem; color: #64748b; font-weight: 500;">/ base wholesale unit</span></p>
          
          <div style="border-top: 1px solid #edf2f7; border-bottom: 1px solid #edf2f7; padding: 20px 0; margin-bottom: 25px; line-height: 1.6; color: #475569;">
            <h3 style="margin: 0 0 10px 0; font-size: 1rem; text-transform: uppercase; letter-spacing: 0.5px; color: #1e293b;">Product Description</h3>
            <p style="margin: 0; font-size: 0.95rem; white-space: pre-line;"><?= !empty($productRecord['description']) ? htmlspecialchars($productRecord['description']) : 'Premium customized corporate giveaway option tailored perfectly with high-grade materials and multi-color logo layout printing profiles.' ?></p>
          </div>

          <div style="display: flex; gap: 15px;">
            <a href="quote.php?item=<?= urlencode($productRecord['slug']) ?>" class="btn btn-primary" style="padding: 14px 28px; font-weight: 700; background: #ff6b00; color: #fff; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 4px 10px rgba(255,107,0,0.2);"><i class="fas fa-file-invoice-dollar"></i> Get Custom Quote</a>
            <a href="accessories.php" class="btn" style="padding: 14px 24px; font-weight: 600; background: #f1f5f9; color: #334155; text-decoration: none; border-radius: 6px; display: inline-flex; align-items: center; gap: 6px; border: 1px solid #cbd5e1;"><i class="fas fa-chevron-left"></i> Back to Catalog</a>
          </div>
        </div>

      </div>

    <?php else: ?>
      
      <div style="margin-bottom: 30px; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 15px;">
        <div>
          <h1 style="margin: 0 0 5px 0; font-size: 1.8rem; font-weight: 700; color: #1e293b; text-transform: uppercase;">
            <?= $viewMode === 'category_filter' ? htmlspecialchars($catRecord['name']) : 'All Promotional Products' ?>
          </h1>
          <p style="margin: 0; font-size: 0.9rem; color: #64748b;">Browse through our active line-up of custom corporate giveaways and custom gift item bundles.</p>
        </div>
        
        <div>
          <div style="position: relative;">
            <input type="text" id="cat-search-input" placeholder="Search within collection..." style="padding: 10px 15px 10px 35px; width: 260px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; outline: none; box-sizing: border-box;">
            <i class="fas fa-search" style="position: absolute; left: 12px; top: 13px; color: #94a3b8; font-size: 0.85rem;"></i>
          </div>
        </div>
      </div>

      <?php 
      $hasChildrenCategories = false;
      $childCategoriesResult = null;
      
      if ($viewMode === 'category_filter' && $catRecord) {
          $checkChildrenQuery = mysqli_prepare($conn, "SELECT * FROM categories WHERE parent_id = ? ORDER BY name ASC");
          if ($checkChildrenQuery) {
              mysqli_stmt_bind_param($checkChildrenQuery, 'i', $catRecord['id']);
              mysqli_stmt_execute($checkChildrenQuery);
              $childCategoriesResult = mysqli_stmt_get_result($checkChildrenQuery);
              if ($childCategoriesResult && mysqli_num_rows($childCategoriesResult) > 0) {
                  $hasChildrenCategories = true;
              }
          }
      }
      ?>

      <?php if ($hasChildrenCategories && $childCategoriesResult): ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; margin-top: 20px;">
          <?php while ($subFolder = mysqli_fetch_assoc($childCategoriesResult)): ?>
            
           
            <?php 
              $categorySlug  = htmlspecialchars($subFolder['slug']);
              $localPngPath  = "assets/categories/" . $categorySlug . ".png";
              $localJpgPath  = "assets/categories/" . $categorySlug . ".jpg";
              
              $useVisualImage = false;
              $finalImgSource = "";

              if (file_exists(__DIR__ . "/" . $localPngPath)) {
                  $useVisualImage = true;
                  $finalImgSource = $localPngPath;
              } elseif (file_exists(__DIR__ . "/" . $localJpgPath)) {
                  $useVisualImage = true;
                  $finalImgSource = $localJpgPath;
              }
            ?>
         

            <div onclick="window.location.href='accessories.php?cat=<?= $subFolder['slug'] ?>'" style="background: #ffffff; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; gap: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.02);" onmouseover="this.style.transform='translateY(-3px)'; this.style.borderColor='var(--orange-primary, #FF6B00)'; this.style.boxShadow='0 6px 15px rgba(0,0,0,0.05)';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#e2e8f0'; this.style.boxShadow='0 2px 5px rgba(0,0,0,0.02)';">
              
             
              <?php if ($useVisualImage): ?>
                <div style="width: 55px; height: 55px; border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #fafafa; border: 1px solid #edf2f7; flex-shrink: 0;">
                  <img src="<?= $finalImgSource ?>?t=<?= time() ?>" alt="<?= htmlspecialchars($subFolder['name']) ?>" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
              <?php else: ?>
                <div style="background: #fff0e6; color: #ff6b00; width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
                  <i class="fas fa-folder-open"></i>
                </div>
              <?php endif; ?>
          

              <div style="flex: 1;">
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($subFolder['name']) ?></h3>
                <span style="font-size: 0.78rem; color: #ff6b00; font-weight: 600; text-transform: uppercase; display: inline-flex; align-items: center; gap: 4px; margin-top: 2px;">Explore Type <i class="fas fa-arrow-right" style="font-size:0.7rem;"></i></span>
              </div>
            </div>
          <?php endwhile; ?>
        </div>

      <?php else: ?>
        <?php
        if ($viewMode === 'category_filter' && $catRecord) {
            $catalogQueryStr = "SELECT p.* FROM products p 
                                WHERE p.status = 'active' AND p.category_id = {$catRecord['id']}
                                ORDER BY p.id ASC";
        } else {
            $catalogQueryStr = "SELECT * FROM products WHERE status = 'active' ORDER BY id ASC";
        }
        $catalogResult = mysqli_query($conn, $catalogQueryStr);
        ?>

        <div id="cat-main-grid" class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 25px;">
          <?php if ($catalogResult && mysqli_num_rows($catalogResult) > 0): ?>
            <?php while ($prod = mysqli_fetch_assoc($catalogResult)): ?>
              <?php 
                $displayImg = !empty($prod['image']) ? htmlspecialchars($prod['image']) : 'assets/placeholder.png';
                $productUrl = "accessories.php?item=" . urlencode($prod['slug']);
              ?>
              <div class="product-card" data-name="<?= htmlspecialchars(strtolower($prod['name'])) ?>" style="background: #fff; border-radius: 8px; border: 1px solid #e2e8f0; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; padding: 15px;" onclick="window.location.href='<?= $productUrl ?>'" onmouseover="this.style.transform='translateY(-4px)'; this.style.box-shadow='0 8px 20px rgba(0,0,0,0.06)'" onmouseout="this.style.transform='translateY(0)'; this.style.box-shadow='none'">
                
                <div>
                  <div class="product-card-img" style="text-align: center; margin-bottom: 15px; background: #fafafa; border-radius: 6px; padding: 10px;">
                    <img src="<?= $displayImg ?>?t=<?= time() ?>" alt="<?= htmlspecialchars($prod['name']) ?>" style="max-width: 100%; height: 180px; object-fit: contain;">
                  </div>
                  <div class="product-card-body" style="padding: 0 5px;">
                    <p class="product-card-title" style="margin: 0 0 8px 0; font-weight: 600; font-size: 0.95rem; color: #1e293b; line-height: 1.4; min-height: 42px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($prod['name']) ?></p>
                    <p class="product-card-price" style="margin: 0; font-weight: 700; color: #ff6b00; font-size: 1.1rem;">₱<?= number_format($prod['price'], 2) ?> <span style="font-size: 0.75rem; color: #64748b; font-weight: 500;">ea</span></p>
                  </div>
                </div>
                
                <div class="product-card-footer" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #f1f5f9;">
                  <span class="customize-btn" style="color: #ff6b00; font-size: 0.8rem; font-weight: 700; display: inline-flex; align-items: center; gap: 5px;"><i class="fas fa-cog"></i> CUSTOMIZE PROPS <i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></span>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px;">
              <i class="fas fa-boxes" style="font-size: 2.5rem; color: #cbd5e1; margin-bottom: 12px;"></i>
              <p style="margin: 0; color: #64748b; font-style: italic; font-size: 0.95rem;">No active products are registered under this classification group yet.</p>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>

    <?php endif; ?>

  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('cat-search-input');
  const catalogGrid = document.getElementById('cat-main-grid');

  if (searchInput && catalogGrid) {
    const originalCardsArr = Array.from(catalogGrid.querySelectorAll('.product-card'));
    
    searchInput.addEventListener('input', function() {
      const queryText = this.value.trim().toLowerCase();
      
      originalCardsArr.forEach(card => {
        const cardNameAttr = card.getAttribute('data-name') || '';
        if (cardNameAttr.includes(queryText)) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }
});
</script>

<?php include 'includes/footer.php'; ?>