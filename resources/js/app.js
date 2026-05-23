function qs(sel, root = document) {
    return root.querySelector(sel);
}

function qsa(sel, root = document) {
    return Array.from(root.querySelectorAll(sel));
}

function showToast(message, tone = 'info', sub = '') {
    const root = qs('#toast-root');
    if (!root) return;

    const toneClass =
        tone === 'success'
            ? 'bg-emerald-600'
            : tone === 'danger'
              ? 'bg-rose-600'
              : tone === 'warning'
                ? 'bg-amber-600'
                : 'bg-slate-900';

    const el = document.createElement('div');
    el.className =
        'pointer-events-auto bakery-card overflow-hidden ring-0 shadow-lg';
    el.innerHTML = `
      <div class="${toneClass} px-4 py-3">
        <div class="text-sm font-extrabold text-white">${message}</div>
        <div class="mt-1 text-xs font-semibold text-white/80">${sub}</div>
      </div>
    `;

    root.appendChild(el);

    setTimeout(() => {
        el.style.transition = 'opacity 200ms ease';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 220);
    }, 2600);
}

// Sidebar toggle (mobile)
qsa('[data-sidebar-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const sidebar = qs('[data-sidebar]');
        const overlay = qs('[data-sidebar-overlay]');
        if (!sidebar || !overlay) return;
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });
});

// Overlay click closes sidebar
const overlay = qs('[data-sidebar-overlay]');
if (overlay) {
    overlay.addEventListener('click', () => {
        const sidebar = qs('[data-sidebar]');
        if (!sidebar) return;
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
}

// Simple dropdown toggle
qsa('[data-dropdown]').forEach((wrap) => {
    const btn = qs('[data-dropdown-button]', wrap);
    const menu = qs('[data-dropdown-menu]', wrap);
    if (!btn || !menu) return;

    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        menu.classList.toggle('hidden');
    });
});

document.addEventListener('click', () => {
    qsa('[data-dropdown-menu]').forEach((menu) => menu.classList.add('hidden'));
});

// Modal (dialog) open/close
qsa('[data-modal-open]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-modal-open');
        const dlg = id ? qs(`[data-modal="${id}"]`) : null;
        if (!dlg) return;
        if (typeof dlg.showModal === 'function') dlg.showModal();
        else dlg.classList.remove('hidden');
    });
});

qsa('[data-modal-close]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const dlg = btn.closest('[data-modal]');
        if (!dlg) return;
        if (typeof dlg.close === 'function') dlg.close();
        else dlg.classList.add('hidden');
    });
});

// Dummy actions for non-wired buttons/links
document.addEventListener('click', (e) => {
    const t = e.target;
    if (!(t instanceof Element)) return;

    const el = t.closest('[data-dummy]');
    if (!el) return;

    e.preventDefault();
    const msg = qs('meta[name="dummy-toast"]')?.content || 'Fitur ini belum diaktifkan';
    const sub = qs('meta[name="dummy-toast-sub"]')?.content || 'Tugas Backend ah';
    showToast(msg, 'warning', sub);
});
