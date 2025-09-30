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

// Card Slider
const cardSize = 200 + 20; // Combined card width and margin for all sliders

// Slider 1
let slideIndex1 = 0;
const totalCards1 = document.querySelectorAll('#slider1 .card').length;

function showSlide1(index) {
    const cardContainer1 = document.querySelector('#slider1 .card-container');
    const offset = -index * cardSize; // Using cardSize
    cardContainer1.style.transform = `translateX(${offset}px)`;
}

function changeSlide1(direction) {
    slideIndex1 += direction;
    if (slideIndex1 < 0) slideIndex1 = totalCards1 - 1;
    else if (slideIndex1 >= totalCards1) slideIndex1 = 0;
    showSlide1(slideIndex1);
}

// Event listeners for Slider 1 previous and next buttons
document.querySelector('#slider1 .prev').addEventListener('click', () => changeSlide1(-1));
document.querySelector('#slider1 .next').addEventListener('click', () => changeSlide1(1));

// Automatic sliding and pause on hover for Slider 1
let slideInterval1 = setInterval(() => changeSlide1(1), 3000);
document.querySelector('#slider1').addEventListener('mouseover', () => clearInterval(slideInterval1));
document.querySelector('#slider1').addEventListener('mouseleave', () => {
    slideInterval1 = setInterval(() => changeSlide1(1), 3000);
});

// Initial display for Slider 1
showSlide1(slideIndex1);


// Slider 2
let slideIndex2 = 0;
const totalCards2 = document.querySelectorAll('#slider2 .card').length;

function showSlide2(index) {
    const cardContainer2 = document.querySelector('#slider2 .card-container');
    const offset = -index * cardSize; // Using cardSize
    cardContainer2.style.transform = `translateX(${offset}px)`;
}

function changeSlide2(direction) {
    slideIndex2 += direction;
    if (slideIndex2 < 0) slideIndex2 = totalCards2 - 1;
    else if (slideIndex2 >= totalCards2) slideIndex2 = 0;
    showSlide2(slideIndex2);
}

// Event listeners for Slider 2 previous and next buttons
document.querySelector('#slider2 .prev').addEventListener('click', () => changeSlide2(-1));
document.querySelector('#slider2 .next').addEventListener('click', () => changeSlide2(1));

// Automatic sliding and pause on hover for Slider 2
let slideInterval2 = setInterval(() => changeSlide2(1), 3000);
document.querySelector('#slider2').addEventListener('mouseover', () => clearInterval(slideInterval2));
document.querySelector('#slider2').addEventListener('mouseleave', () => {
    slideInterval2 = setInterval(() => changeSlide2(1), 3000);
});

// Initial display for Slider 2
showSlide2(slideIndex2);


// Slider 3
let slideIndex3 = 0;
const totalCards3 = document.querySelectorAll('#slider3 .card').length;

function showSlide3(index) {
    const cardContainer3 = document.querySelector('#slider3 .card-container');
    const offset = -index * cardSize; // Using cardSize
    cardContainer3.style.transform = `translateX(${offset}px)`;
}

function changeSlide3(direction) {
    slideIndex3 += direction;
    if (slideIndex3 < 0) slideIndex3 = totalCards3 - 1;
    else if (slideIndex3 >= totalCards3) slideIndex3 = 0;
    showSlide3(slideIndex3);
}

// Event listeners for Slider 3 previous and next buttons
document.querySelector('#slider3 .prev').addEventListener('click', () => changeSlide3(-1));
document.querySelector('#slider3 .next').addEventListener('click', () => changeSlide3(1));

// Automatic sliding and pause on hover for Slider 3
let slideInterval3 = setInterval(() => changeSlide3(1), 3000);
document.querySelector('#slider3').addEventListener('mouseover', () => clearInterval(slideInterval3));
document.querySelector('#slider3').addEventListener('mouseleave', () => {
    slideInterval3 = setInterval(() => changeSlide3(1), 3000);
});

// Initial display for Slider 3
showSlide3(slideIndex3);

// Courses JavaScript

const faqs = document.querySelectorAll(".faq");

faqs.forEach((faq) => {
    faq.addEventListener("click", () => {
        faq.classList.toggle("active");
    });
});