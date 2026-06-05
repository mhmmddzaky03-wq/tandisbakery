import { initDashboardCharts } from './dashboard-charts';

function handleProductionDelete(btn) {
    if (!btn) return;

    const isLinked = btn.getAttribute('data-has-product') === '1';
    if (isLinked) {
        const message = btn.getAttribute('data-linked-message') || 'Data tidak dapat dihapus. Sudah terdaftar sebagai produk.';
        showToast(message, 'danger');
        return;
    }

    const formId = btn.getAttribute('data-delete-form');
    const form = formId ? document.getElementById(formId) : null;
    if (!form) return;

    const confirmMsg = btn.getAttribute('data-confirm-message') || 'Hapus data produksi ini?';
    if (window.confirm(confirmMsg)) {
        form.submit();
    }
}

window.handleProductionDelete = handleProductionDelete;

function handleConfirmDelete(btn) {
    if (!btn) return;

    const formId = btn.getAttribute('data-delete-form');
    const form = formId ? document.getElementById(formId) : null;
    if (!form) return;

    const confirmMsg = btn.getAttribute('data-confirm-message') || 'Hapus data ini?';
    if (window.confirm(confirmMsg)) {
        form.submit();
    }
}

window.handleConfirmDelete = handleConfirmDelete;

function handleBlockedDelete(btn) {
    if (!btn) return;

    const confirmMsg = btn.getAttribute('data-confirm-message') || 'Hapus data ini?';
    if (!window.confirm(confirmMsg)) return;

    if (btn.getAttribute('data-delete-blocked') === '1') {
        showToast(
            btn.getAttribute('data-blocked-message') || 'Data tidak dapat dihapus.',
            'danger'
        );
        return;
    }

    const formId = btn.getAttribute('data-delete-form');
    const form = formId ? document.getElementById(formId) : null;
    if (form) form.submit();
}

window.handleBlockedDelete = handleBlockedDelete;

function qs(sel, root = document) {
    return root.querySelector(sel);
}

function qsa(sel, root = document) {
    return Array.from(root.querySelectorAll(sel));
}

function toTitleCase(value) {
    if (!value) return value;

    return value
        .toLowerCase()
        .replace(/(?:^|\s|[-/])\S/g, (match) => match.toUpperCase());
}

function applyTitleCaseInput(input) {
    if (!input || input.readOnly || input.disabled) return;

    const formatted = toTitleCase(input.value.trim());
    if (formatted !== input.value) {
        input.value = formatted;
    }
}

function bindTitleCaseInputs(root = document) {
    qsa('[data-title-case]', root).forEach((input) => {
        if (input.dataset.titleCaseBound === 'true') return;

        input.dataset.titleCaseBound = 'true';
        input.addEventListener('blur', () => applyTitleCaseInput(input));
        input.addEventListener('change', () => applyTitleCaseInput(input));
    });
}

function sanitizeIntegerValue(raw) {
    let value = String(raw ?? '').trim().replace(',', '.');

    if (value.includes('.')) {
        const parsed = parseFloat(value);
        if (Number.isFinite(parsed)) {
            return String(Math.round(parsed));
        }
    }

    return value.replace(/\D/g, '');
}

function isZeroLikeQtyValue(value) {
    const trimmed = String(value ?? '').trim();
    return trimmed === '' || trimmed === '0' || trimmed === '0,0' || trimmed === '0.0' || trimmed === '0,';
}

function normalizeQtyInputValue(value) {
    return isZeroLikeQtyValue(value) ? '' : String(value ?? '');
}

function isQtyLikeInput(input) {
    return input instanceof HTMLInputElement && (
        isDecimalOneInput(input)
        || isIntegerInput(input)
        || input.hasAttribute('data-material-qty')
        || input.hasAttribute('data-bd-qty')
    );
}

function applyIntegerInput(input) {
    if (!input) return;
    input.value = sanitizeIntegerValue(input.value);
}

function sanitizeDecimalOneValue(raw) {
    let value = String(raw ?? '').replace(',', '.').replace(/[^\d.]/g, '');

    const dotIndex = value.indexOf('.');
    if (dotIndex === -1) {
        return value;
    }

    const whole = value.slice(0, dotIndex);
    const fraction = value.slice(dotIndex + 1).replace(/\./g, '').slice(0, 1);

    return `${whole}.${fraction}`;
}

function formatDecimalOneValue(raw) {
    const cleaned = sanitizeDecimalOneValue(raw);
    if (cleaned === '' || cleaned === '.') return '';

    const n = Math.round(parseFloat(cleaned) * 10) / 10;
    if (!Number.isFinite(n) || n < 0) return cleaned;

    return n % 1 === 0 ? String(Math.trunc(n)) : n.toFixed(1);
}

function applyDecimalOneInput(input) {
    if (!input) return;
    input.value = formatDecimalOneValue(input.value);
}

function isDecimalOneInput(input) {
    return input instanceof HTMLInputElement && input.hasAttribute('data-decimal-one');
}

function isIntegerInput(input) {
    return input instanceof HTMLInputElement && input.hasAttribute('data-integer-only');
}

function initQtyInputHandlers() {
    document.addEventListener(
        'keydown',
        (e) => {
            if (!isDecimalOneInput(e.target) && !isIntegerInput(e.target)) return;

            if (e.key === ',' || e.key === '.') {
                if (isIntegerInput(e.target)) {
                    e.preventDefault();
                    return;
                }

                if (isDecimalOneInput(e.target) && e.target.value.includes('.')) {
                    e.preventDefault();
                }
            }
        },
        true
    );

    document.addEventListener(
        'input',
        (e) => {
            if (isDecimalOneInput(e.target)) {
                const cleaned = sanitizeDecimalOneValue(e.target.value);
                if (e.target.value !== cleaned) {
                    e.target.value = cleaned;
                }
                return;
            }

            if (isIntegerInput(e.target)) {
                const cleaned = sanitizeIntegerValue(e.target.value);
                if (e.target.value !== cleaned) {
                    e.target.value = cleaned;
                }
            }
        },
        true
    );

    document.addEventListener(
        'paste',
        (e) => {
            if (isDecimalOneInput(e.target)) {
                e.preventDefault();
                const pasted = e.clipboardData?.getData('text') ?? '';
                e.target.value = formatDecimalOneValue(pasted);
                return;
            }

            if (isIntegerInput(e.target)) {
                e.preventDefault();
                const pasted = e.clipboardData?.getData('text') ?? '';
                e.target.value = sanitizeIntegerValue(pasted);
            }
        },
        true
    );

    document.addEventListener(
        'blur',
        (e) => {
            if (isDecimalOneInput(e.target)) {
                applyDecimalOneInput(e.target);
                return;
            }

            if (isIntegerInput(e.target)) {
                applyIntegerInput(e.target);
            }
        },
        true
    );

    document.addEventListener(
        'focus',
        (e) => {
            if (!isQtyLikeInput(e.target)) return;
            if (isZeroLikeQtyValue(e.target.value)) {
                e.target.value = '';
            }
        },
        true
    );

    document.addEventListener(
        'wheel',
        (e) => {
            const target = e.target;
            if (
                target instanceof HTMLInputElement
                && target.type === 'number'
                && document.activeElement === target
            ) {
                e.preventDefault();
            }
        },
        { passive: false }
    );
}

function bindIntegerInputs() {}

function bindDecimalOneInputs() {}

bindTitleCaseInputs();
initQtyInputHandlers();

