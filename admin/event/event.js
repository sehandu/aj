function filterEvents() {
    let search = (document.getElementById("search")?.value || "").toLowerCase().trim();
    let category = (document.getElementById("categoryFilter")?.value || "all").toLowerCase().trim();

    document.querySelectorAll(".event-card").forEach(function(card) {
        let title = card.querySelector("h2")?.textContent.toLowerCase() || "";
        let cardCat = (card.getAttribute("data-category") || "").toLowerCase().trim();

        let match = title.includes(search) && (category === "all" || cardCat === category);
        card.style.display = match ? "" : "none";
    });
}

function confirmDelete(title) {
    return confirm("Delete event '" + title + "'?");
}
