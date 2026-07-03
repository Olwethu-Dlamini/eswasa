<?php
include_once __DIR__ . '/includes/db_connect.php';
$conn->set_charset('utf8mb4');
include_once __DIR__ . '/includes/breadcrumb_helper.php';
require_once __DIR__ . '/includes/cms_helpers.php';
require __DIR__ . '/includes/cms_keys_policies.php';

$pc = pc_get_many($conn, $policies_keys, $policies_defaults);

$policies = $conn->query('SELECT * FROM eswasa_policies ORDER BY sort_order ASC, id ASC');

function policy_link_href(string $stored, bool $internal): string {
    if ($internal) return $stored;
    // External / PDF — URL-encode each segment so spaces and unicode resolve.
    $parts = array_map('rawurlencode', explode('/', $stored));
    return implode('/', $parts);
}
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= pc_h($pc['policies_breadcrumb_title']) ?> - ESWASA</title>
    <meta name="description" content="Public policies and procedures of the Eswatini Standards Authority — impartiality, complaints, appeals, certification rules, privacy and more.">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/logo/ESWASA_LOGO.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/tg-cursor.css">
    <link rel="stylesheet" href="assets/css/main.css">

    <style>
        /* ESWASA theme base — locked spec (#2B3388, #fff, Arial 15px) */
        body { font-family: Arial, sans-serif; font-size: 15px; color: #2B3388; }
        body h1, body h2, body h3, body h4, body h5, body h6 { font-family: Arial, sans-serif; color: #2B3388; }
        body p, body li, body span, body a, body div, body button, body input, body label, body textarea, body table, body th, body td { font-family: Arial, sans-serif; }
        .text-muted { color: #2B3388 !important; }
        .breadcrumb-content .breadcrumb a,
        .breadcrumb-content .breadcrumb span,
        .breadcrumb-content .title { color: #fff !important; }
        .breadcrumb-separator i { color: #fff !important; }
        .bg-light { background-color: rgba(43, 51, 136, 0.04) !important; }

        /* Section heading + canonical intro card (no left-blue accent) */
        .policies-section-title {
            color: #2B3388;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin: 0;
        }
        .policies-section-divider {
            width: 60px; height: 2px; background: #2B3388;
            margin: 14px auto 28px; border-radius: 0;
        }
        .info-box {
            background-color: rgba(43, 51, 136, 0.04);
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 25px;
            margin-bottom: 36px;
        }
        .info-box.is-intro { text-align: center; }
        .info-box.is-intro p { text-align: left; margin: 0; line-height: 1.65; }

        /* Policies grid — flat cards, no left accent */
        .policies-section { padding: 50px 0 70px; }
        .policy-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }
        .policy-card {
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            padding: 22px 24px;
            display: flex;
            gap: 18px;
            text-decoration: none !important;
            color: inherit;
            transition: border-color .25s ease, box-shadow .25s ease;
        }
        .policy-card:hover {
            border-color: #2B3388;
            box-shadow: 0 6px 18px rgba(43, 51, 136, 0.10);
        }
        .policy-icon {
            flex-shrink: 0;
            width: 48px; height: 48px;
            background: rgba(43, 51, 136, 0.06);
            color: #2B3388;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
        }
        .policy-body { flex: 1; min-width: 0; }
        .policy-category {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: rgba(43, 51, 136, 0.70);
            margin-bottom: 6px;
        }
        .policy-title {
            color: #2B3388;
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0 0 8px;
            line-height: 1.3;
        }
        .policy-desc {
            color: #2B3388;
            font-size: 0.9rem;
            line-height: 1.55;
            margin: 0 0 10px;
        }
        .policy-cta {
            color: #2B3388;
            font-weight: 600;
            font-size: 0.82rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .policy-cta i { font-size: 11px; transition: transform .2s ease; }
        .policy-card:hover .policy-cta i { transform: translateX(3px); }

        .policy-empty {
            padding: 40px 20px;
            text-align: center;
            color: rgba(43, 51, 136, 0.75);
            background: #fff;
            border: 1px solid rgba(43, 51, 136, 0.15);
            border-radius: 4px;
        }

        @media (max-width: 767.98px) {
            .policies-section { padding: 30px 0 40px; }
            .policy-grid { grid-template-columns: 1fr; }
            .policy-card { padding: 18px; gap: 14px; }
            .policy-icon { width: 42px; height: 42px; font-size: 17px; }
            .policies-section-title { font-size: 1.25rem; }
            .breadcrumb-content .title { font-size: 1.5rem; }
        }
    </style>
</head>
<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>

    <?php include("includes/header.php")?>

    <main class="main-area fix">

        <section class="breadcrumb-area breadcrumb-bg" style="background-image: url('<?= get_breadcrumb_bg('policies', 'assets/img/bg/breadcrumb_bg.jpg') ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span><a href="index.php"><?= pc_h($pc['policies_breadcrumb_home_label']) ?></a></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><?= pc_h($pc['policies_breadcrumb_parent_label']) ?></span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span><?= pc_h($pc['policies_breadcrumb_current_label']) ?></span>
                            </nav>
                            <h3 class="title"><?= pc_h($pc['policies_breadcrumb_title']) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="policies-section">
            <div class="container">
                <h2 class="policies-section-title"><?= pc_h($pc['policies_section_title']) ?></h2>
                <div class="policies-section-divider"></div>

                <div class="info-box is-intro">
                    <?= pc_paragraphs_html($pc['policies_intro_body']) ?>
                </div>

                <?php if ($policies && $policies->num_rows > 0): ?>
                    <div class="policy-grid">
                        <?php while ($p = $policies->fetch_assoc()):
                            $internal = (int)$p['is_internal'] === 1;
                            $href = policy_link_href($p['file_path'], $internal);
                            $icon = !empty($p['icon']) ? $p['icon'] : 'fa-file-alt';
                        ?>
                            <a class="policy-card" href="<?= htmlspecialchars($href) ?>" <?= $internal ? '' : 'target="_blank" rel="noopener"' ?>>
                                <div class="policy-icon"><i class="fas <?= htmlspecialchars($icon) ?>"></i></div>
                                <div class="policy-body">
                                    <span class="policy-category"><?= htmlspecialchars($p['category']) ?></span>
                                    <h4 class="policy-title"><?= htmlspecialchars($p['title']) ?></h4>
                                    <p class="policy-desc"><?= htmlspecialchars($p['description']) ?></p>
                                    <span class="policy-cta">
                                        <?= $internal ? 'Open page' : 'Open PDF' ?>
                                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="policy-empty"><?= pc_h($pc['policies_empty_state']) ?></div>
                <?php endif; ?>

            </div>
        </section>

    </main>

    <?php include("includes/footer.php")?>

    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/tg-cursor.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
