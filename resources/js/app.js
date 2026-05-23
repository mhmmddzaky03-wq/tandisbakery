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
    el.className = 'pointer-events-auto bakery-card overflow-hidden ring-0 shadow-lg';
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

function closeModal(dlg) {
    if (!dlg) return;
    if (typeof dlg.close === 'function' && dlg.open) {
        dlg.close();
    } else {
        dlg.classList.add('hidden');
    }
}

function openModal(dlg) {
    if (!dlg) return;
    if (typeof dlg.showModal === 'function') {
        dlg.showModal();
    } else {
        dlg.classList.remove('hidden');
    }

    const focusable = dlg.querySelector(
        'input:not([type="hidden"]):not([disabled]), select:not([disabled]), textarea:not([disabled])'
    );
    if (focusable) {
        requestAnimationFrame(() => focusable.focus());
    }
}

let lastModalTrigger = null;

qsa('[data-modal-open]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const id = btn.getAttribute('data-modal-open');
        const dlg = id ? qs(`[data-modal="${id}"]`) : null;
        lastModalTrigger = btn;
        openModal(dlg);
    });
});

qsa('[data-modal-close]').forEach((btn) => {
    btn.addEventListener('click', () => {
        closeModal(btn.closest('[data-modal], dialog'));
    });
});

qsa('dialog[data-modal]').forEach((dlg) => {
    dlg.addEventListener('click', (e) => {
        if (e.target === dlg) {
            closeModal(dlg);
        }
    });

    dlg.addEventListener('close', () => {
        if (lastModalTrigger && typeof lastModalTrigger.focus === 'function') {
            lastModalTrigger.focus();
        }
    });

    dlg.addEventListener('cancel', (e) => {
        e.preventDefault();
        closeModal(dlg);
    });
});

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    const openDlg = qs('dialog[data-modal][open]');
    if (openDlg) {
        e.preventDefault();
        closeModal(openDlg);
    }
});

qsa('form[data-modal-form]').forEach((form) => {
    form.addEventListener('submit', () => {
        const btn = qs('[data-submit-btn]', form);
        const label = qs('[data-submit-label]', form);
        const loading = qs('[data-submit-loading]', form);
        if (!btn) return;
        btn.disabled = true;
        if (label) label.classList.add('hidden');
        if (loading) loading.classList.remove('hidden');
    });
});

qsa('form[data-product-form]').forEach((form) => {
    const select = qs('[name="product_id"]', form);
    const nameInput = qs('[data-product-name]', form);
    const unitInput = qs('[data-product-unit]', form);
    if (!select || !nameInput) return;

    const sync = () => {
        const opt = select.options[select.selectedIndex];
        if (opt?.value) {
            nameInput.value = opt.dataset.nama || opt.textContent.trim();
            if (unitInput && opt.dataset.satuan) {
                unitInput.value = opt.dataset.satuan;
            }
        }
    };

    select.addEventListener('change', sync);
});

qsa('form[data-journal-form]').forEach((form) => {
    form.addEventListener('submit', (e) => {
        const amount = Number(qs('[name="jumlah"]', form)?.value || 0);
        const debitAcc = qs('[name="akun_debit"]', form)?.value;
        const creditAcc = qs('[name="akun_kredit"]', form)?.value;

        if (!amount || amount <= 0) {
            e.preventDefault();
            showToast('Nominal harus lebih dari 0', 'danger');
            return;
        }

        if (debitAcc === creditAcc) {
            e.preventDefault();
            showToast('Akun debit dan kredit harus berbeda', 'danger');
            return;
        }

        const ensureHidden = (name, value) => {
            let el = qs(`input[name="${name}"]`, form);
            if (!el) {
                el = document.createElement('input');
                el.type = 'hidden';
                el.name = name;
                form.appendChild(el);
            }
            el.value = value;
        };

        ensureHidden('lines[0][account_kode]', debitAcc);
        ensureHidden('lines[0][debit]', String(amount));
        ensureHidden('lines[0][credit]', '0');
        ensureHidden('lines[1][account_kode]', creditAcc);
        ensureHidden('lines[1][debit]', '0');
        ensureHidden('lines[1][credit]', String(amount));
    });
});

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

const overlay = qs('[data-sidebar-overlay]');
if (overlay) {
    overlay.addEventListener('click', () => {
        const sidebar = qs('[data-sidebar]');
        if (!sidebar) return;
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
}

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

qsa('[data-print]').forEach((btn) => {
    btn.addEventListener('click', () => window.print());
});

const flash = qs('[data-flash-success]');
if (flash) {
    showToast(flash.textContent?.trim() || 'Berhasil', 'success');
}

qsa('dialog[data-auto-open="true"]').forEach((dlg) => openModal(dlg));
