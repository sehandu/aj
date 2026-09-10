document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("eventForm");
  const errorMessage = document.getElementById("errorMessage");

  if (form) {
    form.addEventListener("submit", function (event) {
      if (errorMessage) errorMessage.textContent = "";

      const title = document.getElementById("eventTitle").value.trim();
      const description = document.getElementById("description").value.trim();
      const category = document.getElementById("category").value.trim();
      const date = document.getElementById("event_date").value.trim();
      const time = document.getElementById("eventTime").value.trim();
      const venue = document.getElementById("eventVenue").value.trim();
      const participants = document.getElementById("participants")
        ? document.getElementById("participants").value.trim()
        : "";
      const organizer = document.getElementById("organizer")
        ? document.getElementById("organizer").value.trim()
        : "";

      if (title === "") {
        event.preventDefault();
        if (errorMessage)
          errorMessage.textContent = "Please enter an event title.";
        document.getElementById("eventTitle").focus();
        return;
      }
      if (description === "") {
        event.preventDefault();
        if (errorMessage)
          errorMessage.textContent = "Please enter event description.";
        document.getElementById("description").focus();
        return;
      }
      if (category === "") {
        event.preventDefault();
        if (errorMessage)
          errorMessage.textContent = "Please select a category.";
        document.getElementById("category").focus();
        return;
      }
      if (date === "") {
        event.preventDefault();
        if (errorMessage) errorMessage.textContent = "Please select a date.";
        document.getElementById("event_date").focus();
        return;
      }
      if (time === "") {
        event.preventDefault();
        if (errorMessage)
          errorMessage.textContent = "Please select event time.";
        document.getElementById("eventTime").focus();
        return;
      }
      if (venue === "") {
        event.preventDefault();
        if (errorMessage) errorMessage.textContent = "Please enter a venue.";
        document.getElementById("eventVenue").focus();
        return;
      }
      if (participants === "" || Number(participants) < 1) {
        event.preventDefault();
        if (errorMessage)
          errorMessage.textContent = "Max participants should be at least 1.";
        document.getElementById("participants").focus();
        return;
      }
      if (organizer === "") {
        event.preventDefault();
        if (errorMessage)
          errorMessage.textContent = "Please enter the organizer name.";
        document.getElementById("organizer").focus();
        return;
      }
    });
  }
});

function clearForm() {
  const confirmation = confirm("Are you sure you want to clear the form?");
  if (confirmation) {
    document.getElementById("eventForm").reset();
    if (document.getElementById("errorMessage")) {
      document.getElementById("errorMessage").textContent = "";
    }
  }
}
