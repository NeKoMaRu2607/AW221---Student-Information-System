// Sidebar toggle functionality
const menuToggle = document.querySelector('.menuToggle');
const sidebar = document.getElementById('sidebar');
const closeBtn = document.getElementById('closeBtn');

menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
});

closeBtn.addEventListener('click', () => {
    sidebar.classList.remove('open');
});