import * as bootstrap from 'bootstrap';
import $ from 'jquery';
import select2 from 'select2';
import Sortable from 'sortablejs';

window.bootstrap = bootstrap;
window.$ = window.jQuery = $;
select2($);

const body = document.body;
const sidebar = document.querySelector('[data-admin-sidebar]');
const topbar = document.querySelector('[data-admin-topbar]');
const content = document.querySelector('[data-admin-content]');
const overlay = document.querySelector('[data-admin-overlay]');
const collapseButton = document.querySelector('[data-admin-sidebar-collapse]');
const openButton = document.querySelector('[data-admin-sidebar-open]');

const closeMobileSidebar = () => {
    sidebar?.classList.remove('is-mobile-open');
    overlay?.classList.remove('is-visible');
    body.classList.remove('admin-navigation-open');
    openButton?.setAttribute('aria-expanded', 'false');
};

collapseButton?.addEventListener('click', () => {
    const collapsed = sidebar?.classList.toggle('is-collapsed') ?? false;

    topbar?.classList.toggle('is-expanded', collapsed);
    content?.classList.toggle('is-expanded', collapsed);
    collapseButton.setAttribute('aria-expanded', String(!collapsed));
    collapseButton.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
});

openButton?.addEventListener('click', () => {
    sidebar?.classList.add('is-mobile-open');
    overlay?.classList.add('is-visible');
    body.classList.add('admin-navigation-open');
    openButton.setAttribute('aria-expanded', 'true');
});

overlay?.addEventListener('click', closeMobileSidebar);

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeMobileSidebar();
    }
});

window.addEventListener('resize', () => {
    if (window.innerWidth >= 992) {
        closeMobileSidebar();
    }
});

document.querySelectorAll('[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (! window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('[data-permission-parent]').forEach((parent) => {
    parent.addEventListener('change', () => {
        document
            .querySelectorAll(`[data-permission-child="${parent.dataset.permissionParent}"]`)
            .forEach((child) => {
                child.checked = parent.checked;
            });
    });
});

document.querySelectorAll('[data-permission-child]').forEach((child) => {
    child.addEventListener('change', () => {
        if (! child.checked) {
            return;
        }

        const parent = document.querySelector(
            `[data-permission-parent="${child.dataset.permissionChild}"]`,
        );

        if (parent) {
            parent.checked = true;
        }
    });
});

const permissionGroupSelect = document.querySelector('[data-permission-group-select]');
const permissionParentSelect = document.querySelector('[data-permission-parent-select]');

const filterPermissionParents = () => {
    if (! permissionGroupSelect || ! permissionParentSelect) {
        return;
    }

    const groupId = permissionGroupSelect.value;

    permissionParentSelect
        .querySelectorAll('option[data-permission-group]')
        .forEach((option) => {
            option.disabled = option.dataset.permissionGroup !== groupId;
        });

    if (permissionParentSelect.selectedOptions[0]?.disabled) {
        permissionParentSelect.value = '0';
    }
};

permissionGroupSelect?.addEventListener('change', filterPermissionParents);
filterPermissionParents();

const categoryName = document.querySelector('[data-category-name]');
const categorySlug = document.querySelector('[data-category-slug]');

if (categoryName && categorySlug) {
    let categorySlugIsManual = categorySlug.value.trim() !== '';
    const slugifyCategoryName = () => categoryName.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');

    categoryName.addEventListener('input', () => {
        if (! categorySlugIsManual) {
            categorySlug.value = slugifyCategoryName();
        }
    });

    categorySlug.addEventListener('input', () => {
        categorySlugIsManual = categorySlug.value.trim() !== '';
    });
}

$('.js-category-parent-select').each(function () {
    $(this).select2({
        width: '100%',
        minimumResultsForSearch: 0,
        placeholder: this.dataset.placeholder,
    });
});

const categorySortableBody = document.querySelector('[data-category-sortable]');

if (categorySortableBody) {
    const sortStatus = document.querySelector('[data-category-sort-status]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let previousOrder = [];

    const setSortStatus = (message, state = '') => {
        if (! sortStatus) {
            return;
        }

        sortStatus.textContent = message;
        sortStatus.dataset.state = state;
    };

    const updateDisplayedPositions = () => {
        categorySortableBody
            .querySelectorAll('tr[data-category-id]')
            .forEach((row, position) => {
                const positionCell = row.querySelector('[data-category-position]');

                if (positionCell) {
                    positionCell.textContent = String(position);
                }
            });
    };

    const categorySortable = new Sortable(categorySortableBody, {
        animation: 160,
        dataIdAttr: 'data-category-id',
        handle: '[data-sort-handle]',
        ghostClass: 'is-sorting-ghost',
        chosenClass: 'is-sorting-chosen',
        dragClass: 'is-sorting-drag',
        onStart: () => {
            previousOrder = categorySortable.toArray();
            setSortStatus('Move the category to its new display position.');
        },
        onEnd: async (event) => {
            if (event.oldIndex === event.newIndex) {
                setSortStatus('Drag rows by the handle to change their display order.');

                return;
            }

            const categoryIds = categorySortable.toArray().map(Number);

            categorySortable.option('disabled', true);
            setSortStatus('Saving the new category order…', 'saving');

            try {
                const response = await fetch(categorySortableBody.dataset.sortUrl, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify({ category_ids: categoryIds }),
                });
                const result = await response.json().catch(() => ({}));

                if (! response.ok) {
                    throw new Error(
                        result.errors?.category_ids?.[0]
                        ?? result.message
                        ?? 'The category order could not be saved.',
                    );
                }

                updateDisplayedPositions();
                setSortStatus(result.message ?? 'Category order updated successfully.', 'success');
            } catch (error) {
                categorySortable.sort(previousOrder);
                updateDisplayedPositions();
                setSortStatus(error.message ?? 'The category order could not be saved.', 'error');
            } finally {
                categorySortable.option('disabled', false);
            }
        },
    });
}
