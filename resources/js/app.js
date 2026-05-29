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
    const dotIndex = value.indexOf('.');

    if (dotIndex !== -1) {
        value = value.slice(0, dotIndex);
    }

    return value.replace(/\D/g, '');
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

qsa('[data-print]').forEach((btn) => {
    btn.addEventListener('click', () => window.print());
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

function formatQtyOneDisplay(value) {
    const n = Math.round(parseFloat(value) * 10) / 10;
    if (!Number.isFinite(n)) return '0';
    if (Math.floor(n) === n) {
        return String(Math.trunc(n));
    }
    return n.toFixed(1).replace(/\.0$/, '');
}

function initProductionMaterialSection(section) {
    let materials = [];
    let initialRows = [{ raw_material_id: '', jumlah: '' }];
    const selectPlaceholder = section.dataset.selectPlaceholder || '—';

    try {
        materials = JSON.parse(section.dataset.materials || '[]');
        initialRows = JSON.parse(section.dataset.initialRows || '[[]]');
    } catch {
        materials = [];
    }

    const rowsWrap = qs('[data-production-rows]', section);
    const template = qs('[data-production-row-template]', section);
    const addBtn = qs('[data-production-add-row]', section);
    if (!rowsWrap || !template) return;

    let rowIndex = 0;

    function getMaterial(id) {
        return materials.find((item) => item.id === id);
    }

    function buildSelectOptions(select, selectedId) {
        select.innerHTML = '';

        const empty = document.createElement('option');
        empty.value = '';
        empty.textContent = selectPlaceholder;
        select.appendChild(empty);

        materials.forEach((material) => {
            const opt = document.createElement('option');
            opt.value = material.id;
            opt.textContent = material.nama;
            opt.selected = material.id === selectedId;
            select.appendChild(opt);
        });
    }

    function setQtyEnabled(row, enabled, keepValue = '') {
        const qtyInput = qs('[data-material-qty]', row);
        if (!qtyInput) return;

        qtyInput.disabled = !enabled;
        qtyInput.required = enabled;

        if (!enabled) {
            qtyInput.value = '0';
            qtyInput.placeholder = '0';
            return;
        }

        qtyInput.placeholder = '0';
        qtyInput.value = keepValue && keepValue !== '0' ? keepValue : '';
    }

    function syncRow(row) {
        const select = qs('[data-material-select]', row);
        const qtyInput = qs('[data-material-qty]', row);
        const unitEl = qs('[data-material-unit]', row);
        const stockEl = qs('[data-material-stock]', row);
        const remainEl = qs('[data-material-remain]', row);
        if (!select || !qtyInput || !unitEl || !stockEl || !remainEl) return;

        const material = getMaterial(select.value);
        const insufficientClasses = ['bg-rose-50/80', 'ring-1', 'ring-inset', 'ring-rose-200'];

        setQtyEnabled(row, Boolean(select.value), qtyInput.value);

        if (!material) {
            unitEl.textContent = '—';
            stockEl.textContent = '—';
            remainEl.textContent = '—';
            remainEl.classList.remove('text-rose-600');
            remainEl.classList.add('text-emerald-600');
            insufficientClasses.forEach((cls) => row.classList.remove(cls));
            return;
        }

        const qty = parseFloat(qtyInput.value) || 0;
        unitEl.textContent = material.satuan;
        stockEl.textContent = `${formatQtyOneDisplay(material.jumlah)} ${material.satuan}`;

        const isInsufficient = qty > material.jumlah;
        insufficientClasses.forEach((cls) => row.classList.toggle(cls, isInsufficient));

        if (isInsufficient) {
            remainEl.textContent = `-${formatQtyOneDisplay(qty - material.jumlah)} ${material.satuan}`;
            remainEl.classList.remove('text-emerald-600');
            remainEl.classList.add('text-rose-600');
        } else {
            remainEl.textContent = `${formatQtyOneDisplay(material.jumlah - qty)} ${material.satuan}`;
            remainEl.classList.remove('text-rose-600');
            remainEl.classList.add('text-emerald-600');
        }
    }

    function reindexRows() {
        qsa('[data-production-row]', rowsWrap).forEach((row, idx) => {
            const select = qs('[data-material-select]', row);
            const qtyInput = qs('[data-material-qty]', row);
            if (select) select.name = `materials[${idx}][raw_material_id]`;
            if (qtyInput) qtyInput.name = `materials[${idx}][jumlah]`;
        });
        rowIndex = qsa('[data-production-row]', rowsWrap).length;
    }

    function updateRemoveButtons() {
        const rows = qsa('[data-production-row]', rowsWrap);
        rows.forEach((row) => {
            const btn = qs('[data-production-remove-row]', row);
            if (btn) {
                const isOnly = rows.length <= 1;
                btn.disabled = isOnly;
                btn.classList.toggle('opacity-30', isOnly);
                btn.classList.toggle('pointer-events-none', isOnly);
            }
        });
    }

    function addRow(data = { raw_material_id: '', jumlah: '' }) {
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
            select.addEventListener('change', () => syncRow(row));
        }
        if (qtyInput) {
            const initialQty = data.jumlah || '';
            qtyInput.addEventListener('input', () => syncRow(row));
            qtyInput.addEventListener('blur', () => syncRow(row));
            if (select?.value) {
                qtyInput.value = initialQty;
            }
        }

        qs('[data-production-remove-row]', row)?.addEventListener('click', () => {
            if (qsa('[data-production-row]', rowsWrap).length <= 1) {
                showToast('Minimal satu bahan baku', 'warning');
                return;
            }
            row.remove();
            reindexRows();
            updateRemoveButtons();
        });

        rowIndex += 1;
        syncRow(row);
        updateRemoveButtons();
    }

    rowsWrap.innerHTML = '';
    rowIndex = 0;

    if (initialRows.length === 0) {
        addRow();
    } else {
        initialRows.forEach((row) => addRow(row));
    }

    addBtn?.addEventListener('click', () => addRow());
}

qsa('[data-production-materials]').forEach((section) => initProductionMaterialSection(section));

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
