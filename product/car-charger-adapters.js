/* ============================================================
   items-page.js
   JavaScript for items-page.html ONLY.

   ADDED: This is a brand new JS file. No existing JS was modified.

   HOW IT WORKS:
   1. Reads URL query params:
        ?cat=accessories-gadgets   → the main category slug
        &subcat=car                → the subcategory key
        &item=car-charger-adapter  → the specific product slug
   2. Looks up the product in ITEMS_DATA (defined below).
   3. Renders:  breadcrumb, hero banner, variant filter tabs, item cards,
                and the "You May Also Like" related products section.

   HOW TO LINK HERE FROM category-page.html:
   In category-page.html, inside the buildCard() function, change
   the card click handler (or the card's data-url) to point to:
     items-page.html?cat=CATEGORY_SLUG&subcat=SUBCAT_KEY&item=ITEM_SLUG

   Example:
     items-page.html?cat=accessories-gadgets&subcat=car&item=car-charger-adapter

   See the comment block at the bottom of this file for the
   exact one-line change needed in category-page.html.
   ============================================================ */

(function () {
  'use strict';

  /* ============================================================
     ITEMS DATA
     Each entry maps to a product card on category-page.html.
     Structure:
       ITEMS_DATA[categorySlug][itemSlug] = {
         name, price, heroImg, desc, eyebrow,
         variantTypes: [ { key, label } ],   // optional filter tabs
         items: [ { id, name, specs, price, img, badge, variantType, url } ]
       }

     HOW TO ADD A NEW SUB-CATEGORY ITEM PAGE:
       1. Add a new entry under the correct category slug.
       2. Give it a unique itemSlug.
       3. In category-page.html buildCard(), point the URL to:
          items-page.html?cat=SLUG&subcat=SUBCAT&item=ITEM_SLUG
  ============================================================ */
  /* ============================================================
     ✏️  CONTENT EDITOR SECTION — EDIT YOUR ITEMS HERE
     ============================================================
     This is the ONLY section you need to touch to change:
       • Page title          → name: 'Your Title'
       • Hero description    → desc: 'Your description text'
       • Hero image          → heroImg: 'path/to/your/image.jpg'
       • Price shown in hero → price: 'As low as ₱XX.00'
       • Eyebrow tag         → eyebrow: 'Category Label'
       • Filter tab labels   → variantTypes: [ { key, label } ]
       • Each item card:
           name   → the item's display name
           specs  → short spec line (shown in grey under name)
           price  → the item's price
           img    → the item's image path or full URL
           badge  → optional label: 'New' | 'Best Seller' | 'Premium' | '' (empty = hidden)
           variantType → must match one of the variantTypes keys above

     HOW TO USE AN IMAGE:
       - Local file in your project:  img: 'assets/my-photo.jpg'
       - Full URL from the web:       img: 'https://example.com/photo.jpg'

     DO NOT edit anything below the "ENGINE — DO NOT EDIT" line.
     ============================================================ */

  const ITEMS_DATA = {

    /* ══════════════════════════════════════════════════════════
       CATEGORY: ACCESSORIES & GADGETS
       (matches category-page slug: 'accessories-gadgets')
    ══════════════════════════════════════════════════════════ */
    'accessories-gadgets': {

      /* ----------------------------------------------------------
         SUB-CATEGORY: Car Charger Adapters
         URL that opens this page:
           items-page.html?cat=accessories-gadgets&subcat=car&item=car-charger-adapter
      ---------------------------------------------------------- */
      'car-charger-adapter': {
        name:    'Car Charger Adapters',          // ← Page title & hero heading
        eyebrow: 'Car Accessories',               // ← Small label above title
        price:   'As low as ₱85.00',             // ← Starting price in hero
        heroImg: '/assets/car-charger-adapters.png',                  // ← Hero banner image (change this!)
        desc:    'Custom-branded car charger adapters — ideal for corporate giveaways, events, and everyday use. Choose from single-port, dual-port, and fast-charge variants.',

        // Filter tabs at the top of the items grid.
        // key must match variantType values in items[] below.
        variantTypes: [
          { key: 'single',   label: 'Single Port' },
          { key: 'dual',     label: 'Dual Port'   },
          
          //{ key: 'fast',     label: 'Fast Charge' },
          //{ key: 'wireless', label: 'Wireless'    },
        ],

        // Individual item cards shown on the page.
        // img: use 'assets/filename.jpg' for local files, or a full https:// URL
        // CHANGED: Each item now has its own img path instead of all sharing 'assets/1.png'.
        //          Replace each img value below with the correct file for that product.
        //          Example: img: 'assets/car-charger-single-usba.jpg'
        //                   img: 'https://yourcdn.com/car-charger-usba.jpg'
        items: [
          { id:1, name:'Single USB-A Car Charger',       specs:'5V / 2.1A • USB-A • Compact',           price:'As low as ₱85.00',   img:'/assets/1.png',    badge:'',            variantType:'single',   url:'/product/car-charger-single-usba/'    },
          { id:2, name:'Single USB-C Car Charger',       specs:'5V / 3A • USB-C • Compact',             price:'As low as ₱95.00',   img:'/assets/usb-c-adapter.png',    badge:'New',         variantType:'single',   url:'/product/car-charger-single-usbc/'    },
          { id:3, name:'Dual Port USB-A Charger',        specs:'2x USB-A • 3.1A Total • Logo Printing', price:'As low as ₱110.00',  img:'/assets/dual-usb-adapter.png',      badge:'Best Seller', variantType:'dual',     url:'/product/car-charger-dual-usba/'      },
          
        ]
      },

      /* ----------------------------------------------------------
         SUB-CATEGORY: Car Organizers & Holders
         URL: items-page.html?cat=accessories-gadgets&subcat=car&item=car-organizers
      ---------------------------------------------------------- */
      'car-organizers': {
        name:    'Car Organizers & Holders',
        eyebrow: 'Car Accessories',
        price:   'As low as ₱120.00',
        heroImg: 'assets/car-organizer.png',      // ← Change to your real image
        desc:    'Keep your car tidy and your brand visible. Choose from seat-back organizers, console trays, cup holders, and more — all customizable with your company logo.',
        variantTypes: [
          { key: 'seatback',  label: 'Seat-Back'  },
          { key: 'console',   label: 'Console'    },
          { key: 'dashboard', label: 'Dashboard'  },
        ],
        items: [
          // CHANGED: Each item now has its own img path. Replace with your actual filenames.
          { id:1, name:'Seat-Back Organizer (5 Pocket)', specs:'Oxford Cloth • 5 Pockets • Fits All Seats', price:'As low as ₱120.00', img:'assets/car-organizer-seatback-5p.jpg',   badge:'Best Seller', variantType:'seatback',  url:'/product/car-organizer-seatback-5p/'   },
          { id:2, name:'Seat-Back Tablet Holder',        specs:'Fits 7"–10" Tablets • Adjustable Strap',   price:'As low as ₱145.00', img:'assets/car-organizer-tablet-holder.jpg', badge:'',            variantType:'seatback',  url:'/product/car-organizer-tablet-holder/' },
          { id:3, name:'Center Console Organizer',       specs:'PU Leather • Armrest Box Insert',           price:'As low as ₱195.00', img:'assets/car-organizer-console.jpg',       badge:'',            variantType:'console',   url:'/product/car-organizer-console/'       },
          { id:4, name:'Cup & Card Holder Insert',       specs:'Fits Standard Cup Holders • Logo Pad',      price:'As low as ₱135.00', img:'assets/car-organizer-cup-card.jpg',      badge:'',            variantType:'console',   url:'/product/car-organizer-cup-card/'      },
          { id:5, name:'Dashboard Phone Mount & Tray',   specs:'Non-Slip Mat • Multi-Device Tray',          price:'As low as ₱110.00', img:'assets/car-organizer-dash-tray.jpg',     badge:'',            variantType:'dashboard', url:'/product/car-organizer-dash-tray/'     },
          { id:6, name:'Magnetic Dashboard Mount',       specs:'360° Rotation • Universal Phone Fit',       price:'As low as ₱125.00', img:'assets/car-organizer-mag-mount.jpg',     badge:'New',         variantType:'dashboard', url:'/product/car-organizer-mag-mount/'     },
        ]
      },

      /* ----------------------------------------------------------
         SUB-CATEGORY: Dash Cams
         URL: items-page.html?cat=accessories-gadgets&subcat=car&item=car-dashcams
      ---------------------------------------------------------- */
      'car-dashcams': {
        name:    'Dash Cams',
        eyebrow: 'Car Accessories',
        price:   'As low as ₱450.00',
        heroImg: 'assets/dash-cam.png',           // ← Change to your real image
        desc:    'Brand your fleet or gift your clients a dash cam. Choose from single-lens, dual-lens, and 360° variants — all customizable with your company logo on the casing.',
        variantTypes: [
          { key: 'single', label: 'Single Lens' },
          { key: 'dual',   label: 'Dual Lens'   },
          { key: '360',    label: '360°'         },
        ],
        items: [
          // CHANGED: Each item now has its own img path. Replace with your actual filenames.
          { id:1, name:'1080p Full HD Dash Cam',        specs:'1080p • 140° FOV • Loop Recording',       price:'As low as ₱450.00',  img:'assets/dashcam-1080p.jpg',    badge:'',        variantType:'single', url:'/product/dashcam-1080p/'         },
          { id:2, name:'2K QHD Night Vision Dash Cam',  specs:'2K • Night Vision • G-Sensor • WDR',      price:'As low as ₱680.00',  img:'assets/dashcam-2k-night.jpg', badge:'Popular', variantType:'single', url:'/product/dashcam-2k-night/'      },
          { id:3, name:'4K Ultra HD Dash Cam',          specs:'4K • Sony Sensor • Parking Mode',         price:'As low as ₱950.00',  img:'assets/dashcam-4k.jpg',       badge:'Premium', variantType:'single', url:'/product/dashcam-4k/'            },
          { id:4, name:'Dual-Lens Front & Rear',        specs:'FHD + FHD • Front & Rear • 170° FOV',    price:'As low as ₱780.00',  img:'assets/dashcam-dual-fhd.jpg', badge:'',        variantType:'dual',   url:'/product/dashcam-dual-fhd/'      },
          { id:5, name:'Dual-Lens 4K + 1080p',         specs:'4K Front + 1080p Rear • GPS Logger',      price:'As low as ₱1,100.00',img:'assets/dashcam-dual-4k.jpg',  badge:'New',     variantType:'dual',   url:'/product/dashcam-dual-4k/'       },
          { id:6, name:'360° Interior & Exterior Cam', specs:'360° Cabin View • IR Night Vision • 4CH', price:'As low as ₱1,350.00',img:'assets/dashcam-360.jpg',      badge:'',        variantType:'360',    url:'/product/dashcam-360/'           },
        ]
      },

      /* ----------------------------------------------------------
         SUB-CATEGORY: Audio Accessories
         URL: items-page.html?cat=accessories-gadgets&subcat=computer&item=computer-audio
      ---------------------------------------------------------- */
      'computer-audio': {
        name:    'Audio Accessories',
        eyebrow: 'Computer Accessories',
        price:   'As low as ₱95.00',
        heroImg: 'assets/audio-accessories.png',              // ← Change to your real image
        desc:    'Custom-branded earphones, headsets, and speakers for corporate giveaways, events, and WFH kits.',
        variantTypes: [
          { key: 'earphone', label: 'Earphones' },
          { key: 'headset',  label: 'Headsets'  },
          { key: 'speaker',  label: 'Speakers'  },
        ],
        items: [
          // CHANGED: Each item now has its own img path. Replace with your actual filenames.
          { id:1, name:'Wired Earphones (3.5mm)',     specs:'3.5mm Jack • In-Ear • Logo Print',      price:'As low as ₱95.00',  img:'assets/audio-earphone-wired.jpg',   badge:'',            variantType:'earphone', url:'/product/audio-earphone-wired/'   },
          { id:2, name:'TWS Wireless Earbuds',        specs:'Bluetooth 5.0 • TWS • Charging Case',   price:'As low as ₱380.00', img:'assets/audio-earphone-tws.jpg',     badge:'Best Seller', variantType:'earphone', url:'/product/audio-earphone-tws/'     },
          { id:3, name:'USB Mono Headset',            specs:'USB • Noise Cancelling Mic • Foldable', price:'As low as ₱180.00', img:'assets/audio-headset-usb.jpg',      badge:'',            variantType:'headset',  url:'/product/audio-headset-usb/'      },
          { id:4, name:'Wireless Over-Ear Headset',   specs:'BT 5.0 • 20hr Battery • Foldable',     price:'As low as ₱560.00', img:'assets/audio-headset-wireless.jpg', badge:'Premium',     variantType:'headset',  url:'/product/audio-headset-wireless/' },
          { id:5, name:'Mini Bluetooth Speaker',      specs:'BT 5.0 • IPX5 • 6hr Battery',          price:'As low as ₱280.00', img:'assets/audio-speaker-mini.jpg',     badge:'',            variantType:'speaker',  url:'/product/audio-speaker-mini/'     },
          { id:6, name:'Cylindrical BT Speaker',      specs:'360° Sound • BT 5.0 • Fabric Body',    price:'As low as ₱450.00', img:'assets/audio-speaker-cylinder.jpg', badge:'New',         variantType:'speaker',  url:'/product/audio-speaker-cylinder/' },
        ]
      },

      /* ----------------------------------------------------------
         SUB-CATEGORY: Mobile Power Banks
         URL: items-page.html?cat=accessories-gadgets&subcat=mobile&item=mobile-powerbanks
      ---------------------------------------------------------- */
      'mobile-powerbanks': {
        name:    'Mobile Power Banks',
        eyebrow: 'Mobile Accessories',
        price:   'As low as ₱350.00',
        heroImg: 'assets/mobile-powerbank.png',          // ← Change to your real image
        desc:    'Keep devices charged on the go. Corporate-branded power banks available in slim card-style, standard, and high-capacity options.',
        variantTypes: [
          { key: 'slim',     label: 'Slim / Card'        },
          { key: 'standard', label: 'Standard 10,000mAh' },
          { key: 'high',     label: 'High Capacity'      },
          { key: 'solar',    label: 'Solar'              },
        ],
        items: [
          // CHANGED: Each item now has its own img path. Replace with your actual filenames.
          { id:1, name:'Card-Style Power Bank 5,000mAh', specs:'5,000mAh • Credit Card Size • USB-C',   price:'As low as ₱350.00', img:'assets/pb-card-5000.jpg',    badge:'',            variantType:'slim',     url:'/product/pb-card-5000/'    },
          { id:2, name:'Slim Bar 8,000mAh',              specs:'8,000mAh • Slim Body • Dual Output',     price:'As low as ₱420.00', img:'assets/pb-slim-8000.jpg',    badge:'Best Seller', variantType:'slim',     url:'/product/pb-slim-8000/'    },
          { id:3, name:'Standard 10,000mAh Power Bank',  specs:'10,000mAh • USB-A + USB-C • 18W PD',    price:'As low as ₱480.00', img:'assets/pb-std-10000.jpg',    badge:'',            variantType:'standard', url:'/product/pb-std-10000/'    },
          { id:4, name:'Premium 10,000mAh with Display', specs:'10,000mAh • LED Display • 22.5W Fast',  price:'As low as ₱580.00', img:'assets/pb-disp-10000.jpg',   badge:'New',         variantType:'standard', url:'/product/pb-disp-10000/'   },
          { id:5, name:'20,000mAh High Capacity',        specs:'20,000mAh • 65W PD • Airline Safe',     price:'As low as ₱780.00', img:'assets/pb-high-20000.jpg',   badge:'',            variantType:'high',     url:'/product/pb-high-20000/'   },
          { id:6, name:'30,000mAh Rugged Power Bank',    specs:'30,000mAh • IP67 • Solar Panel',        price:'As low as ₱980.00', img:'assets/pb-rugged-30000.jpg', badge:'Premium',     variantType:'high',     url:'/product/pb-rugged-30000/' },
          { id:7, name:'Solar Charging Power Bank',      specs:'10,000mAh • Solar + USB-C • Rugged',    price:'As low as ₱650.00', img:'assets/pb-solar.jpg',        badge:'Eco',         variantType:'solar',    url:'/product/pb-solar/'        },
        ]
      },

    }, /* end accessories-gadgets */


    /* ══════════════════════════════════════════════════════════
       CATEGORY: DRINKWARE
    ══════════════════════════════════════════════════════════ */
    'drinkware': {

      /* ----------------------------------------------------------
         SUB-CATEGORY: Bamboo Tumblers
         URL: items-page.html?cat=drinkware&subcat=tumbler&item=tumbler-bamboo
      ---------------------------------------------------------- */
      'tumbler-bamboo': {
        name:    'Bamboo Tumblers',
        eyebrow: 'Tumblers & Flasks',
        price:   'As low as ₱220.00',
        heroImg: 'assets/tumbler.png',            // ← Change to your real image
        desc:    'Eco-friendly bamboo tumblers with stainless steel liner — a perfect sustainable corporate giveaway. Available in multiple sizes and color wrap options.',
        variantTypes: [
          { key: 'small',  label: '350ml' },
          { key: 'medium', label: '500ml' },
          { key: 'large',  label: '700ml' },
        ],
        items: [
          // CHANGED: Each item now has its own img path. Replace with your actual filenames.
          { id:1, name:'Bamboo Tumbler 350ml',      specs:'350ml • Bamboo Shell • Stainless Liner', price:'As low as ₱220.00', img:'assets/tumbler-bamboo-350.jpg',   badge:'',            variantType:'small',  url:'/product/tumbler-bamboo-350/'   },
          { id:2, name:'Bamboo Tumbler 500ml',      specs:'500ml • Bamboo Shell • Leak-Proof Lid',  price:'As low as ₱250.00', img:'assets/tumbler-bamboo-500.jpg',   badge:'Best Seller', variantType:'medium', url:'/product/tumbler-bamboo-500/'   },
          { id:3, name:'Bamboo Tumbler 700ml',      specs:'700ml • Bamboo Shell • Wide Mouth',      price:'As low as ₱290.00', img:'assets/tumbler-bamboo-700.jpg',   badge:'',            variantType:'large',  url:'/product/tumbler-bamboo-700/'   },
          { id:4, name:'Bamboo Travel Flask 500ml', specs:'500ml • Flask Shape • Gift Box Option',  price:'As low as ₱310.00', img:'assets/tumbler-bamboo-flask.jpg', badge:'New',         variantType:'medium', url:'/product/tumbler-bamboo-flask/' },
        ]
      },

    }, /* end drinkware */


    /* ══════════════════════════════════════════════════════════
       CATEGORY: PEN & PAPER
    ══════════════════════════════════════════════════════════ */
    'pen-paper': {

      /* ----------------------------------------------------------
         SUB-CATEGORY: Metal Pens
         URL: items-page.html?cat=pen-paper&subcat=pen&item=pen-metal
      ---------------------------------------------------------- */
      'pen-metal': {
        name:    'Metal Pens',
        eyebrow: 'Pens',
        price:   'As low as ₱55.00',
        heroImg: 'https://pinoyballers.com/wp-content/uploads/2025/03/Silicone_Wristband-300x130.png',
        desc:    'Sleek metal ballpoint and rollerball pens — a timeless corporate giveaway. Laser-engrave your logo for a premium executive look.',
        variantTypes: [
          { key: 'ballpoint',  label: 'Ballpoint' },
          { key: 'rollerball', label: 'Rollerball' },
          { key: 'giftset',   label: 'Gift Set'   },
        ],
        items: [
          { id:1, name:'Classic Metal Ballpoint',      specs:'Twist Mechanism • Refillable • Chrome',         price:'As low as ₱55.00',  img:'https://pinoyballers.com/wp-content/uploads/2025/03/Silicone_Wristband-300x130.png', badge:'',        variantType:'ballpoint',  url:'/product/pen-metal-ballpoint/'     },
          { id:2, name:'Slim Executive Ballpoint',     specs:'Slim Body • Satin Finish • Laser Engrave',      price:'As low as ₱75.00',  img:'https://pinoyballers.com/wp-content/uploads/2025/03/Silicone_Wristband-300x130.png', badge:'Best Seller', variantType:'ballpoint',  url:'/product/pen-metal-slim/'          },
          { id:3, name:'Capacitive Stylus Ballpoint',  specs:'Ballpoint + Stylus Tip • Multi-Surface',        price:'As low as ₱95.00',  img:'https://pinoyballers.com/wp-content/uploads/2025/03/Silicone_Wristband-300x130.png', badge:'New',     variantType:'ballpoint',  url:'/product/pen-metal-stylus/'        },
          { id:4, name:'Metal Rollerball Pen',         specs:'Fine 0.5mm Tip • Smooth Flow Ink',              price:'As low as ₱110.00', img:'https://pinoyballers.com/wp-content/uploads/2025/03/Silicone_Wristband-300x130.png', badge:'',        variantType:'rollerball', url:'/product/pen-metal-rollerball/'    },
          { id:5, name:'Executive Rollerball w/ Case', specs:'Rollerball • Velvet Pouch Included',            price:'As low as ₱180.00', img:'https://pinoyballers.com/wp-content/uploads/2025/03/Silicone_Wristband-300x130.png', badge:'Premium', variantType:'rollerball', url:'/product/pen-metal-rollerball-case/' },
          { id:6, name:'2-Pen Presentation Gift Set',  specs:'1x Ballpoint + 1x Rollerball • Box Included',  price:'As low as ₱220.00', img:'https://pinoyballers.com/wp-content/uploads/2025/03/Silicone_Wristband-300x130.png', badge:'',        variantType:'giftset',    url:'/product/pen-metal-set-2/'         },
          { id:7, name:'3-Pen Executive Gift Set',     specs:'Ballpoint + Rollerball + Pencil • Leather Box', price:'As low as ₱380.00', img:'https://pinoyballers.com/wp-content/uploads/2025/03/Silicone_Wristband-300x130.png', badge:'Premium', variantType:'giftset',    url:'/product/pen-metal-set-3/'         },
        ]
      },

    }, /* end pen-paper */

  }; /* end ITEMS_DATA */


  /* ============================================================
     HELPER: Parse URL Query Parameters
     Reads: ?cat=...&subcat=...&item=...
  ============================================================ */
  function getParams() {
    const params  = new URLSearchParams(window.location.search);
    return {
      cat:    params.get('cat')    || 'accessories-gadgets',
      subcat: params.get('subcat') || 'car',
      item:   params.get('item')   || 'car-charger-adapter',
    };
  }


  /* ============================================================
     RENDER BREADCRUMB
     Updates: Home > Category > Subcategory > Item Name
  ============================================================ */
  function renderBreadcrumb(params, itemData) {
    // Category link → back to category-page.html
    const bcCatLink   = document.getElementById('bc-category-link');
    const bcSubcatLink = document.getElementById('bc-subcat-link');
    const bcItemName  = document.getElementById('bc-item-name');

    if (bcCatLink) {
      // Pretty-print the category name from the slug
      const catLabel = params.cat
        .replace(/-/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase());
      bcCatLink.textContent = catLabel;
      bcCatLink.href = 'category-page.html?cat=' + params.cat;
    }

    if (bcSubcatLink) {
      // Pretty-print the subcat name from the slug
      const subcatLabel = params.subcat
        .replace(/-/g, ' ')
        .replace(/\b\w/g, c => c.toUpperCase());
      bcSubcatLink.textContent = subcatLabel;
      // Back to category page filtered to this subcat
      bcSubcatLink.href = 'category-page.html?cat=' + params.cat + '#subcat-' + params.subcat;
    }

    if (bcItemName && itemData) {
      bcItemName.textContent = itemData.name;
    }

    // Also update page title
    if (itemData) {
      document.title = itemData.name + ' | CreativeKit3A';
    }
  }


  /* ============================================================
     RENDER HERO BANNER
     Populates the item hero section at the top of the page.
     CHANGED: Added onerror on heroImg so a missing image shows
              a styled placeholder instead of a broken icon.
  ============================================================ */
  function renderHero(itemData) {
    const title   = document.getElementById('item-hero-title');
    const eyebrow = document.getElementById('item-eyebrow');
    const desc    = document.getElementById('item-hero-desc');
    const price   = document.getElementById('item-hero-price');
    const img     = document.getElementById('item-hero-img');

    if (title)   title.textContent   = itemData.name;
    if (eyebrow) eyebrow.textContent = itemData.eyebrow;
    if (desc)    desc.textContent    = itemData.desc;
    if (price)   price.textContent   = itemData.price;
    if (img) {
      img.src = itemData.heroImg;
      img.alt = itemData.name;
      // CHANGED: if the heroImg path is wrong/missing, swap in a visible placeholder
      img.onerror = function () {
        this.style.display = 'none';
        const ph = document.createElement('div');
        ph.className = 'item-hero-img-placeholder';
        ph.innerHTML = '<i class="fas fa-image"></i><span>No image set</span>';
        this.parentNode.appendChild(ph);
      };
    }
  }


  /* ============================================================
     RENDER VARIANT FILTER TABS
     If the item has variantTypes, renders filter tabs.
     "All" tab always shown first.
  ============================================================ */
  let activeVariantType = ''; // '' means show all

  function renderVariantTabs(itemData) {
    const container = document.getElementById('item-variants-tabs-container');
    if (!container) return;
    container.innerHTML = '';

    // Hide the tab bar if no variant types defined
    const wrapper = document.getElementById('item-variants-tabs-wrapper');
    if (!itemData.variantTypes || !itemData.variantTypes.length) {
      if (wrapper) wrapper.style.display = 'none';
      return;
    }
    if (wrapper) wrapper.style.display = '';

    // "All" tab
    const allBtn = document.createElement('button');
    allBtn.className   = 'item-variant-tab' + (!activeVariantType ? ' active' : '');
    allBtn.dataset.key = '';
    allBtn.innerHTML   = 'All <span class="vtab-count">' + itemData.items.length + '</span>';
    allBtn.addEventListener('click', () => {
      activeVariantType = '';
      renderVariantTabs(itemData);
      renderItemsGrid(itemData, getSortValue());
    });
    container.appendChild(allBtn);

    // One tab per variant type
    itemData.variantTypes.forEach(vt => {
      const count = itemData.items.filter(i => i.variantType === vt.key).length;
      const btn = document.createElement('button');
      btn.className   = 'item-variant-tab' + (activeVariantType === vt.key ? ' active' : '');
      btn.dataset.key = vt.key;
      btn.innerHTML   = vt.label + ' <span class="vtab-count">' + count + '</span>';
      btn.addEventListener('click', () => {
        activeVariantType = vt.key;
        renderVariantTabs(itemData);
        renderItemsGrid(itemData, getSortValue());
      });
      container.appendChild(btn);
    });
  }


  /* ============================================================
     BUILD INDIVIDUAL ITEM CARD
     Renders a single item variant card.
     CHANGED: Added onerror fallback on <img> so that if the image
              file is missing, a grey placeholder is shown instead
              of a broken image icon.
  ============================================================ */
  function buildItemCard(item) {
    const badge = item.badge
      ? '<span class="item-card-badge">' + item.badge + '</span>'
      : '';

    // CHANGED: onerror hides the broken img and shows the .item-card-img-placeholder div instead
    return `
      <div class="item-card" data-item-url="${item.url}">
        <div class="item-card-img">
          <img src="${item.img}" alt="${item.name}" loading="lazy"
               onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
          <div class="item-card-img-placeholder" style="display:none;">
            <i class="fas fa-image"></i>
            <span>No image yet</span>
          </div>
          ${badge}
        </div>
        <div class="item-card-body">
          <div class="item-card-title">${item.name}</div>
          <div class="item-card-specs">${item.specs}</div>
          <div class="item-card-price">${item.price}</div>
        </div>
        <div class="item-card-footer">
          <span class="customize-btn"><i class="fas fa-chevron-right"></i> CUSTOMIZE</span>
        </div>
      </div>`;
  }


  /* ============================================================
     RENDER ITEMS GRID
     Populates the main grid with item cards.
     Groups by variantType when showing "All".
  ============================================================ */
  function renderItemsGrid(itemData, sortVal) {
    const grid = document.getElementById('items-grid');
    if (!grid) return;
    grid.innerHTML = '';

    let items = activeVariantType
      ? itemData.items.filter(i => i.variantType === activeVariantType)
      : itemData.items;

    // Apply sort
    if (sortVal === 'name-asc')  items = [...items].sort((a,b) => a.name.localeCompare(b.name));
    if (sortVal === 'name-desc') items = [...items].sort((a,b) => b.name.localeCompare(a.name));

    // Update count label
    const countEl = document.getElementById('items-showing-count');
    const nameEl  = document.getElementById('items-showing-name');
    if (countEl) countEl.textContent = items.length;
    if (nameEl)  nameEl.textContent  = itemData.name;

    if (!items.length) {
      grid.innerHTML = '<div class="items-no-results"><i class="fas fa-box-open"></i><p>No variants found.</p></div>';
      return;
    }

    // Group by variantType when showing all, if variantTypes exist
    if (!activeVariantType && itemData.variantTypes && itemData.variantTypes.length) {
      itemData.variantTypes.forEach(vt => {
        const group = items.filter(i => i.variantType === vt.key);
        if (!group.length) return;

        // Section heading
        const heading = document.createElement('div');
        heading.className = 'items-type-heading';
        heading.innerHTML = `<h3>${vt.label}</h3><span class="type-count">${group.length}</span>`;
        grid.appendChild(heading);

        group.forEach(item => {
          grid.insertAdjacentHTML('beforeend', buildItemCard(item));
        });
      });
    } else {
      // Just render flat
      items.forEach(item => {
        grid.insertAdjacentHTML('beforeend', buildItemCard(item));
      });
    }

    // Attach click → navigate on each card
    grid.querySelectorAll('.item-card').forEach(card => {
      card.addEventListener('click', () => {
        window.location.href = card.dataset.itemUrl;
      });
    });
  }


  /* ============================================================
     RENDER RELATED PRODUCTS
     Shows sibling sub-category cards from the same category
     so the user can easily browse nearby products.
     Uses the CATEGORY_DATA structure from category-page.html —
     but since we're on a separate page, we define a lean copy
     of just what we need here.
  ============================================================ */

  /* Lean sibling map: categorySlug → array of { name, price, img, url } siblings */
  const SIBLING_DATA = {
    'accessories-gadgets': [
      { name:'Car Organizers & Holders', price:'As low as ₱120.00', img:'/assets/car-organizer.png', url:'car-organizers.html?cat=accessories-gadgets&subcat=car&item=car-organizers'  },
      { name:'Dash Cams',               price:'As low as ₱450.00', img:'/assets/dash-cam.png',       url:'items-page.html?cat=accessories-gadgets&subcat=car&item=car-dashcams'    },
      { name:'Mobile Power Banks',      price:'As low as ₱350.00', img:'/assets/mobile-powerbank.png', url:'items-page.html?cat=accessories-gadgets&subcat=mobile&item=mobile-powerbanks' },
      { name:'Audio Accessories',       price:'As low as ₱95.00',  img:'/assets/audio-accessories.png', url:'items-page.html?cat=accessories-gadgets&subcat=computer&item=computer-audio' },
    ],
    'drinkware': [
      { name:'Insulated & Double-Walled', price:'As low as ₱280.00', img:'https://pinoyballers.com/wp-content/uploads/2025/03/Silicone_Wristband-300x130.png', url:'category-page.html?cat=drinkware' },
      { name:'Stainless Steel Tumblers',  price:'As low as ₱310.00', img:'https://pinoyballers.com/wp-content/uploads/2017/11/Custom-Baller-Bands-Xavier-Ateneo-High-School-2-300x182.jpeg', url:'category-page.html?cat=drinkware' },
    ],
    'pen-paper': [
      { name:'Bamboo Pens',         price:'As low as ₱35.00',  img:'https://pinoyballers.com/wp-content/uploads/2021/12/pb-016-1-min-300x192.jpg',    url:'category-page.html?cat=pen-paper' },
      { name:'Plastic Pens',        price:'As low as ₱22.00',  img:'https://pinoyballers.com/wp-content/uploads/2019/08/Soft-Enamel-Lapel-Pins-001-1-300x300.png', url:'category-page.html?cat=pen-paper' },
      { name:'Custom Notebooks',    price:'As low as ₱95.00',  img:'https://pinoyballers.com/wp-content/uploads/2025/09/Elections-Ballers-Gov-Jerry-Singson-ONE-ILOCOS-SUR-300x209.jpg', url:'category-page.html?cat=pen-paper' },
    ],
  };

  function renderRelated(params, currentItemSlug) {
    const relatedGrid = document.getElementById('related-grid');
    if (!relatedGrid) return;
    relatedGrid.innerHTML = '';

    const siblings = (SIBLING_DATA[params.cat] || [])
      .filter(s => !s.url.includes('item=' + currentItemSlug)); // exclude current

    if (!siblings.length) {
      document.getElementById('related-section').style.display = 'none';
      return;
    }

    siblings.forEach(sib => {
      const card = document.createElement('div');
      card.className = 'product-card'; // reuse existing style from style.css
      card.innerHTML = `
        <div class="product-card-img">
          <img src="${sib.img}" alt="${sib.name}" loading="lazy">
        </div>
        <div class="product-card-body">
          <div class="product-card-title">${sib.name}</div>
          <div class="product-card-price">${sib.price}</div>
        </div>
        <div class="product-card-footer">
          <span class="customize-btn"><i class="fas fa-chevron-right"></i> VIEW</span>
        </div>`;
      card.style.cursor = 'pointer';
      card.addEventListener('click', () => { window.location.href = sib.url; });
      relatedGrid.appendChild(card);
    });
  }


  /* ============================================================
     HELPER: Get current sort dropdown value
  ============================================================ */
  function getSortValue() {
    const sel = document.getElementById('items-sort-select');
    return sel ? sel.value : 'default';
  }


  /* ============================================================
     SEARCH: Live filter on items grid
  ============================================================ */
  function initSearch(itemData) {
    const input = document.getElementById('items-search-input');
    if (!input) return;

    input.addEventListener('input', function () {
      const q = this.value.trim().toLowerCase();
      if (!q) {
        renderItemsGrid(itemData, getSortValue());
        return;
      }

      const grid = document.getElementById('items-grid');
      if (!grid) return;
      grid.innerHTML = '';

      const results = itemData.items.filter(
        i => i.name.toLowerCase().includes(q) || i.specs.toLowerCase().includes(q)
      );

      if (!results.length) {
        grid.innerHTML = '<div class="items-no-results"><i class="fas fa-search"></i><p>No items match "<strong>' + q + '</strong>"</p></div>';
        return;
      }

      results.forEach(item => grid.insertAdjacentHTML('beforeend', buildItemCard(item)));
      grid.querySelectorAll('.item-card').forEach(card => {
        card.addEventListener('click', () => { window.location.href = card.dataset.itemUrl; });
      });
    });
  }


  /* ============================================================
     INIT — Runs on page load
  ============================================================ */
  (function init() {
    const params   = getParams();
    const catData  = ITEMS_DATA[params.cat];

    // Guard: category not found
    if (!catData) {
      console.warn('[items-page.js] Category "' + params.cat + '" not found in ITEMS_DATA.');
      return;
    }

    const itemData = catData[params.item];

    // Guard: item not found
    if (!itemData) {
      console.warn('[items-page.js] Item "' + params.item + '" not found in ITEMS_DATA["' + params.cat + '"].');
      // Fallback: redirect to category page
      window.location.href = 'category-page.html?cat=' + params.cat;
      return;
    }

    // Render all sections
    renderBreadcrumb(params, itemData);
    renderHero(itemData);
    renderVariantTabs(itemData);
    renderItemsGrid(itemData, 'default');
    renderRelated(params, params.item);
    initSearch(itemData);

    // Sort dropdown listener
    const sortSel = document.getElementById('items-sort-select');
    if (sortSel) {
      sortSel.addEventListener('change', function () {
        renderItemsGrid(itemData, this.value);
      });
    }
  })();

})();


/* ============================================================
   HOW TO LINK FROM category-page.html → items-page.html
   ============================================================

   In category-page.html, inside the buildCard() function
   (around line 1080), the card currently navigates to:
       card.dataset.url  (e.g., '/product/car-charger-adapter')

   CHANGE (do not remove the original — just update the URL):
   Replace the product.url with a link to items-page.html.

   Find this block in buildCard():
   ──────────────────────────────────────────────────────────
     <div class="product-card" data-product-id="${product.id}" data-url="${product.url}">
   ──────────────────────────────────────────────────────────

   Replace with:
   ──────────────────────────────────────────────────────────
     // ADDED: point card click to items-page.html with query params
     // instead of the old product.url, so clicking a sub-category
     // card opens the new items page showing variants.
     <div class="product-card"
          data-product-id="${product.id}"
          data-url="items-page.html?cat=${slugKey}&subcat=${product.subcat}&item=${product.url.replace(/^\/product\//, '')}">
   ──────────────────────────────────────────────────────────

   That single attribute change in category-page.html is all
   that is needed. The rest of the logic remains unchanged.
============================================================ */