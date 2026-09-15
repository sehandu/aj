function filterEvents() {
  let category = (document.getElementById("categoryFilter")?.value || "all")
    .toLowerCase()
    .trim();

  document.querySelectorAll(".event-card").forEach(function (card) {
    let cardCat = (card.getAttribute("data-category") || "")
      .toLowerCase()
      .trim();

    let match = category === "all" || cardCat === category;
    card.style.display = match ? "" : "none";
  });
}

function confirmDelete(title) {
  return confirm("Delete event '" + title + "'?");
}
