'use strict';

/* ==========================================
   Supabase Database Initialization
   ========================================== */
const supabaseUrl = 'https://bzxpswlhqqolcqcqbddo.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJ6eHBzd2xocXFvbGNxY3FiZGRvIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzkxNjk5ODYsImV4cCI6MjA5NDc0NTk4Nn0.HcnaW445nnPtocalCz5_0J3_AGT6YRC7fK7RGZLbpGQ';

// Assign to window so auth.js and all other scripts share the same instance
window.supabase = window.supabase.createClient(supabaseUrl, supabaseKey);

/* ============================================================
   MODAL CONTROL FUNCTIONS (GLOBALLY EXPOSED FOR FLUID ARCHITECTURE)
   ============================================================ */

/**
 * Invokes and displays a target overlay container card layout view
 * @param {string} modalId - The explicit DOM node ID attribute string
 */
window.openModal = function(modalId) {
  console.log("[Fluid UI] Invoking modal wrapper target container:", modalId);
  const modal = document.getElementById(modalId);
  const overlay = document.getElementById('lm-overlay');
  
  if (modal) {
      modal.classList.add('open');
      // Fallback display handling to support secondary stylesheet configurations
      modal.style.display = 'flex'; 
      
      if (overlay) {
          overlay.classList.add('open');
          overlay.style.display = 'block';
      }
      document.body.style.overflow = 'hidden'; // Protect viewport from background scroll leakage
  } else {
      console.error("[Fluid UI Fault] Target modal ID element not discovered:", modalId);
  }
};

/**
* Gracefully dismisses and seals an active overlay container card layout view
* @param {string} modalId - The explicit DOM node ID attribute string
*/
window.closeModal = function(modalId) {
  console.log("[Fluid UI] Dismissing modal container view instance:", modalId);
  const modal = document.getElementById(modalId);
  const overlay = document.getElementById('lm-overlay');
  
  if (modal) {
      modal.classList.remove('open');
      modal.style.setProperty('display', 'none', 'important'); // Overrides layout inheritance paths instantly
  }
  
  // Check if there are any other modular panels active on screen
  const remainingOpenModals = document.querySelectorAll('.lm-modal.open');
  if (remainingOpenModals.length === 0) {
      if (overlay) {
          overlay.classList.remove('open');
          overlay.style.display = 'none';
      }
      document.body.style.overflow = ''; // Instantly restores fluid body scroll functionality
  }
};

/**
* Universal safe catch engine to drop all modal screens concurrently
*/
window.closeAllModals = function() {
  const activeModals = document.querySelectorAll('.lm-modal');
  const overlay = document.getElementById('lm-overlay');
  
  activeModals.forEach(modal => {
      modal.classList.remove('open');
      modal.style.display = 'none';
  });
  
  if (overlay) {
      overlay.classList.remove('open');
      overlay.style.display = 'none';
  }
  document.body.style.overflow = '';
};

/* ============================================================
   UTILITY HELPERS
   ============================================================ */
const $ = (selector, context = document) => context.querySelector(selector);
const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

const on = (el, event, handler, options = {}) => {
  if (!el) return;
  el.addEventListener(event, handler, options);
};

const debounce = (fn, wait = 100) => {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn.apply(this, args), wait);
  };
};

/* ============================================================
   1. HERO CAROUSEL
   ============================================================ */
