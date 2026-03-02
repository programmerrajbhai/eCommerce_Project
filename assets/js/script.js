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