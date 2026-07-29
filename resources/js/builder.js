import $ from 'jquery';
import * as bootstrap from 'bootstrap';

const root = document.querySelector('[data-builder-root]');

if (root) {
    const payload = JSON.parse(root.querySelector('[data-builder-payload]').textContent);
    const sectionList = root.querySelector('[data-section-list]');
    const preview = root.querySelector('[data-builder-preview]');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const saveStatus = root.querySelector('[data-save-status]');
    const saveLabel = root.querySelector('[data-save-label]');
    const sectionsUrl = root.dataset.sectionsUrl;
    const timers = new Map();
    let draggedSection = null;
    let draggedItem = null;
    let dragIntent = null;

    const fieldDefinitions = {
        skills: [
            ['name', 'Skill name', 'text', 'e.g. Laravel'],
            ['level', 'Level', 'select', ['Beginner', 'Intermediate', 'Advanced', 'Expert']],
            ['category', 'Category', 'text', 'e.g. Backend'],
        ],
        education: [
            ['degree', 'Degree', 'text', 'e.g. BSc in Computer Science'],
            ['institution', 'Institution', 'text', 'University name'],
            ['location', 'Location', 'text', 'City, Country'],
            ['start_date', 'Start date', 'month'],
            ['end_date', 'End date', 'month'],
            ['description', 'Description', 'textarea', 'Relevant coursework, achievements, or activities'],
        ],
        experience: [
            ['title', 'Job title', 'text', 'e.g. Software Engineer'],
            ['company', 'Company', 'text', 'Company name'],
            ['location', 'Location', 'text', 'City, Country'],
            ['start_date', 'Start date', 'month'],
            ['end_date', 'End date', 'month'],
            ['current', 'I currently work here', 'checkbox'],
            ['description', 'Responsibilities & achievements', 'textarea', 'Describe your impact and measurable results'],
        ],
        projects: [
            ['name', 'Project name', 'text', 'Project title'],
            ['role', 'Role', 'text', 'e.g. Lead Developer'],
            ['tech_stack', 'Technology stack', 'text', 'Laravel, MySQL, Bootstrap'],
            ['url', 'Project link', 'url', 'https://example.com'],
            ['description', 'Description', 'textarea', 'What you built and the result'],
        ],
        courses: [
            ['name', 'Course name', 'text', 'Course or certification'],
            ['provider', 'Provider', 'text', 'Institution or platform'],
            ['date', 'Year / date', 'text', 'e.g. 2025'],
            ['description', 'Description', 'textarea', 'Key topics or achievement'],
        ],
        awards: [
            ['title', 'Award title', 'text', 'Award or recognition'],
            ['organization', 'Organization', 'text', 'Issuing organization'],
            ['date', 'Date', 'text', 'e.g. March 2025'],
            ['description', 'Description', 'textarea', 'Why you received this award'],
        ],
        languages: [
            ['name', 'Language', 'text', 'e.g. English'],
            ['proficiency', 'Proficiency', 'select', ['Beginner', 'Conversational', 'Proficient', 'Fluent', 'Native']],
        ],
        custom: [
            ['title', 'Item title', 'text', 'Item heading'],
            ['content', 'Content', 'textarea', 'Add the details you want to show'],
        ],
    };

    const sectionIcons = {
        personal: '<circle cx="12" cy="8" r="4"/><path d="M5 21a7 7 0 0 1 14 0"/>',
        summary: '<path d="M4 5h16M4 10h16M4 15h10M4 20h7"/>',
        skills: '<path d="m12 3 2.1 4.7L19 8.3l-3.6 3.4.9 4.8L12 14.1l-4.3 2.4.9-4.8L5 8.3l4.9-.6z"/>',
        education: '<path d="m3 9 9-5 9 5-9 5zM6 11v5c3 2 9 2 12 0v-5"/>',
        experience: '<path d="M9 7V4h6v3M4 8h16v11H4zM4 12h16"/>',
        projects: '<path d="M4 5h16v14H4zM8 9h8M8 13h5"/>',
        courses: '<path d="M6 3h9l3 3v15H6zM14 3v4h4M9 12h6M9 16h6"/>',
        awards: '<circle cx="12" cy="9" r="6"/><path d="m8 14-1 7 5-3 5 3-1-7"/>',
        languages: '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/>',
        custom: '<path d="M12 5v14M5 12h14"/>',
    };

    const escapeHtml = (value = '') => String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const request = ({ url, method = 'GET', data = {}, formData = false }) => $.ajax({
        url,
        method,
        data,
        processData: !formData,
        contentType: formData ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
    });

    const markSaving = () => {
        saveStatus.classList.add('is-saving');
        saveStatus.classList.remove('is-error');
        saveLabel.textContent = 'Saving…';
    };

    const markSaved = (message = 'All changes saved') => {
        saveStatus.classList.remove('is-saving', 'is-error');
        saveLabel.textContent = message;
    };

    const markError = (message = 'Could not save') => {
        saveStatus.classList.remove('is-saving');
        saveStatus.classList.add('is-error');
        saveLabel.textContent = message;
    };

    const debounce = (key, callback, delay = 700) => {
        clearTimeout(timers.get(key));
        timers.set(key, setTimeout(callback, delay));
    };

    const refreshPreview = () => {
        const loader = root.querySelector('[data-preview-loading]');
        loader.classList.remove('d-none');
        const url = new URL(payload.preview_url, window.location.origin);
        url.searchParams.set('_refresh', Date.now());
        preview.src = url.toString();
        preview.addEventListener('load', () => loader.classList.add('d-none'), { once: true });
    };

    const showError = (xhr) => {
        const errors = xhr.responseJSON?.errors;
        const message = errors
            ? Object.values(errors).flat()[0]
            : (xhr.responseJSON?.message ?? 'Something went wrong. Please try again.');

        Object.entries(errors ?? {}).forEach(([key, messages]) => {
            const field = key.replace(/^content\./, '');
            const input = sectionList.querySelector(`[data-content-field="${field}"]`);
            const feedback = sectionList.querySelector(`[data-field-error="${field}"]`);
            input?.classList.add('is-invalid');
            if (feedback) feedback.textContent = messages[0];
        });

        markError(message);
    };

    const contentInput = (name, label, type = 'text', placeholder = '') => {
        const value = payload.content[name] ?? '';
        const input = type === 'textarea'
            ? `<textarea class="form-control" rows="4" maxlength="${name === 'summary' ? 1000 : 255}" data-content-field="${name}" placeholder="${escapeHtml(placeholder)}">${escapeHtml(value)}</textarea>`
            : `<input class="form-control" type="${type}" value="${escapeHtml(value)}" data-content-field="${name}" placeholder="${escapeHtml(placeholder)}">`;

        return `<label class="dynamic-field"><span>${escapeHtml(label)}</span>${input}<small data-field-error="${name}"></small></label>`;
    };

    const personalSection = () => {
        const initials = (payload.content.full_name || 'Your Name')
            .split(/\s+/)
            .slice(0, 2)
            .map((part) => part[0])
            .join('')
            .toUpperCase();
        const avatar = payload.profile_image_url
            ? `<img src="${escapeHtml(payload.profile_image_url)}" alt="Profile photo">`
            : `<span>${escapeHtml(initials)}</span>`;
        const imageButtonLabel = payload.profile_image_url ? 'Change photo' : 'Add photo';
        const removeButton = payload.profile_image_url
            ? `<button
                    type="button"
                    class="btn btn-outline-danger btn-sm builder-image-button builder-image-remove-button"
                    data-remove-builder-image
                    aria-label="Remove profile photo"
                    title="Remove profile photo"
                >
                    <svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3M7 7l1 13h8l1-13M10 11v5M14 11v5"/></svg>
                </button>`
            : '';

        return `
            <div class="builder-profile-row">
                <div class="builder-profile-image" data-builder-profile>${avatar}</div>
                <div>
                    <div class="builder-profile-actions">
                        <label class="btn btn-outline-primary btn-sm builder-image-button">
                            <svg viewBox="0 0 24 24"><path d="M12 16V4m0 0L8 8m4-4 4 4M5 14v6h14v-6"/></svg>
                            ${imageButtonLabel}
                            <input type="file" class="visually-hidden" accept=".jpg,.jpeg,.png,.webp" data-builder-image-input>
                        </label>
                        ${removeButton}
                    </div>
                    <p>JPG, PNG, or WebP. Maximum 2 MB.</p>
                </div>
            </div>
            <div class="dynamic-field-grid">
                ${contentInput('full_name', 'Full name', 'text', 'Your full name')}
                ${contentInput('professional_title', 'Professional title', 'text', 'e.g. Laravel Developer')}
                ${contentInput('email', 'Email address', 'email', 'you@example.com')}
                ${contentInput('phone', 'Phone number', 'text', '+880…')}
                ${contentInput('location', 'Location', 'text', 'City, Country')}
                ${contentInput('website', 'Website / portfolio', 'url', 'https://yourwebsite.com')}
                ${contentInput('linkedin', 'LinkedIn', 'url', 'https://linkedin.com/in/username')}
                ${contentInput('github', 'GitHub', 'url', 'https://github.com/username')}
            </div>`;
    };

    const summarySection = () => `
        <div class="summary-editor">
            ${contentInput('summary', 'Professional summary', 'textarea', 'Write a concise summary of your experience, strengths, and goals.')}
            <div class="summary-guidance">
                <svg viewBox="0 0 24 24"><path d="M12 8v4m0 4h.01M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Z"/></svg>
                Aim for 3–5 sentences focused on your impact and specialty.
            </div>
        </div>`;

    const fieldInput = (definition, item) => {
        const [name, label, type, options] = definition;
        const value = item.data?.[name] ?? '';

        if (type === 'textarea') {
            return `<label class="dynamic-field field-wide"><span>${label}</span><textarea class="form-control" rows="3" data-item-field="${name}" placeholder="${escapeHtml(options)}">${escapeHtml(value)}</textarea></label>`;
        }

        if (type === 'select') {
            const optionHtml = options.map((option) => `<option value="${escapeHtml(option)}" ${value === option ? 'selected' : ''}>${escapeHtml(option)}</option>`).join('');
            return `<label class="dynamic-field"><span>${label}</span><select class="form-select" data-item-field="${name}"><option value="">Select level</option>${optionHtml}</select></label>`;
        }

        if (type === 'checkbox') {
            return `<label class="dynamic-checkbox field-wide"><input class="form-check-input" type="checkbox" data-item-field="${name}" ${value ? 'checked' : ''}><span>${label}</span></label>`;
        }

        return `<label class="dynamic-field"><span>${label}</span><input class="form-control" type="${type}" value="${escapeHtml(value)}" data-item-field="${name}" placeholder="${escapeHtml(options ?? '')}"></label>`;
    };

    const itemHeading = (section, item, index) => {
        const data = item.data ?? {};
        return data.title || data.name || data.degree || data.company || data.institution || `${section.title} ${index + 1}`;
    };

    const renderItems = (section) => {
        if (!section.items.length) {
            return `<div class="builder-empty-state">
                <span><svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg></span>
                <strong>No entries yet</strong>
                <p>Add your first ${escapeHtml(section.title.toLowerCase())} entry.</p>
            </div>`;
        }

        const definitions = fieldDefinitions[section.type] ?? fieldDefinitions.custom;

        return `<div class="section-item-list" data-item-list="${section.id}">
            ${section.items.map((item, index) => `
                <article class="repeatable-item" data-item-id="${item.id}" draggable="true">
                    <header>
                        <button type="button" class="item-drag-handle" aria-label="Drag to reorder">
                            <svg viewBox="0 0 24 24"><path d="M9 5h.01M15 5h.01M9 12h.01M15 12h.01M9 19h.01M15 19h.01"/></svg>
                        </button>
                        <strong data-item-heading>${escapeHtml(itemHeading(section, item, index))}</strong>
                        <button type="button" class="remove-entry-button" data-remove-item aria-label="Remove entry">
                            <svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3m-9 0 1 14h10l1-14M10 11v6m4-6v6"/></svg>
                        </button>
                    </header>
                    <div class="repeatable-fields">
                        ${definitions.map((definition) => fieldInput(definition, item)).join('')}
                    </div>
                </article>`).join('')}
        </div>`;
    };

    const sectionBody = (section) => {
        if (section.type === 'personal') return personalSection();
        if (section.type === 'summary') return summarySection();
        return renderItems(section);
    };

    const renderSections = () => {
        payload.sections.sort((a, b) => a.sort_order - b.sort_order);
        sectionList.innerHTML = payload.sections.map((section, index) => `
            <section class="dynamic-section-card ${section.is_visible ? '' : 'is-hidden'}" data-section-id="${section.id}" draggable="true">
                <header class="dynamic-section-header">
                    <button type="button" class="section-drag-handle" aria-label="Drag ${escapeHtml(section.title)}">
                        <svg viewBox="0 0 24 24"><path d="M9 5h.01M15 5h.01M9 12h.01M15 12h.01M9 19h.01M15 19h.01"/></svg>
                    </button>
                    <span class="dynamic-section-icon">
                        <svg viewBox="0 0 24 24">${sectionIcons[section.type] ?? sectionIcons.custom}</svg>
                    </span>
                    <div class="dynamic-section-title">
                        ${section.is_custom
                            ? `<input value="${escapeHtml(section.title)}" maxlength="100" data-section-title aria-label="Custom section name">`
                            : `<strong>${escapeHtml(section.title)}</strong>`}
                        <small>${section.items.length ? `${section.items.length} ${section.items.length === 1 ? 'entry' : 'entries'}` : (section.type === 'personal' || section.type === 'summary' ? 'Autosaved' : 'Empty')}</small>
                    </div>
                    <div class="section-card-actions">
                        <button type="button" data-toggle-visibility title="${section.is_visible ? 'Hide section' : 'Show section'}">
                            <svg viewBox="0 0 24 24">${section.is_visible
                                ? '<path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle cx="12" cy="12" r="2.5"/>'
                                : '<path d="m3 3 18 18M10.6 6.1A8.8 8.8 0 0 1 12 6c6 0 9.5 6 9.5 6a15 15 0 0 1-2 2.8M6.2 6.2C3.8 8 2.5 12 2.5 12S6 18 12 18a9 9 0 0 0 2.2-.3"/>'}</svg>
                        </button>
                        ${section.is_custom ? `<button type="button" data-remove-section title="Delete custom section"><svg viewBox="0 0 24 24"><path d="M4 7h16M9 7V4h6v3m-9 0 1 14h10l1-14"/></svg></button>` : ''}
                        <button type="button" data-toggle-section aria-expanded="${index < 2 ? 'true' : 'false'}">
                            <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
                        </button>
                    </div>
                </header>
                <div class="dynamic-section-body ${index < 2 ? '' : 'is-collapsed'}">
                    ${sectionBody(section)}
                    ${!['personal', 'summary'].includes(section.type)
                        ? `<button type="button" class="add-entry-button" data-add-item>
                            <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                            Add ${section.type === 'custom' ? 'item' : escapeHtml(section.title.replace(/s$/, '').toLowerCase())}
                        </button>`
                        : ''}
                </div>
            </section>`).join('');

        root.querySelector('[data-section-count]').textContent = payload.sections.length;
    };

    const findSection = (element) => {
        const id = Number(element.closest('[data-section-id]')?.dataset.sectionId);
        return payload.sections.find((section) => section.id === id);
    };

    const findItem = (section, element) => {
        const id = Number(element.closest('[data-item-id]')?.dataset.itemId);
        return section.items.find((item) => item.id === id);
    };

    const saveContent = () => {
        markSaving();
        request({
            url: root.dataset.contentUrl,
            method: 'PATCH',
            data: payload.content,
        })
            .done(() => {
                markSaved();
                refreshPreview();
            })
            .fail(showError);
    };

    const saveItem = (section, item) => {
        markSaving();
        request({
            url: `${sectionsUrl}/${section.id}/items/${item.id}`,
            method: 'PATCH',
            data: { data: item.data },
        })
            .done(() => {
                markSaved();
                refreshPreview();
            })
            .fail(showError);
    };

    const defaultItem = (type) => {
        if (type === 'experience') return { title: 'New position', company: '' };
        if (type === 'education') return { degree: 'New qualification', institution: '' };
        if (type === 'awards') return { title: 'New award', organization: '' };
        if (type === 'custom') return { title: 'New item', content: '' };
        return { name: `New ${type.replace(/s$/, '')}` };
    };

    sectionList.addEventListener('input', (event) => {
        const contentField = event.target.dataset.contentField;
        if (contentField) {
            payload.content[contentField] = event.target.value;
            event.target.classList.remove('is-invalid');
            const feedback = sectionList.querySelector(`[data-field-error="${contentField}"]`);
            if (feedback) feedback.textContent = '';
            markSaving();
            debounce('content', saveContent);
            return;
        }

        if (event.target.dataset.sectionTitle !== undefined) {
            const section = findSection(event.target);
            section.title = event.target.value;
            markSaving();
            debounce(`section-${section.id}`, () => {
                request({
                    url: `${sectionsUrl}/${section.id}`,
                    method: 'PATCH',
                    data: { title: section.title },
                }).done(() => {
                    markSaved();
                    refreshPreview();
                }).fail(showError);
            });
            return;
        }

        const itemField = event.target.dataset.itemField;
        if (itemField) {
            const section = findSection(event.target);
            const item = findItem(section, event.target);
            item.data[itemField] = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
            const heading = event.target.closest('[data-item-id]').querySelector('[data-item-heading]');
            heading.textContent = itemHeading(section, item, section.items.indexOf(item));
            markSaving();
            debounce(`item-${item.id}`, () => saveItem(section, item));
        }
    });

    sectionList.addEventListener('click', (event) => {
        const removeProfileImage = event.target.closest('[data-remove-builder-image]');
        if (removeProfileImage) {
            if (!window.confirm('Remove the profile photo from this resume?')) return;

            markSaving();
            request({
                url: root.dataset.profileRemoveUrl,
                method: 'DELETE',
            }).done((response) => {
                payload.profile_image_url = null;
                renderSections();
                markSaved(response.message);
                refreshPreview();
            }).fail(showError);
            return;
        }

        const toggle = event.target.closest('[data-toggle-section]');
        if (toggle) {
            const body = toggle.closest('[data-section-id]').querySelector('.dynamic-section-body');
            const collapsed = body.classList.toggle('is-collapsed');
            toggle.setAttribute('aria-expanded', String(!collapsed));
            return;
        }

        const add = event.target.closest('[data-add-item]');
        if (add) {
            const section = findSection(add);
            markSaving();
            request({
                url: `${sectionsUrl}/${section.id}/items`,
                method: 'POST',
                data: { data: defaultItem(section.type) },
            }).done((response) => {
                section.items.push(response.item);
                renderSections();
                markSaved('Entry added');
                refreshPreview();
                const card = sectionList.querySelector(`[data-section-id="${section.id}"]`);
                card.querySelector('.dynamic-section-body').classList.remove('is-collapsed');
                card.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }).fail(showError);
            return;
        }

        const removeItem = event.target.closest('[data-remove-item]');
        if (removeItem) {
            const section = findSection(removeItem);
            const item = findItem(section, removeItem);
            if (!window.confirm('Remove this entry from your resume?')) return;
            markSaving();
            request({
                url: `${sectionsUrl}/${section.id}/items/${item.id}`,
                method: 'DELETE',
            }).done(() => {
                section.items = section.items.filter((candidate) => candidate.id !== item.id);
                renderSections();
                markSaved('Entry removed');
                refreshPreview();
            }).fail(showError);
            return;
        }

        const visibility = event.target.closest('[data-toggle-visibility]');
        if (visibility) {
            const section = findSection(visibility);
            section.is_visible = !section.is_visible;
            markSaving();
            request({
                url: `${sectionsUrl}/${section.id}`,
                method: 'PATCH',
                data: { is_visible: section.is_visible ? 1 : 0 },
            }).done(() => {
                renderSections();
                markSaved(section.is_visible ? 'Section shown' : 'Section hidden');
                refreshPreview();
            }).fail(showError);
            return;
        }

        const removeSection = event.target.closest('[data-remove-section]');
        if (removeSection) {
            const section = findSection(removeSection);
            if (!window.confirm(`Delete “${section.title}” and all of its entries?`)) return;
            markSaving();
            request({
                url: `${sectionsUrl}/${section.id}`,
                method: 'DELETE',
            }).done(() => {
                payload.sections = payload.sections.filter((candidate) => candidate.id !== section.id);
                payload.sections.forEach((candidate, index) => { candidate.sort_order = index; });
                renderSections();
                markSaved('Custom section deleted');
                refreshPreview();
            }).fail(showError);
        }
    });

    sectionList.addEventListener('change', (event) => {
        if (event.target.matches('[data-builder-image-input]')) {
            const file = event.target.files?.[0];
            if (!file) return;
            const localUrl = URL.createObjectURL(file);
            root.querySelector('[data-builder-profile]').innerHTML = `<img src="${localUrl}" alt="Selected profile photo">`;
            const formData = new FormData();
            formData.append('profile_image', file);
            markSaving();
            request({
                url: root.dataset.profileUrl,
                method: 'POST',
                data: formData,
                formData: true,
            }).done((response) => {
                payload.profile_image_url = response.profile_image_url;
                renderSections();
                markSaved('Profile photo saved');
                refreshPreview();
                URL.revokeObjectURL(localUrl);
            }).fail(showError);
        }
    });

    sectionList.addEventListener('pointerdown', (event) => {
        const itemHandle = event.target.closest('.item-drag-handle');
        const sectionHandle = event.target.closest('.section-drag-handle');
        dragIntent = itemHandle?.closest('[data-item-id]')
            ?? sectionHandle?.closest('[data-section-id]')
            ?? null;
    });

    sectionList.addEventListener('dragstart', (event) => {
        const sectionCard = event.target.closest('[data-section-id]');
        const itemCard = event.target.closest('[data-item-id]');
        if (itemCard && dragIntent === itemCard) {
            draggedItem = itemCard;
            itemCard.classList.add('is-dragging');
            event.stopPropagation();
        } else if (sectionCard && dragIntent === sectionCard) {
            draggedSection = sectionCard;
            sectionCard.classList.add('is-dragging');
        } else {
            event.preventDefault();
        }
    });

    sectionList.addEventListener('dragend', () => {
        draggedSection?.classList.remove('is-dragging');
        draggedItem?.classList.remove('is-dragging');
        draggedSection = null;
        draggedItem = null;
        dragIntent = null;
    });

    sectionList.addEventListener('dragover', (event) => {
        event.preventDefault();
        if (draggedItem) {
            const list = event.target.closest('[data-item-list]');
            if (!list || list !== draggedItem.parentElement) return;
            const siblings = [...list.querySelectorAll('[data-item-id]:not(.is-dragging)')];
            const next = siblings.find((item) => event.clientY < item.getBoundingClientRect().top + item.offsetHeight / 2);
            list.insertBefore(draggedItem, next ?? null);
        } else if (draggedSection) {
            const cards = [...sectionList.querySelectorAll('[data-section-id]:not(.is-dragging)')];
            const next = cards.find((card) => event.clientY < card.getBoundingClientRect().top + card.offsetHeight / 2);
            sectionList.insertBefore(draggedSection, next ?? null);
        }
    });

    sectionList.addEventListener('drop', (event) => {
        event.preventDefault();
        if (draggedItem) {
            const section = findSection(draggedItem);
            const ids = [...draggedItem.parentElement.querySelectorAll('[data-item-id]')].map((element) => Number(element.dataset.itemId));
            section.items = ids.map((id, index) => {
                const item = section.items.find((candidate) => candidate.id === id);
                item.sort_order = index;
                return item;
            });
            markSaving();
            request({
                url: `${sectionsUrl}/${section.id}/items/reorder`,
                method: 'POST',
                data: { item_ids: ids },
            }).done(() => {
                markSaved('Entry order saved');
                refreshPreview();
            }).fail(showError);
        } else if (draggedSection) {
            const ids = [...sectionList.querySelectorAll('[data-section-id]')].map((element) => Number(element.dataset.sectionId));
            payload.sections = ids.map((id, index) => {
                const section = payload.sections.find((candidate) => candidate.id === id);
                section.sort_order = index;
                return section;
            });
            markSaving();
            request({
                url: root.dataset.reorderUrl,
                method: 'POST',
                data: { section_ids: ids },
            }).done(() => {
                markSaved('Section order saved');
                refreshPreview();
            }).fail(showError);
        }
    });

    const addSectionModalElement = document.getElementById('addSectionModal');
    const addSectionModal = bootstrap.Modal.getOrCreateInstance(addSectionModalElement);
    root.querySelector('[data-add-section]').addEventListener('click', () => addSectionModal.show());

    root.querySelector('[data-custom-section-form]').addEventListener('submit', (event) => {
        event.preventDefault();
        const form = event.currentTarget;
        const title = form.elements.title.value.trim();
        if (!title) return;
        markSaving();
        request({
            url: sectionsUrl,
            method: 'POST',
            data: { title },
        }).done((response) => {
            payload.sections.push(response.section);
            renderSections();
            addSectionModal.hide();
            form.reset();
            markSaved('Custom section added');
            refreshPreview();
            sectionList.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }).fail((xhr) => {
            showError(xhr);
            const feedback = form.querySelector('[data-custom-section-error]');
            feedback.textContent = xhr.responseJSON?.errors?.title?.[0] ?? 'Enter a valid section name.';
            form.elements.title.classList.add('is-invalid');
        });
    });

    root.querySelector('[data-refresh-preview]').addEventListener('click', refreshPreview);

    renderSections();
}