const Carousel = (() => {
  let currentIndex = 0;
  let autoplayTimer = null;
  const AUTOPLAY_DELAY = 7000;

  const slides = $$('.hero-slide');
  const dots   = $$('.carousel-dot');
  const prevBtn = $('.carousel-btn.prev');
  const nextBtn = $('.carousel-btn.next');

  if (!slides.length) return { init: () => {} };

  const goTo = (index) => {
    slides[currentIndex].classList.remove('active');
    dots[currentIndex]?.classList.remove('active');
    currentIndex = (index + slides.length) % slides.length;
    slides[currentIndex].classList.add('active');
    dots[currentIndex]?.classList.add('active');
  };

  const next = () => goTo(currentIndex + 1);
  const prev = () => goTo(currentIndex - 1);

  const startAutoplay = () => {
    stopAutoplay();
    autoplayTimer = setInterval(next, AUTOPLAY_DELAY);
  };

  const stopAutoplay = () => {
    if (autoplayTimer) clearInterval(autoplayTimer);
  };

  const init = () => {
    if (!slides.length) return;

    slides[0].classList.add('active');
    dots[0]?.classList.add('active');

    on(nextBtn, 'click', () => { next(); startAutoplay(); });
    on(prevBtn, 'click', () => { prev(); startAutoplay(); });

    dots.forEach((dot, i) => {
      on(dot, 'click', () => { goTo(i); startAutoplay(); });
    });

    const heroEl = $('.hero-section');
    on(heroEl, 'mouseenter', stopAutoplay);
    on(heroEl, 'mouseleave', startAutoplay);

    let touchStartX = 0;
    on(heroEl, 'touchstart', (e) => {
      touchStartX = e.touches[0].clientX;
    }, { passive: true });
    on(heroEl, 'touchend', (e) => {
      const diff = touchStartX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 40) {
        diff > 0 ? next() : prev();
        startAutoplay();
      }
    }, { passive: true });

    startAutoplay();
  };

  return { init };
})();

/* ============================================================
   2. SIDENAV (Mobile Drawer)
   ============================================================ */
const Sidenav = (() => {
  const sidenav  = $('.sidenav');
  const overlay  = $('.sidenav-overlay');
  const openBtn  = $('.hamburger');
  const closeBtn = $('.sidenav-close');

  const open = () => {
    sidenav?.classList.add('open');
    overlay?.classList.add('open');
    document.body.style.overflow = 'hidden';
  };

  const close = () => {
    sidenav?.classList.remove('open');
    overlay?.classList.remove('open');
    document.body.style.overflow = '';
  };

  const init = () => {
    on(openBtn,  'click', open);
    on(closeBtn, 'click', close);
    on(overlay,  'click', close);
    on(document, 'keydown', (e) => {
      if (e.key === 'Escape') close();
    });
  };

  return { init, open, close };
})();

/* ============================================================
   3. COLLAPSIBLE ACCORDION (Sidenav Categories)
   ============================================================ */
const Accordion = (() => {
  const init = () => {
    $$('.sidenav-accordion-toggle').forEach((toggle) => {
      on(toggle, 'click', () => {
        const parent  = toggle.closest('.sidenav-accordion-item');
        const body    = parent?.querySelector('.sidenav-accordion-body');
        const chevron = toggle.querySelector('.acc-chevron');
        const isOpen  = parent?.classList.contains('open');

        $$('.sidenav-accordion-item.open').forEach((item) => {
          item.classList.remove('open');
          const b = item.querySelector('.sidenav-accordion-body');
          const c = item.querySelector('.acc-chevron');
          if (b) b.style.maxHeight = '0px';
          if (c) c.style.transform = '';
        });

        if (!isOpen && parent) {
          parent.classList.add('open');
          if (body)   body.style.maxHeight = body.scrollHeight + 'px';
          if (chevron) chevron.style.transform = 'rotate(180deg)';
        }
      });
    });
  };

  return { init };
})();

/* ============================================================
   4. DISCOUNT POPUP MODAL
   ============================================================ */
