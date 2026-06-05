<?php
/**
 * AlumniX Core Administration Enterprise Architecture
 * Elite Production Engine - Polished Light Crimson Framework
 * Fixed Sidebar & Header with Smooth Main Content Scrolling
 */

// Production Baseline Static Dataset
$stats = [
    "total_verified" => 1240,
    "active_postings" => 38,
    "upcoming_events" => 6,
    "total_applications" => 184
];

$distribution = [
    "employed" => 65,      
    "higher_studies" => 20, 
    "entrepreneur" => 15   
];

$monthly_signups = [
    "Jan" => 25, "Feb" => 40, "Mar" => 55, "Apr" => 45, 
    "May" => 70, "Jun" => 85, "Jul" => 90, "Aug" => 75, 
    "Sep" => 95, "Oct" => 110, "Nov" => 125, "Dec" => 140
];

$recent_job_streams = [
    ["id" => 1, "title" => "Senior Backend Engineer (PHP)", "company" => "InfoCepts Technologies", "location" => "Nagpur IT Park", "apps" => 42, "status" => "Active", "trend" => [20, 40, 30, 80]],
    ["id" => 2, "title" => "Full Stack Developer", "company" => "Persistent Systems", "location" => "Mihan SEZ", "apps" => 56, "status" => "Active", "trend" => [40, 20, 70, 90]],
    ["id" => 3, "title" => "Data Solutions Analyst", "company" => "TCS Innovation Hub", "location" => "Nagpur Campus", "apps" => 18, "status" => "Reviewing", "trend" => [10, 50, 30, 40]],
    ["id" => 4, "title" => "UI/UX Product Designer", "company" => "AlumniX Venture Labs", "location" => "Remote / India", "apps" => 29, "status" => "Active", "trend" => [30, 60, 40, 70]]
];

$recent_activities = [
    ["time" => "10 mins ago", "type" => "verification", "title" => "New Alumni Verified", "desc" => "Amit Sharma (Batch of 2022, G H Raisoni) approved."],
    ["time" => "1 hour ago", "type" => "job", "title" => "Placement Posted", "desc" => "Persistent Systems opened a slot for Full Stack Dev."],
    ["time" => "3 hours ago", "type" => "event", "title" => "Corporate Meet Scheduled", "desc" => "Annual Alumni Conclave 2026 page generated."]
];

