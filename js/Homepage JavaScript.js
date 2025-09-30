// Slider/Carousel
let slideIndex = 0;
showSlides();

function showSlides() {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    let dots = document.getElementsByClassName("dot");
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none"; // Hide all slides
    }
    slideIndex++;
    if (slideIndex > slides.length) { slideIndex = 1; }
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", ""); // Remove active class from dots
    }
    slides[slideIndex - 1].style.display = "block"; // Show the current slide
    dots[slideIndex - 1].className += " active"; // Highlight the current dot
    setTimeout(showSlides, 4500); // Change image every 4.5 seconds
}

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