const Modal = (() => {
  const overlay = $('.modal-overlay');
  const closeBtn = $('.modal-close');
  const form     = $('.modal-form');

  const SHOWN_KEY    = 'pb_modal_shown';
  const COOKIE_HOURS = 24;

  const hasBeenShown = () => {
    const ts = parseInt(localStorage.getItem(SHOWN_KEY) || '0', 10);
    return Date.now() - ts < COOKIE_HOURS * 3600 * 1000;
  };

  const markShown = () => localStorage.setItem(SHOWN_KEY, Date.now().toString());

  const open = () => {
    overlay?.classList.add('open');
    document.body.style.overflow = 'hidden';
    markShown();
  };

  const close = () => {
    overlay?.classList.remove('open');
    document.body.style.overflow = '';
  };

  const init = () => {
    if (!overlay) return;

    if (!hasBeenShown()) {
      setTimeout(open, 9000);
    }

    on(closeBtn, 'click', close);
    on(overlay, 'click', (e) => {
      if (e.target === overlay) close();
    });
    on(document, 'keydown', (e) => {
      if (e.key === 'Escape') close();
    });

    on($('.discount-sticky'), 'click', open);

    on(form, 'submit', (e) => {
      e.preventDefault();
      const firstInput = form.querySelector('input[type="text"]');
      const emailInput = form.querySelector('input[type="email"]');
      if (!emailInput?.value || !firstInput?.value) {
        shakeForm();
        return;
      }
      handleSubscription(firstInput.value, emailInput.value);
    });
  };

  const shakeForm = () => {
    form?.classList.add('shake');
    setTimeout(() => form?.classList.remove('shake'), 400);
  };

  const handleSubscription = (name, email) => {
    console.log('Subscription:', { name, email });
    if (form) {
      form.innerHTML = `
        <div style="text-align:center;padding:12px 0;">
          <div style="font-size:2.5rem;margin-bottom:10px;">🎉</div>
          <p style="font-weight:800;font-size:1.1rem;margin-bottom:6px;">You're in!</p>
          <p style="opacity:0.85;font-size:0.88rem;">Use coupon code <strong>PBALL5OFF</strong> at checkout.</p>
        </div>`;
    }
    setTimeout(close, 3000);
  };

  return { init, open, close };
})();

/* ============================================================
   5. MINI CART
   ============================================================ */
const Cart = (() => {
  let items = [];
  const STORAGE_KEY = 'pb_cart';

  const load = () => {
    try {
      items = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    } catch {
      items = [];
    }
  };

  const save = () => localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
  const getCount = () => items.reduce((acc, item) => acc + (item.qty || 1), 0);

  const updateUI = () => {
    const count = getCount();
    $$('.cart-count').forEach((el) => {
      el.textContent = count;
      el.style.display = count > 0 ? 'flex' : 'none';
    });
  };

  const addItem = (product) => {
    const existing = items.find((i) => i.id === product.id);
    if (existing) {
      existing.qty = (existing.qty || 1) + 1;
    } else {
      items.push({ ...product, qty: 1 });
    }
    save();
    updateUI();
    showToast(`"${product.name}" added to cart!`);
  };

  const init = () => {
    load();
    updateUI();

    on(document, 'click', (e) => {
      const btn = e.target.closest('[data-add-to-cart]');
      if (!btn) return;
      const card = btn.closest('.product-card');
      const id   = card?.dataset.productId || Math.random().toString(36).slice(2);
      const name = card?.querySelector('.product-card-title')?.textContent?.trim() || 'Product';
      const price = card?.querySelector('.product-card-price')?.textContent?.trim() || '';
      addItem({ id, name, price });
    });
  };

  return { init, addItem, getCount };
})();

/* ============================================================
   6. TOAST NOTIFICATION
   ============================================================ */
const showToast = (() => {
  let container = null;

  const ensureContainer = () => {
    if (container) return;
    container = document.createElement('div');
    container.className = 'toast-container';
    container.style.cssText = `
      position: fixed; bottom: 24px; right: 24px; z-index: 3000;
      display: flex; flex-direction: column; gap: 10px; pointer-events: none;
    `;
    document.body.appendChild(container);
  };

  return (message, duration = 3000) => {
    ensureContainer();
    const toast = document.createElement('div');
    toast.style.cssText = `
      background: var(--gradient-hero);
      color: #fff;
      padding: 12px 20px;
      border-radius: 999px;
      font-family: var(--font-body);
      font-weight: 600;
      font-size: 0.88rem;
      box-shadow: 0 8px 32px rgba(255,107,0,0.3);
      opacity: 0;
      transform: translateY(10px);
      transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
      pointer-events: auto;
      white-space: nowrap;
    `;
    toast.textContent = message;
    container.appendChild(toast);

    requestAnimationFrame(() => {
      toast.style.opacity = '1';
      toast.style.transform = 'translateY(0)';
    });

    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(10px)';
      setTimeout(() => toast.remove(), 300);
    }, duration);
  };
})();

