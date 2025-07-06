document.addEventListener('DOMContentLoaded', function () {
    function slugify(text) {
        return text
            .toString()
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/\s+/g, '-')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '-')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    }

    // Rechercher tous les formulaires qui ont les éléments requis
    document.querySelectorAll('form').forEach(form => {
        const titleInput = form.querySelector('[id$="_title"]');
        const subtitleInput = form.querySelector('[id$="_subtitle"]');
        const slugInput = form.querySelector('[id$="_slug"]');
        const slugAutoCheckbox = form.querySelector('[id$="_slugAuto"]');

        function updateSlug() {
            if (slugAutoCheckbox?.checked && titleInput && slugInput) {
                // Concaténer title et subtitle (si subtitle existe)
                let combinedText = titleInput.value;
                if (subtitleInput && subtitleInput.value.trim()) {
                    combinedText += ' ' + subtitleInput.value;
                }
                slugInput.value = slugify(combinedText);
            }
        }

        if (titleInput && slugInput && slugAutoCheckbox) {
            titleInput.addEventListener('input', updateSlug);
            // Ajouter l'événement sur subtitle si il existe
            if (subtitleInput) {
                subtitleInput.addEventListener('input', updateSlug);
            }
            updateSlug(); // Initial auto-fill
        }
    });
});