document.addEventListener(
    'submit',
    (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;

        bindTitleCaseInputs(form);
        qsa('[data-title-case]', form).forEach(applyTitleCaseInput);
        qsa('input[data-decimal-one]', form).forEach(applyDecimalOneInput);
        qsa('input[data-integer-only]', form).forEach(applyIntegerInput);
    },
    true
);

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
        ${sub ? `<div class="mt-1 text-xs font-semibold text-white/80">${sub}</div>` : ''}
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

    ensureProductionMaterialSections(dlg);
    ensureProductionBahanDasarSections(dlg);

    if (dlg.getAttribute('data-modal') === 'prod-baru' || qs('[data-production-materials]', dlg)) {
        const section = qs('[data-production-materials]', dlg);
        if (section?.dataset.productionMaterialsBroken === '1') {
            delete section.dataset.productionMaterialsBroken;
            delete section.dataset.productionMaterialsBound;
            initProductionMaterialSection(section);
        }
    }

    const errorBanner = qs('[data-form-error-banner]', dlg);
    if (errorBanner) {
        requestAnimationFrame(() => {
            errorBanner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
        return;
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
        const productionSection = qs('[data-production-materials]', form);
        const productionApi = productionSection ? productionSectionRegistry.get(productionSection) : null;
        if (productionApi?.reindexRows) {
            productionApi.reindexRows();
        }

        const bahanDasarSection = qs('[data-production-bahan-dasar]', form);
        const bahanDasarApi = bahanDasarSection ? bahanDasarSectionRegistry.get(bahanDasarSection) : null;
        if (bahanDasarApi?.reindexRows) {
            bahanDasarApi.reindexRows();
        }

        const btn = qs('[data-submit-btn]', form)
            ?? (form.id ? qs(`[data-submit-btn][form="${form.id}"]`) : null);
        const label = btn ? qs('[data-submit-label]', btn) : null;
        const loading = btn ? qs('[data-submit-loading]', btn) : null;
        if (!btn) return;
        btn.disabled = true;
        if (label) label.classList.add('hidden');
        if (loading) loading.classList.remove('hidden');
    });
});

qsa('form[data-stock-form]').forEach((form) => {
    const select = qs('[name="satuan"]', form);
    const suffixes = qsa('[data-stock-unit-suffix]', form);
    if (!select || suffixes.length === 0) return;

    const sync = () => {
        const unit = select.value || '—';
        suffixes.forEach((el) => {
            el.textContent = unit;
        });
    };

    select.addEventListener('change', sync);
    sync();
});

qsa('form[data-production-select-form]').forEach((form) => {
    const select = qs('[name="production_record_id"]', form);
    const namePreview = qs('[name="nama_preview"]', form);
    const unitPreview = qs('[name="satuan_preview"]', form);
    if (!select || !namePreview || !unitPreview) return;

    const sync = () => {
        const opt = select.options[select.selectedIndex];
        if (opt?.value) {
            namePreview.value = opt.dataset.nama || '';
            unitPreview.value = opt.dataset.satuan || '';
        } else {
            namePreview.value = '';
            unitPreview.value = '';
        }
    };

    select.addEventListener('change', sync);
    sync();
});

// Sidebar toggle (mobile)
function closeSidebar() {
    const sidebar = qs('[data-sidebar]');
    const overlay = qs('[data-sidebar-overlay]');
    const toggle = qs('[data-sidebar-toggle]');
    if (sidebar) sidebar.classList.add('-translate-x-full');
    if (overlay) overlay.classList.add('hidden');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
}

function openSidebar() {
    const sidebar = qs('[data-sidebar]');
    const overlay = qs('[data-sidebar-overlay]');
    const toggle = qs('[data-sidebar-toggle]');
    if (sidebar) sidebar.classList.remove('-translate-x-full');
    if (overlay) overlay.classList.remove('hidden');
    if (toggle) toggle.setAttribute('aria-expanded', 'true');
}

qsa('[data-sidebar-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const sidebar = qs('[data-sidebar]');
        if (!sidebar) return;
        const isClosed = sidebar.classList.contains('-translate-x-full');
        if (isClosed) openSidebar();
        else closeSidebar();
    });
});

qsa('[data-sidebar-close]').forEach((btn) => {
    btn.addEventListener('click', closeSidebar);
});

const overlay = qs('[data-sidebar-overlay]');
if (overlay) {
    overlay.addEventListener('click', closeSidebar);
}

const sidebarEl = qs('[data-sidebar]');
if (sidebarEl) {
    qsa('a', sidebarEl).forEach((link) => {
        link.addEventListener('click', () => {
            if (window.matchMedia('(max-width: 1023px)').matches) {
                closeSidebar();
            }
        });
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

qsa('[data-lang-picker-inactive]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const menu = btn.closest('[data-dropdown-menu]');
        if (menu) {
            menu.classList.add('hidden');
        }
        showToast('Bahasa Inggris belum tersedia', 'info', 'Masih dalam pengembangan');
    });
});

qsa('[data-lang-picker-active]').forEach((btn) => {
    btn.addEventListener('click', (e) => {
        e.stopPropagation();
        const menu = btn.closest('[data-dropdown-menu]');
        if (menu) {
            menu.classList.add('hidden');
        }
    });
});

const flash = qs('[data-flash-success]');
if (flash) {
    showToast(flash.textContent?.trim() || 'Berhasil', 'success');
}

const flashError = qs('[data-flash-error]');
if (flashError) {
    showToast(flashError.textContent?.trim() || 'Terjadi kesalahan', 'danger');
}

window.showToast = showToast;

qsa('dialog[data-auto-open="true"]').forEach((dlg) => openModal(dlg));

function debounce(fn, delay = 250) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
}

function filterSearchableTable(wrapper) {
    const input = qs('[data-table-search-input]', wrapper);
    const body = qs('[data-table-search-body]', wrapper);
    if (!input || !body) return;

    const rows = qsa('[data-searchable-row]', body);
    const emptyRow = qs('[data-table-empty]', body);
    const noResultsRow = qs('[data-table-no-results]', body);
    const query = input.value.trim().toLowerCase();

    let visible = 0;
    rows.forEach((row) => {
        const haystack = (row.dataset.search || row.textContent || '').toLowerCase();
        const match = !query || haystack.includes(query);
        row.classList.toggle('hidden', !match);
        if (match) visible += 1;
    });

    if (emptyRow) {
        emptyRow.classList.toggle('hidden', rows.length > 0);
    }

    if (noResultsRow) {
        noResultsRow.classList.toggle('hidden', visible > 0 || rows.length === 0);
    }
}

qsa('[data-table-search]').forEach((wrapper) => {
    const input = qs('[data-table-search-input]', wrapper);
    if (!input) return;

    const run = debounce(() => filterSearchableTable(wrapper), 200);
    input.addEventListener('input', run);
    input.addEventListener('search', run);
    filterSearchableTable(wrapper);
});

qsa('[data-unit-add-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const card = btn.closest('[data-unit-card]');
        const formWrap = card ? qs('[data-unit-add-form]', card) : null;
        if (!formWrap) return;

        const isHidden = formWrap.classList.contains('hidden');
        formWrap.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');

        if (isHidden) {
            const input = qs('input[name="nama_satuan"]', formWrap);
            input?.focus();
        }
    });
});

qsa('[data-category-add-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const card = btn.closest('[data-category-card]');
        const formWrap = card ? qs('[data-category-add-form]', card) : null;
        if (!formWrap) return;

        const isHidden = formWrap.classList.contains('hidden');
        formWrap.classList.toggle('hidden');
        btn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');

        if (isHidden) {
            const input = qs('input[name="nama"]', formWrap);
            input?.focus();
        }
    });
});

function formatQtyOneDisplay(value, unit = null) {
    return formatIdQty(value, unit);
}