/* ============================================================
   7. LAZY IMAGE LOADING
   ============================================================ */
const LazyImages = (() => {
  const init = () => {
    if (!('IntersectionObserver' in window)) {
      $$('img[data-src]').forEach((img) => {
        img.src = img.dataset.src;
        img.removeAttribute('data-src');
      });
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          const img = entry.target;
          if (img.dataset.src) { img.src = img.dataset.src; img.removeAttribute('data-src'); }
          if (img.dataset.srcset) { img.srcset = img.dataset.srcset; img.removeAttribute('data-srcset'); }
          observer.unobserve(img);
        });
      },
      { rootMargin: '300px 0px', threshold: 0 }
    );

    $$('img[data-src]').forEach((img) => observer.observe(img));
  };

  return { init };
})();

/* ============================================================
   8. STICKY NAVBAR SCROLL BEHAVIOUR
   ============================================================ */
const StickyNav = (() => {
  const navbar = $('.navbar');
  let lastScroll = 0;

  const onScroll = debounce(() => {
    if (!navbar) return;
    const scroll = window.scrollY;

    if (scroll > 80) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }

    if (scroll > lastScroll && scroll > 200) {
      navbar.classList.add('hide-nav');
    } else {
      navbar.classList.remove('hide-nav');
    }

    lastScroll = scroll;
  }, 10);

  const init = () => {
    on(window, 'scroll', onScroll, { passive: true });

    const style = document.createElement('style');
    style.textContent = `
      .navbar.scrolled { box-shadow: 0 4px 30px rgba(255,107,0,0.15); }
      .navbar.hide-nav { transform: translateY(-100%); transition: transform 0.35s cubic-bezier(0.4,0,0.2,1); }
      .navbar { transition: transform 0.35s cubic-bezier(0.4,0,0.2,1), box-shadow 0.3s ease; }
      .modal-form.shake { animation: shakeAnim 0.35s ease; }
      @keyframes shakeAnim {
        0%,100% { transform: translateX(0); }
        20%,60% { transform: translateX(-6px); }
        40%,80% { transform: translateX(6px); }
      }
      .sidenav-accordion-body { max-height: 0; overflow: hidden; transition: max-height 0.3s cubic-bezier(0.4,0,0.2,1); }
    `;
    document.head.appendChild(style);
  };

  return { init };
})();

/* ============================================================
   9. SEARCH BAR
   ============================================================ */
const Search = (() => {
  const inputs = $$('.navbar-search input, .search-form input');

  const init = () => {
    inputs.forEach((input) => {
      on(input, 'keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          const q = input.value.trim();
          if (q) window.location.href = `/?s=${encodeURIComponent(q)}`;
        }
      });
    });
  };

  return { init };
})();

/* ============================================================
   10. PRODUCT CARD — "CUSTOMIZE" CLICK
   ============================================================ */
const ProductCards = (() => {
  const init = () => {
    on(document, 'click', (e) => {
      const card = e.target.closest('.product-card');
      if (!card) return;
      if (e.target.closest('[data-add-to-cart]')) return;
      const url = card.dataset.url;
      if (url) window.location.href = url;
    });
  };

  return { init };
})();

/* ============================================================
   11. SMOOTH ANCHOR SCROLL
   ============================================================ */
const SmoothScroll = (() => {
  const init = () => {
    on(document, 'click', (e) => {
      const link = e.target.closest('a[href^="#"]');
      if (!link) return;
      const target = $(link.getAttribute('href'));
      if (!target) return;
      e.preventDefault();
      Sidenav.close();
      window.scrollTo({ top: target.offsetTop - 80, behavior: 'smooth' });
    });
  };

  return { init };
})();

