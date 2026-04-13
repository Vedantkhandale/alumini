<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}
include("../includes/header.php"); 
?>

<style>
/* ADMIN PAGE WRAPPER */
.admin-dashboard {
    padding: 80px 10%;
    background: #0f172a; /* Dark Navy/Black Theme */
    min-height: 100vh;
    color: #fff;
}

/* HEADER SECTION */
.admin-header {
    margin-bottom: 50px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding-bottom: 20px;
}

.admin-header h2 {
    font-size: 2.5rem;
    font-weight: 800;
    letter-spacing: -1.5px;
}

.admin-header span {
    color: #ff3b3b;
}

/* CONTROL GRID */
.admin-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}

/* POWER CARDS */
.admin-card {
    background: rgba(255, 255, 255, 0.03);
    padding: 40px;
    border-radius: 30px;
    text-decoration: none;
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    position: relative;
    overflow: hidden;
}

.admin-card i {
    font-size: 2.2rem;
    color: #ff3b3b;
    margin-bottom: 20px;
}

.admin-card h3 {
    color: #fff;
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.admin-card p {
    color: #94a3b8;
    font-size: 0.95rem;
    line-height: 1.6;
}

/* HOVER EFFECTS */
.admin-card:hover {
    transform: translateY(-10px);
    background: rgba(255, 255, 255, 0.07);
    border-color: #ff3b3b;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
}

.admin-card::after {
    content: '→';
    position: absolute;
    right: 30px;
    bottom: 30px;
    font-size: 1.5rem;
    color: #ff3b3b;
    opacity: 0;
    transition: 0.3s;
}

.admin-card:hover::after {
    opacity: 1;
    right: 40px;
}

/* LOGOUT BUTTON */
.admin-logout {
    margin-top: 50px;
    display: inline-block;
    padding: 12px 30px;
    border: 1px solid #ef4444;
    color: #ef4444;
    text-decoration: none;
    border-radius: 100px;
    font-weight: 600;
    transition: 0.3s;
}

.admin-logout:hover {
    background: #ef4444;
    color: #fff;
}

/* MOBILE RESPONSIVE */
@media(max-width:768px){
    .admin-dashboard { padding: 40px 20px; }
    .admin-header h2 { font-size: 2rem; }
}
</style>

<div class="admin-dashboard">
    <div class="admin-header reveal">
        <h2>Admin <span>Control Center</span></h2>
        <p>Manage your alumni network, jobs, and upcoming events.</p>
    </div>

    <div class="admin-grid">
        <a href="jobs.php" class="admin-card reveal">
            <i class="fas fa-tasks"></i>
            <h3>Manage Jobs</h3>
            <p>Approve or reject job postings from alumni and track current openings.</p>
        </a>

        <a href="events.php" class="admin-card reveal" style="transition-delay: 0.1s;">
            <i class="fas fa-calendar-check"></i>
            <h3>Manage Events</h3>
            <p>Schedule new meetups, webinars, and alumni gatherings.</p>
        </a>

        <a href="alumni_list.php" class="admin-card reveal" style="transition-delay: 0.2s;">
            <i class="fas fa-users-cog"></i>
            <h3>Alumni Directory</h3>
            <p>View and manage the complete list of registered alumni members.</p>
        </a>
    </div>

    <a href="../logout.php" class="admin-logout reveal">
        <i class="fas fa-power-off"></i> Secure Logout
    </a>
</div>

<?php include("../includes/footer.php"); ?>