const UNIT_ALIASES = {
    kilogram: 'kg',
    kg: 'kg',
    gram: 'gram',
    g: 'gram',
    liter: 'L',
    l: 'L',
    ml: 'ml',
    mililiter: 'ml',
    milliliter: 'ml',
};

const UNIT_MULTIPLIERS = {
    kg: 1,
    gram: 0.001,
    L: 1,
    ml: 0.001,
};

const UNIT_SHORT_LABELS = {
    kg: 'kg',
    gram: 'gr',
    L: 'L',
    ml: 'mL',
};

function resolveUnit(unit) {
    if (!unit) return null;
    return UNIT_ALIASES[String(unit).trim().toLowerCase()] ?? null;
}

function roundConvertedQty(value, unit) {
    const resolved = resolveUnit(unit);
    const n = parseFloat(value);
    if (!Number.isFinite(n)) return 0;

    const rounded = Math.round(n * 1_000_000) / 1_000_000;

    if (resolved === 'gram' || resolved === 'ml') {
        if (Math.abs(rounded - Math.round(rounded)) < 0.000_01) {
            return Math.round(rounded);
        }
        return Math.round(rounded * 10) / 10;
    }

    if (Math.abs(rounded) < 1 && rounded !== 0) {
        return Math.round(rounded * 10_000) / 10_000;
    }

    const oneDecimal = Math.round(rounded * 10) / 10;
    return Math.floor(oneDecimal) === oneDecimal ? Math.trunc(oneDecimal) : oneDecimal;
}

function convertUnitQty(qty, fromUnit, toUnit) {
    const exact = convertUnitQtyExact(qty, fromUnit, toUnit);
    if (exact === null) return null;

    return roundConvertedQty(exact, resolveUnit(toUnit) || toUnit);
}

function convertUnitQtyExact(qty, fromUnit, toUnit) {
    const from = resolveUnit(fromUnit);
    const to = resolveUnit(toUnit);
    if (!from || !to) {
        return from === to ? qty : null;
    }
    if (from === to) return qty;

    const fromFactor = UNIT_MULTIPLIERS[from];
    const toFactor = UNIT_MULTIPLIERS[to];
    if (fromFactor === undefined || toFactor === undefined) return null;

    const result = qty * (fromFactor / toFactor);

    return Math.round(result * 1_000_000_000) / 1_000_000_000;
}

function parseIdQty(value) {
    const raw = String(value ?? '').trim();
    if (raw === '') return 0;

    if (raw.includes(',') && raw.includes('.')) {
        return parseFloat(raw.replace(/\./g, '').replace(',', '.')) || 0;
    }

    if (raw.includes(',')) {
        return parseFloat(raw.replace(',', '.')) || 0;
    }

    return parseFloat(raw) || 0;
}

function formatIdQty(value, unit = null) {
    const resolved = resolveUnit(unit) || unit;
    const isSmallUnit = resolved === 'gram' || resolved === 'ml';
    const n = roundConvertedQty(value, resolved || unit);

    if (!Number.isFinite(n)) return '0';
    if (n === 0) return '0';

    if (Number.isInteger(n) && Math.abs(n) >= 1) {
        return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    const decimals = isSmallUnit ? 1 : Math.abs(n) < 1 ? 4 : 1;
    const fixed = n.toFixed(decimals);
    const [intPart, decPart] = fixed.split('.');
    const intFormatted = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    const trimmedDec = decPart.replace(/0+$/, '');

    return trimmedDec ? `${intFormatted},${trimmedDec}` : intFormatted;
}

function showUnitEl(el) {
    if (!el) return;
    el.classList.remove('hidden');
    el.hidden = false;
}

function hideUnitEl(el) {
    if (!el) return;
    el.classList.add('hidden');
    el.hidden = true;
}

function unitShortLabel(unit, labels = UNIT_SHORT_LABELS) {
    return labels[unit] || unit || '—';
}

function setUnitToggleValue(toggle, value) {
    if (!toggle) return;

    toggle.dataset.selected = value;
    const input = qs('[data-unit-toggle-input]', toggle.closest('[data-material-unit-wrap], [data-unit-display], [data-material-stock-unit-wrap]') || toggle.parentElement) || qs('[data-unit-toggle-input]', toggle.parentElement);

    if (input) input.value = value;

    qsa('[data-unit-value]', toggle).forEach((btn) => {
        const active = btn.dataset.unitValue === value;
        btn.classList.toggle('unit-toggle-btn--active', active);
        btn.setAttribute('aria-pressed', active ? 'true' : 'false');
    });
}

function buildUnitToggle(toggle, units, selectedUnit, labels = UNIT_SHORT_LABELS) {
    if (!toggle) return;

    toggle.innerHTML = '';
    units.forEach((unit) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'unit-toggle-btn';
        btn.dataset.unitValue = unit;
        btn.textContent = labels[unit] || unit;
        toggle.appendChild(btn);
    });

    delete toggle.dataset.unitToggleBound;
    initUnitToggles(toggle);
    setUnitToggleValue(toggle, selectedUnit || units[0] || '');
}

function refreshUnitDisplay(wrap) {
    const baseQty = parseFloat(wrap.dataset.baseQty);
    const baseUnit = wrap.dataset.baseUnit || '';
    const qtyEl = qs('[data-unit-display-qty]', wrap);
    const toggle = qs('[data-unit-toggle]', wrap);
    if (!qtyEl || !Number.isFinite(baseQty)) return;

    const targetUnit = toggle?.dataset.selected || baseUnit;
    const converted = convertUnitQty(baseQty, baseUnit, targetUnit);
    qtyEl.textContent = formatIdQty(converted ?? baseQty, targetUnit);
}

function collectUnitToggles(root = document) {
    if (root instanceof Element && root.matches('[data-unit-toggle]')) {
        return [root];
    }

    return qsa('[data-unit-toggle]', root);
}

function initUnitToggles(root = document) {
    collectUnitToggles(root).forEach((toggle) => {
        if (toggle.dataset.unitToggleBound === '1') return;
        toggle.dataset.unitToggleBound = '1';

        qsa('button[data-unit-value]', toggle).forEach((btn) => {
            btn.addEventListener('click', () => {
                if (toggle.hasAttribute('data-unit-toggle-disabled')) return;

                const value = btn.dataset.unitValue;

                if (toggle.closest('[data-production-row]')) {
                    toggle.dispatchEvent(new CustomEvent('unit-toggle-change', { bubbles: true, detail: { value } }));
                    return;
                }

                setUnitToggleValue(toggle, value);

                const wrap = toggle.closest('[data-unit-display]');
                if (wrap) refreshUnitDisplay(wrap);

                toggle.dispatchEvent(new CustomEvent('unit-toggle-change', { bubbles: true, detail: { value } }));
            });
        });

        const initial = toggle.dataset.selected || qs('[data-unit-toggle-input]', toggle.parentElement)?.value;
        if (initial) setUnitToggleValue(toggle, initial);
    });
}

function initUnitDisplays(root = document) {
    qsa('[data-unit-display]', root).forEach((wrap) => {
        if (wrap.dataset.unitDisplayBound === '1') return;
        wrap.dataset.unitDisplayBound = '1';
        initUnitToggles(wrap);
        refreshUnitDisplay(wrap);
    });
}

const productionSectionRegistry = new WeakMap();

