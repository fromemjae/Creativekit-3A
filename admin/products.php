<?php
// ============================================================
// SELF-CONTAINED INLINE PRODUCTS DASHBOARD (WITH FILE UPLOAD)
// admin/products.php
// ============================================================
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$success = $error = '';

// DELETE product logic
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id = $id");
    $success = 'Product deleted successfully.';
}

// ADD / EDIT product processing engine
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    
    // ============================================================
    // LOUD UPDATE COMMENTS: CAPTURING THE DEEPEST SELECTED HIERARCHY ID
    // Check if the deepest level 'type_id' is set, if not fall back to 
    // 'sub_category_id', and finally 'main_category_id'. This makes sure
    // your products are always attached to the most specific tier chosen.
    // ============================================================
    /* */
    $category_id = (int)($_POST['type_id'] ?? 0);
    if ($category_id === 0) {
        $category_id = (int)($_POST['sub_category_id'] ?? 0);
    }
    if ($category_id === 0 && isset($_POST['main_category_id'])) {
        $category_id = (int)$_POST['main_category_id'];
    }
    /* */
    
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $status      = $_POST['status'] ?? 'active';
    $slug        = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    
    // Default image value
    $image_path = trim($_POST['existing_image'] ?? '');

    // ============================================================
    // LOUD UPDATE COMMENTS: BINARY UPLOAD ARCHITECTURE INTERCEPT
    // Checks if a fresh file stream was submitted via the browser form.
    // If true, reads binary content and handles directory mapping paths.
    // ============================================================
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName    = time() . '_' . basename($_FILES['product_image']['name']);
        $uploadDir   = '../assets/';
        $destination = $uploadDir . $fileName;

        if (move_uploaded_file($fileTmpPath, $destination)) {
            $image_path = 'assets/' . $fileName;
        } else {
            $error = 'Failed to save upload image file to local assets directory.';
        }
    }

    if (empty($name) || empty($price) || !$category_id) {
        $error = 'Product name, price, and category classification parameters are mandatory.';
    } elseif (empty($error)) {
        if ($id) {
            $stmt = mysqli_prepare($conn, "UPDATE products SET category_id = ?, name = ?, slug = ?, description = ?, price = ?, image = ?, status = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'isssdssi', $category_id, $name, $slug, $description, $price, $image_path, $status, $id);
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Product record parameters modified and updated successfully.';
            } else {
                $error = 'Database statement execution failed during product update.';
            }
            mysqli_stmt_close($stmt);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO products (category_id, name, slug, description, price, image, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'isssdss', $category_id, $name, $slug, $description, $price, $image_path, $status);
            if (mysqli_stmt_execute($stmt)) {
                $success = 'New product entry appended to database master inventory registry.';
            } else {
                $error = 'Database statement execution failed during item initialization.';
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Fetch editing target row data properties if edit flag is passed
$edit_product = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
    $edit_product = mysqli_fetch_assoc($res);
}

// Fetch all registered data collections to populate master dashboard tracking layouts
$products = mysqli_query($conn, "SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inventory Master Control Panel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="admin.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="adm-container">
  <div class="adm-card">
    
    <div class="admin-elem-p2-13">
      <a href="dashboard.php" class="admin-elem-p2-14">
        <i class="fas fa-arrow-left"></i> Return to Main Admin Dashboard
      </a>
    </div>

    <div class="adm-header">
      <h2>Unified Master Products Manager</h2>
      <a href="?add=1" class="btn"><i class="fas fa-plus"></i> Add New Product</a>
    </div>

    <?php if ($success): ?><div class="admin-elem-p2-15"><?= $success ?></div><?php endif; ?>
    <?php if ($error): ?><div class="admin-elem-p2-16"><?= $error ?></div><?php endif; ?>

    <?php if (isset($_GET['add']) || $edit_product): ?>
    <div class="admin-elem-p2-17">
      <div class="admin-elem-p2-18">
        <h3 class="admin-elem-p2-19"><?= $edit_product ? 'Modify Inventory Profile Properties' : 'Create Brand New Multi-tier Product Entry' ?></h3>
      </div>
      
      <form method="POST" action="products.php" enctype="multipart/form-data" class="admin-elem-p2-20">
        <?php if ($edit_product): ?>
          <input type="hidden" name="id" value="<?= $edit_product['id'] ?>">
          <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_product['image'] ?? '') ?>">
        <?php endif; ?>

        <div class="adm-field">
          <label>Product Title Name *</label>
          <input type="text" name="name" required value="<?= htmlspecialchars($edit_product['name'] ?? '') ?>">
        </div>

        <div class="adm-field">
          <label>Target Inventory Category Section *</label>
          <select id="main_category_select" name="main_category_id" required>
            <option value="">Select Primary Classification</option>
            <?php
            $main_cats = mysqli_query($conn, "SELECT * FROM categories WHERE parent_id IS NULL ORDER BY id ASC");
            while ($cat = mysqli_fetch_assoc($main_cats)):
                $selected = '';
                if ($edit_product) {
                    $trace_id = $edit_product['category_id'];
                    while ($trace_id > 0) {
                        $trace_q = mysqli_query($conn, "SELECT id, parent_id FROM categories WHERE id = $trace_id");
                        if ($trace_row = mysqli_fetch_assoc($trace_q)) {
                            if (is_null($trace_row['parent_id']) && $trace_row['id'] == $cat['id']) {
                                $selected = 'selected';
                                break;
                            }
                            $trace_id = (int)$trace_row['parent_id'];
                        } else {
                            break;
                        }
                    }
                }
            ?>
              <option value="<?= $cat['id'] ?>" <?= $selected ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="adm-field" id="sub_category_container">
          <label>Inventory Sub-Category Section *</label>
          <select id="sub_category_select" name="sub_category_id" required>
            <option value="">-- Please select a Main Category first --</option>
            <?php
            $sub_cats = mysqli_query($conn, "SELECT c1.* FROM categories c1 JOIN categories c2 ON c1.parent_id = c2.id WHERE c2.parent_id IS NULL ORDER BY c1.name ASC");
            while ($sub = mysqli_fetch_assoc($sub_cats)):
                $selected = '';
                if ($edit_product) {
                    $trace_id = $edit_product['category_id'];
                    while ($trace_id > 0) {
                        $trace_q = mysqli_query($conn, "SELECT id, parent_id FROM categories WHERE id = $trace_id");
                        if ($trace_row = mysqli_fetch_assoc($trace_q)) {
                            if ($trace_row['id'] == $sub['id']) {
                                $selected = 'selected';
                                break;
                            }
                            $trace_id = (int)$trace_row['parent_id'];
                        } else {
                            break;
                        }
                    }
                }
            ?>
              <option value="<?= $sub['id'] ?>" data-parent="<?= $sub['parent_id'] ?>" <?= $selected ?>><?= htmlspecialchars($sub['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="adm-field" id="type_container">
          <label>Specific Type Section</label>
          <select id="type_select" name="type_id">
            <option value="">-- Please select a Sub-Category first --</option>
            <?php
            $type_cats = mysqli_query($conn, "SELECT c1.* FROM categories c1 JOIN categories c2 ON c1.parent_id = c2.id WHERE c2.parent_id IS NOT NULL ORDER BY c1.name ASC");
            while ($type = mysqli_fetch_assoc($type_cats)):
                $selected = ($edit_product && $edit_product['category_id'] == $type['id']) ? 'selected' : '';
            ?>
              <option value="<?= $type['id'] ?>" data-parent="<?= $type['parent_id'] ?>" <?= $selected ?>><?= htmlspecialchars($type['name']) ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="adm-field">
          <label>Base Price Value Rate (₱) *</label>
          <input type="number" name="price" step="0.01" required value="<?= $edit_product['price'] ?? '' ?>">
        </div>

        <div class="adm-field">
          <label>Product Binary Image Upload</label>
          <div class="adm-upload-box">
            <i class="fas fa-cloud-upload-alt"></i>
            <span>Select or drop a photo file directly here</span>
            <input type="file" name="product_image" accept="image/*" class="admin-elem-p2-21">
          </div>
          <?php if ($edit_product && !empty($edit_product['image'])): ?>
            <p class="admin-elem-p2-22">Current Asset: <code><?= htmlspecialchars($edit_product['image']) ?></code></p>
          <?php endif; ?>
        </div>

        <div class="adm-field">
          <label>Public Visibility Accessibility Status</label>
          <select name="status">
            <option value="active" <?= ($edit_product && $edit_product['status'] === 'active') ? 'selected' : '' ?>>Active / Visible Online</option>
            <option value="inactive" <?= ($edit_product && $edit_product['status'] === 'inactive') ? 'selected' : '' ?>>Inactive / Hidden Row</option>
          </select>
        </div>

        <div class="adm-field">
          <label>Technical Product Description</label>
          <textarea name="description" rows="4"><?= htmlspecialchars($edit_product['description'] ?? '') ?></textarea>
        </div>

        <button type="submit" class="btn">Commit Configurations</button>
        <a href="products.php" class="btn btn-secondary">Discard Changes</a>
      </form>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const mainSelect = document.getElementById('main_category_select');
        const subSelect = document.getElementById('sub_category_select');
        const typeSelect = document.getElementById('type_select');
        
        if (!mainSelect || !subSelect || !typeSelect) return;

        const initialSubValue = "<?php
          if ($edit_product) {
              $trace_id = $edit_product['category_id'];
              while ($trace_id > 0) {
                  $q = mysqli_query($conn, "SELECT id, parent_id FROM categories WHERE id = $trace_id");
                  if ($r = mysqli_fetch_assoc($q)) {
                      $pq = mysqli_query($conn, "SELECT parent_id FROM categories WHERE id = {$r['parent_id']}");
                      if ($pr = mysqli_fetch_assoc($pq)) {
                          if (is_null($pr['parent_id'])) { echo $r['id']; break; }
                      }
                      $trace_id = (int)$r['parent_id'];
                  } else { break; }
              }
          }
        ?>";

        const initialTypeValue = "<?= ($edit_product) ? $edit_product['category_id'] : '' ?>";

        const allSubOptions = Array.from(subSelect.options).filter(opt => opt.value !== "");
        const allTypeOptions = Array.from(typeSelect.options).filter(opt => opt.value !== "");

        function filterSubCategories() {
          const selectedParentId = mainSelect.value;
          subSelect.innerHTML = '';
          typeSelect.innerHTML = '';
          
          const typePlaceholder = document.createElement('option');
          typePlaceholder.value = '';
          typePlaceholder.textContent = '-- Please select a Sub-Category first --';
          typeSelect.appendChild(typePlaceholder);

          if (!selectedParentId) {
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Please select a Main Category first --';
            subSelect.appendChild(defaultOpt);
            return;
          }

          const matchedSubs = allSubOptions.filter(opt => opt.getAttribute('data-parent') === selectedParentId);

          if (matchedSubs.length === 0) {
            const noOpt = document.createElement('option');
            noOpt.value = '';
            noOpt.textContent = 'No Sub-categories Configured';
            subSelect.appendChild(noOpt);
          } else {
            const subPlaceholder = document.createElement('option');
            subPlaceholder.value = '';
            subPlaceholder.textContent = 'Select Sub-Classification Parameter';
            subSelect.appendChild(subPlaceholder);
            
            matchedSubs.forEach(opt => {
              if (opt.value === initialSubValue) {
                opt.selected = true;
              }
              subSelect.appendChild(opt);
            });
          }
          filterSpecificTypes();
        }

        function filterSpecificTypes() {
          const selectedSubId = subSelect.value;
          typeSelect.innerHTML = '';

          if (!selectedSubId) {
            const defaultOpt = document.createElement('option');
            defaultOpt.value = '';
            defaultOpt.textContent = '-- Please select a Sub-Category first --';
            typeSelect.appendChild(defaultOpt);
            return;
          }

          const matchedTypes = allTypeOptions.filter(opt => opt.getAttribute('data-parent') === selectedSubId);

          if (matchedTypes.length === 0) {
            const noOpt = document.createElement('option');
            noOpt.value = '';
            noOpt.textContent = 'General Classification Only (No deep types)';
            typeSelect.appendChild(noOpt);
          } else {
            const selectPlaceholder = document.createElement('option');
            selectPlaceholder.value = '';
            selectPlaceholder.textContent = 'Select Specific Type Assignment';
            typeSelect.appendChild(selectPlaceholder);
            
            matchedTypes.forEach(opt => {
              if (opt.value === initialTypeValue) {
                opt.selected = true;
              }
              typeSelect.appendChild(opt);
            });
          }
        }

        mainSelect.addEventListener('change', filterSubCategories);
        subSelect.addEventListener('change', filterSpecificTypes);
        
        if (mainSelect.value !== "") {
          filterSubCategories();
        }
      });
    </script>
    <?php endif; ?>

    <?php if (!isset($_GET['add'])): ?>
    <div class="admin-elem-p2-23">
      <table class="adm-table">
        <thead>
          <tr>
            <th>Thumbnail</th>
            <th>Product Identity Label</th>
            <th>Assigned Core Allocation Hierarchy</th>
            <th>Base Price</th>
            <th>Status</th>
            <th class="admin-elem-p2-24">Controls</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($p = mysqli_fetch_assoc($products)): ?>
          <tr>
            <td>
              <?php if (!empty($p['image'])): ?>
                <img src="../<?= htmlspecialchars($p['image']) ?>?t=<?= time() ?>" style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #e2e8f0; padding: 2px; background: #fff; border-radius: 4px;" alt="Thumb">
              <?php else: ?>
                <img src="../assets/default-placeholder.png" class="admin-elem-p2-25" alt="Default Thumb">
              <?php endif; ?>
            </td>
            <td class="admin-elem-p2-26"><?= htmlspecialchars($p['name']) ?></td>
            
            
            <td>
              <span class="admin-elem-p2-27">
                <?php 
                $trail = [];
                $trace_id = (int)$p['category_id'];
                
                // Keep moving up until we run out of parent rows
                while ($trace_id > 0) {
                    $trace_q = mysqli_query($conn, "SELECT name, parent_id FROM categories WHERE id = $trace_id");
                    if ($trace_q && $trace_row = mysqli_fetch_assoc($trace_q)) {
                        array_unshift($trail, htmlspecialchars($trace_row['name']));
                        $trace_id = (int)$trace_row['parent_id'];
                    } else {
                        break;
                    }
                }
                
                // Output the complete trail separated by arrows
                if (!empty($trail)) {
                    echo implode(' <i class="fas fa-long-arrow-alt-right admin-elem-p2-28"></i> ', $trail);
                } else {
                    echo 'Unassigned Primary Section';
                }
                ?>
              </span>
            </td>
            
            
            <td class="admin-elem-p2-29">₱<?= number_format($p['price'], 2) ?></td>
            <td>
              <?php if ($p['status'] === 'active'): ?>
                <span class="badge badge-active">Active</span>
              <?php else: ?>
                <span class="badge badge-inactive">Inactive</span>
              <?php endif; ?>
            </td>

            <td class="admin-elem-p2-30">
              <a href="?edit=<?= $p['id'] ?>" class="btn btn-normal btn-sm"><i class="fas fa-edit"></i> Modify</a>
              <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you completely confident you want to drop this dynamic inventory entry from your database tables completely?')"><i class="fas fa-trash"></i> Drop</a>
            </td>

          </tr>
          <?php endwhile; ?>
          <?php if (mysqli_num_rows($products) === 0): ?>
          <tr>
            <td colspan="6" class="admin-elem-p2-31">
              No product items have been registered inside the database catalog yet.
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

  </div>
</div>

</body>
</html>