<?php
require_once __DIR__ . '/includes/db_connect.php';
$conn->set_charset('utf8mb4');
require_once __DIR__ . '/includes/event_images.php';
require_once __DIR__ . '/includes/breadcrumb_helper.php';

// Get event ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: events.php");
    exit;
}
$id = (int)$_GET['id'];

// Fetch the event
$stmt = $conn->prepare("SELECT * FROM eswasa_events WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

// Gallery for this event (falls back to legacy single `image` column if empty)
$event_images = $event ? eswasa_get_event_images($conn, (int)$event['id']) : [];

if (!$event) {
    header("HTTP/1.0 404 Not Found");
    include '404.php'; // or show message
    exit;
}

// Fetch 3 recent events for sidebar (excluding current)
$recentStmt = $conn->prepare("SELECT id, title, event_date, image FROM eswasa_events WHERE id != ? ORDER BY event_date DESC LIMIT 3");
$recentStmt->bind_param("i", $id);
$recentStmt->execute();
$recentEvents = $recentStmt->get_result();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= htmlspecialchars($event['title']) ?> | ESWASA</title>
    <meta name="description" content="<?= htmlspecialchars(strip_tags(substr($event['description'], 0, 160))) ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <!-- Keep other CSS if needed, but main.css should cover most -->
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
        .text-muted { color: #2B3388 !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        .event-content img {
            max-width: 100%;
            height: auto;
            margin: 15px 0;
        }
        .event-meta {
            color: #2B3388;
            font-size: 0.95rem;
            margin-bottom: 20px;
        }
        .event-meta i {
            margin-right: 8px;
        }
        .blog-widget {
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 20px;
            background: #fff;
        }
        .blog-widget .widget-title {
            color: #2B3388;
            font-weight: 700;
            font-size: 1.15rem;
            margin: 0 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.15);
        }
        .rc-post-item {
            display: flex;
            gap: 12px;
            padding-bottom: 12px;
            margin-bottom: 12px;
            border-bottom: 1px solid rgba(43, 51, 136, 0.15);
        }
        .rc-post-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .rc-post-thumb img {
            border-radius: 4px;
        }
        .rc-post-content .title {
            font-size: 0.95rem;
            margin: 0 0 5px 0;
            line-height: 1.3;
        }
        .rc-post-content .title a {
            color: #2B3388;
            text-decoration: none;
        }
        .rc-post-content .title a:hover {
            color: #2B3388;
        }
        .rc-post-content .date {
            color: #2B3388;
            font-size: 0.85rem;
        }

        /* Event Gallery */
        .event-gallery {
            margin: 24px 0 32px;
        }
        .event-gallery__main {
            position: relative;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            overflow: hidden;
            background: #fff;
        }
        .event-gallery__main a { display: block; }
        .event-gallery__main img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            display: block;
        }
        .event-gallery__zoom-hint {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(43, 51, 136, 0.85);
            color: #fff;
            padding: 6px 10px;
            border-radius: 3px;
            font-size: 0.8rem;
            font-weight: 600;
            pointer-events: none;
        }
        .event-gallery__zoom-hint i { margin-right: 5px; }
        .event-gallery__thumbs {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 4px;
        }
        .event-gallery__thumb {
            flex: 0 0 90px;
            height: 70px;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 3px;
            overflow: hidden;
            scroll-snap-align: start;
            cursor: pointer;
            background: #fff;
            padding: 0;
            transition: border-color .15s ease;
        }
        .event-gallery__thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .event-gallery__thumb.is-active,
        .event-gallery__thumb:hover {
            border-color: #2B3388;
        }
        .event-gallery__count {
            font-size: 0.85rem;
            color: rgba(43, 51, 136, 0.75);
            margin-top: 6px;
            text-align: right;
        }

        /* Magnific popup theme tweaks */
        .mfp-bg { background: rgba(43, 51, 136, 0.92); opacity: 1; }
        .mfp-title { color: #fff; font-family: Arial, sans-serif; font-size: 0.95rem; padding-right: 60px; }
        .mfp-counter { color: #fff; font-family: Arial, sans-serif; }

        @media (max-width: 767.98px) {
            .event-content {
                font-size: 15px !important;
            }
            .blog-widget {
                margin-top: 30px;
            }
            .event-meta span {
                display: block;
                margin-bottom: 5px;
                margin-left: 0 !important;
            }
            .event-gallery__main img { height: 260px; }
            .event-gallery__thumb { flex: 0 0 72px; height: 56px; }
        }
    </style>
</head>
<body>
    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>

    <main class="main-area fix">
        <!-- Breadcrumb -->
        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('event-details', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span><a href="index.php">Home</a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><a href="events.php">Events</a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><?= htmlspecialchars($event['title']) ?></span>
                            </nav>
                            <h3 class="title"><?= htmlspecialchars($event['title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Event Details -->
        <section class="blog-standard-area section-py-120">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="event-meta">
                            <span><i class="far fa-calendar-alt"></i> <?= date('d M, Y', strtotime($event['event_date'])) ?></span>
                            <?php if (!empty($event['location'])): ?>
                                <span class="ms-3"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></span>
                            <?php endif; ?>
                            <?php if (!empty($event['category'])): ?>
                                <span class="ms-3"><i class="fas fa-tag"></i> <?= ucfirst(htmlspecialchars($event['category'])) ?></span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($event_images)): ?>
                            <?php $first = $event_images[0]; ?>
                            <div class="event-gallery" id="event-gallery-<?= (int)$event['id'] ?>">
                                <div class="event-gallery__main">
                                    <?php foreach ($event_images as $idx => $img): ?>
                                        <a href="admin/uploads/<?= htmlspecialchars($img['image']) ?>"
                                           data-mfp-gallery="event-<?= (int)$event['id'] ?>"
                                           title="<?= htmlspecialchars($event['title']) ?> &mdash; image <?= $idx + 1 ?> of <?= count($event_images) ?>"
                                           class="event-gallery__lightbox<?= $idx === 0 ? '' : ' d-none' ?>">
                                            <?php if ($idx === 0): ?>
                                                <img src="admin/uploads/<?= htmlspecialchars($img['image']) ?>"
                                                     alt="<?= htmlspecialchars($event['title']) ?>"
                                                     id="event-gallery-main-img-<?= (int)$event['id'] ?>"
                                                     onerror="this.src='assets/img/default-event.jpg'; this.onerror=null;">
                                                <span class="event-gallery__zoom-hint"><i class="fas fa-search-plus"></i>Tap to zoom</span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                                <?php if (count($event_images) > 1): ?>
                                    <div class="event-gallery__thumbs" role="tablist">
                                        <?php foreach ($event_images as $idx => $img): ?>
                                            <button type="button"
                                                    class="event-gallery__thumb<?= $idx === 0 ? ' is-active' : '' ?>"
                                                    data-img="admin/uploads/<?= htmlspecialchars($img['image']) ?>"
                                                    data-index="<?= $idx ?>"
                                                    aria-label="Show image <?= $idx + 1 ?>">
                                                <img src="admin/uploads/<?= htmlspecialchars($img['image']) ?>"
                                                     alt="<?= htmlspecialchars($event['title']) ?> thumbnail <?= $idx + 1 ?>"
                                                     onerror="this.src='assets/img/default-thumb.jpg'; this.onerror=null;">
                                            </button>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="event-gallery__count"><?= count($event_images) ?> images &mdash; swipe or click to zoom</div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="event-content" style="font-size:1.13em; line-height:1.7;">
                            <?= nl2br(htmlspecialchars($event['description'])) ?>
                        </div>
                    </div>

                    <!-- Sidebar: Recent Events -->
                    <div class="col-lg-4">
                        <div class="blog-widget">
                            <h4 class="widget-title">Recent Events</h4>
                            <?php while ($r = $recentEvents->fetch_assoc()): ?>
                                <div class="rc-post-item mb-3">
                                    <div class="rc-post-thumb">
                                        <?php if (!empty($r['image'])): ?>
                                            <a href="event-details.php?id=<?= (int)$r['id'] ?>">
                                                <img src="admin/uploads/<?= htmlspecialchars($r['image']) ?>" 
                                                     alt="<?= htmlspecialchars($r['title']) ?>" 
                                                     style="width:80px; height:60px; object-fit:cover;">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="rc-post-content">
                                        <h4 class="title">
                                            <a href="event-details.php?id=<?= (int)$r['id'] ?>">
                                                <?= htmlspecialchars($r['title']) ?>
                                            </a>
                                        </h4>
                                        <span class="date">
                                            <i class="far fa-calendar-alt"></i> 
                                            <?= date('d M, Y', strtotime($r['event_date'])) ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include("includes/footer.php")?>

    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script>
        jQuery(function ($) {
            'use strict';
            if (!$.fn.magnificPopup) {
                console.warn('[event-gallery] magnificPopup plugin not loaded');
                return;
            }

            $('.event-gallery').each(function () {
                var $g = $(this);
                var $main = $g.find('.event-gallery__main');
                if (!$main.length || !$g.find('a.event-gallery__lightbox').length) return;

                // Canonical magnific gallery: bind to container, delegate to anchors.
                $main.magnificPopup({
                    delegate: 'a.event-gallery__lightbox',
                    type: 'image',
                    gallery: {
                        enabled: true,
                        navigateByImgClick: true,
                        preload: [0, 1],
                        tCounter: '<span class="mfp-counter">%curr% of %total%</span>'
                    },
                    image: {
                        titleSrc: function (item) {
                            return item.el ? item.el.attr('title') : '';
                        }
                    },
                    closeOnContentClick: false,
                    mainClass: 'mfp-with-zoom',
                    zoom: { enabled: true, duration: 250, easing: 'ease-in-out' },
                    callbacks: {
                        // Honor the thumbnail the user previewed before tapping zoom
                        open: function () {
                            var idx = parseInt($main.data('current-index'), 10) || 0;
                            if (idx > 0 && typeof this.goTo === 'function') {
                                this.goTo(idx);
                            }
                        }
                    }
                });

                // Thumb click → swap main preview + remember index for next tap-to-zoom
                $g.on('click', '.event-gallery__thumb', function (e) {
                    e.preventDefault();
                    var $btn = $(this);
                    var idx = parseInt($btn.attr('data-index'), 10) || 0;
                    var src = $btn.attr('data-img');
                    $g.find('.event-gallery__thumb').removeClass('is-active');
                    $btn.addClass('is-active');
                    $g.find('[id^="event-gallery-main-img-"]').attr('src', src);
                    $main.data('current-index', idx);
                });
            });
        });
    </script>
</body>
</html>