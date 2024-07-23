document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('categoryFilter').addEventListener('change', function () {
        const selectedCategory = this.value;
        const mediaType = document.getElementById('media_type').value;
        let url = '/' + mediaType;
        if (selectedCategory !== 'Tout') {
            url += '?category=' + encodeURIComponent(selectedCategory);
        }
        window.location.href = url;
    });
});
