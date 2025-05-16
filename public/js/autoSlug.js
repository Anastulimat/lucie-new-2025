document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.querySelector('#category_form_title');
    const slugInput = document.querySelector('#category_form_slug');
    const slugAutoCheckbox = document.querySelector('#category_form_slugAuto');
    function slugify(text) {
        return text
            .toString()
            .toLowerCase()
            .normalize('NFD')                     // Remove accents
            .replace(/[\u0300-\u036f]/g, '')     // Remove accents continued
            .replace(/\s+/g, '-')                // Replace spaces with -
            .replace(/[^\w\-]+/g, '')            // Remove all non-word chars
            .replace(/\-\-+/g, '-')              // Replace multiple - with single -
            .replace(/^-+/, '')                  // Trim - from start of text
            .replace(/-+$/, '');                 // Trim - from end of text
    }

    function updateSlug() {
        if (slugAutoCheckbox.checked && titleInput && slugInput) {
            slugInput.value = slugify(titleInput.value);
        }
    }

    if (slugAutoCheckbox && titleInput) {
        titleInput.addEventListener('input', updateSlug);
    }

    // Initial auto-fill on load
    updateSlug();
});