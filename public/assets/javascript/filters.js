document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded and parsed');
    
    document.getElementById('categoryFilter').addEventListener('change', function() {
        console.log('Category changed');
        var selectedCategory = this.value;
        var mediaType = "{{ media_type }}";
        var url = '/' + mediaType;
        if (selectedCategory !== 'Tout') {
            url += '?category=' + encodeURIComponent(selectedCategory);
        }
        console.log('Redirecting to: ' + url);
        window.location.href = url;
    });
});