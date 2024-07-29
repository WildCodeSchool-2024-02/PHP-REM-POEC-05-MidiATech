document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('categoryFilter');
    const typeFilter = document.getElementById('typeFilter');
    const mediaType = document.getElementById('media_type').value;

    categoryFilter.addEventListener('change', function() {
        updateFilter();
    });

    if (typeFilter) {
        typeFilter.addEventListener('change', function() {
            updateFilter();
        });
    }

    function updateFilter() {

        const selectedCategory = categoryFilter.value;


        let url = '/' + mediaType;

        if (mediaType === 'videos') {
            const selectedType = typeFilter.value;



            if (selectedCategory !== 'Tout' || selectedType !== 'Tout') {
                url += '?';

                if (selectedCategory !== 'Tout') {
                    url += 'category=' + encodeURIComponent(selectedCategory);
                    if (selectedType !== 'Tout') {

        
                        url += '&type=' + encodeURIComponent(selectedType) ;
                    }

                }
                else{
                    url+= 'category=Tout&' ;
                    if (selectedType !== 'Tout') {

        
                        url += 'type=' + encodeURIComponent(selectedType) ;
                    }


                }


                
            }
        } else {
            if (selectedCategory !== 'Tout') {
                url += '?category=' + encodeURIComponent(selectedCategory);
            }
        }

        window.location.href = url;
    }

    // Persists the selected values after reloading
    const urlParams = new URLSearchParams(window.location.search);
    const currentCategory = urlParams.get('category');
    const currentType = urlParams.get('type');

    if (currentCategory) {
        categoryFilter.value = currentCategory;
    }
    if (currentType && typeFilter) {
        typeFilter.value = currentType;
    }
});
