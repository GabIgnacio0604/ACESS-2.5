document.addEventListener("DOMContentLoaded", () => {

  // Handle delete clicks globally
  document.body.addEventListener("click", async (e) => {
    const eventBtn = e.target.closest(".deleteEventBtn");
    const announceBtn = e.target.closest(".deleteAnnouncementBtn");

    // If not a delete button, ignore
    if (!eventBtn && !announceBtn) return;

    // Identify type & ID
    const btn = eventBtn || announceBtn;
    const id = btn.dataset.id;
    const type = eventBtn ? "event" : "announcement";

    if (!confirm(`Delete this ${type}?`)) return;

    try {
      const res = await fetch("../API/delete_items.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id, type })
      });
      const data = await res.json();

      if (data.success) {
        alert(`${type.charAt(0).toUpperCase() + type.slice(1)} deleted successfully!`);
        btn.parentElement.remove();
      } else {
        alert("Failed to delete: " + (data.error || "Unknown error"));
      }
    } catch (err) {
      console.error(`Error deleting ${type}:`, err);
      alert(`Error deleting ${type}.`);
    }
  });

});

// Edit mode
document.querySelectorAll(".edit-btn").forEach(btn => {
  btn.addEventListener("click", () => {
    const type = btn.dataset.type;
    const date = prompt("Enter date (YYYY-MM-DD):");
    const title = prompt("Enter title:");
    if (date && title) {
      fetch("../API/update_dashboard_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ type, action: "add", date, title })
      })
        .then(res => res.json())
        .then(() => loadDashboard());
    }
  });
});

