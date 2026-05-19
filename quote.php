<?php $pageTitle = "Request a Quote | CreativeKit 3A"; ?>

<?php 
include 'includes/header.php'; 
require_once 'includes/db.php';

// ============================================================
// MULTI-LEVEL HIERARCHY DATA EXTRACTION
// Extracts categories, subcategories, and types directly out of your
// database to map out a 3-level tree structure array for the dropdowns.
// ============================================================
$allCategoriesData = [];

// Step 1: Grab Level 1 Parent Categories
$lvl1Query = mysqli_query($conn, "SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
if ($lvl1Query) {
    while ($lvl1 = mysqli_fetch_assoc($lvl1Query)) {
        $parent_id = $lvl1['id'];
        $allCategoriesData[$parent_id] = [
            'name' => $lvl1['name'],
            'subcategories' => []
        ];

        // Step 2: Grab Level 2 Sub-Categories matching this parent ID
        $lvl2Query = mysqli_query($conn, "SELECT id, name FROM categories WHERE parent_id = $parent_id ORDER BY name ASC");
        if ($lvl2Query) {
            while ($lvl2 = mysqli_fetch_assoc($lvl2Query)) {
                $sub_id = $lvl2['id'];
                $allCategoriesData[$parent_id]['subcategories'][$sub_id] = [
                    'name' => $lvl2['name'],
                    'types' => []
                ];

                // Step 3: Grab Level 3 Types matching this sub-category ID
                $lvl3Query = mysqli_query($conn, "SELECT id, name FROM categories WHERE parent_id = $sub_id ORDER BY name ASC");
                if ($lvl3Query) {
                    while ($lvl3 = mysqli_fetch_assoc($lvl3Query)) {
                        $type_id = $lvl3['id'];
                        $allCategoriesData[$parent_id]['subcategories'][$sub_id]['types'][$type_id] = $lvl3['name'];
                    }
                }
            }
        }
    }
}
?>

<main class="quote-main">
  <div class="container quote-container">
    
    <div class="quote-header">
      <h1 class="quote-title">Request A Quotation</h1>
      <p class="quote-subtitle">
        Tell us about your corporate giveaway needs, custom branding parameters, and timeline. Our marketing team will compile an official proposal strategy for you.
      </p>
    </div>

    <div class="quote-card">
      
      <div id="quote-error-box" class="lm-msg lm-msg--error quote-error-box" hidden></div>
      <div id="quote-success-box" class="lm-msg lm-msg--success quote-success-box" hidden></div>

      <form id="quoteFormElement" onsubmit="transmitQuotationProposal(event)">
        
        <div class="quote-grid-2">
          <div>
            <label class="quote-label">Full Name <span class="quote-req">*</span></label>
            <input type="text" id="q_name" class="quote-input" required value="<?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '' ?>">
          </div>
          <div>
            <label class="quote-label">Email Address <span class="quote-req">*</span></label>
            <input type="email" id="q_email" class="quote-input" required value="<?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : '' ?>">
          </div>
        </div>

        <div class="quote-grid-2">
          <div>
            <label class="quote-label">Contact Number</label>
            <input type="text" id="q_phone" class="quote-input" placeholder="e.g. +63 917 123 4567">
          </div>
          <div>
            <label class="quote-label">Target Order Quantity <span class="quote-req">*</span></label>
            <input type="number" id="q_qty" class="quote-input" min="1" required placeholder="Enter volume size">
          </div>
        </div>

        <div class="quote-grid-3">
          <div>
            <label class="quote-label">Category (Lvl 1) <span class="quote-req">*</span></label>
            <select id="q_lvl1" class="quote-input" required onchange="updateLvl2Dropdown()">
              <option value="">-- Select Category --</option>
              <?php foreach ($allCategoriesData as $id => $cat): ?>
                <option value="<?= $id ?>"><?= htmlspecialchars($cat['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="quote-label">Sub-Category (Lvl 2)</label>
            <select id="q_lvl2" class="quote-input" onchange="updateLvl3Dropdown()" disabled>
              <option value="">-- Choose Category First --</option>
            </select>
          </div>
          <div>
            <label class="quote-label">Product Type (Lvl 3)</label>
            <select id="q_lvl3" class="quote-input" disabled>
              <option value="">-- Choose Sub-Cat First --</option>
            </select>
          </div>
        </div>

        <div class="quote-group">
          <label class="quote-label">Preferred Handover Target Delivery Date</label>
          <input type="date" id="q_deadline" class="quote-input" min="<?= date('Y-m-d', strtotime('+7 days')) ?>">
        </div>

        <div class="quote-group-large">
          <span class="quote-label">Corporate Logo / Design Brand Asset (Optional)</span>
          
          <label for="q_logo" class="quote-dropzone">
            <i class="fas fa-cloud-upload-alt quote-dropzone-icon"></i>
            <span id="logo-field-display-text" class="quote-dropzone-text">Click or Drop your design file here</span>
          </label>
          
          <input type="file" id="q_logo" class="quote-file-input" accept="image/*,.pdf" onchange="displaySelectedFilename()">
          
          <small class="quote-help-text">Accepted formats: PNG, JPG, JPEG, or PDF assets (Max size parameters apply).</small>
        </div>

        <div class="quote-group-large">
          <label class="quote-label">Custom Print &amp; Specifications Requests</label>
          <textarea id="q_notes" class="quote-input" rows="5" placeholder="Describe layout specifications: custom color match settings, laser engravings criteria, text placements, embroidery threads options..."></textarea>
        </div>

        <button type="submit" id="submitQuoteFormBtn" class="quote-submit-btn">
          Dispatch Corporate Quote Request
        </button>

      </form>
    </div>

  </div>
</main>

<script>
const categoryHierarchyTree = <?php echo json_encode($allCategoriesData); ?>;

function displaySelectedFilename() {
    const inputEl = document.getElementById('q_logo');
    const labelText = document.getElementById('logo-field-display-text');
    if (inputEl.files.length > 0) {
        labelText.textContent = "📄 Selected asset: " + inputEl.files[0].name;
        labelText.classList.add('asset-selected');
    } else {
        labelText.textContent = "Click or Drop your design file here";
        labelText.classList.remove('asset-selected');
    }
}

function updateLvl2Dropdown() {
  const lvl1Select = document.getElementById('q_lvl1');
  const lvl2Select = document.getElementById('q_lvl2');
  const lvl3Select = document.getElementById('q_lvl3');
  
  const parentId = lvl1Select.value;
  
  lvl2Select.innerHTML = '<option value="">-- Select Sub-Category --</option>';
  lvl3Select.innerHTML = '<option value="">-- Choose Sub-Cat First --</option>';
  lvl2Select.disabled = true;
  lvl3Select.disabled = true;

  if (parentId && categoryHierarchyTree[parentId]) {
    const subcats = categoryHierarchyTree[parentId]['subcategories'];
    let hasData = false;
    
    for (const subId in subcats) {
      hasData = true;
      const opt = document.createElement('option');
      opt.value = subId;
      opt.textContent = subcats[subId]['name'];
      lvl2Select.appendChild(opt);
    }
    
    if (hasData) {
      lvl2Select.disabled = false;
    }
  }
}

function updateLvl3Dropdown() {
  const lvl1Select = document.getElementById('q_lvl1');
  const lvl2Select = document.getElementById('q_lvl2');
  const lvl3Select = document.getElementById('q_lvl3');
  
  const parentId = lvl1Select.value;
  const subId = lvl2Select.value;
  
  lvl3Select.innerHTML = '<option value="">-- Select Product Type --</option>';
  lvl3Select.disabled = true;

  if (parentId && subId && categoryHierarchyTree[parentId]['subcategories'][subId]) {
    const types = categoryHierarchyTree[parentId]['subcategories'][subId]['types'];
    let hasData = false;
    
    for (const typeId in types) {
      hasData = true;
      const opt = document.createElement('option');
      opt.value = typeId;
      opt.textContent = types[typeId];
      lvl3Select.appendChild(opt);
    }
    
    if (hasData) {
      lvl3Select.disabled = false;
    }
  }
}

async function transmitQuotationProposal(event) {
  event.preventDefault();
  
  const activeBtn = document.getElementById('submitQuoteFormBtn');
  const errAlert = document.getElementById('quote-error-box');
  const succAlert = document.getElementById('quote-success-box');
  
  errAlert.hidden = true;
  succAlert.hidden = true;

  const l1 = document.getElementById('q_lvl1');
  const l2 = document.getElementById('q_lvl2');
  const l3 = document.getElementById('q_lvl3');

  let chosenItemName = "";
  if (l1.value && categoryHierarchyTree[l1.value]) {
      chosenItemName += categoryHierarchyTree[l1.value]['name'];
      if (l2.value && categoryHierarchyTree[l1.value]['subcategories'][l2.value]) {
          chosenItemName += " > " + categoryHierarchyTree[l1.value]['subcategories'][l2.value]['name'];
          if (l3.value && categoryHierarchyTree[l1.value]['subcategories'][l2.value]['types'][l3.value]) {
              chosenItemName += " > " + categoryHierarchyTree[l1.value]['subcategories'][l2.value]['types'][l3.value];
          }
      }
  }

  const formDataPayload = new FormData();
  formDataPayload.append('full_name', document.getElementById('q_name').value.trim());
  formDataPayload.append('email', document.getElementById('q_email').value.trim());
  formDataPayload.append('phone', document.getElementById('q_phone').value.trim());
  formDataPayload.append('item_type', chosenItemName);
  formDataPayload.append('quantity', parseInt(document.getElementById('q_qty').value) || 0);
  formDataPayload.append('deadline', document.getElementById('q_deadline').value);
  formDataPayload.append('custom_notes', document.getElementById('q_notes').value.trim());

  const fileSelectorElement = document.getElementById('q_logo');
  if (fileSelectorElement.files.length > 0) {
      formDataPayload.append('logo_file', fileSelectorElement.files[0]);
  }

  activeBtn.disabled = true;
  const initialHtmlStr = activeBtn.innerHTML;
  
  activeBtn.innerHTML = '<span class="quote-btn-spinner"></span> Transmitting Proposal Criteria...';

  try {
    const rawRes = await fetch('includes/quote_process.php', {
      method: 'POST',
      body: formDataPayload
    });

    const parsedJson = await rawRes.json();

    if (rawRes.ok && parsedJson.success) {
      succAlert.innerText = "🎉 " + parsedJson.message;
      succAlert.hidden = false;
      document.getElementById('quoteFormElement').reset();
      
      l2.innerHTML = '<option value="">-- Choose Category First --</option>';
      l3.innerHTML = '<option value="">-- Choose Sub-Cat First --</option>';
      l2.disabled = true;
      l3.disabled = true;
      
      document.getElementById('logo-field-display-text').textContent = "Click or Drop your design file here";
      document.getElementById('logo-field-display-text').classList.remove('asset-selected');

      window.scrollTo({ top: succAlert.offsetTop - 120, behavior: 'smooth' });
    } else {
      errAlert.innerText = "⚠️ " + (parsedJson.message || "An unhandled processing error occurred.");
      errAlert.hidden = false;
    }
  } catch (netErr) {
    console.error('[Quote Pipeline Fault]', netErr);
    errAlert.innerText = "⚠️ Network connection processing breakdown. Check connection...";
    errAlert.hidden = false;
  } finally {
    activeBtn.disabled = false;
    activeBtn.innerHTML = initialHtmlStr;
  }
}
</script>

<?php include 'includes/footer.php'; ?>
</html>