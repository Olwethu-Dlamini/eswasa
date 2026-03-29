<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Announcements - ESWASA</title>
    <meta name="description" content="Stay informed with the latest announcements from the Eswatini Standards Authority (ESWASA).">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <!-- Place favicon.ico in the root directory -->

    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/select2.min.css">
    <link rel="stylesheet" href="assets/css/odometer.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/aos.css">
    <link rel="stylesheet" href="assets/css/spacing.css">
    <link rel="stylesheet" href="assets/css/tg-cursor.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <style>
        .btn-announcement {
            background-color: #2E3191;
            color: white;
            border-color: #2E3191;
            margin: 5px;
        }
        .btn-announcement:hover {
            background-color: #1a1f71;
            border-color: #1a1f71;
            color: white;
        }
        .highlighted-section {
            border-left: 5px solid #2E3191;
            background-color: #eef5ff;
            padding: 20px;
            margin: 25px 0;
            border-radius: 0 5px 5px 0;
        }
        .highlighted-section h3 {
            color: #2E3191;
            margin-top: 0;
        }
        .announcement-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .announcement-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .announcement-title {
            color: #2E3191;
            font-weight: bold;
        }
        .announcement-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        .announcement-description {
            margin-bottom: 15px;
        }
        .announcement-link {
            display: inline-block;
            margin-top: 10px;
        }
        .announcement-link a {
            color: #2E3191;
            text-decoration: none;
        }
        .announcement-link a:hover {
            text-decoration: underline;
        }
        .announcement-type {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        .type-news { background-color: #e6f0fa; color: #0072A5; }
        .type-update { background-color: #e0f5e0; color: #1BBC9B; }
        .type-closure { background-color: #fff3cd; color: #856404; }
        .type-event { background-color: #f8d7da; color: #721c24; }
        .type-policy { background-color: #d1ecf1; color: #0c5460; }

        :root {
            --primary: #2E3191;
            --news: #0072A5;
            --update: #1BBC9B;
            --closure: #F59E0B;
            --event: #EC4899;
            --policy: #8B5CF6;
        }
        body { background: #fafbfc; }
        .hero { background: var(--primary); padding: 80px 0 60px; color: white; }
        .hero h1 { font-size: 3rem; font-weight: 700; margin-bottom: 15px; }
        .hero p { font-size: 1.1rem; opacity: 0.9; max-width: 700px; margin: 0 auto; }
        
        .filter-bar { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); margin: -40px auto 50px; max-width: 95%; position: relative; z-index: 2; }
        .filter-btn { padding: 10px 24px; border: 2px solid #e5e7eb; background: white; color: #64748b; border-radius: 50px; font-weight: 600; margin: 5px; transition: all 0.3s; cursor: pointer; }
        .filter-btn:hover { border-color: var(--primary); color: var(--primary); transform: translateY(-2px); }
        .filter-btn.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 6px 20px rgba(46,49,145,0.3); }
        
        .announcements-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(360px, 1fr)); gap: 25px; margin: 40px 0; }
        .card { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); transition: all 0.4s ease; border: 2px solid transparent; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 20px 50px rgba(0,0,0,0.12); border-color: var(--primary); }
        
        .badge { display: inline-flex; align-items: center; gap: 8px; padding: 8px 18px; border-radius: 50px; font-size: 0.8rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: white; }
        .badge-news { background: var(--news); box-shadow: 0 4px 15px rgba(0,114,165,0.3); }
        .badge-update { background: var(--update); box-shadow: 0 4px 15px rgba(27,188,155,0.3); }
        .badge-closure { background: var(--closure); box-shadow: 0 4px 15px rgba(245,158,11,0.3); }
        .badge-event { background: var(--event); box-shadow: 0 4px 15px rgba(236,72,153,0.3); }
        .badge-policy { background: var(--policy); box-shadow: 0 4px 15px rgba(139,92,246,0.3); }
        
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        .card-date { color: #94a3b8; font-size: 0.9rem; font-weight: 600; }
        .card-title { color: #1e293b; font-size: 1.3rem; font-weight: 700; margin: 15px 0; line-height: 1.4; }
        .card:hover .card-title { color: var(--primary); }
        .card-desc { color: #64748b; line-height: 1.7; margin-bottom: 20px; }
        
        .card-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 20px; border-top: 2px solid #f1f5f9; }
        .btn-action { display: inline-flex; align-items: center; gap: 10px; padding: 12px 28px; background: var(--primary); color: white; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.3s; }
        .btn-action:hover { background: #1a1f71; transform: translateX(5px); box-shadow: 0 6px 20px rgba(46,49,145,0.3); color: white; }
        .btn-action i { transition: transform 0.3s; }
        .btn-action:hover i { transform: translateX(5px); }
        
        .file-tag { padding: 6px 14px; background: #f8fafc; border-radius: 50px; color: #64748b; font-size: 0.85rem; font-weight: 600; border: 2px solid #e2e8f0; }
        
        .info-box { 
            background: white; 
            border-radius: 16px; 
            padding: 30px; 
            margin-bottom: 40px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            /* Removed border-left: 4px solid var(--primary); */
        }
        .info-box h3 { 
            color: var(--primary); 
            font-size: 1.4rem; 
            font-weight: 700; 
            margin-bottom: 12px; 
        }
        .info-box p {  
            color: #475569;
            line-height: 1.7; 
            margin: 0; 
        }
        
        .links-box { background: white; border-radius: 16px; padding: 35px; margin-top: 50px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .links-box h3 { color: var(--primary); font-size: 1.4rem; font-weight: 700; margin-bottom: 20px; }
        .links-box ul { list-style: none; padding: 0; margin: 0; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; }
        .links-box a { display: flex; align-items: center; gap: 10px; padding: 14px 20px; background: #f8fafc; border-radius: 12px; color: #475569; text-decoration: none; font-weight: 600; transition: all 0.3s; border: 2px solid transparent; }
        .links-box a:hover { background: var(--primary); color: white; transform: translateX(5px); border-color: var(--primary); }
        
        .empty { text-align: center; padding: 80px 20px; }
        .empty i { font-size: 4rem; color: #cbd5e1; margin-bottom: 20px; }
        .empty h3 { color: #64748b; font-size: 1.6rem; font-weight: 700; }
        
        @keyframes fadeUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .card { animation: fadeUp 0.5s ease backwards; }
        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
        .card:nth-child(5) { animation-delay: 0.5s; }
        .card:nth-child(6) { animation-delay: 0.6s; }
        
        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .announcements-grid { grid-template-columns: 1fr; }
            .card-footer { flex-direction: column; align-items: flex-start; gap: 15px; }
        }
        
        .breadcrumb-area {
            background-position: center;
            background-size: cover;
            padding: 100px 0;
        }
        
        .main-content {
            padding: 80px 0;
        }
    </style>
</head>

<body>

    <!-- Scroll-top -->
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <!-- Scroll-top-end-->

    <!-- header-area -->
    <?php include("includes/header.php")?>
    <!-- header-area-end -->

    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'eswasa');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // Function to get icon based on announcement type
    function getIcon($type) {
        $icons = [
            'news' => 'fa-newspaper',
            'update' => 'fa-bullhorn',
            'closure' => 'fa-door-closed',
            'event' => 'fa-calendar-alt',
            'policy' => 'fa-file-contract'
        ];
        return isset($icons[$type]) ? $icons[$type] : 'fa-bullhorn';
    }

    // Get filter from URL if exists
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
    
    // Build query based on filter
    if ($filter === 'all') {
        $query = "SELECT * FROM eswasa_announcements ORDER BY published_date DESC";
    } else {
        $query = "SELECT * FROM eswasa_announcements WHERE announcement_type = '$filter' ORDER BY published_date DESC";
    }
    
    $announcements = $conn->query($query);
    ?>

    <!-- main-area -->
    <main class="main-area fix">

        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Announcements</span>
                            </nav>
                            <h3 class="title">Announcements</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <section class="main-content">
            <div class="container">
                <!-- Filter Bar -->
                <div class="filter-bar">
                    <div class="text-center mb-3">
                        <h4 class="mb-3" style="color: #2E3191;">Filter by Type</h4>
                    </div>
                    <div class="text-center">
                        <button class="filter-btn <?php echo ($filter === 'all') ? 'active' : ''; ?>" data-filter="all">
                            <i class="fas fa-th-large me-2"></i> All Announcements
                        </button>
                        <button class="filter-btn <?php echo ($filter === 'news') ? 'active' : ''; ?>" data-filter="news">
                            <i class="fas fa-newspaper me-2"></i> News
                        </button>
                        <button class="filter-btn <?php echo ($filter === 'update') ? 'active' : ''; ?>" data-filter="update">
                            <i class="fas fa-bullhorn me-2"></i> Updates
                        </button>
                        <button class="filter-btn <?php echo ($filter === 'event') ? 'active' : ''; ?>" data-filter="event">
                            <i class="fas fa-calendar-alt me-2"></i> Events
                        </button>
                        <button class="filter-btn <?php echo ($filter === 'policy') ? 'active' : ''; ?>" data-filter="policy">
                            <i class="fas fa-file-contract me-2"></i> Policies
                        </button>
                        <button class="filter-btn <?php echo ($filter === 'closure') ? 'active' : ''; ?>" data-filter="closure">
                            <i class="fas fa-door-closed me-2"></i> Closures
                        </button>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <h3><i class="fas fa-info-circle me-2"></i>About Announcements</h3>
                    <p>This page provides the latest updates, news, and important announcements from ESWASA. Stay informed about policy changes, public consultations, events, office closures, and other relevant news.</p>
                </div>
                
                <!-- Announcements Grid -->
                <?php if ($announcements && $announcements->num_rows > 0): ?>
                <div class="announcements-grid">
                    <?php while ($a = $announcements->fetch_assoc()): 
                        $type = $a['announcement_type'];
                        $label = ucfirst($type);
                        $icon = getIcon($type);
                    ?>
                    <div class="card" data-type="<?= $type ?>">
                        <div class="card-header">
                            <span class="badge badge-<?= $type ?>">
                                <i class="fas <?= $icon ?>"></i>
                                <?= $label ?>
                            </span>
                            <span class="card-date">
                                <i class="far fa-calendar"></i> <?= date('M d, Y', strtotime($a['published_date'])) ?>
                            </span>
                        </div>
                        <h4 class="card-title"><?= htmlspecialchars($a['title']) ?></h4>
                        <p class="card-desc"><?= nl2br(htmlspecialchars(substr($a['description'], 0, 200))) ?><?= strlen($a['description']) > 200 ? '...' : '' ?></p>
                        <div class="card-footer">
                            <?php if (!empty($a['file_path'])): ?>
                                <a href="admin/uploads/<?= htmlspecialchars($a['file_path']) ?>" target="_blank" class="btn-action">
                                    <span>View Document</span><i class="fas fa-arrow-right"></i>
                                </a>
                                <span class="file-tag"><i class="fas fa-file-pdf"></i> <?= strtoupper(pathinfo($a['file_path'], PATHINFO_EXTENSION)) ?></span>
                            <?php elseif (!empty($a['external_link'])): ?>
                                <a href="<?= htmlspecialchars($a['external_link']) ?>" target="_blank" class="btn-action">
                                    <span>Learn More</span><i class="fas fa-external-link-alt"></i>
                                </a>
                                <span class="file-tag"><i class="fas fa-link"></i> External</span>
                            <?php else: ?>
                                <a href="announcement-details.php?id=<?= (int)$a['id'] ?>" class="btn-action">
                                    <span>Read More</span><i class="fas fa-arrow-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="empty">
                    <i class="fas fa-inbox"></i>
                    <h3>No Announcements Yet</h3>
                    <p>Check back soon for updates</p>
                </div>
                <?php endif; ?>
                
                <!-- Related Links -->
                <div class="links-box">
                    <h3><i class="fas fa-link me-2"></i>Related Links</h3>
                    <ul>
                        <li><a href="events.php"><i class="fas fa-calendar-alt"></i> Events Calendar</a></li>
                        <li><a href="publications.php"><i class="fas fa-book"></i> Publications</a></li>
                        <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact Us</a></li>
                         <li><a href="faq.php"><i class="fas fa-newspaper"></i> FAQ's</a></li>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <!-- footer-area -->
    <?php include("includes/footer.php")?>
    <!-- footer-area-end -->

    <!-- JS here -->
    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/isotope.pkgd.min.js"></script>
    <script src="assets/js/imagesloaded.pkgd.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/jquery.odometer.min.js"></script>
    <script src="assets/js/jquery.appear.js"></script>
    <script src="assets/js/tween-max.min.js"></script>
    <script src="assets/js/select2.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/slick-animation.min.js"></script>
    <script src="assets/js/tg-cursor.min.js"></script>
    <script src="assets/js/form-contact.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/aos.js"></script>
    <script src="assets/js/main.js"></script>

    <script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add click event to filter buttons
        const filterButtons = document.querySelectorAll('.filter-btn');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Update URL with filter parameter
                if(filter === 'all') {
                    window.location.href = 'announcements.php';
                } else {
                    window.location.href = 'announcements.php?filter=' + filter;
                }
            });
        });
        
        // Add animation to cards on scroll
        const cards = document.querySelectorAll('.card');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        cards.forEach(card => {
            observer.observe(card);
        });
    });
    </script>

</body>
</html>