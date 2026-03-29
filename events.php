<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Events - ESWASA</title>
    <meta name="description" content="Stay updated with upcoming events, workshops, and conferences organized by the Eswatini Standards Authority (ESWASA).">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <!-- Keep other CSS if needed, but main.css should include your custom styles -->
    <style>
        /* BASE STYLES & BRANDING */
        :root {
            --eswasa-blue: #2E3191;
            --eswasa-dark: #1a1f71;
            --eswasa-light-blue: #eef5ff;
        }

        .btn-event {
            background-color: var(--eswasa-blue);
            color: white;
            border-color: var(--eswasa-blue);
            margin: 5px;
        }
        .btn-event:hover {
            background-color: var(--eswasa-dark);
            border-color: var(--eswasa-dark);
            color: white;
        }

        /* Event Item Styles */
        .events__item {
            transition: box-shadow 0.3s ease;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        .events__item:hover {
            box-shadow: 0 12px 25px rgba(0,0,0,0.15);
        }
        .events__item-thumb {
            position: relative;
        }
        .events__item-thumb img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .events__date {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background-color: var(--eswasa-blue);
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.9rem;
            font-weight: 600;
            z-index: 10;
        }
        .events__item-content {
            padding: 15px;
        }
        .events__item-content .title a {
            color: #333;
            text-decoration: none;
        }
        .events__item-content .title a:hover {
            color: var(--eswasa-blue);
        }
        .location {
            color: #666;
            font-size: 0.9rem;
        }

        /* Recent Events Sidebar */
        .rc-post-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .rc-post-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .rc-post-thumb {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            margin-right: 15px;
            overflow: hidden;
            border-radius: 4px;
        }
        .rc-post-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .rc-post-content .title a {
            font-size: 0.95rem;
            line-height: 1.3;
            color: #333;
            text-decoration: none;
        }
        .rc-post-content .title a:hover {
            color: var(--eswasa-blue);
        }
        .rc-post-content .date {
            font-size: 0.8rem;
            color: #666;
            display: block;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>

    <?php
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'eswasa');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");

    // Fetch all upcoming & recent events (sorted by date, soonest first)
    $result = $conn->query("
        SELECT * FROM eswasa_events 
        ORDER BY event_date ASC
    ");

    // Fetch recent events for sidebar (latest 4)
    $recent_result = $conn->query("
        SELECT id, title, event_date, image 
        FROM eswasa_events 
        ORDER BY event_date DESC 
        LIMIT 4
    ");
    ?>

    <main class="main-area fix">
        <section class="breadcrumb-area breadcrumb-bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span><a href="index.php">Home</a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span>Events</span>
                            </nav>
                            <h3 class="title">Our ESWASA Events</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="events-area section-pt-120 section-pb-90">
            <div class="container">
                <div class="row">
                    <!-- Main Events Grid -->
                    <div class="col-xl-9 col-lg-8">
                        <div class="row events__wrapper">
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while ($event = $result->fetch_assoc()): ?>
                                    <?php
                                    $date = date('d M, Y', strtotime($event['event_date']));
                                    $image = !empty($event['image']) ? 'admin/uploads/' . htmlspecialchars($event['image']) : 'assets/img/default-event.jpg';
                                    ?>
                                    <div class="col-xl-4 col-md-6">
                                        <div class="events__item shine__animate-item">
                                            <div class="events__item-thumb">
                                                <a href="event-details.php?id=<?= (int)$event['id'] ?>" class="shine__animate-link">
                                                    <img src="<?= $image ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                                                </a>
                                                <span class="events__date"><i class="far fa-calendar-alt"></i> <?= $date ?></span>
                                            </div>
                                            <div class="events__item-content">
                                                <h4 class="title">
                                                    <a href="event-details.php?id=<?= (int)$event['id'] ?>">
                                                        <?= htmlspecialchars($event['title']) ?>
                                                    </a>
                                                </h4>
                                                <span class="location">
                                                    <i class="fas fa-map-marker-alt"></i> 
                                                    <?= htmlspecialchars($event['location'] ?: 'Online') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <p class="text-center py-5">No upcoming events scheduled.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Pagination (static for now) -->
                        <?php if ($result && $result->num_rows > 9): ?>
                            <nav class="pagination__wrap mt-30">
                                <ul class="list-wrap">
                                    <li class="active"><a href="#">1</a></li>
                                    <li><a href="#">2</a></li>
                                    <li><a href="#">Next</a></li>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar -->
                    <div class="col-xl-3 col-lg-4 order-2 order-lg-0">
                        <div class="events__sidebar">
                            <!-- Filter Form (non-functional for now) -->
                            <div class="blog-widget">
                                <h4 class="widget-title">Find Your Events</h4>
                                <div class="events__sidebar-filter">
                                    <form action="#" method="GET">
                                        <div class="form-grp mb-2">
                                            <input type="text" class="form-control" placeholder="Keywords" name="q">
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Search</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Recent Events -->
                            <?php if ($recent_result && $recent_result->num_rows > 0): ?>
                            <div class="blog-widget">
                                <h4 class="widget-title">Recent ESWASA Events</h4>
                                <?php while ($event = $recent_result->fetch_assoc()): ?>
                                    <?php
                                    $thumb = !empty($event['image']) ? 'admin/uploads/' . htmlspecialchars($event['image']) : 'assets/img/default-thumb.jpg';
                                    $date = date('d M, Y', strtotime($event['event_date']));
                                    ?>
                                    <div class="rc-post-item">
                                        <div class="rc-post-thumb">
                                            <a href="event-details.php?id=<?= (int)$event['id'] ?>">
                                                <img src="<?= $thumb ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                                            </a>
                                        </div>
                                        <div class="rc-post-content">
                                            <h4 class="title">
                                                <a href="event-details.php?id=<?= (int)$event['id'] ?>">
                                                    <?= htmlspecialchars($event['title']) ?>
                                                </a>
                                            </h4>
                                            <span class="date"><i class="far fa-calendar-alt"></i> <?= $date ?></span>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <?php endif; ?>
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