function bindProductionMaterialDocumentEvents() {
    if (bindProductionMaterialDocumentEvents.initialized) return;
    bindProductionMaterialDocumentEvents.initialized = true;

    document.addEventListener('unit-toggle-change', (event) => {
        const toggle = event.target?.closest?.('[data-unit-toggle]');
        if (!toggle) return;

        const row = toggle.closest('[data-production-row]');
        if (!row) return;

        const section = row.closest('[data-production-materials]');
        const api = section ? productionSectionRegistry.get(section) : null;
        if (!api?.rowsWrap?.contains(row)) return;

        const material = api.getMaterial(qs('[data-material-select]', row)?.value);
        if (!material) return;

        api.handleUnitSwitch(row, material, event.detail.value, toggle);
    });

    document.addEventListener('change', (event) => {
        const select = event.target?.closest?.('[data-material-select]');
        if (!select) return;

        const row = select.closest('[data-production-row]');
        if (!row) return;

        const section = row.closest('[data-production-materials]');
        const api = section ? productionSectionRegistry.get(section) : null;
        if (!api?.rowsWrap?.contains(row)) return;

        delete row.dataset.materialId;
        delete row.dataset.usageUnit;

        const picked = api.getMaterial(select.value);
        api.setupBatchControls(row, picked, '');
        api.setupUnitControls(row, picked, picked?.satuan || '');
        if (picked) row.dataset.materialId = String(picked.id);
        api.syncRow(row);
        api.syncMaterialRequiredState?.();
    });

    document.addEventListener('change', (event) => {
        const batchSelect = event.target?.closest?.('[data-material-batch-select]');
        if (!batchSelect) return;

        const row = batchSelect.closest('[data-production-row]');
        if (!row) return;

        const section = row.closest('[data-production-materials]');
        const api = section ? productionSectionRegistry.get(section) : null;
        if (!api?.rowsWrap?.contains(row)) return;

        if (batchSelect.value) row.dataset.batchId = batchSelect.value;
        api.refreshAllBatchControls();
    });
}