/* ============================================================
   12. PRODUCT LOADER
   ============================================================ */
const ProductLoader = (() => {
  const load = async (apiUrl, containerSelector) => {
    const container = $(containerSelector);
    if (!container) return;

    container.innerHTML = '<div class="loading-spinner">Loading...</div>';

    try {
      const res  = await fetch(apiUrl);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();

      container.innerHTML = '';
      (data.products || []).forEach((p) => {
        container.insertAdjacentHTML('beforeend', renderProductCard(p));
      });

      LazyImages.init();
    } catch (err) {
      console.error('ProductLoader error:', err);
      container.innerHTML = '<p class="error-msg">Failed to load products. Please try again.</p>';
    }
  };

  const renderProductCard = ({ id, name, price, image, url }) => `
    <div class="product-card" data-product-id="${id}" data-url="${url || '#'}">
      <div class="product-card-img">
        <img data-src="${image}" alt="${name}" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 200'%3E%3C/svg%3E">
      </div>
      <div class="product-card-body">
        <div class="product-card-title">${name}</div>
        <div class="product-card-price">As low as ${price}</div>
      </div>
      <div class="product-card-footer">
        <span class="customize-btn">&#9654; CUSTOMIZE</span>
        <button class="btn btn-primary" style="padding:6px 14px;font-size:0.75rem;" data-add-to-cart>Add</button>
      </div>
    </div>`;

  return { load };
})();

/* ============================================================
   13. CONTACT FORM AJAX SUBMIT
   ============================================================ */
const ContactForm = (() => {
  const init = () => {
    const form = $('#contact-form');
    if (!form) return;

    on(form, 'submit', async (e) => {
      e.preventDefault();
      const submitBtn = form.querySelector('[type="submit"]');
      const data = Object.fromEntries(new FormData(form));

      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending…';

      try {
        const res = await fetch('/api/contact', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data),
        });
        if (!res.ok) throw new Error('Server error');
        showToast('Message sent! We\'ll get back to you soon.', 4000);
        form.reset();
      } catch (err) {
        showToast('Something went wrong. Please try again.', 4000);
        console.error('ContactForm error:', err);
      } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Message';
      }
    });
  };

  return { init };
})();

/* ============================================================
   14. ANIMATION ON SCROLL (Reveal)
   ============================================================ */
const ScrollReveal = (() => {
  const init = () => {
    if (!('IntersectionObserver' in window)) return;

    const style = document.createElement('style');
    style.textContent = `
      .reveal { opacity: 0; transform: translateY(28px); transition: opacity 0.55s ease, transform 0.55s ease; }
      .reveal.visible { opacity: 1; transform: translateY(0); }
    `;
    document.head.appendChild(style);

    $$('.product-card, .client-card, .pricing-table, .hero-slide-content').forEach(
      (el) => el.classList.add('reveal')
    );

    const obs = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            obs.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.1 }
    );

    $$('.reveal').forEach((el) => obs.observe(el));
  };

  return { init };
})();

/* ============================================================
   15. AUTH MODAL MODULE
   ============================================================ */
