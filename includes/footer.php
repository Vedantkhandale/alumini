<style>
/* FOOTER */
.footer {
    background: #ffffff;
    padding-top: 50px;
    margin-top: 50px;
    border-top: 1px solid #eee;
}

.footer-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 30px;
    padding: 0 80px;
}

/* BOX */
.footer-box h3 {
    margin-bottom: 15px;
    color: #222;
}

.footer-box p,
.footer-box a {
    color: #666;
    font-size: 14px;
    margin-bottom: 8px;
    display: block;
    text-decoration: none;
}

/* LOGO */
.footer-logo {
    font-size: 22px;
    font-weight: 600;
}

.footer-logo span {
    color: #ff3b3b;
}

/* LINKS HOVER */
.footer-box a:hover {
    color: #ff3b3b;
}

/* SOCIAL ICONS */
.socials {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.socials a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    background: #f1f1f1;
    border-radius: 50%;
    color: #333;
    transition: 0.3s;
}

.socials a:hover {
    background: #ff3b3b;
    color: #fff;
}

/* BOTTOM */
.footer-bottom {
    text-align: center;
    padding: 15px;
    margin-top: 30px;
    background: #f9f9f9;
    font-size: 14px;
    color: #666;
}

/* MOBILE */
@media(max-width:768px){
    .footer-container {
        padding: 0 20px;
    }
}

</style>
<footer class="footer">

    <div class="footer-container">

        <!-- 🔥 LOGO + ABOUT -->
        <div class="footer-box">
            <h2 class="footer-logo">Alumni<span>X</span></h2>
            <p>Connecting alumni, sharing opportunities, and building a strong network for future growth.</p>
        </div>

        <!-- 🔗 QUICK LINKS -->
        <div class="footer-box">
            <h3>Quick Links</h3>
            <a href="index.php">Home</a>
            <a href="events.php">Events</a>
            <a href="jobs.php">Jobs</a>
            <a href="alumni.php">Alumni</a>
        </div>

        <!-- 📞 CONTACT -->
        <div class="footer-box">
            <h3>Contact</h3>
            <p>Email: info@alumniX.com</p>
            <p>Phone: +91 9876543210</p>
            <p>Location: India</p>
        </div>

        <!-- 🌐 SOCIAL -->
        <div class="footer-box">
            <h3>Follow Us</h3>
            <div class="socials">
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>

    </div>

    <!-- 🔥 BOTTOM -->
    <div class="footer-bottom">
        © 2026 AlumniX | All Rights Reserved
    </div>

</footer>

</body>
</html>