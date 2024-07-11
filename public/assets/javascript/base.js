let goToTop = document.getElementById("goToTop");

window.onscroll = function () {
    scrollFunction();
};

function scrollFunction()
{
    if (document.documentElement.scrollTop > 200 && window.innerWidth > 768) {
        goToTop.style.display = "flex";
    } else if (
        document.documentElement.scrollTop <= 200 &&
        window.innerWidth > 768
    ) {
        goToTop.style.display = "none";
    } else if (
        document.documentElement.scrollTop > 110 &&
        window.innerWidth <= 768
    ) {
        goToTop.style.display = "flex";
    } else if (
        document.documentElement.scrollTop <= 109 &&
        window.innerWidth <= 768
    ) {
        goToTop.style.display = "none";
    }
}

function topFunction()
{
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}


document.getElementById('searchInput').addEventListener('input', function () {
    const searchTerm = this.value.trim();
    const resultsDiv = document.getElementById('results');

    if (searchTerm.length > 2) {
        fetch(`/search?term=${encodeURIComponent(searchTerm)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const contentType = response.headers.get("content-type");

                if (!contentType || !contentType.includes("application/json")) {
                    throw new TypeError("Oops, we haven't got JSON!");
                }

                return response.json();
            })
            .then(data => {
                resultsDiv.innerHTML = '';

                if (data.musics) {
                    data.musics.forEach(item => {
                        resultsDiv.innerHTML +=
                            `<p>
                                <a href="musics/details/show?id=${item.id}">
                                    ${item.title} chantés par ${item.singer}
                                </a>
                            </p>`;
                    });
                }
                if (data.books) {
                    data.books.forEach(item => {
                        resultsDiv.innerHTML +=
                            `<p>
                                <a href="musics/details/show?id=${item.id}">
                                    ${item.title} écris par ${item.author}
                                </a>
                            </p>`;
                    });
                }
                if (data.videos) {
                    data.videos.forEach(item => {
                        resultsDiv.innerHTML +=
                            `<p>
                                <a href="musics/details/show?id=${item.id}">
                                    ${item.title} réalisés par ${item.director}
                                </a>
                            </p>`;
                    });
                }

                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div>Aucun résultat</div>';
                }

                resultsDiv.style.display = 'initial';

            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
                resultsDiv.innerHTML = `<p>Erreur lors de la recherche</p>`;
                resultsDiv.style.display = 'initial';
            });
    } else {
        resultsDiv.innerHTML = '';
        resultsDiv.style.display = 'none';
    }
});