const AuthModal = (() => {

  // UPDATE THIS INSIDE YOUR main.js FILE
window.switchTab = (tab) => {
  const tabs = {
    login:    { btn: 'tab-login',    panel: 'panel-login'    },
    register: { btn: 'tab-register', panel: 'panel-register' },
  };

  // Base Reset: Strip active focus states and hide standard fields using our CSS classes
  Object.values(tabs).forEach(({ btn, panel }) => {
    const b = document.getElementById(btn);
    const p = document.getElementById(panel);
    if (b) { b.classList.remove('active'); b.setAttribute('aria-selected', 'false'); }
    if (p) p.classList.add('panel-hidden'); // Handled via utility classes now
  });

  // Safety Pass: Always make sure the forgot-password panel gets tucked away when changing modes
  const forgotPanel = document.getElementById('panel-forgot');
  if (forgotPanel) forgotPanel.classList.add('panel-hidden');

  // Activate the user's selected component panel view
  const target = tabs[tab];
  if (!target) return;
  const activeBtn   = document.getElementById(target.btn);
  const activePanel = document.getElementById(target.panel);
  if (activeBtn)   { activeBtn.classList.add('active'); activeBtn.setAttribute('aria-selected', 'true'); }
  if (activePanel) activePanel.classList.remove('panel-hidden'); // Displays cleanly

  // Clear obsolete warning logs from layout nodes
  ['lm-login-error', 'lm-reg-error', 'lm-reg-success'].forEach((msgId) => {
    const el = document.getElementById(msgId);
    if (el) el.hidden = true;
  });
};

  window.togglePw = (inputId, btn) => {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    const icon = btn.querySelector('i');
    if (icon) {
      icon.classList.toggle('fa-eye',      !isHidden);
      icon.classList.toggle('fa-eye-slash', isHidden);
    }
  };

  const STRENGTH_LEVELS = [
    { label: '',       color: '',        width: '0%'   },
    { label: 'Weak',   color: '#e53935', width: '25%'  },
    { label: 'Fair',   color: '#fb8c00', width: '50%'  },
    { label: 'Good',   color: '#43a047', width: '75%'  },
    { label: 'Strong', color: '#1b5e20', width: '100%' },
  ];

  window.updateStrength = (pw) => {
    let score = 0;
    if (pw.length >= 8)           score++;
    if (/[A-Z]/.test(pw))         score++;
    if (/[0-9]/.test(pw))         score++;
    if (/[^A-Za-z0-9]/.test(pw)) score++;

    const level = pw.length ? STRENGTH_LEVELS[score] : STRENGTH_LEVELS[0];
    const fill  = document.getElementById('lm-strength-fill');
    const label = document.getElementById('lm-strength-label');
    if (fill)  { fill.style.width = level.width; fill.style.backgroundColor = level.color; }
    if (label) { label.textContent = level.label; label.style.color = level.color; }
  };

  const showMsg = (id, text, isError = true) => {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = text;
    el.className   = `lm-msg lm-msg--${isError ? 'error' : 'success'}`;
    el.hidden      = false;
  };

  const hideMsg = (id) => {
    const el = document.getElementById(id);
    if (el) el.hidden = true;
  };

  const setLoading = (btn, loading) => {
    if (!btn) return;
    btn.disabled = loading;
    if (loading) {
      btn.dataset.orig = btn.innerHTML;
      btn.innerHTML    = '<span class="lm-spinner"></span> Please wait…';
    } else {
      btn.innerHTML = btn.dataset.orig || btn.innerHTML;
    }
  };

  /* ── LOGIN ── */
  window.submitLogin = async () => {
    const email    = document.getElementById('lm-login-email')?.value.trim();
    const password = document.getElementById('lm-login-password')?.value;
    const btn      = document.getElementById('lm-login-btn');

    hideMsg('lm-login-error');

    if (!email || !password) {
      showMsg('lm-login-error', '⚠️ Please fill in all fields.');
      return;
    }

    setLoading(btn, true);
    try {
      // Uses auth.js handleSignIn logic via RPC
      const { data, error } = await supabase.rpc('login_user', {
        p_email:    email,
        p_password: password
      });

      if (error) throw error;

      if (!data || data.length === 0) {
        showMsg('lm-login-error', '⚠️ Invalid email or password.');
        return;
      }

      const user = data[0];
      setSession(user); // from auth.js
      showToast(`Welcome back, ${user.first_name}! 👋`);
      window.closeModal('login-modal');
      setTimeout(() => updateAuthUI(), 300);

    } catch (err) {
      console.error('[AuthModal] Login error:', err);
      showMsg('lm-login-error', '⚠️ ' + (err.message || 'Login failed. Try again.'));
    } finally {
      setLoading(btn, false);
    }
  };

  /* ============================================================
   AUTH: SUPABASE LIVE SESSION CONTROLLER (REWRITE)
   ============================================================ */
async function updateAuthUI(passedSession = null) {
  try {
    // 1. Establish the session token smoothly whether passed by listener or fetched fresh
    let session = passedSession;
    if (!session) {
      const { data: { session: fetchedSession }, error: sessionError } = await supabase.auth.getSession();
      if (sessionError) throw sessionError;
      session = fetchedSession;
    }

    const guestGroup = document.getElementById('auth-guest');
    const customerGroup = document.getElementById('auth-customer');
    const adminGroup = document.getElementById('auth-admin');

    // 2. Baseline Reset: Clear active class tokens from all layout blocks cleanly
    if (guestGroup) guestGroup.classList.remove('active');
    if (customerGroup) customerGroup.classList.remove('active');
    if (adminGroup) adminGroup.classList.remove('active');

    if (!session) {
      console.log("[Auth UI] No active session profiles detected. Rendering Guest view.");
      if (guestGroup) guestGroup.classList.add('active'); // Safely reveals Sign In / Register
    } else {
      console.log("[Auth UI] Active user session verified. Resolving permissions matrix...");
      
      // 3. Query the profiles table data matrix safely for role metrics
      const { data: profile, error: profileError } = await supabase
        .from('profiles')
        .select('role, full_name')
        .eq('id', session.user.id)
        .single();

      if (profileError) {
        // Fallback safety catch: If a user profile row isn't constructed yet, treat them as a baseline customer
        console.warn('[Auth UI Profile Missing]', profileError.message);
        if (customerGroup) customerGroup.classList.add('active');
        const nameEl = document.getElementById('user-display-name');
        if (nameEl) nameEl.textContent = session.user.email.split('@')[0];
        return;
      }

      // 4. Inject structural classes fluidly based on actual administrative roles
      if (profile && profile.role === 'admin') {
        if (adminGroup) adminGroup.classList.add('active');
      } else {
        if (customerGroup) customerGroup.classList.add('active');
        const nameEl = document.getElementById('user-display-name');
        if (nameEl) nameEl.textContent = profile?.full_name || 'Customer';
      }
    }
  } catch (err) {
    console.error('[Auth UI Failure]', err.message);
  }
}

  /* ── REGISTER ── */
  window.submitRegister = async () => {
    const btn    = document.getElementById('lm-reg-btn');
    const errEl  = document.getElementById('lm-reg-error');
    const succEl = document.getElementById('lm-reg-success');

    if (errEl)  errEl.hidden  = true;
    if (succEl) succEl.hidden = true;

    const elFirstName = document.getElementById('lm-reg-firstname');
    const elLastName  = document.getElementById('lm-reg-lastname');
    const elEmail     = document.getElementById('lm-reg-email');
    const elPhone     = document.getElementById('lm-reg-phone');
    const elPassword  = document.getElementById('lm-reg-password');
    const elConfirm   = document.getElementById('lm-reg-confirm');
    const elTerms     = document.getElementById('lm-reg-terms');

    if (!elFirstName || !elLastName || !elEmail || !elPassword || !elConfirm) {
      console.error('[AuthModal] Missing registration form elements.');
      return;
    }

    const first_name = elFirstName.value.trim();
    const last_name  = elLastName.value.trim();
    const email      = elEmail.value.trim();
    const phone      = elPhone?.value.trim() || null;
    const password   = elPassword.value;
    const confirm    = elConfirm.value;

    if (!first_name || !last_name || !email || !password) {
      showMsg('lm-reg-error', '⚠️ Please fill out all required fields.');
      return;
    }
    if (password !== confirm) {
      showMsg('lm-reg-error', '⚠️ Passwords do not match!');
      return;
    }
    if (elTerms && !elTerms.checked) {
      showMsg('lm-reg-error', '⚠️ You must accept the Terms & Conditions.');
      return;
    }

    setLoading(btn, true);
    try {
      // Check duplicate email
      const { data: existing } = await supabase
        .from('users')
        .select('id')
        .eq('email', email)
        .maybeSingle();

      if (existing) {
        showMsg('lm-reg-error', '⚠️ An account with this email already exists.');
        return;
      }

      const { error } = await supabase
        .from('users')
        .insert({ first_name, last_name, email, password, phone });

      if (error) throw error;

      showMsg('lm-reg-success', '🎉 Account created! You can now sign in.', false);
      setTimeout(() => {
        window.switchTab('login');
        const loginEmail = document.getElementById('lm-login-email');
        if (loginEmail) loginEmail.value = email;
      }, 1500);

    } catch (err) {
      console.error('[AuthModal] Register error:', err);
      showMsg('lm-reg-error', '⚠️ ' + (err.message || 'Registration failed.'));
    } finally {
      setLoading(btn, false);
    }
  };

  const init = () => {
    on(document, 'keydown', (e) => {
      if (e.key === 'Escape') window.closeModal('login-modal');
    });
  };

  return { init };
})();

