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

const initializeLiveTemplatePreviews = () => {
    const previews = Array.from(document.querySelectorAll('[data-live-template-preview]'));

    if (previews.length === 0) {
        return;
    }

    const loadPreview = (preview) => {
        if (preview.dataset.previewState !== 'idle') {
            return;
        }

        const mount = preview.querySelector('[data-live-preview-mount]');
        const previewUrl = preview.dataset.livePreviewUrl;

        if (!mount || !previewUrl) {
            preview.dataset.previewState = 'error';
            preview.setAttribute('aria-busy', 'false');

            return;
        }

        preview.dataset.previewState = 'loading';
        const iframe = document.createElement('iframe');
        const loadingTimeout = window.setTimeout(() => {
            if (preview.dataset.previewState !== 'loading') {
                return;
            }

            preview.dataset.previewState = 'error';
            preview.setAttribute('aria-busy', 'false');
            iframe.remove();
        }, 15000);

        const showFallback = () => {
            window.clearTimeout(loadingTimeout);
            preview.dataset.previewState = 'error';
            preview.setAttribute('aria-busy', 'false');
            iframe.remove();
        };

        iframe.src = previewUrl;
        iframe.title = preview.dataset.livePreviewTitle || 'Resume template live preview';
        iframe.loading = 'eager';
        iframe.tabIndex = -1;
        iframe.setAttribute('aria-hidden', 'true');

        iframe.addEventListener('load', () => {
            const iframeUrl = new URL(iframe.src, window.location.href);
            const hasResumeCanvas = iframeUrl.origin !== window.location.origin
                || iframe.contentDocument?.querySelector('.resume-canvas');

            if (!hasResumeCanvas) {
                showFallback();

                return;
            }

            window.clearTimeout(loadingTimeout);
            preview.dataset.previewState = 'ready';
            preview.setAttribute('aria-busy', 'false');
            preview.classList.add('is-live-preview-ready');
        }, { once: true });
        iframe.addEventListener('error', showFallback, { once: true });

        mount.replaceChildren(iframe);
    };

    if (!('IntersectionObserver' in window)) {
        previews.forEach(loadPreview);

        return;
    }

    const previewGroups = new Map();

    previews.forEach((preview) => {
        const root = preview.closest('[data-template-slider-track]');
        const groupKey = root || document;

        if (!previewGroups.has(groupKey)) {
            previewGroups.set(groupKey, { root, previews: [] });
        }

        previewGroups.get(groupKey).previews.push(preview);
    });

    previewGroups.forEach(({ root, previews: groupedPreviews }) => {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) {
                    return;
                }

                loadPreview(entry.target);
                observer.unobserve(entry.target);
            });
        }, {
            root,
            rootMargin: '0px 50%',
            threshold: 0.05,
        });

        groupedPreviews.forEach((preview) => observer.observe(preview));
    });
};

initializeLiveTemplatePreviews();

document.querySelectorAll('[data-template-slider]').forEach((slider) => {
    const track = slider.querySelector('[data-template-slider-track]');
    const previousButton = slider.querySelector('[data-template-slider-previous]');
    const nextButton = slider.querySelector('[data-template-slider-next]');

    if (!track) {
        return;
    }

    const updateNavigation = () => {
        const maximumScroll = Math.max(0, track.scrollWidth - track.clientWidth);

        if (previousButton) {
            previousButton.disabled = track.scrollLeft <= 1;
        }

        if (nextButton) {
            nextButton.disabled = track.scrollLeft >= maximumScroll - 1;
        }
    };

    const moveSlider = (direction) => {
        const card = track.querySelector('[data-template-card]');

        if (!card) {
            return;
        }

        const gap = Number.parseFloat(getComputedStyle(track).columnGap) || 0;
        track.scrollBy({
            left: direction * (card.getBoundingClientRect().width + gap),
            behavior: 'smooth',
        });
    };

    previousButton?.addEventListener('click', () => moveSlider(-1));
    nextButton?.addEventListener('click', () => moveSlider(1));

    track.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') {
            return;
        }

        event.preventDefault();
        moveSlider(event.key === 'ArrowRight' ? 1 : -1);
    });

    let navigationFrame;
    track.addEventListener('scroll', () => {
        cancelAnimationFrame(navigationFrame);
        navigationFrame = requestAnimationFrame(updateNavigation);
    }, { passive: true });

    if ('ResizeObserver' in window) {
        new ResizeObserver(updateNavigation).observe(track);
    }

    requestAnimationFrame(updateNavigation);
});

const showToast = (message) => {
    const toastElement = document.querySelector('[data-app-toast]');

    if (!toastElement) {
        return;
    }

    toastElement.querySelector('[data-toast-message]').textContent = message;
    bootstrap.Toast.getOrCreateInstance(toastElement, { delay: 2800 }).show();
};

document.querySelectorAll('[data-auto-dismiss-alert]').forEach((alertElement) => {
    const dismissAfter = Number.parseInt(alertElement.dataset.dismissAfter, 10) || 5000;

    window.setTimeout(() => {
        if (alertElement.isConnected) {
            bootstrap.Alert.getOrCreateInstance(alertElement).close();
        }
    }, dismissAfter);
});

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
    button.classList.add('is-creating');
    button.setAttribute('aria-label', 'Creating resume');

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
                cardButton.classList.toggle('is-created', isSelected);
                cardButton.classList.remove('is-creating');
                cardButton.disabled = isSelected;
                cardButton.setAttribute('aria-label', isSelected ? 'Resume created' : 'Create resume');
                cardButton.title = isSelected ? 'Resume created' : 'Create resume';
            });

            const nextBar = document.querySelector('[data-template-next]');
            nextBar?.classList.remove('d-none');
            const builderLink = nextBar?.querySelector('[data-builder-link]');
            if (builderLink) builderLink.href = response.builder_url;

            showToast(response.message);
        })
        .fail((xhr) => {
            button.disabled = false;
            button.classList.remove('is-creating');
            button.setAttribute('aria-label', 'Create resume');
            showToast(xhr.responseJSON?.message ?? 'Could not create the resume. Please try again.');
        });
});

$(document).on('click', '[data-print-resume]', () => window.print());

$(document).on('submit', '[data-delete-resume-form]', function (event) {
    const resumeTitle = this.dataset.resumeTitle || 'this resume';
    const confirmed = window.confirm(`Delete "${resumeTitle}"? This action cannot be undone.`);

    if (!confirmed) {
        event.preventDefault();
    }
});

$(document).on('input', '[data-summary-input]', function () {
    document.querySelector('[data-summary-count]').textContent = this.value.length;
});
