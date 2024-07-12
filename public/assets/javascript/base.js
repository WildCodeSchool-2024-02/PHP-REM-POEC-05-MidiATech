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

    if (searchTerm.length > 2) {
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

                if (data.musics) {
                    data.musics.forEach((item) => {
                        let paragraph = document.createElement("p");
                        let link = document.createElement("a");
                        link.textContent = `${item.title} chantés par ${item.author}`;
                        link.href = `musics/details/show?id=${item.id}`;
                        paragraph.appendChild(link);
                        resultsDiv.appendChild(paragraph);
                    });
                }
                if (data.books) {
                    data.books.forEach((item) => {
                        let paragraph = document.createElement("p");
                        let link = document.createElement("a");
                        link.textContent = `${item.title} écris par ${item.author}`;
                        link.href = `books/details/show?id=${item.id}`;
                        paragraph.appendChild(link);
                        resultsDiv.appendChild(paragraph);
                    });
                }
                if (data.videos) {
                    data.videos.forEach((item) => {
                        let paragraph = document.createElement("p");
                        let link = document.createElement("a");
                        link.textContent = `${item.title} réalisés par ${item.author}`;
                        link.href = `videos/details/show?id=${item.id}`;
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
