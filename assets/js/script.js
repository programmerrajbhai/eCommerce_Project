// assets/js/script.js

// কার্ট টগল (ওপেন এবং ক্লোজ) করার ফাংশন
function toggleCart() {
    const cart = document.getElementById('offcanvas-cart');
    const overlay = document.getElementById('cart-overlay');
    
    cart.classList.toggle('active');
    overlay.classList.toggle('active');
    
    // কার্ট ওপেন হলে পেছনের বডি স্ক্রল করা বন্ধ করে দেওয়া
    if (cart.classList.contains('active')) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = 'auto';
    }
}

// স্ক্রল প্যারালাক্স ইফেক্ট (হিরো সেকশনের গ্লোয়িং শেপের জন্য)
window.addEventListener('scroll', function() {
    const glowShape = document.querySelector('.glow-shape');
    
    if (glowShape) {
        let scrollPosition = window.pageYOffset;
        // ইউজার নিচে স্ক্রল করলে শেপটি হালকা উপরে উঠবে এবং একটু ঘুরবে
        glowShape.style.transform = `rotate(${45 + scrollPosition * 0.05}deg) translateY(${scrollPosition * -0.2}px)`;
    }
});


// ==========================================
// ডাইনামিক কার্ট ফাংশন (AJAX)
// ==========================================

// কার্ট আপডেট করে UI তে দেখানো
function updateCartUI() {
    const formData = new FormData();
    formData.append('action', 'fetch');

    fetch('cart_logic.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('cart-items-container').innerHTML = data.html;
        document.querySelector('.cart-footer h3').innerHTML = 'Total: <span class="highlight">৳ ' + data.total + '</span>';
        document.getElementById('cart-badge-count').innerText = data.count;
    });
}

// প্রোডাক্ট কার্টে অ্যাড করা
function addToCart(productId) {
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);

    fetch('cart_logic.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            updateCartUI();
            
            // কার্ট যদি আগে থেকে ওপেন না থাকে, তাহলে ওপেন করবে
            const cart = document.getElementById('offcanvas-cart');
            if(!cart.classList.contains('active')) {
                toggleCart(); 
            }
        }
    });
}

// কার্ট থেকে প্রোডাক্ট রিমুভ করা
function removeFromCart(index) {
    const formData = new FormData();
    formData.append('action', 'remove');
    formData.append('index', index);

    fetch('cart_logic.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === 'success') {
            updateCartUI();
        }
    });
}

// পেজ লোড হওয়ার সাথে সাথে একবার কার্ট চেক করবে
document.addEventListener('DOMContentLoaded', updateCartUI);


// ==========================================
// হিরো স্লাইডার ফাংশন (Premium Slider Logic)
// ==========================================
let slideIndex = 0;
const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
let slideInterval;

function showSlide(index) {
    if (!slides.length) return;
    
    // সবগুলো স্লাইড এবং ডট থেকে active ক্লাস রিমুভ করা
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    
    // ইনডেক্স ঠিক করা
    if (index >= slides.length) slideIndex = 0;
    if (index < 0) slideIndex = slides.length - 1;
    
    // বর্তমান স্লাইড এবং ডট অ্যাকটিভ করা
    slides[slideIndex].classList.add('active');
    dots[slideIndex].classList.add('active');
}

function moveSlide(step) {
    slideIndex += step;
    showSlide(slideIndex);
    resetInterval(); // ম্যানুয়ালি ক্লিক করলে টাইমার রিসেট হবে
}

function currentSlide(index) {
    slideIndex = index;
    showSlide(slideIndex);
    resetInterval();
}

// অটোমেটিক স্লাইড হওয়ার জন্য (প্রতি ৫ সেকেন্ডে)
function startSlider() {
    slideInterval = setInterval(() => {
        slideIndex++;
        showSlide(slideIndex);
    }, 5000);
}

function resetInterval() {
    clearInterval(slideInterval);
    startSlider();
}

// পেজ লোড হলে স্লাইডার চালু হবে
if(slides.length > 0) {
    startSlider();
}