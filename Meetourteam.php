<?php
include_once 'includes/db_connect.php';
include_once 'includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';

// Page-level text via page_content
$pc = pc_get_many($conn, [
    'team_hero_title',
    'team_intro_body',
    'team_section_main_title',
    'team_section_council_title',
    'team_section_executive_title',
    'team_staff_group_photo',
], [
    'team_hero_title'              => 'Meet Our Team',
    'team_intro_body'              => 'Meet the leadership team dedicated to helping you achieve compliance, ensure quality, and promote the sustainability of Eswatini’s industries.',
    'team_section_main_title'      => 'Our Council and Management',
    'team_section_council_title'   => 'Members of the Council',
    'team_section_executive_title' => 'Executive Team',
    'team_staff_group_photo'       => 'admin/uploads/staff_group_photo.jpg',
]);

// Team members from dedicated table (live rows only — is_vacant = 0).
// The page has two sections: Council (governance) and Executive Team.
// The existing 'management' section value is treated as Executive Team.
$council    = [];
$management = []; // = Executive Team in the UI
$staff      = [];
try {
    if ($res = @$conn->query("SELECT * FROM eswasa_team_members WHERE is_vacant = 0 ORDER BY sort_order ASC, name ASC")) {
        while ($r = $res->fetch_assoc()) {
            $sec = (string)($r['section'] ?? 'management');
            if      ($sec === 'council') $council[]    = $r;
            elseif  ($sec === 'staff')   $staff[]      = $r;
            else                          $management[] = $r;
        }
    }
} catch (\Throwable $e) {
    // table/schema missing on this environment — fall back to empty lists
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>ESWASA - Meet Our Team</title>
    <meta name="description" content="">
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
        /* ========== ESWASA Theme Base (locked spec: #2B3388, #fff, Arial 15px) ========== */
        body {
            font-family: Arial, sans-serif;
            font-size: 15px;
            color: #2B3388;
        }
        body h1, body h2, body h3, body h4, body h5, body h6 {
            font-family: Arial, sans-serif;
            color: #2B3388;
        }
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea, body table, body th, body td {
            font-family: Arial, sans-serif;
        }
        .text-muted {
            color: #2B3388 !important;
        }

        /* Breadcrumb stays white over the dark breadcrumb-bg image */
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title {
            color: #fff !important;
        }
        .breadcrumb-separator i { color: #fff !important; }

        /* Page intro */
        .team-header {
            text-align: center;
            margin-bottom: 56px;
        }
        .team-header h2 {
            color: #2B3388;
            font-weight: 700;
            font-size: 32px;
            margin: 0;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }
        .team-header .section-divider {
            margin: 14px auto 22px;
        }
        .team-header p {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.65;
            max-width: 760px;
            margin: 0 auto;
        }

        /* Sub-section title — sits BELOW the .team-header h2 parent, so
           uses h3 markup at canonical 24px to keep parent > child hierarchy. */
        .section-title {
            text-align: center;
            margin: 60px 0 0;
            font-weight: 700;
            color: #2B3388;
            font-size: 24px;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }
        .section-divider {
            width: 60px;
            height: 2px;
            background: #2B3388;
            margin: 16px auto 36px;
            border-radius: 0;
        }

        /* Pyramid layout — featured leader on top, members in a centered grid below */
        .team-section { margin-bottom: 60px; }
        /* Breathing room between the section-divider underline and the
           first team-card / staff content below. */
        .team-section .section-divider { margin-bottom: 36px; }
        .team-layout {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 32px;
        }
        .team-leader { flex: 0 0 auto; }
        .team-members {
            width: 100%;
            max-width: 1000px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 22px;
            margin: 0 auto;
            justify-items: center;
        }

        /* Member cards — uniform size */
        .team-card {
            background-color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 22px 18px;
            border: 1px solid rgba(43, 51, 136, 0.12);
            border-radius: 4px;
            height: 100%;
            max-width: 220px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            transition: border-color .2s ease, box-shadow .2s ease, transform .2s ease;
        }
        .team-card:hover {
            border-color: rgba(43, 51, 136, 0.40);
            box-shadow: 0 6px 16px rgba(43, 51, 136, 0.10);
            transform: translateY(-3px);
        }

        /* Featured leader card — slightly larger, top accent bar, stronger shadow */
        .team-leader .team-card {
            max-width: 260px;
            padding: 28px 22px 24px;
            border-color: rgba(43, 51, 136, 0.22);
            box-shadow: 0 4px 14px rgba(43, 51, 136, 0.10);
        }
        .team-leader .team-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: #2B3388;
            border-radius: 4px 4px 0 0;
        }
        .team-leader .team-card:hover {
            box-shadow: 0 10px 24px rgba(43, 51, 136, 0.18);
        }

        /* Circular portraits */
        .team-img-container {
            position: relative;
            overflow: hidden;
            border-radius: 50%;
            border: 2px solid rgba(43, 51, 136, 0.15);
            margin-bottom: 18px;
            width: 140px;
            padding-top: 140px;
        }
        .team-leader .team-img-container {
            width: 180px;
            padding-top: 180px;
            border-width: 3px;
            margin-bottom: 22px;
        }
        .team-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .team-name {
            font-weight: 700;
            font-size: 15px;
            color: #2B3388;
            margin: 0 0 4px;
            line-height: 1.3;
        }
        .team-role {
            font-weight: 600;
            color: #2B3388;
            font-size: 13px;
            margin: 0 0 8px;
            line-height: 1.3;
        }
        .team-leader .team-name {
            font-size: 18px;
            margin-bottom: 6px;
        }
        .team-leader .team-role {
            font-size: 13px;
            color: #2B3388;
            font-weight: 700;
        }
        .team-bio {
            color: #2B3388;
            font-size: 14px;
            margin-bottom: 1rem;
            line-height: 1.55;
        }
        .team-social {
            margin-top: auto;
        }
        .social-icon {
            display: inline-block;
            width: 32px;
            height: 32px;
            line-height: 32px;
            text-align: center;
            background-color: rgba(43, 51, 136, 0.08);
            color: #2B3388;
            border-radius: 50%;
            margin: 0 4px;
            transition: background-color .2s ease, color .2s ease;
        }
        .social-icon:hover {
            background-color: #2B3388;
            color: #fff;
        }

        /* ESWASA staff intro paragraph */
        .eswasa-staff-content p {
            color: #2B3388;
            font-size: 15px;
            line-height: 1.65;
            max-width: 820px;
            margin: 0 auto;
        }

        /* Staff group photo */
        .staff-photo-container {
            width: 100%;
            max-width: 900px;
            height: 0;
            padding-bottom: 40%;
            background: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            position: relative;
            overflow: hidden;
            margin: 0 auto;
        }
        .staff-photo {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
        .staff-photo[src]:not([src=""]) {
            display: block !important;
        }
        .staff-photo-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: transparent;
            color: rgba(43, 51, 136, 0.55);
            display: none; /* hidden by default — only shows when no image src */
            align-items: center;
            justify-content: center;
            font-style: italic;
            font-size: 14px;
            text-align: center;
            padding: 0 20px;
        }
        .staff-photo[src=""] + .staff-photo-placeholder,
        .staff-photo:not([src]) + .staff-photo-placeholder {
            display: flex;
        }

        /* ========== Mobile responsive ========== */
        @media (max-width: 991.98px) {
            .team-layout { gap: 28px; }
            .team-img-container { width: 120px; padding-top: 120px; }
            .team-leader .team-img-container { width: 160px; padding-top: 160px; }
            .team-header h2 { font-size: 28px; }
            .section-title { font-size: 22px; }
        }
        @media (max-width: 767.98px) {
            .team-layout { gap: 28px; }
            .team-members {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 16px;
            }
            .team-img-container { width: 110px; padding-top: 110px; }
            .team-leader .team-img-container { width: 150px; padding-top: 150px; }
            .team-leader .team-card { max-width: 240px; padding: 24px 18px 22px; }
            .team-leader .team-name { font-size: 17px; }
            .team-header h2 { font-size: 24px; }
            .section-title { font-size: 20px; margin: 40px 0 0; }
            .staff-photo-container { padding-bottom: 50%; }
            .team-header p { font-size: 15px; }
        }
        @media (max-width: 575.98px) {
            .team-members { grid-template-columns: repeat(2, 1fr); }
            .team-card { padding: 18px 14px; }
            .team-name { font-size: 13px; }
            .team-role { font-size: 12px; }
            .team-leader .team-card { max-width: 220px; }
            .team-leader .team-img-container { width: 130px; padding-top: 130px; }
            .team-leader .team-name { font-size: 16px; }
            .team-leader .team-role { font-size: 12px; }
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
    <!-- main-area -->
    <main class="main-area fix">
        <!-- breadcrumb-area -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('meetourteam', 'assets/img/bg/bg.png') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.php">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem"><?= pc_h($pc['team_hero_title']) ?></span>
                            </nav>
                            <h1 class="title"><?= pc_h($pc['team_hero_title']) ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->
        <div class="container py-5">
            <!-- Page intro -->
            <div class="team-header">
                <h2><?= pc_h($pc['team_section_main_title']) ?></h2>
                <div class="section-divider"></div>
                <p><?= pc_h($pc['team_intro_body']) ?></p>
            </div>

            <!-- Council Section -->
            <?php if (!empty($council)):
                $council_chair   = $council[0];
                $council_members = array_slice($council, 1);
            ?>
            <div class="team-section">
                <h3 class="section-title"><?= pc_h($pc['team_section_council_title']) ?></h3>
                <div class="section-divider"></div>
                <div class="team-layout">
                    <div class="team-leader">
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="<?= pc_h(pc_image_src($council_chair['photo'], 'assets/img/instructor/instructor01.png')) ?>" alt="<?= pc_h($council_chair['name']) ?>" class="team-img">
                            </div>
                            <h4 class="team-name"><?= pc_h($council_chair['name']) ?></h4>
                            <p class="team-role"><?= pc_h($council_chair['role']) ?></p>
                            <div class="team-social">
                                <?php if (!empty($council_chair['social_linkedin'])): ?>
                                    <a href="<?= pc_h($council_chair['social_linkedin']) ?>" class="social-icon" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($council_members)): ?>
                    <div class="team-members">
                        <?php foreach ($council_members as $c): ?>
                            <div class="team-card">
                                <div class="team-img-container">
                                    <img src="<?= pc_h(pc_image_src($c['photo'], 'assets/img/instructor/instructor01.png')) ?>" alt="<?= pc_h($c['name']) ?>" class="team-img">
                                </div>
                                <h4 class="team-name"><?= pc_h($c['name']) ?></h4>
                                <p class="team-role"><?= pc_h($c['role']) ?></p>
                                <div class="team-social">
                                    <?php if (!empty($c['social_linkedin'])): ?>
                                        <a href="<?= pc_h($c['social_linkedin']) ?>" class="social-icon" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Management Section -->
            <?php if (!empty($management)): ?>
            <div class="team-section">
                <h3 class="section-title"><?= pc_h($pc['team_section_executive_title']) ?></h3>
                <div class="section-divider"></div>
                <div class="team-layout">
                    <?php
                        // First row (lowest sort_order) is the featured leader; the rest are members.
                        $mgmt_leader = $management[0];
                        $mgmt_members = array_slice($management, 1);
                    ?>
                    <div class="team-leader">
                        <div class="team-card">
                            <div class="team-img-container">
                                <img src="<?= pc_h(pc_image_src($mgmt_leader['photo'], 'assets/img/instructor/instructor01.png')) ?>" alt="<?= pc_h($mgmt_leader['name']) ?>" class="team-img">
                            </div>
                            <h4 class="team-name"><?= pc_h($mgmt_leader['name']) ?></h4>
                            <p class="team-role"><?= pc_h($mgmt_leader['role']) ?></p>
                            <?php if (!empty($mgmt_leader['bio'])): ?>
                                <p class="team-bio"><?= pc_h($mgmt_leader['bio']) ?></p>
                            <?php endif; ?>
                            <div class="team-social">
                                <?php if (!empty($mgmt_leader['social_linkedin'])): ?>
                                    <a href="<?= pc_h($mgmt_leader['social_linkedin']) ?>" class="social-icon" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($mgmt_members)): ?>
                    <div class="team-members">
                        <?php foreach ($mgmt_members as $m): ?>
                            <div class="team-card">
                                <div class="team-img-container">
                                    <img src="<?= pc_h(pc_image_src($m['photo'], 'assets/img/instructor/instructor01.png')) ?>" alt="<?= pc_h($m['name']) ?>" class="team-img">
                                </div>
                                <h4 class="team-name"><?= pc_h($m['name']) ?></h4>
                                <p class="team-role"><?= pc_h($m['role']) ?></p>
                                <?php if (!empty($m['bio'])): ?>
                                    <p class="team-bio"><?= pc_h($m['bio']) ?></p>
                                <?php endif; ?>
                                <div class="team-social">
                                    <?php if (!empty($m['social_linkedin'])): ?>
                                        <a href="<?= pc_h($m['social_linkedin']) ?>" class="social-icon" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- ESWASA STAFF Section (now at bottom) -->
            <div class="team-section">
                <h3 class="section-title">ESWASA Staff</h3>
                <div class="section-divider"></div>
                <div class="eswasa-staff-content text-center mb-4">
                    <p>
                        The Eswatini Standards Authority (ESWASA) operates through a dedicated team of professionals committed to upholding national and international standards. Our staff spans disciplines in standardisation, metrology, testing, certification, and quality assurance—working collaboratively to support industry growth, consumer protection, and regional trade compliance.
                    </p>
                </div>

                <?php if (!empty($staff)): ?>
                <div class="team-layout">
                    <div class="team-members">
                        <?php foreach ($staff as $s): ?>
                            <div class="team-card">
                                <div class="team-img-container">
                                    <img src="<?= pc_h(pc_image_src($s['photo'], 'assets/img/instructor/instructor01.png')) ?>" alt="<?= pc_h($s['name']) ?>" class="team-img">
                                </div>
                                <h4 class="team-name"><?= pc_h($s['name']) ?></h4>
                                <p class="team-role"><?= pc_h($s['role']) ?></p>
                                <?php if (!empty($s['bio'])): ?>
                                    <p class="team-bio"><?= pc_h($s['bio']) ?></p>
                                <?php endif; ?>
                                <div class="team-social">
                                    <?php if (!empty($s['social_linkedin'])): ?>
                                        <a href="<?= pc_h($s['social_linkedin']) ?>" class="social-icon" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Rectangular Group Photo -->
                <div class="text-center">
                    <div class="staff-photo-container mx-auto">
                        <img src="<?= pc_h(pc_image_src($pc['team_staff_group_photo'], 'admin/uploads/staff_group_photo.jpg')) ?>" alt="ESWASA Staff Group Photo" class="staff-photo">
                        <div class="staff-photo-placeholder">
                            Staff Group Photo<br><em>(900 × 360 px recommended)</em>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- main-area-end -->
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
</body>
</html>