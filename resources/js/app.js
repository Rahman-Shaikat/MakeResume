import * as bootstrap from 'bootstrap';
import $ from 'jquery';
import './builder';

window.bootstrap = bootstrap;
window.$ = window.jQuery = $;

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

const fitEmbeddedTemplatePreview = () => {
    const canvas = document.querySelector('.is-embedded:not(.is-builder-embedded) .resume-canvas');
    const resumePage = canvas?.firstElementChild;

    if (!canvas || !resumePage) {
        return;
    }

    const viewportWidth = document.documentElement.clientWidth;
    const pageWidth = resumePage.offsetWidth;

    if (viewportWidth > 0 && pageWidth > 0) {
        canvas.style.setProperty('--embedded-preview-scale', String(viewportWidth / pageWidth));
    }
};

requestAnimationFrame(fitEmbeddedTemplatePreview);
window.addEventListener('resize', fitEmbeddedTemplatePreview);

const showToast = (message) => {
    const toastElement = document.querySelector('[data-app-toast]');

    if (!toastElement) {
        return;
    }

    toastElement.querySelector('[data-toast-message]').textContent = message;
    bootstrap.Toast.getOrCreateInstance(toastElement, { delay: 2800 }).show();
};

$(document).on('click', '[data-password-toggle]', function () {
    const input = document.querySelector($(this).data('password-toggle'));

    if (!input) {
        return;
    }

    const willShow = input.type === 'password';
    input.type = willShow ? 'text' : 'password';
    this.setAttribute('aria-label', willShow ? 'Hide password' : 'Show password');
});

$(document).on('change', '[data-image-input]', function () {
    const file = this.files?.[0];
    const form = this.closest('[data-image-form]');

    if (!file || !form) {
        return;
    }

    const preview = form.querySelector('[data-profile-preview]');
    const selectedFile = form.querySelector('[data-selected-file]');
    const uploadButton = form.querySelector('[data-upload-button]');

    selectedFile.classList.remove('d-none');
    selectedFile.querySelector('[data-file-name]').textContent = file.name;
    uploadButton.disabled = false;

    const reader = new FileReader();
    reader.addEventListener('load', () => {
        preview.innerHTML = `<img src="${reader.result}" alt="Selected profile photo preview">`;
    });
    reader.readAsDataURL(file);
});

$(document).on('click', '.js-select-template', function () {
    const button = this;
    const template = button.dataset.template;

    button.disabled = true;
    button.textContent = 'Creating…';

    $.ajax({
        url: button.dataset.url,
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        data: {
            template_slug: template,
        },
    })
        .done((response) => {
            document.querySelectorAll('[data-template-card]').forEach((card) => {
                const isSelected = card.dataset.templateCard === template;
                const cardButton = card.querySelector('.js-select-template');

                card.classList.toggle('is-selected', isSelected);
                cardButton.classList.toggle('btn-success', isSelected);
                cardButton.classList.toggle('btn-primary', !isSelected);
                cardButton.disabled = isSelected;
                cardButton.textContent = isSelected ? 'Created' : 'Create resume';
            });

            const nextBar = document.querySelector('[data-template-next]');
            nextBar?.classList.remove('d-none');
            const builderLink = nextBar?.querySelector('[data-builder-link]');
            if (builderLink) builderLink.href = response.builder_url;

            showToast(response.message);
        })
        .fail((xhr) => {
            button.disabled = false;
            button.textContent = 'Create resume';
            showToast(xhr.responseJSON?.message ?? 'Could not create the resume. Please try again.');
        });
});

$(document).on('click', '[data-print-resume]', () => window.print());

$(document).on('input', '[data-summary-input]', function () {
    document.querySelector('[data-summary-count]').textContent = this.value.length;
});
