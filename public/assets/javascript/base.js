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

document.getElementById("searchInput").addEventListener("input", function () {
    const searchTerm = this.value.trim();
    const resultsDiv = document.getElementById("results");
    const currentPageMetaTag = document.getElementById('page');
    const currentPage = currentPageMetaTag.getAttribute('content');

    if (searchTerm.length > 1) {
        fetch(`/search?term=${encodeURIComponent(searchTerm)}`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const contentType = response.headers.get("content-type");

                if (!contentType || !contentType.includes("application/json")) {
                    throw new TypeError("Oops, we haven't got JSON!");
                }

                return response.json();
            })
            .then((data) => {
                while (resultsDiv.firstChild) {
                    resultsDiv.removeChild(resultsDiv.firstChild);
                }

                if ((currentPage == 'home' || currentPage == 'musics') && (data.musics)) {
                    data.musics.forEach((item) => {
                        let paragraph = document.createElement("p");
                        let link = document.createElement("a");
                        let strong = document.createElement("strong");
                        strong.textContent = 'Musique';
                        let textNode = document.createTextNode(` - ${item.title} chantés par ${item.singer}`);
                        link.appendChild(strong);
                        link.appendChild(textNode);
                        link.href = `musics/show?id=${item.id}`;
                        paragraph.appendChild(link);
                        resultsDiv.appendChild(paragraph);
                    });
                }
                if ((currentPage === 'home' || currentPage === 'books') && (data.books)) {
                    data.books.forEach((item) => {
                        let paragraph = document.createElement("p");
                        let link = document.createElement("a");
                        let strong = document.createElement("strong");
                        strong.textContent = 'Livre';
                        let textNode = document.createTextNode(` - ${item.title} écrit par ${item.author}`);
                        link.appendChild(strong);
                        link.appendChild(textNode);
                        link.href = `books/show?id=${item.id}`;
                        paragraph.appendChild(link);
                        resultsDiv.appendChild(paragraph);
                    });
                }
                if ((currentPage === 'home' || currentPage === 'videos') && (data.videos)) {
                    data.videos.forEach((item) => {
                        let paragraph = document.createElement("p");
                        let link = document.createElement("a");
                        let strong = document.createElement("strong");
                        strong.textContent = `${item.name}`;
                        let textNode = document.createTextNode(` - ${item.title} réalisé par ${item.director}`);
                        link.appendChild(strong);
                        link.appendChild(textNode);
                        link.href = `videos/show?id=${item.id}`;
                        paragraph.appendChild(link);
                        resultsDiv.appendChild(paragraph);
                    });
                }

                if (data.length === 0) {
                    let paragraph = document.createElement("p");
                    paragraph.textContent = `Aucun résultat`;
                    resultsDiv.appendChild(paragraph);
                }

                resultsDiv.style.display = "initial";
            })
            .catch((error) => {
                console.error(
                    "There was a problem with the fetch operation:",
                    error
                );
                let paragraph = document.createElement("p");
                paragraph.textContent = `Erreur lors de la recherche`;
                resultsDiv.appendChild(paragraph);
                resultsDiv.style.display = "initial";
            });
    } else {
        while (resultsDiv.firstChild) {
            resultsDiv.removeChild(resultsDiv.firstChild);
        }
        resultsDiv.style.display = "none";
    }
});
