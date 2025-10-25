document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.querySelector('.sidebar');
  const mainContent = document.querySelector('.main-content');
  const topbar = document.querySelector('.topbar');
  const menuToggle = document.getElementById('menu-toggle');

  if (menuToggle) {
    menuToggle.addEventListener('click', () => {
      sidebar.classList.toggle('closed');
      mainContent.classList.toggle('expanded');
      topbar.classList.toggle('expanded');
      menuToggle.classList.toggle('active');
    });
  }
});