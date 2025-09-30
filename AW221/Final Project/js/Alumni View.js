document.addEventListener('DOMContentLoaded', () => {
    let profile = document.querySelector('.header .flex .profile');
    let sideBar = document.querySelector('.side-bar');
    let body = document.querySelector('body');

    document.querySelector('#user-btn').onclick = () => {
        profile.classList.toggle('active');
    };

    window.onscroll = () => {
        profile.classList.remove('active');

        if (window.innerWidth < 1200) {
            sideBar.classList.remove('active');
            body.classList.remove('active');
        }
    };

    document.querySelector('#menu-btn').onclick = () => {
        sideBar.classList.toggle('active');
        body.classList.toggle('active');
    };

    document.querySelector('.side-bar .close-side-bar').onclick = () => {
        sideBar.classList.remove('active');
        body.classList.remove('active');
    };

    // Result Table Toggle Logic
    document.querySelectorAll('.class-btn').forEach(button => {
        button.addEventListener('click', function () {
            // Hide all tables
            document.querySelectorAll('.class-table').forEach(table => {
                table.style.display = 'none';
            });

            // Show the selected table
            const selectedClass = this.getAttribute('data-class');
            document.getElementById(selectedClass).style.display = 'block';

            // Remove active class from all buttons
            document.querySelectorAll('.class-btn').forEach(btn => {
                btn.classList.remove('active');
            });

            // Add active class to the clicked button
            this.classList.add('active');
        });
    });

    // Modal Logic
    document.querySelector('.open-modal-btn').onclick = openModal;
    document.querySelector('.close-modal-btn').onclick = closeModal;
});

function openModal() {
    document.getElementById("modal-container").style.display = "flex";
}

function closeModal() {
    document.getElementById("modal-container").style.display = "none";
}