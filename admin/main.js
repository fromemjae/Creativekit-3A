/* ==========================================
   Products Dropdown Logic
   ========================================== */
document.addEventListener('DOMContentLoaded', function() {
        const mainSelect = document.getElementById('main_category_select');
        const subSelect = document.getElementById('sub_category_select');
        const typeSelect = document.getElementById('type_select');
        
        if (!mainSelect || !subSelect || !typeSelect) return;

        // REMOVED: PHP block and data-initial-value retrieval.
        // ADDED: Set to empty strings for a pure frontend approach. 
        // If you are building an "Edit" page later, you will populate these via a Fetch API call.
        const initialSubValue = "";
        const initialTypeValue = "";

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
              if (opt.value === initialSubValue && initialSubValue !== "") {
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
              if (opt.value === initialTypeValue && initialTypeValue !== "") {
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

/* ==========================================
   Admin Deletion Logic
   ========================================== */
document.addEventListener("DOMContentLoaded", function() {
  
  const deleteButtons = document.querySelectorAll('.delete-admin-btn');
  
  deleteButtons.forEach(button => {
    button.addEventListener('click', function(event) {
      const isConfirmed = confirm('Are you absolutely certain you want to permanently revoke all admin privileges? This action drops their account data immediately.');
      
      // CHANGED: Updated comment. Prevents the anchor tag from navigating to your HTML/API endpoint if cancelled.
      if (!isConfirmed) {
        event.preventDefault(); 
      }
    });
  });

});