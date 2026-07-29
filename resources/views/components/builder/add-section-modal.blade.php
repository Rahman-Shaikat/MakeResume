<div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content custom-section-modal" data-custom-section-form>
            <div class="modal-header">
                <div>
                    <span class="section-kicker">Custom content</span>
                    <h2 class="modal-title" id="addSectionModalLabel">Add a new section</h2>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="custom_section_title" class="form-label">Section name</label>
                <input id="custom_section_title" name="title" class="form-control" maxlength="100" placeholder="e.g. Publications" required>
                <div class="invalid-feedback" data-custom-section-error></div>
                <p>After creating it, you can add and reorder as many text items as you need.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Add section
                </button>
            </div>
        </form>
    </div>
</div>
