function loadLatestBanner() {
  fetch('../Banner/get_banner.php')
    .then(res => res.json())
    .then(data => {
      if (data.file) {
        const bannerImg = document.getElementById('bannerImage');
        if (bannerImg) bannerImg.src = data.file;
      }
    })
    .catch(err => console.error('Banner fetch error:', err));
}

// Upload banner (admin only)
function uploadBannerImage() {
  const fileInput = document.getElementById('bannerUpload');
  const file = fileInput?.files?.[0];
  if (!file) {
    alert('Please select a file first');
    return;
  }

  const formData = new FormData();
  formData.append('banner', file);

  fetch('../Admin/Php/upload_banner.php', {
    method: 'POST',
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert('Banner uploaded successfully!');
        loadLatestBanner();
        window.location.reload(); 
      } else {
        alert('Upload failed: ' + data.error);
      }
    })
    .catch(err => console.error('Upload error:', err));
}

// Attach events
document.addEventListener('DOMContentLoaded', () => {
  loadLatestBanner();

  const uploadBtn = document.getElementById('uploadBtn');
  if (uploadBtn) uploadBtn.addEventListener('click', uploadBannerImage);

  // Delete banner
  const deleteBannerBtn = document.getElementById('deleteBannerBtn');
  if (deleteBannerBtn) {
    deleteBannerBtn.addEventListener('click', async () => {
      const bannerImg = document.getElementById('bannerImage');
      const src = bannerImg?.src;

      if (!src || src.includes('placeholder.com')) {
        alert('No banner to delete.');
        return;
      }

      if (!confirm('Are you sure you want to delete this banner?')) return;

      try {
        const res = await fetch('./Admin/Php/delete_banner.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ file: src })
        });
        const data = await res.json();

        if (data.success) {
          alert('Banner deleted successfully!');
          bannerImg.src =
            'https://via.placeholder.com/1100x300?text=No+Banner';
            window.location.reload(); 
        } else {
          alert('Failed to delete: ' + (data.error || 'Unknown error'));
        }
      } catch (err) {
        console.error('Delete banner error:', err);
        alert('Error deleting banner.');
      }
    });
  }
});

// Delete event
document.addEventListener("click", async (e) => {
  if (e.target.classList.contains("deleteEventBtn")) {
    const id = e.target.dataset.id;
    if (!confirm("Delete this event?")) return;
    try {
      const res = await fetch("./API/update_dashboard_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ type: "event", action: "delete", id })
      });
      const data = await res.json();
      alert(data.success ? "Event deleted!" : "Error deleting event.");
      if (data.success) location.reload();
    } catch (err) {
      console.error("Delete event error:", err);
      alert("Error deleting event.");
    }
  }
});

// Delete club announcement
document.addEventListener("click", async (e) => {
  if (e.target.classList.contains("deleteAnnouncementBtn")) {
    const id = e.target.dataset.id;
    if (!confirm("Delete this announcement?")) return;
    try {
      const res = await fetch("./API/update_dashboard_item.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ type: "announcement", action: "delete", id })
      });
      const data = await res.json();
      alert(data.success ? "Announcement deleted!" : "Error deleting announcement.");
      if (data.success) location.reload();
    } catch (err) {
      console.error("Delete announcement error:", err);
      alert("Error deleting announcement.");
    }
  }
});
