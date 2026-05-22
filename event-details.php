<?php
// Database connection (adjust path if needed)
$conn = new mysqli('localhost', 'root', '', 'eswasa');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

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
    <link rel="stylesheet" href="assets/css/main.css">
    <!-- Keep other CSS if needed, but main.css should cover most -->
    <style>
        /* ========== ESWASA Theme Base (locked spec: #2B3388, #fff, Arial 16px) ========== */
        body {
            font-family: Arial, sans-serif;
            font-size: 16px;
            color: #2B3388;
        }
        body h1, body h2, body h3, body h4, body h5, body h6 {
            font-family: Arial, sans-serif;
            color: #2B3388;
        }
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea, body table, body th, body td {
            font-family: Arial, sans-serif;
        }
        .text-muted { color: rgba(43, 51, 136, 0.7) !important; }
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
        .breadcrumb-area.breadcrumb-bg {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        .breadcrumb-area .breadcrumb-content {
            background: rgba(43, 51, 136, 0.38);
            padding: 35px 0 20px 0;
            border-radius: 4px;
        }
        .event-meta {
            color: rgba(43, 51, 136, 0.75);
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
            color: rgba(43, 51, 136, 0.85);
        }
        .rc-post-content .date {
            color: rgba(43, 51, 136, 0.7);
            font-size: 0.85rem;
        }

        @media (max-width: 767.98px) {
            .breadcrumb-area .breadcrumb-content {
                padding: 20px 10px 12px 10px;
            }
            .breadcrumb-content .title {
                font-size: 1.4rem;
            }
            .event-content {
                font-size: 1rem !important;
            }
            .blog-widget {
                margin-top: 30px;
            }
            .event-meta span {
                display: block;
                margin-bottom: 5px;
                margin-left: 0 !important;
            }
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
        <section class="breadcrumb-area breadcrumb-bg" 
                 style="background-image: url('<?= !empty($event['image']) ? 'admin/uploads/' . htmlspecialchars($event['image']) : 'assets/img/bg/breadcrumb_bg.jpg' ?>');">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content text-white">
                            <nav class="breadcrumb">
                                <a href="index.php">Home</a>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <a href="events.php">Events</a>
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
    <script src="assets/js/main.js"></script>
</body>
</html>