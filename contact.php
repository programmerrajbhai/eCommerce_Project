<?php
// contact.php
require_once 'includes/header.php';

$success = false;

// কন্টাক্ট ফর্ম সাবমিট হলে এই লজিকটি কাজ করবে
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // যেহেতু এটি লোকালহোস্ট, তাই সরাসরি মেইল পাঠানো সম্ভব নয় (SMTP কনফিগার ছাড়া)। 
    // তাই আপাতত ফর্ম সাবমিট হলে একটি সুন্দর সাকসেস মেসেজ দেখাবে।
    $success = true;
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
/* =========================================
   কন্টাক্ট পেজ স্পেশাল ডিজাইন (Contact UI)
========================================= */
.contact-section {
    padding: 120px 8% 80px;
    min-height: 80vh;
}

.contact-header {
    text-align: center;
    margin-bottom: 50px;
}

.contact-header h1 {
    font-size: 45px;
    font-weight: 800;
    text-transform: uppercase;
}

.contact-header p {
    color: #aaa;
    margin-top: 10px;
    font-size: 16px;
}

.contact-container {
    display: flex;
    gap: 40px;
    max-width: 1100px;
    margin: 0 auto;
    flex-wrap: wrap;
}

/* বাম পাশের কন্টাক্ট ইনফরমেশন */
.contact-info {
    flex: 1;
    min-width: 300px;
    padding: 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 30px;
}

.info-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 115, 0, 0.1);
    color: var(--accent-orange);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 20px;
    box-shadow: var(--accent-glow);
    flex-shrink: 0;
}

.info-text h4 {
    color: #fff;
    font-size: 18px;
    margin-bottom: 5px;
}

.info-text p {
    color: #aaa;
    font-size: 14px;
    line-height: 1.6;
}

/* ডান পাশের কন্টাক্ট ফর্ম */
.contact-form-wrapper {
    flex: 1.5;
    min-width: 300px;
    padding: 40px;
}

.contact-form-wrapper h3 {
    font-size: 24px;
    margin-bottom: 25px;
    color: #fff;
}

.form-group {
    margin-bottom: 20px;
}

.form-control {
    width: 100%;
    padding: 15px 20px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #fff;
    border-radius: 10px;
    font-size: 14px;
    transition: 0.3s;
    outline: none;
    font-family: 'Poppins', sans-serif;
}

.form-control:focus {
    border-color: var(--accent-orange);
    box-shadow: 0 0 15px rgba(255, 115, 0, 0.2);
    background: rgba(255, 255, 255, 0.05);
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

/* সোশ্যাল মিডিয়া আইকন */
.social-links {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.social-links a {
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.05);
    color: #fff;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    transition: 0.3s;
    border: var(--glass-border);
}

.social-links a:hover {
    background: var(--accent-orange);
    color: #fff;
    transform: translateY(-5px);
    box-shadow: var(--accent-glow);
}

/* মোবাইল রেসপন্সিভ */
@media (max-width: 768px) {
    .contact-container { flex-direction: column; }
    .contact-info, .contact-form-wrapper { padding: 30px 20px; }
    .contact-header h1 { font-size: 35px; }
}
</style>

<section class="contact-section">
    <div class="contact-header">
        <h1>GET IN <span class="highlight">TOUCH</span></h1>
        <p>Have a question or need help? We're here for you!</p>
    </div>

    <div class="contact-container">
        
        <div class="contact-info glass-panel">
            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-location-dot"></i></div>
                <div class="info-text">
                    <h4>Our Location</h4>
                    <p>Dimla, Nilphamari<br>Bangladesh</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-phone"></i></div>
                <div class="info-text">
                    <h4>Call Us</h4>
                    <p>+880 1700 000000<br>+880 1900 000000</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-icon"><i class="fa-solid fa-envelope"></i></div>
                <div class="info-text">
                    <h4>Email Us</h4>
                    <p>support@rajstore.com<br>info@rajstore.com</p>
                </div>
            </div>

            <div class="info-item">
                <div class="info-text" style="width: 100%;">
                    <h4>Follow Us</h4>
                    <div class="social-links">
                        <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="contact-form-wrapper glass-panel">
            <h3>Send us a Message</h3>
            <form action="" method="POST">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Your Full Name" required>
                </div>
                
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Your Email Address" required>
                </div>
                
                <div class="form-group">
                    <input type="text" name="subject" class="form-control" placeholder="Subject" required>
                </div>
                
                <div class="form-group">
                    <textarea name="message" class="form-control" placeholder="Write your message here..." required></textarea>
                </div>
                
                <button type="submit" name="send_message" class="btn btn-primary" style="width: 100%; font-size: 16px;">
                    Send Message <i class="fa-solid fa-paper-plane" style="margin-left: 8px;"></i>
                </button>
            </form>
        </div>

    </div>
</section>

<?php if($success): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Message Sent!',
        text: 'Thank you for reaching out. We will get back to you soon.',
        background: '#111',
        color: '#fff',
        confirmButtonColor: 'var(--accent-orange)'
    });
</script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>