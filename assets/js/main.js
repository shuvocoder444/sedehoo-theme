/* Sedehoo Theme — main.js */
(function () {
  'use strict';

  /* ─── Sidebar Toggle ────────────────────────────────────── */
  var COLLAPSED_KEY = 'sedehoo_sidebar_collapsed';

  function sdToggleSidebar() {
    var body = document.body;
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    var btn = document.getElementById('sidebar-toggle');
    var isMobile = window.innerWidth <= 768;

    if (isMobile) {
      var isOpen = sidebar.classList.toggle('open');
      if (overlay) overlay.classList.toggle('open', isOpen);
      if (btn) btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    } else {
      var isCollapsed = body.classList.toggle('sidebar-collapsed');
      localStorage.setItem(COLLAPSED_KEY, isCollapsed ? '1' : '0');
      if (btn) btn.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
    }
  }

  function sdCloseSidebar() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebar-overlay');
    var btn = document.getElementById('sidebar-toggle');
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('open');
    if (btn) btn.setAttribute('aria-expanded', 'false');
  }

  // Restore collapsed state on desktop
  (function initSidebar() {
    if (window.innerWidth > 768) {
      var collapsed = localStorage.getItem(COLLAPSED_KEY) === '1';
      if (collapsed) {
        document.body.classList.add('sidebar-collapsed');
        var btn = document.getElementById('sidebar-toggle');
        if (btn) btn.setAttribute('aria-expanded', 'false');
      }
    }
  })();

  window.sdToggleSidebar = sdToggleSidebar;
  window.sdCloseSidebar  = sdCloseSidebar;

  /* ─── Keyboard Shortcuts ─────────────────────────────────── */
  document.addEventListener('keydown', function (e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault();
      var el = document.getElementById('headerSearch') || document.getElementById('toolSearch');
      if (el) el.focus();
    }
    if (e.key === 'Escape') {
      sdCloseSidebar();
      var modals = document.querySelectorAll('.modal-backdrop');
      modals.forEach(function (m) { m.style.display = 'none'; });
    }
  });

  /* ─── Header Search ──────────────────────────────────────── */
  var headerSearch = document.getElementById('headerSearch');
  if (headerSearch) {
    headerSearch.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' && this.value.trim()) {
        window.location.href = home_url + '?s=' + encodeURIComponent(this.value.trim()) + '&post_type=sedehoo_photo';
      }
    });
  }

  /* ─── Notification Toast ─────────────────────────────────── */
  function sdNotify(msg, type) {
    var notif = document.getElementById('sdNotif');
    var dot   = document.getElementById('sdNotifDot');
    var msgEl = document.getElementById('sdNotifMsg');
    if (!notif) return;
    type = type || 'success';
    notif.className = 'notification ' + type;
    if (dot) dot.className = 'notif-dot';
    if (msgEl) msgEl.textContent = msg;
    notif.classList.add('show');
    clearTimeout(notif._t);
    notif._t = setTimeout(function () { notif.classList.remove('show'); }, 3500);
  }
  window.sdNotify = sdNotify;

  /* ─── Modal Open / Close ─────────────────────────────────── */
  function sdCloseModal(id) {
    var el = document.getElementById(id);
    if (el) el.style.display = 'none';
    // Stop any video in modal
    var content = document.getElementById('videoModalContent');
    if (content) content.innerHTML = '';
  }
  window.sdCloseModal = sdCloseModal;

  /* ─── Collection Toggle ──────────────────────────────────── */
  function sdToggleCollection(photoId, btn) {
    if (!sedehooData.isLoggedIn) {
      window.location.href = sedehooData.loginUrl;
      return;
    }
    var isIn = btn.classList.contains('in-collection');
    var action = isIn ? 'remove' : 'add';

    fetch(sedehooData.ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'sedehoo_collection',
        nonce: sedehooData.nonce,
        photo_id: photoId,
        collection_action: action
      })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.success) {
        btn.classList.toggle('in-collection', data.data.in_collection);
        // Update heart icon fill if present
        var heart = btn.querySelector('#collectHeart') || btn.querySelector('svg');
        if (heart) heart.setAttribute('fill', data.data.in_collection ? 'currentColor' : 'none');
        var textEl = btn.querySelector('#collectBtnText') || btn.querySelector('span');
        if (textEl) textEl.textContent = data.data.in_collection ? 'Saved to Collection' : 'Add to Collection';
        sdNotify(data.data.message, 'success');
      } else {
        sdNotify(data.data ? data.data.message : 'Error', 'error');
      }
    });
  }
  window.sdToggleCollection = sdToggleCollection;

  function sdRemoveFromCollection(photoId, itemEl) {
    fetch(sedehooData.ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({
        action: 'sedehoo_collection',
        nonce: sedehooData.nonce,
        photo_id: photoId,
        collection_action: 'remove'
      })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (data.success && itemEl) {
        itemEl.style.opacity = '0';
        itemEl.style.transform = 'scale(.9)';
        itemEl.style.transition = 'all .25s';
        setTimeout(function () { if (itemEl.parentNode) itemEl.parentNode.removeChild(itemEl); }, 250);
        sdNotify('Removed from collection.', 'success');
      }
    });
  }
  window.sdRemoveFromCollection = sdRemoveFromCollection;

  /* ─── Discover Similar ───────────────────────────────────── */
  function sdDiscoverSimilar(photoId) {
    var panel = document.getElementById('similarPanel');
    var grid  = document.getElementById('similarGrid');
    if (!panel || !grid) return;

    if (panel.classList.contains('active')) {
      panel.classList.remove('active');
      return;
    }

    panel.classList.add('active');
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

    // Show skeletons
    grid.innerHTML = '';
    for (var i = 0; i < 6; i++) {
      grid.innerHTML += '<div class="similar-item skeleton" style="border:none;"></div>';
    }

    fetch(sedehooData.ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ action: 'sedehoo_similar', nonce: sedehooData.nonce, photo_id: photoId })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      grid.innerHTML = '';
      if (data.success && data.data.length) {
        data.data.forEach(function (p) {
          var item = document.createElement('a');
          item.href = p.url;
          item.className = 'similar-item';
          item.innerHTML = (p.thumb ? '<img src="' + p.thumb + '" alt="' + p.title + '" loading="lazy">' : '')
            + '<div class="similar-item-name">' + p.title + '</div>';
          grid.appendChild(item);
        });
      } else {
        grid.innerHTML = '<div class="empty-state" style="grid-column:1/-1;padding:20px;"><p>No similar photos found.</p></div>';
      }
    });
  }

  function sdCloseSimilar() {
    var panel = document.getElementById('similarPanel');
    if (panel) panel.classList.remove('active');
  }

  window.sdDiscoverSimilar = sdDiscoverSimilar;
  window.sdCloseSimilar    = sdCloseSimilar;

  /* ─── Download Photo ─────────────────────────────────────── */
  function sdDownloadPhoto(photoId) {
    fetch(sedehooData.ajaxUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ action: 'sedehoo_download', nonce: sedehooData.nonce, photo_id: photoId })
    })
    .then(function (r) { return r.json(); })
    .then(function (data) {
      if (!data.success) {
        if (!sedehooData.isLoggedIn) { window.location.href = sedehooData.loginUrl; return; }
        sdNotify('Download error.', 'error');
        return;
      }
      var d = data.data;
      if (d.type === 'free') {
        // Trigger download
        var a = document.createElement('a');
        a.href = d.url;
        a.download = '';
        a.target = '_blank';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        sdNotify('Download started!', 'success');
      } else {
        // Show checkout modal
        var modal = document.getElementById('checkoutModal');
        if (!modal) return;
        var thumb   = document.getElementById('checkoutThumb');
        var title   = document.getElementById('checkoutTitle');
        var price   = document.getElementById('checkoutPrice');
        var buyBtn  = document.getElementById('checkoutDirectBtn');
        var cartBtn = document.getElementById('checkoutCartBtn');
        if (thumb) thumb.src = d.thumb || '';
        if (title) title.textContent = d.title || '';
        if (price) price.innerHTML = d.price || '';
        if (buyBtn) buyBtn.href = d.checkout_url || '#';
        if (cartBtn) cartBtn.href = d.add_to_cart_url || '#';
        modal.style.display = 'flex';
      }
    });
  }
  window.sdDownloadPhoto = sdDownloadPhoto;

  /* ─── Page entry animation ───────────────────────────────── */
  var main = document.getElementById('main-content');
  if (main) {
    main.style.opacity = '0';
    main.style.transform = 'translateY(6px)';
    requestAnimationFrame(function () {
      requestAnimationFrame(function () {
        main.style.transition = 'opacity .3s, transform .3s';
        main.style.opacity = '1';
        main.style.transform = 'translateY(0)';
      });
    });
  }

})();

// Expose home_url for search redirect (set by wp_localize_script)
var home_url = (typeof sedehooData !== 'undefined') ? sedehooData.homeUrl : '/';
