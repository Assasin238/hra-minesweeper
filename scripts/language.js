document.addEventListener("DOMContentLoaded", () => {
    const currentLangBtn = document.getElementById("current-lang");
    const langMenu = document.querySelector(".language-menu");

    // Přepínání viditelnosti menu při kliknutí na tlačítko
    currentLangBtn.addEventListener("click", (event) => {
        event.stopPropagation(); // Zabrání zavření menu ihned po otevření
        langMenu.classList.toggle("visible"); // Přidá nebo odstraní třídu 'visible'
    });

    // Zavřít menu při kliknutí mimo něj
    document.addEventListener("click", () => {
        langMenu.classList.remove("visible");
    });

    // Přidání funkčnosti výběru jazyka
    langMenu.addEventListener("click", (event) => {
        if (event.target.tagName === "A") {
            event.preventDefault();
            const selectedLang = event.target.getAttribute("data-lang");
            if (selectedLang) {
                loadLanguage(selectedLang); // Funkce načtení jazyka
                localStorage.setItem("language", selectedLang); // Uložit jazyk
                langMenu.classList.remove("visible"); // Zavřít menu po výběru
            }
        }
    });

    // Načtení výchozího jazyka z localStorage nebo výchozí "en"
    let currentLang = localStorage.getItem("language") || "en";
    loadLanguage(currentLang);

    // Funkce pro načtení jazyka
    function loadLanguage(lang) {
        fetch(`translations/${lang}.json`)
            .then((response) => response.json())
            .then((data) => {
                // Aktualizovat texty na stránce
                document.getElementById("title").textContent = data["title"];
                document.getElementById("playBTN").textContent = data["play-btn"];
                document.getElementById("logout-btn").textContent = data["logout-btn"];
                currentLangBtn.textContent = data["current-lang"];

                // Zvýraznění aktuálně vybraného jazyka
                document.querySelectorAll(".language-menu a").forEach((link) => {
                    if (link.getAttribute("data-lang") === lang) {
                        link.style.fontWeight = "bold";
                    } else {
                        link.style.fontWeight = "normal";
                    }
                });
            })
            .catch((error) => console.error("Error loading language:", error));
    }
});