function initProductionMaterialSection(section) {
    if (section.dataset.productionMaterialsBound === '1') return;

    let materials = [];
    let initialRows = [{ raw_material_id: '', raw_material_restock_id: '', jumlah: '', satuan: '' }];
    let unitLabels = UNIT_SHORT_LABELS;
    const selectPlaceholder = section.dataset.selectPlaceholder || '—';
    const batchPlaceholder = section.dataset.batchPlaceholder || 'Pilih batch stok';

    function readSectionJson(selector, fallback) {
        const el = qs(selector, section);
        if (!el) return fallback;

        try {
            return JSON.parse(el.textContent || '');
        } catch {
            return fallback;
        }
    }

    try {
        materials = readSectionJson('[data-production-materials-json]', []);
        initialRows = readSectionJson('[data-production-initial-rows]', [{ raw_material_id: '', raw_material_restock_id: '', jumlah: '', satuan: '' }]);
        unitLabels = { ...UNIT_SHORT_LABELS, ...readSectionJson('[data-production-unit-labels]', {}) };
    } catch {
        materials = [];
    }

    if (!Array.isArray(initialRows) || initialRows.length === 0) {
        initialRows = [{ raw_material_id: '', raw_material_restock_id: '', jumlah: '', satuan: '' }];
    }

    const rowsWrap = qs('[data-production-rows]', section);
    const template = qs('[data-production-row-template]', section);
    const addBtn = qs('[data-production-add-row]', section);
    if (!rowsWrap || !template) {
        section.dataset.productionMaterialsBroken = '1';
        return;
    }

    let rowIndex = 0;

    function getMaterial(id) {
        if (id === '' || id == null) return undefined;

        return materials.find((item) => String(item.id) === String(id));
    }

    function getBatch(material, batchId) {
        if (!material || batchId === '' || batchId == null) return undefined;

        return (material.batches || []).find((batch) => String(batch.id) === String(batchId));
    }

    function getSelectedBatch(row, material) {
        if (!material) return undefined;

        const batchHidden = qs('[data-material-batch-hidden]', row);
        const batchSelect = qs('[data-material-batch-select]', row);

        if (batchHidden && !batchHidden.disabled && batchHidden.value) {
            return getBatch(material, batchHidden.value);
        }

        if (batchSelect && !batchSelect.disabled && batchSelect.value) {
            return getBatch(material, batchSelect.value);
        }

        return undefined;
    }

    function getUsedBatchIds(excludeRow = null) {
        const ids = new Set();

        qsa('[data-production-row]', rowsWrap).forEach((otherRow) => {
            if (otherRow === excludeRow) return;

            const otherMaterial = getMaterial(qs('[data-material-select]', otherRow)?.value);
            const otherBatch = getSelectedBatch(otherRow, otherMaterial);
            if (otherBatch) ids.add(String(otherBatch.id));
        });

        return ids;
    }

    function availableBatchesForRow(row, material, selectedBatchId = '') {
        if (!material) return [];

        const usedElsewhere = getUsedBatchIds(row);

        return (material.batches || []).filter((batch) => {
            const id = String(batch.id);
            return !usedElsewhere.has(id) || id === String(selectedBatchId);
        });
    }

    function refreshAllBatchControls() {
        qsa('[data-production-row]', rowsWrap).forEach((row) => {
            const material = getMaterial(qs('[data-material-select]', row)?.value);
            if (!material) return;

            const currentBatch = getSelectedBatch(row, material);
            const batchId = currentBatch ? String(currentBatch.id) : '';
            setupBatchControls(row, material, batchId);
            syncRow(row);
        });
    }

    function materialHasBatches(material) {
        return (material?.batches || []).length > 0;
    }

    function getStockSource(row, material) {
        const batch = getSelectedBatch(row, material);
        if (batch) {
            return { type: 'batch', stock: batch.sisa };
        }

        if (material && ! materialHasBatches(material)) {
            const stock = material.jumlah;
            if (stock <= 0) return null;

            return { type: 'material', stock };
        }

        return null;
    }

    function setupBatchControls(row, material, selectedBatchId = '') {
        const batchSelect = qs('[data-material-batch-select]', row);
        const batchHidden = qs('[data-material-batch-hidden]', row);
        const batches = availableBatchesForRow(row, material, selectedBatchId);

        if (!batchSelect || !batchHidden) return undefined;

        batchSelect.innerHTML = '';
        const empty = document.createElement('option');
        empty.value = '';
        empty.textContent = batchPlaceholder;
        empty.disabled = true;
        empty.selected = !selectedBatchId;
        batchSelect.appendChild(empty);

        batches.forEach((batch) => {
            const opt = document.createElement('option');
            opt.value = String(batch.id);
            opt.textContent = batch.label;
            opt.selected = String(batch.id) === String(selectedBatchId);
            batchSelect.appendChild(opt);
        });

        if (!material || batches.length === 0) {
            hideUnitEl(batchSelect);
            hideUnitEl(batchHidden);
            batchSelect.disabled = true;
            batchSelect.required = false;
            batchHidden.disabled = true;
            batchHidden.required = false;
            batchHidden.value = '';
            delete row.dataset.batchId;
            reindexRows();
            return undefined;
        }

        const activeId = selectedBatchId || (batches.length === 1 ? String(batches[0].id) : '');

        if (batches.length === 1) {
            hideUnitEl(batchSelect);
            batchSelect.disabled = true;
            batchSelect.required = false;
            batchHidden.value = String(batches[0].id);
            batchHidden.disabled = false;
            batchHidden.required = true;
            row.dataset.batchId = String(batches[0].id);
            reindexRows();
            return batches[0];
        }

        showUnitEl(batchSelect);
        batchSelect.disabled = false;
        batchSelect.required = true;
        batchHidden.disabled = true;
        batchHidden.required = false;
        batchHidden.value = '';

        if (activeId) {
            batchSelect.value = activeId;
            row.dataset.batchId = activeId;
        } else {
            delete row.dataset.batchId;
        }

        reindexRows();

        return activeId ? getBatch(material, activeId) : undefined;
    }

    function materialUnits(material) {
        if (!material) return [];
        return material.alternatives?.length > 1 ? material.alternatives : [material.satuan];
    }

    function hasUnitToggle(material) {
        return materialUnits(material).length > 1;
    }

    function syncSelectAppearance(select) {
        if (!select) return;
        select.classList.toggle('has-value', Boolean(select.value));
    }

    function buildSelectOptions(select, selectedId) {
        select.innerHTML = '';
        const empty = document.createElement('option');
        empty.value = '';
        empty.textContent = selectPlaceholder;
        empty.disabled = true;
        empty.selected = !selectedId;
        select.appendChild(empty);

        materials.forEach((material) => {
            const opt = document.createElement('option');
            opt.value = material.id;
            opt.textContent = material.nama;
            opt.selected = String(material.id) === String(selectedId);
            select.appendChild(opt);
        });

        syncSelectAppearance(select);
    }

    function syncMaterialRequiredState() {
        const isOptional = section.dataset.materialsOptional === 'true';
        qsa('[data-production-row]', rowsWrap).forEach((row) => {
            const select = qs('[data-material-select]', row);
            const qtyInput = qs('[data-material-qty]', row);
            const batchSelect = qs('[data-material-batch-select]', row);
            const hasMaterial = !!select?.value;

            if (select) {
                if (isOptional) {
                    select.required = false;
                } else {
                    select.required = true;
                }
            }

            if (qtyInput && !qtyInput.disabled) {
                qtyInput.required = !isOptional && hasMaterial;
            }

            if (batchSelect && !batchSelect.disabled) {
                batchSelect.required = !isOptional && hasMaterial;
            }
        });
    }

    function setMaterialsOptional(optional) {
        section.dataset.materialsOptional = optional ? 'true' : 'false';
        syncMaterialRequiredState();
        updateRemoveButtons();

        if (!optional && qsa('[data-production-row]', rowsWrap).length === 0) {
            addRow();
        }
    }

    function setQtyEnabled(row, enabled, keepValue = '') {
        const qtyInput = qs('[data-material-qty]', row);
        if (!qtyInput) return;

        qtyInput.disabled = !enabled;
        qtyInput.required = enabled;

        if (!enabled) {
            qtyInput.value = '';
            qtyInput.required = false;
            return;
        }

        qtyInput.value = normalizeQtyInputValue(keepValue);
        syncMaterialRequiredState();
    }

    function getSelectedUnit(row, material) {
        const input = qs('[data-material-unit-input]', row);
        if (input?.value) return input.value;
        const toggle = qs('[data-material-unit-toggle]', row);
        if (toggle?.dataset.selected) return toggle.dataset.selected;
        return row.dataset.usageUnit || material?.satuan || '';
    }

    function getUsageQtyInMaterialUnit(row, material) {
        const qtyInput = qs('[data-material-qty]', row);
        if (!qtyInput || !material) return 0;

        const raw = qtyInput.value.trim();
        if (raw === '') return 0;

        const usageUnit = getSelectedUnit(row, material);
        const converted = convertUnitQtyExact(parseIdQty(raw), usageUnit, material.satuan);

        return converted ?? 0;
    }

    function setupUnitControls(row, material, selectedUnit = '') {
        const unitWrap = qs('[data-material-unit-wrap]', row);
        const unitToggle = qs('[data-material-unit-toggle]', row);
        const unitInput = qs('[data-material-unit-input]', row);
        const unitStatic = qs('[data-material-unit-static]', row);
        const stockWrap = qs('[data-material-stock-unit-wrap]', row);
        const stockToggle = qs('[data-material-stock-unit-toggle]', row);
        const stockStatic = qs('[data-material-stock-unit-static]', row);

        if (!material) {
            hideUnitEl(unitWrap);
            hideUnitEl(stockWrap);
            hideUnitEl(unitStatic);
            hideUnitEl(stockStatic);
            if (unitInput) unitInput.value = '';
            return;
        }

        const units = materialUnits(material);
        const activeUnit = selectedUnit || material.satuan;
        const shortLabel = unitShortLabel(activeUnit, unitLabels);

        if (hasUnitToggle(material)) {
            showUnitEl(unitWrap);
            showUnitEl(stockWrap);
            hideUnitEl(unitStatic);
            hideUnitEl(stockStatic);
            buildUnitToggle(unitToggle, units, activeUnit, unitLabels);
            buildUnitToggle(stockToggle, units, activeUnit, unitLabels);
            if (unitInput) unitInput.value = activeUnit;
            row.dataset.usageUnit = activeUnit;
        } else {
            hideUnitEl(unitWrap);
            hideUnitEl(stockWrap);
            showUnitEl(unitStatic);
            showUnitEl(stockStatic);
            if (unitStatic) unitStatic.textContent = shortLabel;
            if (stockStatic) stockStatic.textContent = shortLabel;
            if (unitInput) unitInput.value = material.satuan || '';
            row.dataset.usageUnit = material.satuan || '';
        }
    }

    function handleUnitSwitch(row, material, newUnit, sourceToggle = null) {
        row.dataset.usageUnit = newUnit;

        const unitInput = qs('[data-material-unit-input]', row);
        const takaranToggle = qs('[data-material-unit-toggle]', row);
        const stockToggle = qs('[data-material-stock-unit-toggle]', row);

        if (unitInput) unitInput.value = newUnit;
        setUnitToggleValue(takaranToggle, newUnit);
        setUnitToggleValue(stockToggle, newUnit);
        syncRow(row);
    }

    function syncRow(row) {
        const select = qs('[data-material-select]', row);
        const qtyInput = qs('[data-material-qty]', row);
        const stockQtyEl = qs('[data-material-stock-qty]', row);
        const remainQtyEl = qs('[data-material-remain-qty]', row);
        if (!select || !qtyInput || !stockQtyEl || !remainQtyEl) return;

        const material = getMaterial(select.value);
        const insufficientClasses = ['bg-rose-50/80', 'ring-1', 'ring-inset', 'ring-rose-200'];

        syncSelectAppearance(select);

        if (!material) {
            stockQtyEl.textContent = '—';
            remainQtyEl.textContent = '—';
            remainQtyEl.classList.remove('text-rose-600');
            remainQtyEl.classList.add('text-emerald-600');
            delete row.dataset.materialId;
            delete row.dataset.usageUnit;
            delete row.dataset.batchId;
            setupUnitControls(row, null);
            setupBatchControls(row, null);
            setQtyEnabled(row, false);
            insufficientClasses.forEach((cls) => row.classList.remove(cls));
            return;
        }

        const stockSource = getStockSource(row, material);

        if (!stockSource) {
            if (materialHasBatches(material)) {
                stockQtyEl.textContent = '—';
            } else {
                const usageUnit = getSelectedUnit(row, material);
                stockQtyEl.textContent = material.jumlah > 0
                    ? formatIdQty(convertUnitQty(material.jumlah, material.satuan, usageUnit) ?? material.jumlah, usageUnit)
                    : '—';
            }
            remainQtyEl.textContent = '—';
            remainQtyEl.classList.remove('text-rose-600');
            remainQtyEl.classList.add('text-emerald-600');
            setQtyEnabled(row, false);
            insufficientClasses.forEach((cls) => row.classList.remove(cls));
            return;
        }

        setQtyEnabled(row, true, qtyInput.value);

        const usageUnit = getSelectedUnit(row, material);
        const usageQtyInBase = getUsageQtyInMaterialUnit(row, material);
        const baseStock = stockSource.stock;
        const displayStock = convertUnitQty(baseStock, material.satuan, usageUnit) ?? baseStock;

        stockQtyEl.textContent = formatIdQty(displayStock, usageUnit);

        const isInsufficient = usageQtyInBase > baseStock + 0.000_001;
        insufficientClasses.forEach((cls) => row.classList.toggle(cls, isInsufficient));

        const baseRemain = baseStock - usageQtyInBase;
        const displayRemain = convertUnitQty(baseRemain, material.satuan, usageUnit) ?? baseRemain;

        if (isInsufficient) {
            remainQtyEl.textContent = `-${formatIdQty(Math.abs(displayRemain), usageUnit)}`;
            remainQtyEl.classList.remove('text-emerald-600');
            remainQtyEl.classList.add('text-rose-600');
        } else {
            remainQtyEl.textContent = formatIdQty(displayRemain, usageUnit);
            remainQtyEl.classList.remove('text-rose-600');
            remainQtyEl.classList.add('text-emerald-600');
        }

        syncMaterialRequiredState();
    }

    function reindexRows() {
        qsa('[data-production-row]', rowsWrap).forEach((row, idx) => {
            const select = qs('[data-material-select]', row);
            const qtyInput = qs('[data-material-qty]', row);
            const unitInput = qs('[data-material-unit-input]', row);
            const batchSelect = qs('[data-material-batch-select]', row);
            const batchHidden = qs('[data-material-batch-hidden]', row);
            const batchField = `materials[${idx}][raw_material_restock_id]`;

            if (select) select.name = `materials[${idx}][raw_material_id]`;
            if (qtyInput) qtyInput.name = `materials[${idx}][jumlah]`;
            if (unitInput) unitInput.name = `materials[${idx}][satuan]`;

            if (batchHidden && !batchHidden.disabled) {
                batchHidden.name = batchField;
                if (batchSelect) batchSelect.removeAttribute('name');
            } else if (batchSelect && !batchSelect.disabled) {
                batchSelect.name = batchField;
                if (batchHidden) batchHidden.removeAttribute('name');
            } else {
                if (batchHidden) {
                    batchHidden.removeAttribute('name');
                    batchHidden.value = '';
                }
                if (batchSelect) batchSelect.removeAttribute('name');
            }
        });
        rowIndex = qsa('[data-production-row]', rowsWrap).length;
    }

    function updateRemoveButtons() {
        const isOptional = section.dataset.materialsOptional === 'true';
        qsa('[data-production-row]', rowsWrap).forEach((row) => {
            const btn = qs('[data-production-remove-row]', row);
            if (!btn) return;
            const isOnly = qsa('[data-production-row]', rowsWrap).length <= 1;
            btn.disabled = isOnly && !isOptional;
            btn.classList.toggle('opacity-30', isOnly && !isOptional);
            btn.classList.toggle('pointer-events-none', isOnly && !isOptional);
        });
    }

    function addRow(data = { raw_material_id: '', raw_material_restock_id: '', jumlah: '', satuan: '' }) {
        const html = template.innerHTML.replaceAll('__INDEX__', String(rowIndex));
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const row = wrapper.firstElementChild;
        if (!row) return;

        rowsWrap.appendChild(row);

        const select = qs('[data-material-select]', row);
        const qtyInput = qs('[data-material-qty]', row);

        if (select) {
            buildSelectOptions(select, data.raw_material_id || '');
            if (data.raw_material_id) {
                select.value = data.raw_material_id;
            }
        }

        if (qtyInput) {
            qtyInput.addEventListener('input', () => syncRow(row));
            qtyInput.addEventListener('blur', () => {
                const material = getMaterial(select?.value);
                const raw = qtyInput.value.trim();
                if (material && raw !== '') {
                    qtyInput.value = formatIdQty(parseIdQty(raw), getSelectedUnit(row, material));
                }
                syncRow(row);
            });
        }

        qs('[data-production-remove-row]', row)?.addEventListener('click', () => {
            const isOptional = section.dataset.materialsOptional === 'true';
            if (qsa('[data-production-row]', rowsWrap).length <= 1 && !isOptional) {
                showToast('Minimal satu bahan baku', 'warning');
                return;
            }
            row.remove();
            reindexRows();
            updateRemoveButtons();
            refreshAllBatchControls();
        });

        rowIndex += 1;

        const material = getMaterial(data.raw_material_id || '');
        if (material) {
            row.dataset.materialId = String(material.id);
            const usageUnit = data.satuan || material.satuan;
            setupBatchControls(row, material, data.raw_material_restock_id || '');
            setupUnitControls(row, material, usageUnit);
            if (qtyInput && data.jumlah !== '' && data.jumlah != null) {
                qtyInput.value = formatIdQty(parseIdQty(String(data.jumlah)), usageUnit);
            }
        } else {
            setupBatchControls(row, null);
        }

        reindexRows();
        syncRow(row);
        syncMaterialRequiredState();
        updateRemoveButtons();
    }

    rowsWrap.innerHTML = '';
    rowIndex = 0;

    const isOptionalOnInit = section.dataset.materialsOptional === 'true';

    if (initialRows.length === 0) {
        if (!isOptionalOnInit) {
            addRow();
        }
    } else {
        initialRows.forEach((rowData) => addRow(rowData));
    }

    if (addBtn && addBtn.dataset.productionAddBound !== '1') {
        addBtn.dataset.productionAddBound = '1';
        addBtn.addEventListener('click', () => addRow());
    }

    syncMaterialRequiredState();

    productionSectionRegistry.set(section, {
        getMaterial,
        handleUnitSwitch,
        setupUnitControls,
        setupBatchControls,
        refreshAllBatchControls,
        reindexRows,
        syncRow,
        rowsWrap,
        setMaterialsOptional,
        syncMaterialRequiredState,
    });

    section.dataset.productionMaterialsBound = '1';
}

