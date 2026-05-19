<?php
$pageTitle = "Admin Accessory Manager | CreativeKit3A";
include 'includes/header.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Force authentication protection layer context rules
requireAdmin();

$feedbackMsg = "";
$feedbackClass = "lm-msg--success";

// ============================================================
// LOUD UPDATE COMMENTS: ENTIRE NEW MULTI-PART PROCESSING LOGIC
// Handles parsing input types, uploading binary files safely to assets/,
// and executing secure transactional parameterized SQL queries.
// ============================================================
/* */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actionType = isset($_POST['action_type']) ? $_POST['action_type'] : '';

    // Action A: Create a brand new catalog product item target
    if ($actionType === 'add_product') {
        $catId     = intval($_POST['category_id']);
        $prodName  = trim($_POST['product_name']);
        $prodPrice = trim($_POST['product_price']);
        // Auto generate semantic URLs slugs
        $prodSlug  = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $prodName)));

        // Handle Image Upload File Stream Mechanics
        $targetImgPath = "assets/default-placeholder.png"; 
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . '_' . basename($_FILES['product_image']['name']);
            $uploadDir = 'assets/';
            
            // Check if defensive output directory is live
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $destination = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['product_image']['tmp_path'], $destination) || move_uploaded_file($_FILES['product_image']['tmp_name'], $destination)) {
                $targetImgPath = $destination;
            }
        }

        if (!empty($prodName) && !empty($prodPrice) && $catId > 0) {
            $insQuery = "INSERT INTO accessory_products (category_id, slug, name, img, price) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insQuery);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'issss', $catId, $prodSlug, $prodName, $targetImgPath, $prodPrice);
                if (mysqli_stmt_execute($stmt)) {
                    $feedbackMsg = "🎉 Successfully registered new product item: $prodName!";
                    $feedbackClass = "lm-msg--success";
                } else {
                    $feedbackMsg = "⚠️ SQL Execution Error. Check for unique name slug duplicates.";
                    $feedbackClass = "lm-msg--error";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $feedbackMsg = "⚠️ Please fill out all required form elements carefully.";
            $feedbackClass = "lm-msg--error";
        }
    }

    // Action B: Create a brand new variation specification type node
    if ($actionType === 'add_type') {
        $productId = intval($_POST['product_id']);
        $typeName  = trim($_POST['type_name']);
        $typeDesc  = !empty($_POST['type_desc']) ? trim($_POST['type_desc']) : null;

        $targetTypeImgPath = null;
        if (isset($_FILES['type_image']) && $_FILES['type_image']['error'] === UPLOAD_ERR_OK) {
            $fileName = time() . '_type_' . basename($_FILES['type_image']['name']);
            $destination = 'assets/' . $fileName;
            if (move_uploaded_file($_FILES['type_image']['tmp_name'], $destination)) {
                $targetTypeImgPath = $destination;
                $typeDesc = null; // Clean out description override context per formatting choices
            }
        }

        if (!empty($typeName) && $productId > 0) {
            $insQuery = "INSERT INTO accessory_product_types (product_id, name, img, `desc`) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insQuery);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'isss', $productId, $typeName, $targetTypeImgPath, $typeDesc);
                if (mysqli_stmt_execute($stmt)) {
                    $feedbackMsg = "🎉 Successfully appended specification model type: $typeName!";
                    $feedbackClass = "lm-msg--success";
                } else {
                    $feedbackMsg = "⚠️ Database validation insertion pipeline error.";
                    $feedbackClass = "lm-msg--error";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            $feedbackMsg = "⚠️ Variation specification model requires a valid structural assignment title.";
            $feedbackClass = "lm-msg--error";
        }
    }
}

// Gather live datasets for form dropdown arrays options mappings
$categoriesArr = [];
$catRes = mysqli_query($conn, "SELECT id, title FROM accessory_categories ORDER BY title ASC");
while ($row = mysqli_fetch_assoc($catRes)) { $categoriesArr[] = $row; }

$productsArr = [];
$prodRes = mysqli_query($conn, "SELECT id, name FROM accessory_products ORDER BY name ASC");
while ($row = mysqli_fetch_assoc($prodRes)) { $productsArr[] = $row; }
/* */
?>

