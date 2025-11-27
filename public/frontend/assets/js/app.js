window.addEventListener("scroll", function () {
    const navbar = document.getElementById("mainNavbar");
    if (window.scrollY > 50) {
        navbar.classList.add("scrolled");
    } else {
        navbar.classList.remove("scrolled");
    }
});

const scrollContainer = document.getElementById('categoryScroll');
const leftBtn = document.querySelector('.carousel-scroll-btn.left');

function scrollRight() {
    scrollContainer.scrollBy({
        left: 300,
        behavior: 'smooth'
    });
}

function scrollLeft() {
    scrollContainer.scrollBy({
        left: -300,
        behavior: 'smooth'
    });
}

function toggleLeftButton() {
    if (scrollContainer.scrollLeft > 20) {
        leftBtn.style.display = 'block';
    } else {
        leftBtn.style.display = 'none';
    }
}

scrollContainer.addEventListener('scroll', toggleLeftButton);
window.addEventListener('load', toggleLeftButton);



// cart js 
