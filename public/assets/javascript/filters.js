document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('categoryFilter').addEventListener('change', function() {
        updateFilter();
    });

    const typeFilter = document.getElementById('typeFilter');
    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            updateFilter();
        });
    }
});

function updateFilter() {
    const selectedCategory = document.getElementById('categoryFilter').value;
    const mediaType = document.getElementById('media_type').value;
    let url = '/' + mediaType;

    if (mediaType === 'videos') {
        const selectedType = document.getElementById('typeFilter').value;
        if (selectedCategory !== 'Tout' || selectedType !== 'Tout') {
            url += '?';
            if (selectedCategory !== 'Tout') {
                url += 'category=' + encodeURIComponent(selectedCategory);
            }
            if (selectedType !== 'Tout') {
                if (selectedCategory !== 'Tout') {
                    url += '&';
                }
                url += 'type=' + encodeURIComponent(selectedType);
            }
        }
    } else {
        if (selectedCategory !== 'Tout') {
            url += '?category=' + encodeURIComponent(selectedCategory);
        }
    }

    window.location.href = url;
}