// SVG Donut Calculations
$radius = 50;
$circumference = 2 * M_PI * $radius; 
$employed_offset = $circumference * (1 - ($distribution["employed"] / 100));
$entrepreneur_offset = $circumference * (1 - (($distribution["employed"] + $distribution["entrepreneur"]) / 100));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniX Operations Center</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --red-primary: #f43f5e;     /* High-Fidelity Crimson Accent */
            --red-dark: #e11d48;        /* Deep Dynamic Crimson */
            --red-light: #fff1f2;       /* Selected UI Accent Tint */
            --black-text: #0f172a;      /* Slate Deep High Contrast Typography */
            --gray-muted: #64748b;      /* Sharp Clean Subtitles */
            --bg-pure-white: #ffffff;   /* Primary Surface */
            --bg-body: #fafafa;         /* Outer Workspace Matte Background */
            --border-sharp: #f1f5f9;    /* Clean Structural Dividers */
            --border-darker: #e2e8f0;   /* Component Grid Limits */
            --radius-box: 16px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(15, 23, 42, 0.03), 0 2px 4px -2px rgba(15, 23, 42, 0.03);
            --transition-bounce: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            --transition-smooth: all 0.2s ease;
        }

        /* Layout Framework Setup */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--black-text);
            background-color: var(--bg-body);
            display: flex;
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Sidebar Viewport Pinning */
        .sidebar {
            width: 280px;
            background: var(--bg-pure-white);
            border-right: 1px solid var(--border-darker);
            display: flex;
            flex-direction: column;
            height: 100vh;
            flex-shrink: 0;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 24px 28px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border-sharp);
        }
        .brand-logo {
            width: 36px; height: 36px; background: var(--black-text); color: var(--bg-pure-white);
            display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: 800;
            box-shadow: var(--shadow-sm); transition: var(--transition-bounce);
        }
        .sidebar-brand:hover .brand-logo { transform: rotate(-10deg) scale(1.05); background: var(--red-primary); }
        .brand-text { font-size: 20px; font-weight: 800; letter-spacing: -0.03em; }
        .brand-text span { color: var(--red-primary); }

        .profile-card {
            padding: 18px 28px; display: flex; align-items: center; gap: 14px;
            background: rgba(241, 245, 249, 0.4); border-bottom: 1px solid var(--border-sharp);
        }
        .profile-avatar {
            width: 42px; height: 42px; border-radius: 50%; background: var(--black-text);
            color: white; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700;
            border: 2px solid white; box-shadow: 0 0 0 1px var(--border-darker);
        }
        .profile-name { font-size: 13px; font-weight: 700; letter-spacing: -0.01em; }
        .profile-role { font-size: 11px; color: var(--gray-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 1px; }

        /* Navigation Interactive Controllers */
        .nav-container { flex: 1; overflow-y: auto; padding: 24px 16px; display: flex; flex-direction: column; gap: 4px; }
        .menu-dropdown-wrapper { display: flex; flex-direction: column; width: 100%; }
        
        .nav-link {
            display: flex; align-items: center; justify-content: space-between; padding: 13px 16px;
            font-size: 13.5px; font-weight: 700; text-decoration: none; color: var(--gray-muted); border-radius: 12px;
            cursor: pointer; background: transparent; border: none; width: 100%; outline: none;
            transition: var(--transition-smooth);
        }
        .nav-link:hover { color: var(--black-text); background: var(--border-sharp); }
        .nav-link.active { background: var(--red-light); color: var(--red-primary); }
        .nav-link div { display: flex; align-items: center; gap: 12px; }
        .nav-link i.fa-chevron-right { font-size: 10px; transition: transform 0.2s ease; }
        .menu-dropdown-wrapper.open .nav-link i.fa-chevron-right { transform: rotate(90deg); color: var(--black-text); }
        
        /* Smooth Interactive Menu Accordion Node */
        .sub-menu { 
            padding-left: 24px; 
            display: flex; 
            flex-direction: column; 
            gap: 4px; 
            max-height: 0; 
            overflow: hidden; 
            transition: max-height 0.25s cubic-bezier(0, 1, 0, 1); 
        }
        .menu-dropdown-wrapper.open .sub-menu { max-height: 200px; margin-top: 4px; margin-bottom: 8px; transition: max-height 0.25s cubic-bezier(1, 0, 1, 0); }
        
        .sub-link { 
            padding: 10px 16px; 
            font-size: 13px; 
            font-weight: 600; 
            color: var(--gray-muted); 
            text-decoration: none; 
            border-radius: 10px; 
            transition: var(--transition-bounce); 
            display: flex; 
            align-items: center; 
            gap: 10px;
        }
        .sub-link::before { content: ''; width: 5px; height: 5px; background: var(--border-darker); border-radius: 50%; transition: var(--transition-smooth); }
        .sub-link:hover { color: var(--black-text); background: var(--border-sharp); transform: translateX(4px); }
        .sub-link:hover::before { background: var(--black-text); transform: scale(1.3); }
        .sub-link.active { color: var(--red-primary) !important; font-weight: 700; background: rgba(244, 63, 94, 0.03); }
        .sub-link.active::before { background: var(--red-primary); }

        /* Workspace Main Layout Shell Frame */
        .workspace { flex: 1; display: flex; flex-direction: column; height: 100vh; max-height: 100vh; overflow: hidden; }
        .top-bar {
            height: 75px; background: var(--bg-pure-white); border-bottom: 1px solid var(--border-darker);
            display: flex; align-items: center; justify-content: space-between; padding: 0 40px; flex-shrink: 0;
        }
        .search-wrapper { position: relative; width: 320px; }
        .search-wrapper input {
            width: 100%; padding: 10px 16px 10px 42px; border-radius: 12px;
            border: 1px solid var(--border-darker); font-size: 13px; background: var(--bg-body); outline: none;
            transition: var(--transition-smooth); font-weight: 500;
        }
        .search-wrapper input:focus { border-color: var(--black-text); background: white; box-shadow: var(--shadow-sm); }
        .search-wrapper i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--gray-muted); font-size: 14px; }

        .top-actions-cluster { display: flex; align-items: center; gap: 16px; }
        .btn-action-trigger {
            background: var(--black-text); color: white; border: none; padding: 11px 20px; border-radius: 12px;
            font-size: 12.5px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px;
            box-shadow: var(--shadow-sm); transition: var(--transition-bounce);
        }
        .btn-action-trigger:hover { background: var(--red-primary); transform: translateY(-2px); box-shadow: 0 6px 12px rgba(244, 63, 94, 0.15); }
        .btn-action-trigger:active { transform: translateY(0); }

        /* FIXED SCROLLER BUG HERE: Forced Precise Viewport Constraints */
        .main-scroller { 
            height: calc(100vh - 75px);
            overflow-y: auto; 
            padding: 32px 40px; 
            display: flex; 
            flex-direction: column; 
            gap: 28px; 
        }
        
        .view-title-flex { display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .view-title-flex h2 { font-size: 26px; font-weight: 800; letter-spacing: -0.04em; color: var(--black-text); }
        
        .live-status-pill { display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; background: white; padding: 6px 14px; border-radius: 99px; border: 1px solid var(--border-darker); box-shadow: var(--shadow-sm); }
        .status-pulse-dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; position: relative; }
        .status-pulse-dot::after { content: ''; position: absolute; width: 100%; height: 100%; background: inherit; border-radius: inherit; animation: pulseWave 1.8s infinite ease-in-out; }

        @keyframes pulseWave { 0% { transform: scale(1); opacity: 1; } 100% { transform: scale(3); opacity: 0; } }

        /* Dashboard Structural Grids Elements */
        .metrics-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; flex-shrink: 0; }
        .card-metric {
            background: var(--bg-pure-white); border: 1px solid var(--border-darker); border-radius: var(--radius-box);
            padding: 20px 24px; display: flex; justify-content: space-between; align-items: center;
            transition: var(--transition-bounce); box-shadow: var(--shadow-md); position: relative; overflow: hidden;
        }
        .card-metric:hover { transform: translateY(-4px); border-color: var(--black-text); box-shadow: 0 12px 24px -10px rgba(15,23,42,0.08); }
        .card-metric::before { content: ''; position: absolute; top: 0; left: 0; width: 4px; height: 100%; background: transparent; transition: var(--transition-smooth); }
        .card-metric:hover::before { background: var(--red-primary); }
        
        .num-val { font-size: 30px; font-weight: 800; line-height: 1; letter-spacing: -0.04em; color: var(--black-text); }
        .lbl-val { font-size: 11px; font-weight: 700; color: var(--gray-muted); text-transform: uppercase; margin-top: 6px; letter-spacing: 0.03em; }
        .icon-box { width: 44px; height: 44px; border-radius: 12px; background: var(--bg-body); display: flex; align-items: center; justify-content: center; font-size: 15px; color: var(--black-text); border: 1px solid var(--border-sharp); transition: var(--transition-smooth); }
        .card-metric:hover .icon-box { background: var(--black-text); color: white; border-color: var(--black-text); }

        /* Charts Configurations Panels View */
        .visualization-row { display: grid; grid-template-columns: 1fr 1.3fr 1fr; gap: 20px; flex-shrink: 0; }
        .panel-block { background: var(--bg-pure-white); border: 1px solid var(--border-darker); border-radius: var(--radius-box); padding: 22px; display: flex; flex-direction: column; box-shadow: var(--shadow-md); position: relative; }
        .panel-heading { font-size: 11.5px; font-weight: 800; text-transform: uppercase; color: var(--black-text); margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid var(--border-sharp); letter-spacing: 0.05em; display: flex; align-items: center; justify-content: space-between; }
        
        .graphics-container { flex: 1; display: flex; align-items: center; justify-content: center; position: relative; min-height: 150px; }
        .donut-turn { transform: rotate(-90deg); transform-origin: center; transition: stroke-dashoffset 0.8s ease-in-out; }

        /* Dynamic Graphics Chart Pillars Base Vector Setup */
        .bar-chart-wrapper { display: flex; align-items: flex-end; justify-content: space-between; width: 100%; height: 140px; padding: 0 4px; }
        .bar-pillar-group { display: flex; flex-direction: column; align-items: center; flex: 1; }
        .bar-pillar { width: 14px; background: var(--black-text); border-radius: 4px 4px 0 0; position: relative; transition: var(--transition-bounce); cursor: pointer; }
        .bar-pillar:hover { background: var(--red-primary); transform: scaleY(1.05); }
        
        .bar-pillar::after {
            content: attr(data-count); position: absolute; top: -28px; left: 50%; transform: translateX(-50%) scale(0.8);
            font-size: 10px; font-weight: 700; background: var(--black-text); color: white; padding: 3px 6px; border-radius: 6px; opacity: 0; transition: var(--transition-bounce); pointer-events: none;
        }
        .bar-pillar:hover::after { opacity: 1; transform: translateX(-50%) scale(1); top: -32px; }
        .axis-labels { display: flex; justify-content: space-between; margin-top: 12px; font-size: 10.5px; color: var(--gray-muted); font-weight: 700; width: 100%; padding: 0 2px; border-top: 1px dashed var(--border-sharp); padding-top: 6px; }

        /* Split Frame Architecture Grid Bottom Block Rows */
        .split-bottom-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 20px; flex-shrink: 0; margin-bottom: 20px; }
        .panel-block.scrollable-list { display: flex; flex-direction: column; min-height: 380px; }
        .table-wrapper { flex: 1; margin-top: 4px; padding-right: 2px; }

        /* Tables Presentations Design Base Styles */
        .table-core { width: 100%; border-collapse: collapse; text-align: left; }
        .table-core th { font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--gray-muted); padding: 10px 12px; border-bottom: 1px solid var(--border-darker); letter-spacing: 0.03em; background: white; }
        .table-core td { padding: 14px 12px; font-size: 13px; font-weight: 600; border-bottom: 1px solid var(--border-sharp); vertical-align: middle; }
        .table-core tr:last-child td { border-bottom: none; }
        .table-core tr:hover td { background: rgba(248,250,252,0.9); }

        /* Sparkline Vector Graphical Representation Nodes Elements */
        .sparkline-flex { display: flex; align-items: flex-end; gap: 3px; height: 16px; width: 55px; }
        .sparkline-bar { flex: 1; background: var(--border-darker); height: 40%; border-radius: 2px; transition: var(--transition-smooth); }
        .table-core tr:hover .sparkline-bar { background: #cbd5e1; }
        .table-core tr:hover .sparkline-bar.top-peak { background: var(--red-primary); }

        .badge-status { font-size: 10px; font-weight: 700; text-transform: uppercase; padding: 3px 8px; border-radius: 6px; display: inline-block; letter-spacing: 0.02em; }
        .badge-status.active { background: #dcfce7; color: #15803d; }
        .badge-status.review { background: #fef9c3; color: #a16207; }

        /* Functional Circle Links Configurations Action Triggers */
        .action-circle-group { display: flex; gap: 6px; justify-content: flex-end; }
        .circle-btn {
            border: 1px solid var(--border-darker); background: var(--bg-pure-white); color: var(--black-text);
            width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center;
            font-size: 11.5px; text-decoration: none; border-radius: 8px; transition: var(--transition-bounce);
        }
        .circle-btn:hover { background: var(--black-text); color: var(--bg-pure-white); border-color: var(--black-text); transform: scale(1.08); }
        .circle-btn.danger-action:hover { background: var(--red-primary); color: white; border-color: var(--red-primary); }

        /* Operational System Feeds Registry Elements Map View */
        .timeline-stream { display: flex; flex-direction: column; gap: 14px; flex: 1; padding-right: 4px; }
        .timeline-item { display: flex; gap: 14px; position: relative; padding-bottom: 2px; }
        .timeline-item::before { content: ''; position: absolute; left: 15px; top: 32px; bottom: -18px; width: 2px; background: var(--border-sharp); }
        .timeline-item:last-child::before { display: none; }
        
        .timeline-icon-frame {
            width: 32px; height: 32px; border-radius: 50%; background: var(--bg-body); border: 1px solid var(--border-darker);
            display: flex; align-items: center; justify-content: center; font-size: 12px; color: var(--black-text); flex-shrink: 0; z-index: 2;
        }
        .timeline-item:hover .timeline-icon-frame { background: var(--red-light); color: var(--red-primary); border-color: var(--red-primary); }
        .timeline-body { flex: 1; background: var(--bg-body); padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border-sharp); transition: var(--transition-smooth); }
        .timeline-body:hover { background: white; border-color: var(--border-darker); box-shadow: var(--shadow-sm); }
        .timeline-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
        .timeline-item-title { font-size: 12.5px; font-weight: 700; color: var(--black-text); }
        .timeline-time { font-size: 10.5px; color: var(--gray-muted); font-weight: 600; }
        .timeline-desc { font-size: 12px; color: var(--gray-muted); line-height: 1.4; font-weight: 500; }

        /* Popups Layout Interface Modules Panels Elements Layout */
        .modal-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(4px); z-index: 1000; display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
        }
        .modal-overlay.open { opacity: 1; pointer-events: auto; }
        .modal-box {
            background: white; border-radius: var(--radius-box); width: 460px; max-width: 90%;
            padding: 28px; border: 1px solid var(--border-darker); box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
            transform: scale(0.9) translateY(10px); transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .modal-overlay.open .modal-box { transform: scale(1) translateY(0); }
        
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .modal-header h3 { font-size: 18px; font-weight: 800; letter-spacing: -0.02em; }
        .modal-close { border: none; background: transparent; font-size: 16px; cursor: pointer; color: var(--gray-muted); width: 28px; height: 28px; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%; transition: var(--transition-smooth); }
        .modal-close:hover { background: var(--border-sharp); color: var(--black-text); }
        
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 11.5px; font-weight: 700; text-transform: uppercase; color: var(--gray-muted); margin-bottom: 6px; letter-spacing: 0.02em; }
        .form-group input, .form-group textarea {
            width: 100%; padding: 10px 14px; border-radius: 10px; border: 1px solid var(--border-darker);
            font-family: inherit; font-size: 13px; font-weight: 600; outline: none; background: var(--bg-body); transition: var(--transition-smooth);
        }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--black-text); background: white; }
        
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--border-sharp); }
        .btn-secondary { background: var(--bg-body); color: var(--black-text); border: 1px solid var(--border-darker); padding: 10px 16px; border-radius: 10px; font-size: 12.5px; font-weight: 700; cursor: pointer; transition: var(--transition-smooth); }
        .btn-secondary:hover { background: var(--border-sharp); }

        /* Toast Layout Structures Panel CSS Styles */
        .toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 1100; display: flex; flex-direction: column; gap: 10px; }
        .toast-card {
            background: var(--black-text); color: white; padding: 14px 20px; border-radius: 12px;
            font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 12px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); transform: translateY(40px); opacity: 0;
            animation: toastIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
        }
        @keyframes toastIn { to { transform: translateY(0); opacity: 1; } }
        .toast-card i { color: var(--red-primary); font-size: 14px; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo"><i class="fas fa-layer-group"></i></div>
            <div class="brand-text">Alumni<span>X</span></div>
        </div>
        
        <div class="profile-card">
            <div class="profile-avatar">VK</div>
            <div>
                <div class="profile-name">Vedant Khandale</div>
                <div class="profile-role">Workspace Administrator</div>
            </div>
        </div>

        <nav class="nav-container">
            <div class="menu-dropdown-wrapper open">
                <button class="nav-link active" onclick="toggleMenuDropdown(this)">
                    <div><i class="fas fa-chart-line"></i><span>Analytics Console</span></div>
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="sub-menu">
                    <a href="admin_dashboard.php" class="sub-link active">Operational Overview</a>
                    <a href="page_dashboard.php" class="sub-link">System Framework Nodes</a>
                </div>
            </div>

            <a href="alumni_list.php" class="nav-link">
                <div><i class="fas fa-users"></i><span>Alumni Directory</span></div>
                <i class="fas fa-arrow-right" style="font-size: 11px; opacity: 0.4;"></i>
            </a>

            <div class="menu-dropdown-wrapper">
                <button class="nav-link" onclick="toggleMenuDropdown(this)">
                    <div><i class="fas fa-briefcase"></i><span>Placement Pipeline</span></div>
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="sub-menu">
                    <a href="jobs.php" class="sub-link">Job Controller</a>
                    <a href="page_jobs.php" class="sub-link">Post New Openings</a>
                    <a href="view_applications.php" class="sub-link">Review Submissions</a>
                </div>
            </div>

            <div class="menu-dropdown-wrapper">
                <button class="nav-link" onclick="toggleMenuDropdown(this)">
                    <div><i class="fas fa-calendar-alt"></i><span>Event Management</span></div>
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="sub-menu">
                    <a href="event.php" class="sub-link">Active Programs</a>
                    <a href="page_event.php" class="sub-link">Schedule Engagement</a>
                </div>
            </div>

            <a href="helpers.php" class="nav-link" style="opacity: 0.6;">
                <div><i class="fas fa-tools"></i><span>System Utilities</span></div>
            </a>

            <a href="admin_login.php" class="nav-link" style="margin-top: auto; border-top: 1px solid var(--border-sharp); padding-top: 16px;">
                <div><i class="fas fa-shield-alt"></i><span>Secure Authentication</span></div>
            </a>
            <a href="logout.php" class="nav-link" style="color: var(--red-primary);">
                <div><i class="fas fa-power-off"></i><span>Terminate Session</span></div>
            </a>
        </nav>
    </aside>

    <main class="workspace">
        <header class="top-bar">
            <div class="search-wrapper">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search parameters, names, records...">
            </div>
            
            <div class="top-actions-cluster">
                <button class="btn-action-trigger" onclick="openBroadcastModal()">
                    <i class="fas fa-bullhorn"></i><span>Broadcast Alert</span>
                </button>
            </div>
        </header>

        <div class="main-scroller">
            <div class="view-title-flex">
                <h2>Control Center Workspace</h2>
                <div class="live-status-pill">
                    <span class="status-pulse-dot"></span><span>Static Operational Engine</span>
                </div>
            </div>

            <section class="metrics-grid">
                <div class="card-metric">
                    <div>
                        <div class="num-val"><?php echo number_format($stats["total_verified"]); ?></div>
                        <div class="lbl-val">Verified Alumni</div>
                    </div>
                    <div class="icon-box"><i class="fas fa-user-graduate"></i></div>
                </div>
                <div class="card-metric">
                    <div>
                        <div class="num-val"><?php echo $stats["active_postings"]; ?></div>
                        <div class="lbl-val">Active Pipelines</div>
                    </div>
                    <div class="icon-box"><i class="fas fa-briefcase"></i></div>
                </div>
                <div class="card-metric">
                    <div>
                        <div class="num-val"><?php echo $stats["upcoming_events"]; ?></div>
                        <div class="lbl-val">Scheduled Programs</div>
                    </div>
                    <div class="icon-box"><i class="fas fa-calendar-day"></i></div>
                </div>
                <div class="card-metric" style="border-color: rgba(244, 63, 94, 0.2); background: var(--red-light);">
                    <div>
                        <div class="num-val" style="color: var(--red-primary);"><?php echo $stats["total_applications"]; ?></div>
                        <div class="lbl-val" style="color: var(--red-primary);">Total Submissions</div>
                    </div>
                    <div class="icon-box" style="background: white; border-color: transparent; color: var(--red-primary);"><i class="fas fa-file-invoice"></i></div>
                </div>
            </section>

            <section class="visualization-row">
                <article class="panel-block">
                    <h3 class="panel-heading">Sector Distribution <i class="fas fa-chart-pie" style="color: var(--gray-muted);"></i></h3>
                    <div class="graphics-container">
                        <svg width="130" height="130" viewBox="0 0 140 140">
                            <circle cx="70" cy="70" r="50" fill="transparent" stroke="var(--border-sharp)" stroke-width="11"/>
                            <circle class="donut-turn" cx="70" cy="70" r="50" fill="transparent" stroke="var(--black-text)" stroke-width="11" stroke-dasharray="<?php echo $circumference; ?>" stroke-dashoffset="<?php echo $employed_offset; ?>" stroke-linecap="round"/>
                            <circle class="donut-turn" cx="70" cy="70" r="50" fill="transparent" stroke="var(--red-primary)" stroke-width="11" stroke-dasharray="<?php echo $circumference; ?>" stroke-dashoffset="<?php echo $entrepreneur_offset; ?>" stroke-linecap="round"/>
                        </svg>
                        <div style="position: absolute; text-align: center;">
                            <div style="font-size: 22px; font-weight: 800; letter-spacing: -0.04em; color: var(--black-text);"><?php echo $distribution["employed"]; ?>%</div>
                            <div style="font-size: 9.5px; font-weight: 700; color: var(--gray-muted); text-transform: uppercase; letter-spacing: 0.01em;">Corporate</div>
                        </div>
                    </div>
                </article>

                <article class="panel-block">
                    <h3 class="panel-heading">Registration Velocity <i class="fas fa-chart-bar" style="color: var(--gray-muted);"></i></h3>
                    <div class="bar-chart-wrapper">
                        <?php foreach($monthly_signups as $month => $val): ?>
                            <div class="bar-pillar-group">
                                <div class="bar-pillar" data-count="<?php echo $val; ?>" style="height: <?php echo ($val / 140) * 100; ?>%;"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="axis-labels">
                        <span>Jan</span><span>Mar</span><span>Jun</span><span>Sep</span><span>Dec</span>
                    </div>
                </article>

                <article class="panel-block">
                    <h3 class="panel-heading">Volatility Index <i class="fas fa-wave-square" style="color: var(--gray-muted);"></i></h3>
                    <div class="graphics-container">
                        <svg class="vector-wave-svg" width="100%" height="110" viewBox="0 0 200 100" preserveAspectRatio="none">
                            <path d="M0,75 Q35,15 75,65 T155,25 T200,5" fill="none" stroke="var(--red-primary)" stroke-width="2.5" stroke-linecap="round"/>
                            <path d="M0,85 Q35,35 75,75 T155,45 T200,25" fill="none" stroke="var(--black-text)" stroke-width="1.2" stroke-dasharray="4,4"/>
                        </svg>
                    </div>
                </article>
            </section>

            <section class="split-bottom-grid">
                <article class="panel-block scrollable-list">
                    <h3 class="panel-heading">Placement Infrastructure Pipelines Management</h3>
                    <div class="table-wrapper">
                        <table class="table-core">
                            <thead>
                                <tr>
                                    <th>Ref ID</th>
                                    <th>Role Framework Pipeline</th>
                                    <th>Status</th>
                                    <th>Activity Dynamics</th>
                                    <th style="text-align: right;">Action Control</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_job_streams as $job): ?>
                                    <tr>
                                        <td style="color: var(--gray-muted); font-family: monospace; font-size: 11px;">#ALX-0<?php echo $job["id"]; ?></td>
                                        <td>
                                            <div style="font-weight: 700; color: var(--black-text); letter-spacing: -0.01em;"><?php echo htmlspecialchars($job["title"]); ?></div>
                                            <div style="font-size: 11.5px; color: var(--gray-muted); font-weight: 600; margin-top: 1px;"><?php echo htmlspecialchars($job["company"]); ?> &middot; <span style="font-weight:500; font-size:11px;"><?php echo htmlspecialchars($job["location"]); ?></span></div>
                                        </td>
                                        <td>
                                            <span class="badge-status <?php echo ($job["status"] == 'Active') ? 'active' : 'review'; ?>">
                                                <?php echo $job["status"]; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="sparkline-flex">
                                                <?php foreach ($job["trend"] as $index => $t_val): ?>
                                                    <div class="sparkline-bar <?php echo ($index == 3) ? 'top-peak' : ''; ?>" style="height: <?php echo $t_val; ?>%;"></div>
                                                <?php endforeach; ?>
                                            </div>
                                        </td>
                                        <td style="text-align: right;">
                                            <div class="action-circle-group">
                                                <a href="page_jobs.php?id=<?php echo $job["id"]; ?>" class="circle-btn" title="Modify Configuration Mapping Node"><i class="fas fa-sliders-h"></i></a>
                                                <a href="view_applications.php?job_id=<?php echo $job["id"]; ?>" class="circle-btn danger-action" title="Review Submission Pipeline Logs"><i class="fas fa-arrow-right"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </article>

                <article class="panel-block scrollable-list">
                    <h3 class="panel-heading">Operational Log Registry Event Feeds</h3>
                    <div class="timeline-stream">
                        <?php foreach($recent_activities as $act): ?>
                            <div class="timeline-item">
                                <div class="timeline-icon-frame">
                                    <?php if($act["type"] == 'verification'): ?><i class="fas fa-user-check"></i>
                                    <?php elseif($act["type"] == 'job'): ?><i class="fas fa-briefcase"></i>
                                    <?php else: ?><i class="fas fa-calendar-alt"></i><?php endif; ?>
                                </div>
                                <div class="timeline-body">
                                    <div class="timeline-meta">
                                        <h4 class="timeline-item-title"><?php echo $act["title"]; ?></h4>
                                        <span class="timeline-time"><?php echo $act["time"]; ?></span>
                                    </div>
                                    <p class="timeline-desc"><?php echo $act["desc"]; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>
            </section>
        </div>
    </main>

    <div class="modal-overlay" id="broadcastModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Broadcast Live Notification</h3>
                <button class="modal-close" onclick="closeBroadcastModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Broadcast Title Target Header</label>
                    <input type="text" id="alertTitle" placeholder="e.g., Placement Drive Urgency Alert Baseline">
                </div>
                <div class="form-group">
                    <label>Message Payload Contents</label>
                    <textarea id="alertMessage" rows="4" placeholder="Enter notification message payload structure to be dispatched to the active nodes channels..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeBroadcastModal()">Cancel</button>
                <button class="btn-action-trigger" style="padding: 10px 20px;" onclick="triggerBroadcastDispatch()">Dispatch Signal Trigger</button>
            </div>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>

    <script>
        // Dropdown Menu Fluid Shifting Collapse/Expand Handler Controller Node
        function toggleMenuDropdown(buttonElement) {
            const wrapper = buttonElement.parentElement;
            const isOpen = wrapper.classList.contains('open');
            
            document.querySelectorAll('.menu-dropdown-wrapper').forEach(item => {
                if(item !== wrapper) item.classList.remove('open');
            });

            if (isOpen) {
                wrapper.classList.remove('open');
            } else {
                wrapper.classList.add('open');
            }
        }

        // Live Action Popups Controllers Management Engine
        function openBroadcastModal() {
            document.getElementById('broadcastModal').classList.add('open');
        }

        function closeBroadcastModal() {
            document.getElementById('broadcastModal').classList.remove('open');
            document.getElementById('alertTitle').value = '';
            document.getElementById('alertMessage').value = '';
        }

        function triggerBroadcastDispatch() {
            const title = document.getElementById('alertTitle').value.trim();
            if(!title) {
                showNotificationAlert('Validation Failure: Missing dispatch title header packet parameters.');
                return;
            }
            closeBroadcastModal();
            showNotificationAlert('Success Flag: Broadcast payload packet signaled and pushed to active matrix channels.');
        }

        function showNotificationAlert(message) {
            const container = document.getElementById('toastContainer');
            const card = document.createElement('div');
            card.className = 'toast-card';
            card.innerHTML = `<i class="fas fa-satellite-dish"></i> <span>${message}</span>`;
            container.appendChild(card);
            
            setTimeout(() => {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'translateY(-20px)';
                setTimeout(() => card.remove(), 300);
            }, 3500);
        }
    </script>
</body>
</html>