<?php
include("includes/header.php");
include("includes/db.php");
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
    :root {
        --primary: #ff3b3b;
        --primary-soft: rgba(255, 59, 59, 0.12);
        --container-padding: 120px;
        --section-gap: 40px;
        --max-content-width: 1200px;
        --bg: #f8fbff;
        --surface: #ffffff;
        --surface-soft: #f4f7ff;
        --text-rich: #0f172a;
        --text-gray: #64748b;
        --border-light: rgba(148, 163, 184, 0.2);
        --shadow-soft: 0 18px 60px rgba(15, 23, 42, 0.08);
        --shadow-strong: 0 30px 90px rgba(15, 23, 42, 0.14);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    html, body {
        background: var(--bg);
        color: var(--text-rich);
        overflow-x: hidden;
        -webkit-font-smoothing: antialiased;
        scroll-behavior: smooth;
    }

    a { color: inherit; text-decoration: none; }
    img { max-width: 100%; display: block; }

    .hero-section {
        width: 100vw;
        min-height: 100vh;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        background: radial-gradient(circle at top, rgba(255, 59, 59, 0.08), transparent 28%),
                    linear-gradient(180deg, #090b13 0%, #05060d 100%);
    }

    .hero-section::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(circle at top center, rgba(255, 77, 77, 0.08), transparent 16%),
                    radial-gradient(circle at 22% 72%, rgba(255, 255, 255, 0.05), transparent 10%);
        opacity: 0.95;
    }

    .hero-content {
        z-index: 10;
        text-align: center;
        /* keep hero content constrained and aligned with site */
        padding: 0 6%;
        width: 100%;
        max-width: 980px;
        box-sizing: border-box;
        margin: calc(var(--nav-height, 86px) + 18px) auto 0;
    }

    .hero-badge {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #fff;
        padding: 16px 42px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 4px;
        text-transform: uppercase;
        margin-bottom: 32px;
        display: inline-block;
        box-shadow: 0 18px 50px rgba(2,6,23,0.45);
        transform-origin: center;
        animation: floatBadge 6s ease-in-out infinite;
    }

    @keyframes floatBadge {
        0%,100%{ transform: translateY(0);} 
        50%{ transform: translateY(-6px);} 
    }

    .hero-content h1 {
        font-family: 'Inter', sans-serif;
        font-size: clamp(48px, 11vw, 110px);
        font-weight: 900;
        letter-spacing: -4px;
        line-height: 1.05;
        color: #fff;
        margin: 0 auto 28px;
        text-transform: uppercase;
        max-width: 1000px;
        text-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
    }

    .hero-content h1 span {
        background: linear-gradient(135deg, #ff3b3b, #ff7266);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-content p {
        font-size: clamp(14px, 1.6vw, 18px);
        color: rgba(255, 255, 255, 0.85);
        max-width: 700px;
        margin: 0 auto 16px;
        font-weight: 400;
        line-height: 1.6;
        text-shadow: 0 8px 24px rgba(0, 0, 0, 0.28);
    }

    .btn-premium {
        background: linear-gradient(270deg, #ff6a6a, #ff3b3b, #ff8a66);
        background-size: 200% 200%;
        color: #fff;
        padding: 12px 40px;
        border-radius: 16px;
        font-weight: 900;
        letter-spacing: 1px;
        display: inline-block;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        text-transform: uppercase;
        font-size: 13px;
        box-shadow: 0 28px 90px rgba(255, 77, 77, 0.26);
        animation: gradientShift 6s ease infinite;
        border: 1px solid rgba(255,255,255,0.06);
        position: relative; z-index: 12;
    }

    .btn-premium:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 34px 90px rgba(255, 77, 77, 0.3);
    }

    @keyframes gradientShift{
        0%{ background-position: 0% 50%; }
        50%{ background-position: 100% 50%; }
        100%{ background-position: 0% 50%; }
    }

    .container-fluid {
        box-sizing: border-box;
        width: 100vw;
        max-width: 100vw;
        padding: clamp(32px, 5vw, 90px) 6%;
        margin-left: calc(50% - 50vw);
        margin-right: calc(50% - 50vw);
        position: relative;
        transition: padding 0.28s ease;
    }

    /* tighter gap when container immediately follows hero */
    .hero-section + .container-fluid { padding-top: 54px; }

    .section-label {
        margin-bottom: calc(var(--section-gap) / 1.2);
        position: relative;
        z-index: 2;
        text-align: center;
        padding: 0 6%;
    }

    .section-label::after {
        content: '';
        position: absolute;
        bottom: -14px;
        left: 50%;
        transform: translateX(-50%);
        width: 110px;
        height: 4px;
        background: linear-gradient(90deg, #ff3b3b, rgba(255, 59, 59, 0.2));
        border-radius: 10px;
    }

    .label-line {
        width: 90px;
        height: 5px;
        background: linear-gradient(90deg, #ff3b3b, rgba(255, 59, 59, 0.25));
        margin: 0 auto 18px;
        border-radius: 999px;
    }

    .section-title {
        font-family: 'Inter', sans-serif;
        font-size: clamp(32px, 4.4vw, 64px);
        font-weight: 900;
        letter-spacing: -2px;
        line-height: 1.02;
        color: var(--text-rich);
        text-transform: uppercase;
        margin: 0 auto;
        max-width: 1000px;
    }

    .section-title span { color: var(--primary); }

    .row {
        display: flex;
        flex-wrap: wrap;
        gap: 40px;
        justify-content: space-between;
        margin: 0;
        align-items: stretch;
    }

    .col-lg-8, .col-lg-7, .col-lg-5, .col-lg-4 {
        box-sizing: border-box;
        padding: 0;
    }

    .col-lg-8 { flex: 0 0 62%; max-width: 62%; }
    .col-lg-7 { flex: 0 0 58%; max-width: 58%; }
    .col-lg-5 { flex: 0 0 38%; max-width: 38%; }
    .col-lg-4 { flex: 0 0 35%; max-width: 35%; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(180px, 1fr));
        gap: 22px;
        margin-bottom: 28px;
        align-items: stretch;
    }

    .stat-card {
        background: linear-gradient(180deg, rgba(255,255,255,0.98), var(--surface));
        border-radius: 20px;
        padding: 22px;
        border: 1px solid rgba(148, 163, 184, 0.12);
        box-shadow: 0 18px 48px rgba(15,23,42,0.06);
        display: flex;
        flex-direction: column;
        gap: 10px;
        transition: transform 0.32s cubic-bezier(.22,1,.36,1), box-shadow 0.32s ease;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 90px rgba(15,23,42,0.12);
    }

    .stat-card span { color: var(--primary); font-size: 32px; font-weight: 900; display:block; }
    .stat-card p { color: var(--text-gray); font-size: 13px; line-height: 1.6; margin: 0; }

    .stat-card::after { content: ''; position: absolute; right: 18px; top: 18px; font-size: 28px; opacity: 0.06; }

    .horizontal-scroll-container {
        display: flex;
        gap: clamp(20px, 2.8vw, 32px);
        overflow-x: auto;
        padding: 24px clamp(24px, 4vw, 72px) 52px;
        width: 100%;
        max-width: 100%;
        justify-content: flex-start;
        scrollbar-width: none;
        -ms-overflow-style: none;
        scroll-snap-type: x mandatory;
        scroll-padding-inline: clamp(24px, 12vw, 120px);
        scroll-behavior: smooth;
        cursor: grab;
        touch-action: pan-x;
    }

    .horizontal-scroll-container:active { cursor: grabbing; }

    .horizontal-scroll-container::-webkit-scrollbar { display: none; }

    .premium-alumni-card {
        --card-scale: 1;
        flex: 0 0 auto;
        width: clamp(260px, 18vw, 340px);
        max-width: 100%;
        background: linear-gradient(180deg, rgba(255,255,255,0.98), #fff);
        border: 1px solid rgba(148, 163, 184, 0.12);
        border-radius: 28px;
        padding: 30px 24px 26px;
        text-align: center;
        transition: transform 0.36s cubic-bezier(.22,1,.36,1), box-shadow 0.36s ease, opacity 0.32s ease, filter 0.32s ease;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.06);
        position: relative;
        overflow: hidden;
        scroll-snap-align: center;
        transform: scale(var(--card-scale));
    }

    .premium-alumni-card:hover {
        transform: scale(calc(var(--card-scale, 1) + 0.04));
        transition: transform 0.2s ease, box-shadow 0.36s ease;
        box-shadow: 0 42px 110px rgba(15, 23, 42, 0.14);
        border-color: rgba(255, 77, 77, 0.3);
    }

    .premium-alumni-card::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 150px;
        height: 150px;
        background: radial-gradient(circle, rgba(255, 59, 59, 0.15), transparent 55%);
        border-radius: 50%;
        z-index: 0;
    }

    .premium-alumni-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 42px 110px rgba(15, 23, 42, 0.14);
        border-color: rgba(255, 77, 77, 0.3);
    }

    .alumni-avatar {
        width: 120px;
        height: 120px;
        border-radius: 30px;
        object-fit: cover;
        margin-bottom: 24px;
        border: 4px solid rgba(255, 77, 77, 0.14);
        transition: all 0.45s ease;
        position: relative;
        z-index: 1;
    }

    .premium-alumni-card:hover .alumni-avatar {
        border-radius: 50%;
        transform: rotate(4deg) scale(1.06);
    }

    .alumni-name { font-weight: 800; font-size: 22px; margin-bottom: 8px; color: var(--text-rich); letter-spacing: -0.4px; z-index: 1; position: relative; }
    .alumni-company { display: inline-flex; align-items: center; justify-content: center; gap: 8px; background: rgba(255, 240, 240, 0.95); color: var(--primary); border-radius: 999px; padding: 8px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.4px; margin-bottom: 18px; z-index: 1; position: relative; }
    .alumni-copy { color: var(--text-gray); font-size: 14px; line-height: 1.75; font-weight: 500; margin: 0; z-index: 1; position: relative; }

    .bento-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px; margin-bottom: 0; }

    .sexy-event-card {
        position: relative;
        border-radius: 28px;
        overflow: hidden;
        min-height: 300px;
        box-shadow: 0 22px 70px rgba(15, 23, 42, 0.08);
        transition: transform 0.36s ease, box-shadow 0.36s ease;
        background-size: cover;
        background-position: center center;
        display: flex;
        align-items: flex-end;
    }

    .sexy-event-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, 0.05), rgba(15, 23, 42, 0.78));
        z-index: 1;
        pointer-events: none;
    }

    .sexy-event-card:hover { transform: translateY(-12px); box-shadow: 0 42px 120px rgba(15, 23, 42, 0.2); }
    .event-meta { position: absolute; top: 20px; left: 20px; z-index: 2; background: rgba(255, 255, 255, 0.12); border: 1px solid rgba(255, 255, 255, 0.18); backdrop-filter: blur(14px); border-radius: 24px; padding: 16px 18px; display: inline-flex; align-items: center; gap: 10px; }
    .event-day { font-size: 28px; font-weight: 900; color: #fff; line-height: 1; }
    .event-month { font-size: 12px; color: #ffb3b3; letter-spacing: 2px; text-transform: uppercase; font-weight: 700; }
    .event-copy { position: relative; z-index: 2; padding: 24px 26px 30px; color: #fff; }
    .event-copy h3 { font-size: clamp(24px, 2vw, 38px); margin: 0 0 14px; line-height: 1.05; }
    .event-copy p { color: rgba(255, 255, 255, 0.9); margin: 0; max-width: 84%; }
    .event-copy .location { display: inline-flex; align-items: center; gap: 10px; margin-top: 18px; color: rgba(255, 255, 255, 0.85); font-weight: 500; font-size: 14px; }

    .timeline-container, .glass-card { background: var(--surface); border-radius: 30px; border: 1px solid rgba(148, 163, 184, 0.15); box-shadow: 0 28px 80px rgba(15, 23, 42, 0.07); padding: 30px; }
    .sexy-post { background: #fff; border: 1px solid rgba(148, 163, 184, 0.22); border-radius: 28px; padding: 24px; margin-bottom: 22px; transition: all 0.35s ease; position: relative; overflow: hidden; }
    .sexy-post::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 77, 77, 0.06), transparent); transition: left 0.6s ease; }
    .sexy-post:hover::before { left: 100%; }
    .sexy-post:hover { transform: translateX(10px); border-color: #ff4d4d; box-shadow: 0 20px 55px rgba(255, 77, 77, 0.12); }
    .post-top { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
    .post-avatar { width: 56px; height: 56px; border-radius: 18px; object-fit: cover; border: 2px solid rgba(248, 248, 255, 0.95); box-shadow: 0 15px 40px rgba(15, 23, 42, 0.08); }
    .post-meta h4 { margin: 0; font-size: 17px; font-weight: 800; color: var(--text-rich); }
    .post-meta small { color: var(--text-gray); font-size: 12px; }
    .post-copy { color: #334155; font-size: 15px; line-height: 1.75; margin: 0; }

    .dashboard-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 28px; }
    .glass-card { display: flex; flex-direction: column; gap: 18px; }
    .card-header { display: flex; align-items: center; justify-content: space-between; gap: 20px; }
    .card-header h3 { margin: 0; font-size: 24px; letter-spacing: -0.8px; line-height: 1.1; }
    .pill { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 999px; background: var(--primary-soft); color: var(--primary); font-weight: 700; font-size: 13px; }
    .notification-card, .comment-card { background: #f8fbff; border-radius: 24px; border: 1px solid rgba(148, 163, 184, 0.18); padding: 20px 22px; display: grid; gap: 12px; transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .notification-card:hover, .comment-card:hover { transform: translateY(-4px); box-shadow: 0 18px 55px rgba(15, 23, 42, 0.08); }
    .notification-card h4, .comment-card h4 { margin: 0; font-size: 15px; line-height: 1.4; }
    .notification-meta, .comment-meta { display: flex; align-items: center; justify-content: space-between; gap: 12px; color: var(--text-gray); font-size: 13px; }
    .comment-meta .tag, .notification-meta .tag { background: rgba(255, 59, 59, 0.1); color: var(--primary); border-radius: 999px; padding: 6px 12px; font-weight: 700; font-size: 12px; }
    .comment-copy { margin: 0; color: #344054; line-height: 1.7; font-size: 14px; }

    .job-list { display: grid; gap: 22px; }
    .job-strip { background: #fff; border: 1px solid rgba(148, 163, 184, 0.12); padding: 24px 28px; border-radius: 20px; display: flex; align-items: center; justify-content: space-between; gap: 20px; transition: transform 0.28s ease, box-shadow 0.28s ease; box-shadow: 0 18px 48px rgba(15, 23, 42, 0.05); position: relative; overflow: hidden; }
    .job-strip::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: linear-gradient(180deg, #ff3b3b, transparent); opacity: 0; transition: opacity 0.3s ease; }
    .job-strip:hover { border-color: rgba(255, 77, 77, 0.35); transform: translateY(-4px); box-shadow: 0 28px 85px rgba(255, 77, 77, 0.12); }
    .job-strip:hover::before { opacity: 1; }
    .job-meta { display: flex; align-items: center; gap: 22px; flex-wrap: wrap; }
    .job-icon-box { min-width: 72px; min-height: 72px; background: linear-gradient(135deg, #fff5f5, #fff0f0); color: #ff3b3b; border-radius: 22px; display: grid; place-items: center; font-size: 28px; transition: all 0.3s ease; border: 1px solid rgba(255, 77, 77, 0.18); }
    .job-strip:hover .job-icon-box { background: linear-gradient(135deg, #ff3b3b, #ff7a64); color: #fff; transform: scale(1.08) rotate(5deg); box-shadow: 0 14px 28px rgba(255, 77, 77, 0.18); }
    .job-copy h3 { font-size: 22px; margin: 0 0 6px; font-weight: 800; color: var(--text-rich); }
    .job-copy p { margin: 0; color: var(--text-gray); font-size: 14px; line-height: 1.7; }
    .job-actions { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
    .job-tag { color: var(--text-gray); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; }
    .job-btn { background: linear-gradient(135deg, #0f172a, #1a2341); color: #fff; padding: 14px 32px; border-radius: 16px; font-weight: 800; text-decoration: none; font-size: 13px; transition: all 0.3s; text-transform: uppercase; letter-spacing: 1px; border: 1px solid rgba(255, 77, 77, 0.18); }
    .job-btn:hover { background: linear-gradient(135deg, #ff3b3b, #ff7a64); transform: translateY(-2px); box-shadow: 0 14px 35px rgba(255, 77, 77, 0.22); }

    .reveal { opacity: 0; transform: translateY(24px); }

    /* utility to prevent overflow and keep text tidy */
    .hero-content, .container-fluid { overflow-wrap: break-word; word-break: break-word; hyphens: auto; }

    @media (max-width: 1200px) {
        .col-lg-8, .col-lg-7, .col-lg-5, .col-lg-4 { max-width: 100%; flex: 1 1 100%; }
        .stats-grid { grid-template-columns: repeat(2, minmax(160px, 1fr)); }
        .dashboard-grid { grid-template-columns: 1fr; }
        .bento-grid { grid-template-columns: 1fr; }
        .container-fluid { padding: 80px 5%; }
        .horizontal-scroll-container { padding: 24px 5% 52px; gap: clamp(18px, 3vw, 28px); justify-content: flex-start; scroll-padding-inline: 5vw; }
        .premium-alumni-card { width: clamp(260px, 40vw, 320px); }
    }

    @media (max-width: 880px) {
        .container-fluid { padding: 64px 5%; }
        .horizontal-scroll-container { padding: 20px 5% 48px; gap: 18px; }
        .premium-alumni-card { width: min(85vw, 320px); }
        .section-title { font-size: clamp(28px, 6vw, 48px); }
        .hero-section { min-height: 72vh; }
        .row { gap: 22px; }
        .job-strip { flex-direction: column; align-items: stretch; padding: 20px; }
        .job-actions { justify-content: space-between; width: 100%; }
    }

    @media (max-width: 640px) {
        .container-fluid { padding: 48px 4%; }
        .stats-grid { grid-template-columns: 1fr; }
        .premium-alumni-card { flex: 0 0 100%; }
        .section-label::after { width: 80px; }
        .section-title { font-size: clamp(26px, 8vw, 40px); }
        .event-meta { flex-wrap: wrap; }
        .event-copy { padding: 18px 16px 20px; }
        .job-strip { padding: 18px; }
        .job-actions { gap: 12px; }
    }
</style>

<section class="hero-section">
    <div class="hero-video-wrap">
        <video autoplay muted loop playsinline>
            <source src="images/hero.mp4" type="video/mp4">
        </video>
    </div>

    <style>
        /* Hero enhancements */
        .hero-video-wrap { position: absolute; inset: 0; z-index: 1; overflow: hidden; }
        .hero-video-wrap video { position: absolute; left: 50%; top: 50%; transform: translate(-50%,-50%) scale(1.3); will-change: transform; min-width: 140%; min-height: 140%; width: auto; height: auto; object-fit: cover; filter: brightness(0.96) saturate(1.15) contrast(1.1); transition: transform 1.2s ease; }
        .hero-section:hover .hero-video-wrap video { transform: translate(-50%,-50%) scale(1.36); }
        .hero-overlay { position: absolute; inset: 0; z-index: 2; background: linear-gradient(180deg, rgba(2,6,23,0.2), rgba(2,6,23,0.12)); pointer-events: none; }
        .hero-inner { display:flex; align-items:center; justify-content:center; gap: 20px; max-width:1100px; margin:0 auto; padding: 40px 20px 86px; z-index:10; position:relative; flex-direction: column; }
        .hero-left, .hero-right { width: 100%; max-width: 880px; color:#fff; }
        .hero-right{ display:flex; align-items:center; justify-content:center; position:relative; }
        .hero-left h1{ font-size: clamp(40px, 7.5vw, 80px); line-height:1.02; margin: 6px 0 12px; letter-spacing:-1px; }
        .hero-left p{ font-size: 18px; color: rgba(255,255,255,0.9); max-width:680px; margin-bottom:22px; }
        .cta-group{ display:flex; gap:16px; align-items:center; justify-content:center; margin-top:6px; }
        .btn-ghost{ background:transparent; color:#fff; padding:14px 28px; border-radius:12px; border:1px solid rgba(255,255,255,0.12); font-weight:700; }
        /* avatars removed for a minimal hero */
        .meta-row{ display:flex; align-items:center; gap:18px; margin-top:20px; justify-content:center; }
        .mini-stats{ color: rgba(255,255,255,0.95); font-weight:800; background: rgba(255,255,255,0.06); padding:8px 12px; border-radius:12px; border:1px solid rgba(255,255,255,0.06); }
        .floating-alert{ background:linear-gradient(180deg,#fff,#fff); color:#0b1220; padding:14px 18px; border-radius:18px; width:260px; box-shadow: 0 18px 50px rgba(2,6,23,0.28); border: 1px solid rgba(2,6,23,0.05); }
        .floating-alert h4{ margin:0 0 6px; font-size:16px; }
        .floating-alert small{ color:#556; font-size:13px; }
        .floating-bubble{ position:absolute; right:-24px; bottom:-28px; background:linear-gradient(135deg,#ff6a6a,#ff3b3b); color:#fff; padding:12px 18px; border-radius:28px; box-shadow: 0 18px 50px rgba(255,59,59,0.18); transform: rotate(-8deg); animation: floatBadge 5s ease-in-out infinite; }
        @media (max-width:880px){ .hero-inner{ flex-direction:column; align-items:center; } .hero-right{ width:100%; justify-content:center; } .hero-left{ text-align:center; } .floating-alert{ position: static; transform: none; } }
    </style>

    <div class="hero-overlay" aria-hidden="true"></div>
    <div class="hero-content">
        <div class="hero-inner" style="flex-direction:column; align-items:center; text-align:center;">
            <div class="hero-badge reveal">AluminiX Excellence</div>
            <h1 class="reveal" id="mainTitle">ALUMNI <span>X</span></h1>
            <p class="reveal" style="max-width:780px;">A curated network for founders, builders and leaders — premium connections, verified roles, and invite-only experiences. Join the pulse.</p>

            <div class="cta-group reveal">
                <a href="registration.php" class="btn-premium">Claim Your Access</a>
            </div>

            <!-- avatar stack removed for a cleaner hero -->
        </div>
    </div>

    <script>
        // subtle hero interactions (centered hero, simple parallax)
        try {
            const hero = document.querySelector('.hero-section');
            const videoWrap = document.querySelector('.hero-video-wrap');
            let rafId;
            // adjust hero vertical space to account for fixed nav height
            function adjustHeroForNav(){
                const nav = document.getElementById('mainNav');
                if(!hero) return;
                const navH = nav ? nav.offsetHeight : 0;
                // expose nav height as a CSS variable and set hero min-height so video fills the viewport
                document.documentElement.style.setProperty('--nav-height', navH + 'px');
                hero.style.minHeight = `calc(100vh - ${navH}px)`;
            }
            window.addEventListener('resize', adjustHeroForNav);
            window.addEventListener('load', adjustHeroForNav);
            adjustHeroForNav();
            hero && hero.addEventListener('mousemove', (e)=>{
                cancelAnimationFrame(rafId);
                rafId = requestAnimationFrame(()=>{
                    const x = (e.clientX / window.innerWidth - 0.5) * 6; // reduced intensity
                    const y = (e.clientY / window.innerHeight - 0.5) * 6;
                    if(videoWrap) videoWrap.style.transform = `translate(${x}px, ${y}px) scale(1.015)`;
                });
            });
            hero && hero.addEventListener('mouseleave', ()=>{ if(videoWrap) videoWrap.style.transform='translate(0,0) scale(1)'; });
        } catch(err){ console.warn(err); }
    </script>
</section>

<?php
$stats = $conn->query("SELECT
    (SELECT COUNT(*) FROM alumni) AS alumni_count,
    (SELECT COUNT(*) FROM jobs WHERE status='approved') AS jobs_count,
    (SELECT COUNT(*) FROM events) AS events_count
")->fetch_assoc();
$alumniCount = $stats['alumni_count'] ?? 0;
$jobsCount = $stats['jobs_count'] ?? 0;
$eventsCount = $stats['events_count'] ?? 0;
?>

<section class="container-fluid section-glow">
    <div class="section-label reveal">
        <div class="label-line"></div>
        <h2 class="section-title">Hall Of <span>Fame</span></h2>
    </div>

    <div class="stats-grid reveal">
        <div class="stat-card">
            <span><?= number_format($alumniCount) ?></span>
            <p>Curated alumni stories, leaders, and alumni mentors shaping the next generation.</p>
        </div>
        <div class="stat-card">
            <span><?= number_format($jobsCount) ?></span>
            <p>Approved premium roles shared across the network. Every listing is verified and ready to hire.</p>
        </div>
        <div class="stat-card">
            <span><?= number_format($eventsCount) ?></span>
            <p>High-energy summits, networking nights, and launch events connecting campus with industry.</p>
        </div>
        <div class="stat-card">
            <span>98%</span>
            <p>Success rate for alumni placed through the portal and mentorship connections created in 2025.</p>
        </div>
    </div>

    <div class="horizontal-scroll-container reveal" id="alumniTrack">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 8");
        while ($row = $res->fetch_assoc()) {
            $imageId = ($row['id'] % 70) + 10;
        ?>
            <div class="premium-alumni-card moving-card">
                <img src="https://i.pravatar.cc/220?img=<?= $imageId ?>" class="alumni-avatar" alt="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>">
                <h3 class="alumni-name"><?= htmlspecialchars($row['name'], ENT_QUOTES) ?></h3>
                <div class="alumni-company"><i class="fas fa-star"></i> <?= htmlspecialchars($row['company'], ENT_QUOTES) ?></div>
                <p class="alumni-copy">Driving impact with deep expertise in product, growth, and global strategy.</p>
                <div style="position: absolute; top: 18px; right: 20px; opacity: 0.08; font-size: 42px; pointer-events: none;"><i class="fas fa-award"></i></div>
            </div>
        <?php } ?>
    </div>
</section>

<section class="container-fluid">
    <div class="row">
        <div class="col-lg-7">
            <div class="section-label reveal">
                <div class="label-line"></div>
                <h2 class="section-title">Global <span>Summits</span></h2>
            </div>
            <div class="bento-grid">
                <?php
                $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 3");
                $count = 0;
                while ($row = $res->fetch_assoc()) {
                    $count++;
                    $heroImage = $count === 1
                        ? 'https://images.unsplash.com/photo-1515169067865-5387ec356754?auto=format&fit=crop&w=1200&q=80'
                        : 'https://images.unsplash.com/photo-1496307042754-b4aa456c4a2d?auto=format&fit=crop&w=1200&q=80';
                ?>
                    <div class="reveal sexy-event-card" style="background-image: url('<?= $heroImage ?>'); min-height: <?= ($count == 1) ? '440px' : '320px' ?>;">
                        <div class="event-meta">
                            <span class="event-day"><?= date('d', strtotime($row['event_date'])) ?></span>
                            <span class="event-month"><?= date('M', strtotime($row['event_date'])) ?></span>
                        </div>
                        <div class="event-copy">
                            <h3><?= htmlspecialchars($row['title'], ENT_QUOTES) ?></h3>
                            <p>Immersive, invite-only alumni meetups built for founders, leaders and hiring champions.</p>
                            <div class="location"><i class="fas fa-location-dot"></i> <?= htmlspecialchars($row['location'], ENT_QUOTES) ?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="section-label reveal">
                <div class="label-line" style="width: 60px; height: 6px; background: linear-gradient(90deg, var(--primary), transparent); border-radius: 10px; margin-bottom: 20px;"></div>
                <h2 class="section-title" style="font-size: clamp(35px, 4vw, 55px); line-height: 0.95;">Community <span>Pulse</span></h2>
            </div>
            <div class="timeline-container reveal">
                <div class="sexy-post">
                    <div class="post-top">
                        <img src="https://i.pravatar.cc/100?img=12" class="post-avatar" alt="Alumni">
                        <div class="post-meta">
                            <h4>Rahul Sharma</h4>
                            <small>2 hours ago</small>
                        </div>
                    </div>
                    <p class="post-copy">Placed at <strong>Microsoft</strong> through a direct network referral. Alumni support made this launch seamless.</p>
                </div>
                <div class="sexy-post">
                    <div class="post-top">
                        <img src="https://i.pravatar.cc/100?img=5" class="post-avatar" alt="Alumni">
                        <div class="post-meta">
                            <h4>Priya Verma</h4>
                            <small>Yesterday</small>
                        </div>
                    </div>
                    <p class="post-copy">Joined the AI Ethics roundtable and met a mentor who helped shape my product roadmap.</p>
                </div>
                <div class="sexy-post">
                    <div class="post-top">
                        <img src="https://i.pravatar.cc/100?img=23" class="post-avatar" alt="Alumni">
                        <div class="post-meta">
                            <h4>Aman Deshmukh</h4>
                            <small>3 days ago</small>
                        </div>
                    </div>
                    <p class="post-copy">Shared an urgent senior designer opening with 125 alumni. Interview shortlist closed in 24 hours.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container-fluid section-glow">
    <div class="section-label reveal">
        <div class="label-line"></div>
        <h2 class="section-title">Insider <span>Alerts</span></h2>
    </div>

    <div class="dashboard-grid reveal">
        <div class="glass-card">    
            <div class="card-header">
                <h3>Notifications</h3>
                <span class="pill"><i class="fas fa-bell"></i> 5 new</span>
            </div>
            <div class="notification-card">
                <h4>New alumnus onboarded with a product leadership role at Google.</h4>
                <div class="notification-meta"><span>Alumni Update</span><span class="tag">Live</span></div>
            </div>
            <div class="notification-card">
                <h4>Upcoming Summit ticket allocation opens tomorrow at 10 AM.</h4>
                <div class="notification-meta"><span>Event Alert</span><span class="tag">RSVP</span></div>
            </div>
            <div class="notification-card">
                <h4>Tech hiring wave: 12 new roles added from partner companies.</h4>
                <div class="notification-meta"><span>Hiring News</span><span class="tag">Hot</span></div>
            </div>
        </div>

        <div class="glass-card">
            <div class="card-header">
                <h3>Campus Chatter</h3>
                <span class="pill"><i class="fas fa-comments"></i> 3 active</span>
            </div>
            <div class="comment-card">
                <h4>“Loved the mentor roundtable today. The networking energy was unreal.”</h4>
                <div class="comment-meta"><span>Shweta K.</span><span class="tag">Alumni</span></div>
            </div>
            <div class="comment-card">
                <h4>“Shared a bootcamp opening and already received 7 applications from within the community.”</h4>
                <div class="comment-meta"><span>Neeraj P.</span><span class="tag">Hiring</span></div>
            </div>
            <div class="comment-card">
                <h4>“Excited to host the next growth workshop with 30+ members joining live.”</h4>
                <div class="comment-meta"><span>Riya S.</span><span class="tag">Community</span></div>
            </div>
        </div>
    </div>
</section>

<section class="container-fluid">
    <div class="section-label reveal">
        <div class="label-line"></div>
        <h2 class="section-title">Career <span>Board</span></h2>
    </div>

    <div class="job-list">
        <?php
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 5");
        while ($row = $res->fetch_assoc()) {
            $location = trim($row['location']) ?: 'Remote';
        ?>
            <div class="job-strip reveal">
                <div class="job-meta">
                    <div class="job-icon-box"><i class="fas fa-briefcase"></i></div>
                    <div class="job-copy">
                        <h3><?= htmlspecialchars($row['title'], ENT_QUOTES) ?></h3>
                        <p><?= htmlspecialchars($row['company'], ENT_QUOTES) ?> • <span style="color: var(--primary);"><?= htmlspecialchars($location, ENT_QUOTES) ?></span></p>
                    </div>
                </div>
                <div class="job-actions">
                    <span class="job-tag">Full-Time</span>
                    <a href="jobs.php" class="job-btn">Apply Now</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<script>
    gsap.registerPlugin(ScrollTrigger);
    
    // Reveal Animations
    document.querySelectorAll('.reveal').forEach(el => {
        gsap.to(el, {
            scrollTrigger: { trigger: el, start: "top 90%" },
            opacity: 1, y: 0, duration: 1, ease: "expo.out"
        });
    });

    // Final Touch: Smooth Auto-Pilot Scroll for Hall of Fame
    const track = document.getElementById('alumniTrack');
    const cards = Array.from(track.querySelectorAll('.premium-alumni-card'));
    let scrollSpeed = 0.45;
    let isHovering = false;
    let scrollDirection = 1;
    let isDown = false;
    let startX;
    let scrollLeft;

    function updateActiveCard() {
        const center = track.scrollLeft + track.clientWidth / 2;
        cards.forEach(card => {
            const cardCenter = card.offsetLeft + card.offsetWidth / 2;
            const distance = Math.abs(center - cardCenter);
            const normalized = Math.min(distance / (track.clientWidth * 0.6), 1);
            const scale = 1.02 - normalized * 0.12;
            card.style.setProperty('--card-scale', scale.toFixed(3));
            card.style.opacity = normalized < 0.85 ? '1' : '0.75';
            card.style.filter = normalized < 0.5 ? 'drop-shadow(0 40px 90px rgba(15, 23, 42, 0.16))' : 'none';
        });
    }

    function autoScroll() {
        if (!isHovering && !isDown) {
            track.scrollLeft += scrollSpeed * scrollDirection;
            if (track.scrollLeft <= 0 || track.scrollLeft >= track.scrollWidth - track.clientWidth) {
                scrollDirection *= -1;
            }
        }
        updateActiveCard();
        requestAnimationFrame(autoScroll);
    }
    
    track.addEventListener('mouseenter', () => isHovering = true);
    track.addEventListener('mouseleave', () => isHovering = false);

    track.addEventListener('scroll', updateActiveCard);
    window.addEventListener('resize', updateActiveCard);

    // Kickstart auto-scroll
    updateActiveCard();
    autoScroll();

    // Horizontal Drag logic
    track.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
    });
    track.addEventListener('mouseleave', () => { isDown = false; });
    track.addEventListener('mouseup', () => { isDown = false; });
    track.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - track.offsetLeft;
        const walk = (x - startX) * 2;
        track.scrollLeft = scrollLeft - walk;
        updateActiveCard();
    });
</script>


<?php include("includes/footer.php"); ?>