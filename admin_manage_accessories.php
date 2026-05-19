<?php
// ============================================================
// SELF-CONTAINED ACCESSORIES INVENTORY CONTROL DASHBOARD
// admin_manage_accessories.php
// ============================================================
session_start();
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
requireAdmin();

$success = $error = '';

// Handle removal drops requests
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM accessory_products WHERE id = $id");
    $success = 'Accessory removed from the database registry successfully.';
}

// Handle data changes submissions (POST edits)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id          = (int)($_POST['id'] ?? 0);
    $name        = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $price       = trim($_POST['price'] ?? '');
    $slug        = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $name));
    $image_path  = trim($_POST['existing_image'] ?? 'assets/default-placeholder.png');

    // Drag-and-Drop Binary upload management pipeline
    if (isset($_FILES['accessory_file']) && $_FILES['accessory_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['accessory_file']['tmp_name'];
        $fileName    = time() . '_' . basename($_FILES['accessory_file']['name']);
        $destination = 'assets/' . $fileName;

        if (move_uploaded_file($fileTmpPath, $destination)) {
            $image_path = $destination;
        } else {
            $error = 'File transfer system write failure error.';
        }
    }

    if (empty($name) || empty($price)) {
        $error = 'All fields marked with an asterisk are required.';
    } elseif (empty($error)) {
        if ($id) {
            $stmt = mysqli_prepare($conn, "UPDATE accessory_products SET category_id = ?, name = ?, slug = ?, price = ?, img = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'issssi', $category_id, $name, $slug, $price, $image_path, $id);
            if (mysqli_stmt_execute($stmt)) {
                $success = 'Accessory item configurations successfully saved!';
            } else {
                $error = 'SQL Runtime Processing Error: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

$edit_item = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = mysqli_query($conn, "SELECT * FROM accessory_products WHERE id = $id");
    $edit_item = mysqli_fetch_assoc($res);
}

// Read records out of your active XAMPP tables
$categories_res = mysqli_query($conn, "SELECT * FROM accessory_categories ORDER BY title ASC");
$products_res   = mysqli_query($conn, "
    SELECT p.*, c.title as cat_title 
    FROM accessory_products p 
    JOIN accessory_categories c ON p.category_id = c.id 
    ORDER BY c.title ASC, p.name ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Accessories Inventory | CreativeKit3A</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="margin: 0; padding: 0; font-family: 'Poppins', sans-serif; background: #f4f6f9; min-height: 100vh;">

  <div style="padding: 40px; box-sizing: border-box;">
    <div style="max-width: 1200px; margin: 0 auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); border: 1px solid #edf2f7;">
      
      <div style="margin-bottom: 20px;">
        <a href="index.php" style="text-decoration: none; color: #ff6b00; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; font-size: 0.9rem;">
          <i class="fas fa-arrow-left"></i> Exit to Public Live Homepage View
        </a>
      </div>

      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
        <h2 style="margin: 0; font-size: 1.8rem; color: #1e293b; font-weight: 700;">Accessories &amp; Gadgets Manager</h2>
        <a href="admin_add_accessory.php" style="background: #ff6b00; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 8px;"><i class="fas fa-plus-circle"></i> Create New Item</a>
      </div>

      <?php if ($success): ?><div style="padding: 14px 18px; background: #dcfce7; color: #15803d; border-left: 5px solid #16a34a; font-weight: 600; border-radius: 4px; margin-bottom: 25px; font-size: 0.92rem;"><?= $success ?></div><?php endif; ?>
      <?php if ($error): ?><div style="padding: 14px 18px; background: #fee2e2; color: #b91c1c; border-left: 5px solid #dc2626; font-weight: 600; border-radius: 4px; margin-bottom: 25px; font-size: 0.92rem;"><?= $error ?></div><?php endif; ?>

      <?php if ($edit_item): ?>
      <div style="background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; max-width: 750px; margin-bottom: 40px; overflow: hidden;">
        <div style="padding: 16px 24px; border-bottom: 1px solid #edf2f7; background: #f1f5f9;">
          <h3 style="margin: 0; font-size: 1.15rem; color: #0f172a; font-weight: 700;"><i class="fas fa-edit" style="color:#ff6b00;"></i> Modify Accessory Specifications</h3>
        </div>
        <form method="POST" action="admin_manage_accessories.php" enctype="multipart/form-data" style="padding: 24px; box-sizing: border-box;">
          <input type="hidden" name="id" value="<?= $edit_item['id'] ?>">
          <input type="hidden" name="existing_image" value="<?= htmlspecialchars($edit_item['img']) ?>">

          <div style="margin-bottom: 18px;">
            <label style="display:block; font-weight:600; margin-bottom:6px; font-size:0.9rem; color:#334155;">Accessory Title Name *</label>
            <input type="text" name="name" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box; font-size:0.92rem;" required value="<?= htmlspecialchars($edit_item['name']) ?>">
          </div>

          <div style="margin-bottom: 18px;">
            <label style="display:block; font-weight:600; margin-bottom:6px; font-size:0.9rem; color:#334155;">Change Parent Category Assignment *</label>
            <select name="category_id" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box; font-size:0.92rem; background:#fff;" required>
              <?php mysqli_data_seek($categories_res, 0); ?>
              <?php while ($cat = mysqli_fetch_assoc($categories_res)): ?>
                <option value="<?= $cat['id'] ?>" <?= $edit_item['category_id'] == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['title']) ?></option>
              <?php endwhile; ?>
            </select>
          </div>

          <div style="margin-bottom: 18px;">
            <label style="display:block; font-weight:600; margin-bottom:6px; font-size:0.9rem; color:#334155;">Price Presentation Text *</label>
            <input type="text" name="price" style="width:100%; padding:10px; border:1px solid #cbd5e1; border-radius:6px; box-sizing:border-box; font-size:0.92rem;" required value="<?= htmlspecialchars($edit_item['price']) ?>">
          </div>

          <div style="margin-bottom: 25px;">
            <label style="display:block; font-weight:600; margin-bottom:6px; font-size:0.9rem; color:#334155;">Upload Display Picture File Asset</label>
            <div style="padding: 20px; border: 2px dashed #cbd5e1; background: #fff; border-radius: 6px; text-align: center;">
              <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #ff6b00; margin-bottom: 8px;"></i>
              <span style="display: block; font-size: 0.85rem; color: #64748b; margin-bottom: 10px;">Drag or browse photo asset binaries</span>
              <input type="file" name="accessory_file" accept="image/*" style="font-size: 0.88rem; max-width: 250px;">
            </div>
          </div>

          <button type="submit" style="background: #ff6b00; color: #fff; border: none; padding: 12px 24px; border-radius: 6px; font-weight: 700; font-size: 0.92rem; cursor: pointer;">Apply Specifications Changes</button>
          <a href="admin_manage_accessories.php" style="text-decoration: none; margin-left: 12px; padding: 11px 22px; background: #cbd5e1; color: #1e293b; border-radius: 6px; font-weight: 600; font-size: 0.92rem; display: inline-block;">Cancel</a>
        </form>
      </div>
      <?php endif; ?>

      <div style="background: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="padding: 18px 24px; border-bottom: 1px solid #e2e8f0; background: #fafafa;">
          <h3 style="margin: 0; font-size: 1.1rem; color: #1e293b; font-weight: 700;">Live Accessories Database Inventory Matrix</h3>
        </div>
        <div style="overflow-x: auto;">
          <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
            <thead>
              <tr style="background: #f8fafc; color: #475569; font-weight: 700; border-bottom: 2px solid #cbd5e1;">
                <th style="padding: 16px 20px; width: 80px;">Thumbnail</th>
                <th style="padding: 16px 20px;">Accessory Item Name</th>
                <th style="padding: 16px 20px;">Parent Category</th>
                <th style="padding: 16px 20px;">Price Presentation</th>
                <th style="padding: 16px 20px; text-align: center; width: 200px;">Actions Controls</th>
              </tr>
            </thead>
            <tbody>
              <?php while ($p = mysqli_fetch_assoc($products_res)): ?>
              <tr style="border-bottom: 1px solid #f1f5f9;">
                <td style="padding: 14px 20px;">
                  <img src="<?= htmlspecialchars($p['img']) ?>?t=<?= time() ?>" style="width: 50px; height: 50px; object-fit: contain; border: 1px solid #e2e8f0; padding: 2px; background: #fff; border-radius: 4px;" alt="Thumb">
                </td>
                <td style="padding: 14px 20px; font-weight: 600; color: #0f172a;"><?= htmlspecialchars($p['name']) ?></td>
                <td style="padding: 14px 20px;"><span style="background: #eef2f7; color: #475569; padding: 4px 10px; border-radius: 4px; font-size: 0.8rem; font-weight: 600;"><?= htmlspecialchars($p['cat_title']) ?></span></td>
                <td style="padding: 14px 20px; font-weight: 700; color: #ff6b00;"><?= htmlspecialchars($p['price']) ?></td>
                <td style="padding: 14px 20px; text-align: center; white-space: nowrap;">
                  <a href="?edit=<?= $p['id'] ?>" style="background: #f1f5f9; color: #334155; text-decoration: none; padding: 6px 14px; border-radius: 4px; font-weight: 600; font-size: 0.82rem; margin-right: 6px; border: 1px solid #cbd5e1;"><i class="fas fa-edit"></i> Edit Item</a>
                  <a href="?delete=<?= $p['id'] ?>" style="background: #fee2e2; color: #dc2626; text-decoration: none; padding: 6px 14px; border-radius: 4px; font-weight: 600; font-size: 0.82rem; border: 1px solid #fca5a5;" onclick="return confirm('Erase this accessory line permanently?')"><i class="fas fa-trash"></i> Drop</a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

</body>
</html>