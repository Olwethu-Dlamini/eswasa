<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Vacancies - ESWASA</title>
    <meta name="description" content="Explore current job opportunities at the Eswatini Standards Authority (ESWASA).">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
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
        /* BASE STYLES & BRANDING */
        :root {
            --eswasa-blue: #2E3191;
            --eswasa-dark: #1a1f71;
            --eswasa-light-blue: #eef5ff;
        }

        .vacancy-area {
            padding-top: 50px;
            padding-bottom: 50px;
        }

        .vacancy-card {
            border: 1px solid #eee;
            border-radius: 6px;
            padding: 25px;
            margin-bottom: 25px;
            transition: box-shadow 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            cursor: pointer;
        }
        .vacancy-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .vacancy-title {
            color: var(--eswasa-blue);
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 1.5rem;
        }
        .vacancy-meta {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #eee;
        }
        .vacancy-meta span {
            margin-right: 25px;
            display: inline-block;
        }
        .vacancy-description {
            margin-bottom: 20px;
            line-height: 1.6;
        }
        .vacancy-description ul {
            list-style: none;
            padding-left: 0;
            margin-top: 10px;
        }
        .vacancy-description ul li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 8px;
            color: #444;
        }
        .vacancy-description ul li::before {
            content: "—";
            color: #888;
            font-weight: normal;
            display: inline-block;
            width: 1em;
            margin-left: -1em;
        }
        .vacancy-application-link a {
            display: inline-block;
            background-color: var(--eswasa-blue);
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        .vacancy-application-link a:hover {
            background-color: var(--eswasa-dark);
            color: white;
        }

        /* General Application Info Box */
        .info-box {
            background-color: #f9f9f9;
            border-radius: 6px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid #eee;
        }
        .info-box h3 {
            color: var(--eswasa-blue);
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 15px;
        }
        .info-box p a {
            color: var(--eswasa-blue);
            font-weight: 600;
            text-decoration: none;
        }
        .info-box p a:hover {
            text-decoration: underline;
        }

        /* Modal Styles - Match Public Page Exactly */
        #vacancyModal .modal-content {
            border-radius: 6px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid #eee;
        }
        #vacancyModal .modal-header {
            background-color: #f9f9f9;
            border-bottom: 1px solid #eee;
            padding: 20px 30px;
        }
        #vacancyModal .modal-title {
            color: var(--eswasa-blue);
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0;
        }
        #vacancyModal .modal-body {
            padding: 30px;
        }
        #vacancyModal .modal-meta {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px dashed #eee;
        }
        #vacancyModal .modal-meta span {
            margin-right: 25px;
            display: inline-block;
        }

        /* Description & Responsibilities in Modal */
        #vacancyModal .vacancy-full-description {
            line-height: 1.6;
            margin-bottom: 20px;
        }
        #vacancyModal .vacancy-full-responsibilities h5 {
            margin: 20px 0 15px 0;
            color: var(--eswasa-blue);
            font-weight: 700;
        }
        #vacancyModal .vacancy-full-responsibilities ul {
            list-style: none;
            padding-left: 0;
            margin-top: 10px;
        }
        #vacancyModal .vacancy-full-responsibilities ul li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 8px;
            color: #444;
        }
        #vacancyModal .vacancy-full-responsibilities ul li::before {
            content: "—";
            color: #888;
            font-weight: normal;
            display: inline-block;
            width: 1em;
            margin-left: -1em;
        }

        /* Modal Buttons */
        #vacancyModal .btn-apply {
            background-color: var(--eswasa-blue);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        #vacancyModal .btn-apply:hover {
            background-color: var(--eswasa-dark);
            color: white;
        }
        #vacancyModal .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            color: white;
        }
    </style>
</head>