function setupProductionProductField(root = document) {
    qsa('[data-production-product-field]', root).forEach((wrap) => {
        if (wrap.dataset.productionProductBound === '1') return;
        wrap.dataset.productionProductBound = '1';

        const checkbox = qs('[data-production-use-catalog]', wrap);
        const textInput = qs('[data-production-product-text]', wrap);
        const select = qs('[data-production-product-select]', wrap);
        if (!checkbox || !textInput || !select) return;

        const sync = () => {
            const useCatalog = checkbox.checked;

            textInput.classList.toggle('hidden', useCatalog);
            select.classList.toggle('hidden', !useCatalog);
            textInput.disabled = useCatalog;
            select.disabled = !useCatalog;
            textInput.required = !useCatalog;
            select.required = useCatalog;

            if (useCatalog) {
                textInput.removeAttribute('name');
                select.setAttribute('name', 'product_name');
                if (select.value) {
                    textInput.value = select.value;
                }
            } else {
                select.removeAttribute('name');
                textInput.setAttribute('name', 'product_name');
            }
        };

        checkbox.addEventListener('change', sync);
        select.addEventListener('change', () => {
            if (checkbox.checked) {
                textInput.value = select.value;
            }
        });

        sync();
    });
}

function setupProductionBahanDasarToggle(root = document) {
    qsa('[data-production-use-bahan-dasar]', root).forEach((checkbox) => {
        const form = checkbox.closest('form');
        if (!form || checkbox.dataset.bahanDasarToggleBound === '1') return;
        checkbox.dataset.bahanDasarToggleBound = '1';

        const panel = qs('[data-production-bahan-dasar-panel]', form);
        const materialSection = qs('[data-production-materials]', form);
        const badge = qs('[data-production-materials-badge]', form);
        const hint = qs('[data-production-materials-hint]', form);

        const syncBdPanelFields = (enabled) => {
            if (!panel) return;
            qsa('input, select, textarea, button', panel).forEach((el) => {
                if (el === checkbox) return;
                if (el.hasAttribute('data-bahan-dasar-add-row')) return;
                if (el.hasAttribute('data-bahan-dasar-remove-row')) return;

                if (enabled) {
                    if (el.dataset.bdDisabledByToggle === '1') {
                        el.disabled = false;
                        delete el.dataset.bdDisabledByToggle;
                    }
                    if (el.dataset.bdRequired === '1' || el.hasAttribute('data-bd-required')) {
                        el.required = true;
                    }
                } else {
                    if (el.disabled) return;
                    el.disabled = true;
                    el.dataset.bdDisabledByToggle = '1';
                    el.required = false;
                }
            });
        };

        const sync = () => {
            const useBahanDasar = checkbox.checked;

            if (panel) {
                panel.classList.toggle('hidden', !useBahanDasar);
            }

            syncBdPanelFields(useBahanDasar);

            const materialApi = materialSection ? productionSectionRegistry.get(materialSection) : null;
            materialApi?.setMaterialsOptional?.(useBahanDasar);

            if (badge) {
                badge.textContent = useBahanDasar ? 'Opsional' : 'Wajib';
                badge.classList.toggle('bg-violet-50', useBahanDasar);
                badge.classList.toggle('text-violet-700', useBahanDasar);
                badge.classList.toggle('bg-rose-50', !useBahanDasar);
                badge.classList.toggle('text-rose-600', !useBahanDasar);
            }

            if (hint) {
                hint.textContent = useBahanDasar
                    ? 'Bahan baku langsung dari stok. Tidak wajib jika sudah memakai bahan dasar.'
                    : 'Bahan baku langsung dari stok. Wajib diisi jika produksi tidak memakai bahan dasar.';
            }

            if (useBahanDasar && panel) {
                const bdSection = qs('[data-production-bahan-dasar]', panel);
                if (bdSection) {
                    if (bdSection.dataset.bahanDasarBroken === '1') {
                        delete bdSection.dataset.bahanDasarBroken;
                        delete bdSection.dataset.bahanDasarBound;
                    }
                    initProductionBahanDasarSection(bdSection);
                    const bdApi = bahanDasarSectionRegistry.get(bdSection);
                    if (bdApi?.rowsWrap && qsa('[data-bahan-dasar-row]', bdApi.rowsWrap).length === 0) {
                        bdApi.addRow?.();
                    }
                }
            }
        };

        checkbox.addEventListener('change', sync);
        sync();
    });
}

