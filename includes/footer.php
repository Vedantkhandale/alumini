<style>
/* ========================================
    ALUMNI PORTAL - SEXY FOOTER
   ======================================== */

.footer {
    background: #0f172a; /* Deep Dark Blue-Black */
    color: #ffffff;
    padding: 80px 0 0 0;
    margin-top: 100px;
    position: relative;
    border-radius: 50px 50px 0 0; /* Sexy Top Curves */
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.footer-container {
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1fr; /* Custom width for about section */
    gap: 50px;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 80px;
}

/* FOOTER BOXES */
.footer-box h3 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 25px;
    position: relative;
    color: #fff;
}

/* Subtle line under headings */
.footer-box h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 30px;
    height: 2px;
    background: #ff3b3b;
}

.footer-box p {
    color: #94a3b8;
    font-size: 15px;
    line-height: 1.8;
}

.footer-box a {
    color: #94a3b8;
    font-size: 15px;
    margin-bottom: 12px;
    display: block;
    text-decoration: none;
    transition: all 0.3s ease;
}

/* LOGO */
.footer-logo {
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -1px;
    margin-bottom: 20px;
    color: #fff;
}

.footer-logo span {
    color: #ff3b3b;
}

/* LINKS HOVER */
.footer-box a:hover {
    color: #ff3b3b;
    transform: translateX(8px); /* Sexy Slide Effect */
}

/* SOCIAL ICONS UPGRADE */
.socials {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.socials a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 14px;
    color: #fff;
    font-size: 18px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.socials a:hover {
    background: #ff3b3b;
    color: #fff;
    transform: translateY(-5px) rotate(10deg);
    box-shadow: 0 10px 20px rgba(255, 59, 59, 0.3);
    border-color: #ff3b3b;
}

/* NEWSLETTER STYLE (Optional sexy addition) */
.contact-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
    color: #94a3b8;
}

.contact-item i {
    color: #ff3b3b;
}

/* BOTTOM STRIP */
.footer-bottom {
    text-align: center;
    padding: 30px;
    margin-top: 60px;
    border-top: 1px solid rgba(255, 255, 255, 0.05);
    font-size: 14px;
    color: #64748b;
    letter-spacing: 1px;
}

/* MOBILE RESPONSIVE */
@media(max-width: 1024px) {
    .footer-container {
        grid-template-columns: repeat(2, 1fr);
        padding: 0 40px;
    }
}

@media(max-width: 600px) {
    .footer-container {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    .footer {
        border-radius: 30px 30px 0 0;
        padding-top: 60px;
    }
}
</style>

<footer class="footer">
    <div class="footer-container">

        <div class="footer-box">
            <h2 class="footer-logo">Alumni<span>X</span></h2>
            <p>Connecting Nagpur's finest minds. We build bridges between legacy and the future, creating opportunities that matter.</p>
            <div class="socials">
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
            </div>
        </div>

        <div class="footer-box">
            <h3>Explore</h3>
            <a href="index.php">Network Hub</a>
            <a href="events.php">Global Meets</a>
            <a href="jobs.php">Career Portal</a>
            <a href="alumni.php">Success Stories</a>
        </div>

        <div class="footer-box">
            <h3>Support</h3>
            <a href="#">Help Center</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Service</a>
            <a href="#">Member Guidelines</a>
        </div>

        <div class="footer-box">
            <h3>Contact Us</h3>
            <div class="contact-item">
                <i class="fas fa-envelope"></i>
                <span>hello@alumnix.com</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-phone-alt"></i>
                <span>+91 98765 43210</span>
            </div>
            <div class="contact-item">
                <i class="fas fa-map-marker-alt"></i>
                <span>Nagpur, India</span>
            </div>
        </div>

    </div>

    <div class="footer-bottom">
        &copy; 2026 ALUMNIX PORTAL. CRAFTED FOR THE ELITE.
    </div>
</footer>

</body>
</html>
