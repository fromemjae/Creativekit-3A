/* ============================================================
   menu.js - DYNAMIC MEGA-MENU GENERATOR
   ============================================================ */

async function renderDynamicMenu() {
    const menuContainer = document.getElementById('dynamic-mega-menu');
    if (!menuContainer) return; // Exit if the container isn't on the page

    try {
        // 1. Fetch Categories (Main Menu Tabs)
        const { data: categories, error: catError } = await supabase
            .from('menu_categories')
            .select('*')
            .order('sort_order', { ascending: true });

        if (catError) throw catError;

        // 2. Fetch Items (Dropdown Links)
        const { data: items, error: itemError } = await supabase
            .from('menu_items')
            .select('*')
            .order('sort_order', { ascending: true });

        if (itemError) throw itemError;

        // 3. Clear the "Loading..." text
        menuContainer.innerHTML = '';

        // 4. Loop through categories and build the HTML
        categories.forEach(category => {
            const dropdownItems = items.filter(item => item.category_id === category.id);
            let htmlString = `<li class="dropdown-parent">`;
            
            if (dropdownItems.length > 0) {
                htmlString += `<a href="${category.link}">${category.title} <i class="fas fa-chevron-down"></i></a>`;
                htmlString += `<ul class="dropdown-content">`;
                dropdownItems.forEach(subItem => {
                    htmlString += `<li><a href="${subItem.link}">${subItem.title}</a></li>`;
                });
                htmlString += `</ul>`;
            } else {
                htmlString += `<a href="${category.link}">${category.title}</a>`;
            }

            htmlString += `</li>`;
            menuContainer.innerHTML += htmlString;
        });

    } catch (error) {
        console.error("Failed to load dynamic menu:", error.message);
        menuContainer.innerHTML = `<li style="color: red;">Menu unavailable</li>`;
    }
}

// Initialize the menu as soon as this specific script loads
document.addEventListener('DOMContentLoaded', () => {
    renderDynamicMenu();
});