function ensureProductionMaterialSections(root = document) {
    bindProductionMaterialDocumentEvents();
    setupProductionProductField(root);
    qsa('[data-production-materials]', root).forEach((section) => initProductionMaterialSection(section));
    setupProductionBahanDasarToggle(root);
}

const bahanDasarSectionRegistry = new WeakMap();

function initProductionBahanDasarSection(section) {
    if (section.dataset.bahanDasarBound === '1') return;

    let items = [];
    let initialRows = [{ bahan_dasar_id: '', batch_bahan_dasar_id: '', jumlah: '', satuan: '' }];
    const selectPlaceholder = section.dataset.selectPlaceholder || 'Pilih bahan dasar';
    const batchPlaceholder = section.dataset.batchPlaceholder || 'Pilih batch adonan';

    function readJson(selector, fallback) {
        const el = qs(selector, section);
        if (!el) return fallback;
        try {
            return JSON.parse(el.textContent || '');
        } catch {
            return fallback;
        }
    }

    items = readJson('[data-bahan-dasar-json]', []);
    initialRows = readJson('[data-bahan-dasar-initial-rows]', initialRows);
    if (!Array.isArray(initialRows)) initialRows = [];

    const rowsWrap = qs('[data-bahan-dasar-rows]', section);
    const template = qs('[data-bahan-dasar-row-template]', section);
    const addBtn = qs('[data-bahan-dasar-add-row]', section);
    if (!rowsWrap || !template) {
        section.dataset.bahanDasarBroken = '1';
        return;
    }

    let rowIndex = 0;

    function getItem(id) {
        if (!id) return undefined;
        return items.find((item) => String(item.id) === String(id));
    }

    function getBatch(item, batchId) {
        if (!item || !batchId) return undefined;
        return (item.batches || []).find((batch) => String(batch.id) === String(batchId));
    }

    function getSelectedBatch(row, item) {
        const batchHidden = qs('[data-bd-batch-hidden]', row);
        const batchSelect = qs('[data-bd-batch-select]', row);
        if (batchHidden && !batchHidden.disabled && batchHidden.value) {
            return getBatch(item, batchHidden.value);
        }
        if (batchSelect && !batchSelect.disabled && batchSelect.value) {
            return getBatch(item, batchSelect.value);
        }
        return undefined;
    }

    function getUsedBatchIds(excludeRow = null) {
        const ids = new Set();
        qsa('[data-bahan-dasar-row]', rowsWrap).forEach((otherRow) => {
            if (otherRow === excludeRow) return;
            const item = getItem(qs('[data-bd-select]', otherRow)?.value);
            const batch = getSelectedBatch(otherRow, item);
            if (batch) ids.add(String(batch.id));
        });
        return ids;
    }

    function availableBatches(row, item, selectedBatchId = '') {
        if (!item) return [];
        const used = getUsedBatchIds(row);
        return (item.batches || []).filter((batch) => {
            const id = String(batch.id);
            return !used.has(id) || id === String(selectedBatchId);
        });
    }

    function buildItemOptions(select, selectedId = '') {
        select.innerHTML = '';
        const empty = document.createElement('option');
        empty.value = '';
        empty.textContent = selectPlaceholder;
        empty.disabled = true;
        empty.selected = !selectedId;
        select.appendChild(empty);
        items.forEach((item) => {
            const opt = document.createElement('option');
            opt.value = String(item.id);
            opt.textContent = item.nama;
            opt.selected = String(item.id) === String(selectedId);
            select.appendChild(opt);
        });
    }

    function setupBatchControls(row, item, selectedBatchId = '') {
        const batchSelect = qs('[data-bd-batch-select]', row);
        const batchHidden = qs('[data-bd-batch-hidden]', row);
        const batches = availableBatches(row, item, selectedBatchId);
        if (!batchSelect || !batchHidden) return undefined;

        batchSelect.innerHTML = '';
        const empty = document.createElement('option');
        empty.value = '';
        empty.textContent = batchPlaceholder;
        empty.disabled = true;
        empty.selected = !selectedBatchId;
        batchSelect.appendChild(empty);

        batches.forEach((batch) => {
            const opt = document.createElement('option');
            opt.value = String(batch.id);
            opt.textContent = batch.label;
            opt.selected = String(batch.id) === String(selectedBatchId);
            batchSelect.appendChild(opt);
        });

        if (!item || batches.length === 0) {
            hideUnitEl(batchSelect);
            hideUnitEl(batchHidden);
            batchSelect.disabled = true;
            batchHidden.disabled = true;
            batchHidden.value = '';
            return undefined;
        }

        const activeId = selectedBatchId || (batches.length === 1 ? String(batches[0].id) : '');

        if (batches.length === 1) {
            hideUnitEl(batchSelect);
            batchSelect.disabled = true;
            batchHidden.value = String(batches[0].id);
            batchHidden.disabled = false;
            return batches[0];
        }

        showUnitEl(batchSelect);
        batchSelect.disabled = false;
        batchHidden.disabled = true;
        batchHidden.value = '';
        if (activeId) batchSelect.value = activeId;
        return activeId ? getBatch(item, activeId) : undefined;
    }

    function syncRow(row) {
        const select = qs('[data-bd-select]', row);
        const qtyInput = qs('[data-bd-qty]', row);
        const unitInput = qs('[data-bd-unit-input]', row);
        const unitStatic = qs('[data-bd-unit-static]', row);
        const stockQty = qs('[data-bd-stock-qty]', row);
        const stockUnit = qs('[data-bd-stock-unit]', row);
        const remainQty = qs('[data-bd-remain-qty]', row);
        const item = getItem(select?.value);

        if (!item) {
            if (qtyInput) {
                qtyInput.disabled = true;
                qtyInput.value = '';
            }
            if (stockQty) stockQty.textContent = '—';
            if (stockUnit) stockUnit.textContent = '—';
            if (remainQty) remainQty.textContent = '—';
            return;
        }

        const batch = getSelectedBatch(row, item);
        const satuan = item.satuan || 'g';
        if (unitInput) unitInput.value = satuan;
        if (unitStatic) unitStatic.textContent = satuan;

        const stock = batch ? batch.sisa : item.jumlah;
        if (stockQty) stockQty.textContent = formatIdQty(stock, satuan);
        if (stockUnit) stockUnit.textContent = satuan;

        if (qtyInput) {
            qtyInput.disabled = !batch;
            qtyInput.required = !!batch;
            if (!batch) {
                qtyInput.value = '';
            } else if (isZeroLikeQtyValue(qtyInput.value)) {
                qtyInput.value = '';
            }
            const used = parseIdQty(qtyInput.value || '');
            const remain = Math.max(0, stock - used);
            if (remainQty) {
                remainQty.textContent = formatIdQty(remain, satuan);
                remainQty.classList.toggle('text-rose-600', remain < 0);
                remainQty.classList.toggle('text-emerald-600', remain >= 0);
            }
        }
    }

    function reindexRows() {
        qsa('[data-bahan-dasar-row]', rowsWrap).forEach((row, idx) => {
            const select = qs('[data-bd-select]', row);
            const qtyInput = qs('[data-bd-qty]', row);
            const unitInput = qs('[data-bd-unit-input]', row);
            const batchSelect = qs('[data-bd-batch-select]', row);
            const batchHidden = qs('[data-bd-batch-hidden]', row);
            const batchField = `bahan_dasar[${idx}][batch_bahan_dasar_id]`;
            if (select) select.name = `bahan_dasar[${idx}][bahan_dasar_id]`;
            if (qtyInput) qtyInput.name = `bahan_dasar[${idx}][jumlah]`;
            if (unitInput) unitInput.name = `bahan_dasar[${idx}][satuan]`;
            if (batchHidden && !batchHidden.disabled) {
                batchHidden.name = batchField;
                if (batchSelect) batchSelect.removeAttribute('name');
            } else if (batchSelect && !batchSelect.disabled) {
                batchSelect.name = batchField;
                if (batchHidden) batchHidden.removeAttribute('name');
            } else if (batchHidden) batchHidden.removeAttribute('name');
        });
        rowIndex = qsa('[data-bahan-dasar-row]', rowsWrap).length;
    }

    function refreshAll() {
        qsa('[data-bahan-dasar-row]', rowsWrap).forEach((row) => {
            const item = getItem(qs('[data-bd-select]', row)?.value);
            if (!item) return;
            const batch = getSelectedBatch(row, item);
            setupBatchControls(row, item, batch ? String(batch.id) : '');
            syncRow(row);
        });
    }

    function addRow(data = { bahan_dasar_id: '', batch_bahan_dasar_id: '', jumlah: '', satuan: '' }) {
        const html = template.innerHTML.replaceAll('__INDEX__', String(rowIndex));
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        const row = wrapper.firstElementChild;
        if (!row) return;
        rowsWrap.appendChild(row);

        const select = qs('[data-bd-select]', row);
        const qtyInput = qs('[data-bd-qty]', row);
        buildItemOptions(select, data.bahan_dasar_id || '');
        if (data.bahan_dasar_id) select.value = data.bahan_dasar_id;

        select?.addEventListener('change', () => {
            const item = getItem(select.value);
            setupBatchControls(row, item, '');
            syncRow(row);
            refreshAll();
        });

        qs('[data-bd-batch-select]', row)?.addEventListener('change', () => {
            refreshAll();
            syncRow(row);
        });

        qtyInput?.addEventListener('input', () => syncRow(row));
        qtyInput?.addEventListener('blur', () => {
            const item = getItem(select?.value);
            if (item && qtyInput.value.trim() !== '') {
                qtyInput.value = formatIdQty(parseIdQty(qtyInput.value), item.satuan);
            }
            syncRow(row);
        });

        qs('[data-bahan-dasar-remove-row]', row)?.addEventListener('click', () => {
            row.remove();
            reindexRows();
            refreshAll();
        });

        const item = getItem(data.bahan_dasar_id || '');
        if (item) {
            setupBatchControls(row, item, data.batch_bahan_dasar_id || '');
            if (qtyInput && data.jumlah !== '' && data.jumlah != null) {
                qtyInput.value = formatIdQty(parseIdQty(String(data.jumlah)), data.satuan || item.satuan);
            }
        }
        reindexRows();
        syncRow(row);
        rowIndex += 1;
    }

    rowsWrap.innerHTML = '';
    rowIndex = 0;
    if (initialRows.length === 0) {
        // optional section — no default row
    } else {
        initialRows.forEach((rowData) => addRow(rowData));
    }

    if (addBtn && addBtn.dataset.bdAddBound !== '1') {
        addBtn.dataset.bdAddBound = '1';
        addBtn.addEventListener('click', () => addRow());
    }

    bahanDasarSectionRegistry.set(section, { reindexRows, refreshAll, rowsWrap, addRow });
    section.dataset.bahanDasarBound = '1';
}