/* ============================================================
   CATALOG SEARCH (accessories page)
   ============================================================ */
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('cat-search-input');
  const catalogGrid = document.getElementById('cat-main-grid');

  if (searchInput && catalogGrid) {
    const originalCardsArr = Array.from(catalogGrid.querySelectorAll('.product-card'));
    searchInput.addEventListener('input', function() {
      const queryText = this.value.trim().toLowerCase();
      originalCardsArr.forEach(card => {
        const cardNameAttr = card.getAttribute('data-name') || '';
        card.style.display = cardNameAttr.includes(queryText) ? '' : 'none';
      });
    });
  }
});

/* ============================================================
   QUOTE FORM (quote page)
   ============================================================ */
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
    if (hasData) lvl2Select.disabled = false;
  }
}

function updateLvl3Dropdown() {
  const lvl1Select = document.getElementById('q_lvl1');
  const lvl2Select = document.getElementById('q_lvl2');
  const lvl3Select = document.getElementById('q_lvl3');
  const parentId = lvl1Select.value;
  const subId    = lvl2Select.value;

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
    if (hasData) lvl3Select.disabled = false;
  }
}

async function transmitQuotationProposal(event) {
  event.preventDefault();

  const activeBtn  = document.getElementById('submitQuoteFormBtn');
  const errAlert   = document.getElementById('quote-error-box');
  const succAlert  = document.getElementById('quote-success-box');

  errAlert.hidden  = true;
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
  formDataPayload.append('full_name',    document.getElementById('q_name').value.trim());
  formDataPayload.append('email',        document.getElementById('q_email').value.trim());
  formDataPayload.append('phone',        document.getElementById('q_phone').value.trim());
  formDataPayload.append('item_type',    chosenItemName);
  formDataPayload.append('quantity',     parseInt(document.getElementById('q_qty').value) || 0);
  formDataPayload.append('deadline',     document.getElementById('q_deadline').value);
  formDataPayload.append('custom_notes', document.getElementById('q_notes').value.trim());

  const fileSelectorElement = document.getElementById('q_logo');
  if (fileSelectorElement.files.length > 0) {
    formDataPayload.append('logo_file', fileSelectorElement.files[0]);
  }

  activeBtn.disabled = true;
  const initialHtmlStr = activeBtn.innerHTML;
  activeBtn.innerHTML = '<span class="quote-btn-spinner"></span> Transmitting Proposal Criteria...';

  try {
    const rawRes    = await fetch('includes/quote_process.php', { method: 'POST', body: formDataPayload });
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

/* ============================================================
   BOOTSTRAP — Run all modules on DOM ready
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
  // auth.js handles updateAuthUI — called automatically via its own DOMContentLoaded
  Carousel.init();
  Sidenav.init();
  Accordion.init();
  Modal.init();
  Cart.init();
  LazyImages.init();
  StickyNav.init();
  Search.init();
  ProductCards.init();
  SmoothScroll.init();
  ContactForm.init();
  ScrollReveal.init();
  AuthModal.init();

  console.log('[CreativeKit3A] All modules initialized ✓');
});