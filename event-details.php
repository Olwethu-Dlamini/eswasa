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
            background: rgba(0,0,0,0.38);
            padding: 35px 0 20px 0;
            border-radius: 8px;
        }
        .event-meta {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 20px;
        }
        .event-meta i {
            margin-right: 8px;
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