function ensureProductionBahanDasarSections(root = document) {
    qsa('[data-production-bahan-dasar]', root).forEach((section) => {
        if (section.dataset.bahanDasarBroken === '1') {
            delete section.dataset.bahanDasarBroken;
            delete section.dataset.bahanDasarBound;
        }
        initProductionBahanDasarSection(section);
    });
}

ensureProductionMaterialSections();
ensureProductionBahanDasarSections();

function getCoaGroupMap() {
    const el = document.getElementById('coa-group-map-data');
    if (!el) return null;

    try {
        return JSON.parse(el.textContent);
    } catch {
        return null;
    }
}

function populateCoaSubGroups(form, groupMap, selectedGrup, selectedSub) {
    const subSelect = qs('select[name="sub_grup"]', form);
    if (!subSelect) return;

    const placeholder = form.dataset.coaPlaceholderSub || '— Pilih sub-grup —';
    const options = groupMap[selectedGrup] || [];

    subSelect.disabled = !selectedGrup;
    subSelect.innerHTML = `<option value="" disabled ${selectedSub ? '' : 'selected'}>${placeholder}</option>`;

    options.forEach((sub) => {
        const opt = document.createElement('option');
        opt.value = sub;
        opt.textContent = sub;
        if (sub === selectedSub) opt.selected = true;
        subSelect.appendChild(opt);
    });
}

function bindCoaForms() {
    const groupMap = getCoaGroupMap();
    if (!groupMap) return;

    qsa('[data-coa-form]').forEach((form) => {
        if (form.dataset.coaFormBound === 'true') return;

        form.dataset.coaFormBound = 'true';
        const grupSelect = qs('select[name="grup"]', form);
        const subSelect = qs('select[name="sub_grup"]', form);
        if (!grupSelect || !subSelect) return;

        populateCoaSubGroups(form, groupMap, grupSelect.value, subSelect.value);

        grupSelect.addEventListener('change', () => {
            populateCoaSubGroups(form, groupMap, grupSelect.value, '');
        });
    });
}

bindCoaForms();

initDashboardCharts();
initUnitToggles();
initUnitDisplays();