<body>

    <button class="scroll__top scroll-to-target" data-target="html">
        <i class="fas fa-angle-up"></i>
    </button>
    <?php include("includes/header.php")?>

    <?php
    $conn = new mysqli('localhost', 'root', '', 'eswasa');
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $conn->set_charset("utf8mb4");
    
    $vacancies = $conn->query("
        SELECT * FROM eswasa_vacancies 
        WHERE closing_date >= CURDATE() 
        ORDER BY closing_date ASC
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
                                <span>Vacancies</span>
                            </nav>
                            <h3 class="title">Current Vacancies</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="vacancy-area">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        
                        <div class="info-box">
                            <h3>Working at ESWASA</h3>
                            <p>The <strong>Eswatini Standards Authority (ESWASA)</strong> is committed to attracting and retaining talented individuals who are passionate about standards, quality, and making a difference in Eswatini. We offer a dynamic and professional work environment where you can grow your career and contribute to the nation's development.</p>
                            <p>We offer competitive packages and a supportive work environment. Find our available positions below.</p>
                        </div>

                        <h2 style="margin-bottom: 25px;">Available Positions</h2>
                        
                        <?php if ($vacancies && $vacancies->num_rows > 0): ?>
                            <?php while ($v = $vacancies->fetch_assoc()): ?>
                                <div class="vacancy-card" onclick="showVacancyDetails(
                                    '<?= addslashes($v['title']) ?>',
                                    '<?= addslashes($v['location']) ?>',
                                    '<?= date('Y-m-d', strtotime($v['closing_date'])) ?>',
                                    `<?= addslashes($v['description']) ?>`,
                                    `<?= addslashes($v['responsibilities']) ?>`
                                )">
                                    <h4 class="vacancy-title"><?= htmlspecialchars($v['title']) ?></h4>
                                    <div class="vacancy-meta">
                                        <span>Location: <?= htmlspecialchars($v['location']) ?></span>
                                        <span>Closing Date: <?= date('Y-m-d', strtotime($v['closing_date'])) ?></span>
                                    </div>
                                    <div class="vacancy-description">
                                        <p><?= nl2br(htmlspecialchars($v['description'])) ?></p>
                                        <?php if (!empty($v['responsibilities'])): ?>
                                            <p><strong>Key Responsibilities:</strong></p>
                                            <ul>
                                                <?php
                                                $lines = explode("\n", trim($v['responsibilities']));
                                                $count = 0;
                                                foreach ($lines as $line):
                                                    $line = trim($line);
                                                    if (!empty($line) && $count < 3):
                                                        $count++;
                                                ?>
                                                        <li><?= htmlspecialchars($line) ?></li>
                                                <?php
                                                    endif;
                                                endforeach;
                                                if (count(array_filter($lines)) > 3): ?>
                                                    <li><em>...and more responsibilities</em></li>
                                                <?php endif; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                    <div class="vacancy-application-link">
                                        <a href="#" onclick="event.stopPropagation(); showVacancyDetails(
                                            '<?= addslashes($v['title']) ?>',
                                            '<?= addslashes($v['location']) ?>',
                                            '<?= date('Y-m-d', strtotime($v['closing_date'])) ?>',
                                            `<?= addslashes($v['description']) ?>`,
                                            `<?= addslashes($v['responsibilities']) ?>`
                                        ); return false;">View Details & Apply</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <p>There are no current vacancies available at this time.</p>
                            </div>
                        <?php endif; ?>

                        <div class="info-box" style="margin-top: 30px;">
                            <h3>How to Apply</h3>
                            <p>Click on any vacancy above to view complete details. When you click "Apply for this Position", your email client will open with the job title pre-filled. Please attach your <strong>cover letter</strong> and <strong>CV</strong> and send to <a href="mailto:hr@eswasa.co.sz">hr@eswasa.co.sz</a>.</p>
                            <p>Ensure you quote the position title in the subject line of your email.</p>
                            <p>Only shortlisted candidates will be contacted for interviews.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Single Modal for Vacancy Details -->
    <div class="modal fade" id="vacancyModal" tabindex="-1" aria-labelledby="vacancyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="vacancyModalLabel">Position Title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="modal-meta">
                        <span>Location: <span id="modalLocation"></span></span>
                        <span>Closing Date: <span id="modalClosingDate"></span></span>
                    </div>
                    
                    <div class="vacancy-full-description" id="modalFullDescription"></div>
                    
                    <div class="vacancy-full-responsibilities" id="modalFullResponsibilities"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="#" class="btn-apply" id="applyEmailLink" target="_blank">Apply for this Position</a>
                </div>
            </div>
        </div>
    </div>

    <?php include("includes/footer.php")?>
    <script src="assets/js/vendor/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        function showVacancyDetails(title, location, closingDate, description, responsibilities) {
            // Set title
            document.getElementById('vacancyModalLabel').textContent = title;
            
            // Set meta
            document.getElementById('modalLocation').textContent = location;
            document.getElementById('modalClosingDate').textContent = closingDate;
            
            // Set description
            document.getElementById('modalFullDescription').innerHTML = 
                '<p>' + description.replace(/\n/g, '<br>') + '</p>';
            
            // Set responsibilities
            const respContainer = document.getElementById('modalFullResponsibilities');
            if (responsibilities && responsibilities.trim()) {
                const lines = responsibilities.split('\n').filter(line => line.trim() !== '');
                let html = '<p><strong>Key Responsibilities:</strong></p><ul>';
                lines.forEach(line => {
                    html += '<li>' + line.trim() + '</li>';
                });
                html += '</ul>';
                respContainer.innerHTML = html;
            } else {
                respContainer.innerHTML = '';
            }
            
            // Set email link
            const emailSubject = encodeURIComponent('Job Application: ' + title);
            const emailBody = encodeURIComponent(
                'Dear ESWASA Hiring Team,\n\n' +
                'I am writing to apply for the position of ' + title + '.\n\n' +
                'Please find my CV and cover letter attached.\n\n' +
                'Thank you for your consideration.\n\n' +
                'Best regards,\n[Your Name]'
            );
            document.getElementById('applyEmailLink').href = 
                'mailto:hr@eswasa.co.sz?subject=' + emailSubject + '&body=' + emailBody;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('vacancyModal'));
            modal.show();
        }
    </script>

</body>
</html>