<main style="padding: 50px 0; background-color: var(--white-off);">
  <div class="container" style="max-width: 900px; margin: 0 auto;">
    
    <div style="margin-bottom: 35px; border-bottom: 2px solid var(--grey-light); padding-bottom: 15px;">
      <span style="color: var(--orange-primary); font-weight: 700; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px;">CreativeKit3A Operations Dashboard</span>
      <h1 style="font-family: 'Poppins', sans-serif; font-size: 2.3rem; font-weight: 900; color: var(--black); margin-top: 5px;">Accessory Catalog Manager</h1>
      <p style="color: var(--grey-dark); margin-top: 5px;">Welcome back, <strong><?php echo adminName(); ?></strong>. Use this secure control terminal to extend website listings instantly.</p>
    </div>

    <?php if (!empty($feedbackMsg)): ?>
      <div class="lm-msg <?php echo $feedbackClass; ?>" style="padding: 16px; font-size: 1rem; font-weight: 600; margin-bottom: 30px; border-radius: 6px;">
        <?php echo $feedbackMsg; ?>
      </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px; align-items: start;">
      
      <div style="background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-top: 4px solid var(--orange-primary);">
        <h3 style="font-family: 'Poppins', sans-serif; font-size: 1.3rem; font-weight: 700; color: var(--black); margin-bottom: 20px;">
          <i class="fas fa-plus-circle" style="color: var(--orange-primary); margin-right: 6px;"></i> Create Main Product
        </h3>
        
        <form action="admin_add_accessory.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action_type" value="add_product">

          <div class="adm-field" style="margin-bottom: 18px;">
            <label style="display:block; font-weight:600; font-size:0.88rem; margin-bottom:6px; color:var(--grey-dark);">Assign Target Category Layer *</label>
            <select name="category_id" required style="width:100%; padding:10px 12px; border:1px solid var(--grey-mid); border-radius:4px; background:var(--white);">
              <option value="" disabled selected>-- Choose Parent Section --</option>
              <?php foreach ($categoriesArr as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['title']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="adm-field" style="margin-bottom: 18px;">
            <label style="display:block; font-weight:600; font-size:0.88rem; margin-bottom:6px; color:var(--grey-dark);">Product Display Name *</label>
            <input type="text" name="product_name" required placeholder="e.g., Premium Dash Cam" style="width:100%; padding:10px 12px; border:1px solid var(--grey-mid); border-radius:4px;">
          </div>

          <div class="adm-field" style="margin-bottom: 18px;">
            <label style="display:block; font-weight:600; font-size:0.88rem; margin-bottom:6px; color:var(--grey-dark);">Starting Pricing Base Label *</label>
            <input type="text" name="product_price" required placeholder="e.g., ₱450.00" style="width:100%; padding:10px 12px; border:1px solid var(--grey-mid); border-radius:4px;">
          </div>

          <div class="adm-field" style="margin-bottom: 25px;">
            <label style="display:block; font-weight:600; font-size:0.88rem; margin-bottom:6px; color:var(--grey-dark);">Upload Product Display Photo *</label>
            <input type="file" name="product_image" required accept="image/*" style="width:100%; padding:8px 0;">
            <small style="display:block; color:var(--grey-mid); margin-top:4px; font-size:0.78rem;">Files are saved safely inside your assets directory architecture.</small>
          </div>

          <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-weight:700;">
            <i class="fas fa-save"></i> Save Product To Database
          </button>
        </form>
      </div>

      <div style="background: var(--white); padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border-top: 4px solid var(--black);">
        <h3 style="font-family: 'Poppins', sans-serif; font-size: 1.3rem; font-weight: 700; color: var(--black); margin-bottom: 20px;">
          <i class="fas fa-sliders-h" style="color: var(--black); margin-right: 6px;"></i> Append Sub-Variant Type
        </h3>

        <form action="admin_add_accessory.php" method="POST" enctype="multipart/form-data">
          <input type="hidden" name="action_type" value="add_type">

          <div class="adm-field" style="margin-bottom: 18px;">
            <label style="display:block; font-weight:600; font-size:0.88rem; margin-bottom:6px; color:var(--grey-dark);">Select Parent Product Item *</label>
            <select name="product_id" required style="width:100%; padding:10px 12px; border:1px solid var(--grey-mid); border-radius:4px; background:var(--white);">
              <option value="" disabled selected>-- Choose Product Line --</option>
              <?php foreach ($productsArr as $p): ?>
                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="adm-field" style="margin-bottom: 18px;">
            <label style="display:block; font-weight:600; font-size:0.88rem; margin-bottom:6px; color:var(--grey-dark);">Variant Model Name *</label>
            <input type="text" name="type_name" required placeholder="e.g., HD Dual Lens WiFi Type" style="width:100%; padding:10px 12px; border:1px solid var(--grey-mid); border-radius:4px;">
          </div>

          <div style="background:#f9f9f9; padding:15px; border-radius:6px; border:1px dashed var(--grey-mid); margin-bottom:20px;">
            <p style="font-size:0.8rem; font-weight:700; margin:0 0 10px 0; color:var(--grey-dark); text-transform:uppercase; letter-spacing:0.5px;">Choose ONE specification layout data type:</p>
            
            <div class="adm-field" style="margin-bottom: 12px;">
              <label style="display:block; font-weight:600; font-size:0.82rem; margin-bottom:4px; color:var(--grey-dark);">Option 1: Sub-Variant Specific Photo</label>
              <input type="file" name="type_image" accept="image/*" style="width:100%;">
            </div>

            <div class="adm-field">
              <label style="display:block; font-weight:600; font-size:0.82rem; margin-bottom:4px; color:var(--grey-dark);">Option 2: Text Description Parameter</label>
              <textarea name="type_desc" rows="3" placeholder="Automated loop clip recording layout with G-sensor analytics profiles..." style="width:100%; padding:8px 10px; border:1px solid var(--grey-mid); border-radius:4px; font-size:0.88rem; font-family:sans-serif; resize:vertical;"></textarea>
            </div>
          </div>

          <button type="submit" class="btn" style="width:100%; padding:12px; font-weight:700; background:var(--black); color:var(--white);">
            <i class="fas fa-layer-group"></i> Save Variant Specification
          </button>
        </form>
      </div>

    </div>

    <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--grey-mid);">
      <a href="accessories.php" class="btn btn-white" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px; font-weight:600;">
        <i class="fas fa-eye"></i> View Live Customer Catalog Interface
      </a>
    </div>

  </div>
</main>

<?php include 'includes/footer.php'; ?>