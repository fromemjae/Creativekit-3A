

/* ==========================================
   Extracted from header.php
   ========================================== */
function toggleSidebarMenu() {
      const sidebar = document.getElementById('sidenav');
      const overlay = document.querySelector('.sidenav-overlay');
      if (sidebar && overlay) {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
      }
    }
