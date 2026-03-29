<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Training - Calendar - SWASA</title>
    <meta name="description" content="View the upcoming training calendar for SWASA. Access the prospectus and apply for courses.">
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
        .text-gradient-primary {
            color: #2E3191;
        }
        .calendar-grid {
            max-width: 900px;
            margin: 0 auto;
        }
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            text-align: center;
            font-weight: bold;
            background-color: #f8f9fa;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .calendar-body {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }
        .day {
            height: 100px;
            border: 1px solid #ddd;
            padding: 5px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
            position: relative;
            overflow: hidden;
        }
        .day:hover {
            background-color: #e9ecef;
            transform: scale(1.02);
        }
        .has-event {
            background-color: #2E3191;
            color: white;
        }
        .has-event:hover {
            background-color: #1a1f71;
        }
        .today {
            border: 2px solid #ff0000;
            background-color: #fff5f5;
            font-weight: bold;
        }
        .has-event.today {
            background-color: #2E3191;
            border: 2px solid #ff0000;
        }
        .event-name {
            font-size: 0.75rem;
            line-height: 1.2;
            margin-top: 5px;
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        /* Additional styles for prospectus link */
        .prospectus-link {
            display: inline-block;
            margin-bottom: 20px;
            font-weight: 500;
            color: #2E3191;
            text-decoration: none;
        }
        .prospectus-link:hover {
            text-decoration: underline;
            color: #00c6ff;
        }
        .prospectus-link i {
            margin-right: 5px;
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
        <section class="breadcrumb-area breadcrumb-bg" data-background="assets/img/bg/breadcrumb_bg.jpg">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="breadcrumb-content">
                            <nav class="breadcrumb">
                                <span property="itemListElement" typeof="ListItem">
                                    <a href="index.html">Home</a>
                                </span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Training</span>
                                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                                <span property="itemListElement" typeof="ListItem">Calendar</span>
                            </nav>
                            <h3 class="title">Training Calendar</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- breadcrumb-area-end -->

        <!-- Training Calendar Section -->
        <section id="training_calendar_section" class="content_section py-5">
            <div class="container">
                <!-- Section Title -->
                <div class="main_title centered upper mb-5">
                    <h2 class="display-6 fw-bold text-center text-gradient-primary">
                        Upcoming Training Sessions
                        <span class="d-block fs-5 text-muted mt-2">Plan Your Learning Journey</span>
                        <span class="d-block mx-auto mt-3 bg-primary rounded-pill" style="width: 100px; height: 4px;"></span>
                    </h2>
                </div>

                <!-- Prospectus Link -->
                <div class="text-center mb-4">
                    <a href="admin/downloads/ESWASA TRAINING PROSPECTUS 2025-26.pdf" class="prospectus-link" target="_blank">
                        <i class="fas fa-file-pdf"></i> Download ESWASA TRAINING PROSPECTUS 2025-26 (PDF)
                    </a>
                </div>

                <!-- Calendar Grid -->
                <div class="calendar-grid">
                    <div class="calendar-header">
                        <button id="prev-month" class="btn btn-outline-primary"><</button>
                        <span id="current-month" class="fs-4 fw-bold text-gradient-primary"></span>
                        <button id="next-month" class="btn btn-outline-primary">></button>
                    </div>
                    <div class="calendar-days">
                        <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                    </div>
                    <div id="calendar-body" class="calendar-body"></div>
                </div>

                <!-- Apply Modal -->
                <div class="modal fade" id="applyModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Apply for Training: <span id="modal-event"></span> on <span id="modal-date"></span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-muted mb-3">Please complete the form below to apply for the selected training session.</p>
                                <form id="applyForm">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" class="form-control" id="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone">
                                    </div>
                                    <div class="mb-3">
                                        <label for="company" class="form-label">Company/Organization</label>
                                        <input type="text" class="form-control" id="company">
                                    </div>
                                    <div class="mb-3">
                                        <label for="position" class="form-label">Position/Title</label>
                                        <input type="text" class="form-control" id="position">
                                    </div>
                                    <div class="mb-3">
                                        <label for="comments" class="form-label">Comments or Questions</label>
                                        <textarea class="form-control" id="comments" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="consentCheck" required>
                                        <label class="form-check-label" for="consentCheck">I agree to the <a href="training_about.php#policiesTabContent">Training Policies</a> and consent to the processing of my personal data as described in the prospectus.</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100"><i class="ico-check3 me-2"></i>Submit Application</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

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

    <script>
        // Event data for the calendar
        const events = {
            '2025-02-10': { code: 'OHS 02', title: 'Occupational Health & Safety Management Systems Understanding & Implementing SZNS ISO 45001:2018', type: 'Included' },
            '2025-02-17': { code: 'QMS 02', title: 'Quality Management Systems Understanding & Implementing SZNS ISO 9001:2015', type: 'Included' },
            '2025-03-03': { code: 'FS 02', title: 'Food Safety Management Systems Understanding & Implementing SZNS ISO 22000:2018', type: 'Included' },
            '2025-03-17': { code: 'WDM 02', title: 'Wellness & Disease Management Systems Understanding & Implementing SZNS SANS 16001:2013', type: 'Included' },
            '2025-03-24': { code: 'EMS 02', title: 'Environmental Management Systems Understanding & Implementing SZNS ISO 14001:2015', type: 'Included' },
            '2025-09-01': { code: 'EnMS', title: 'Energy Management Systems - SZNS ISO 50001:2018', type: 'Included' },
            '2025-09-08': { code: 'QMS 03', title: 'Quality Management Systems Internal Auditing - SZNS ISO 19011:2018', type: 'Included' },
            '2025-09-22': { code: 'FSMS 02', title: 'Food Safety Management Systems SZNS ISO 22000:2018 - Understanding & Implementing', type: 'Included' },
            '2025-10-08': { code: 'FS 04', title: 'Hazard Analysis & Critical Control Point (HACCP) System SZNS SANS 10330:2007 - Understanding & Implementing', type: 'Included' },
            '2025-10-13': { code: 'RCA', title: 'Root Cause Analysis - Understanding & Implementing', type: 'Included' },
            '2025-10-20': { code: 'OHS 02', title: 'Occupational Health & Safety Management Systems SZNS ISO 45001:2018 - Understanding & Implementing', type: 'Included' },
            '2025-11-10': { code: 'GAP 01', title: 'Global GAP - Integrated Farm Assurance', type: 'Included' },
            '2025-11-24': { code: 'SHEQ REP', title: 'Safety, Health and Environment Representative', type: 'Included' },
            '2025-12-08': { code: 'FS 04', title: 'Hazard Analysis & Critical Control Point (HACCP) System SZNS SANS 10330:2007 - Understanding & Implementing', type: 'Included' }
        };

        const today = '2025-11-24'; // Today's date
        let currentDate = new Date(2025, 10, 1); // Start at November 2025 (month 10)

        function renderCalendar() {
            const month = currentDate.getMonth();
            const year = currentDate.getFullYear();
            document.getElementById('current-month').textContent = new Date(year, month).toLocaleString('default', { month: 'long', year: 'numeric' });

            const calendarBody = document.getElementById('calendar-body');
            calendarBody.innerHTML = '';

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let day = 1;
            for (let i = 0; i < 6; i++) { // Max 6 weeks
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < firstDay) {
                        const emptyDiv = document.createElement('div');
                        calendarBody.appendChild(emptyDiv);
                    } else if (day <= daysInMonth) {
                        const dayDiv = document.createElement('div');
                        dayDiv.className = 'day';
                        dayDiv.textContent = day;
                        const dateKey = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        if (dateKey === today) {
                            dayDiv.classList.add('today');
                        }
                        if (events[dateKey]) {
                            dayDiv.classList.add('has-event');
                            dayDiv.dataset.event = JSON.stringify(events[dateKey]);
                            dayDiv.dataset.date = dateKey;
                            const eventName = document.createElement('span');
                            eventName.className = 'event-name';
                            eventName.textContent = `${events[dateKey].code} - ${events[dateKey].type}`;
                            dayDiv.appendChild(eventName);
                        }
                        dayDiv.addEventListener('click', openModal);
                        calendarBody.appendChild(dayDiv);
                        day++;
                    }
                }
            }
        }

        function openModal(e) {
            const dayDiv = e.target.closest('.day');
            if (dayDiv.classList.contains('has-event')) {
                const event = JSON.parse(dayDiv.dataset.event);
                document.getElementById('modal-date').textContent = dayDiv.dataset.date;
                document.getElementById('modal-event').textContent = `${event.code} - ${event.title}`;
                var applyModal = new bootstrap.Modal(document.getElementById('applyModal'));
                applyModal.show();
            }
        }

        document.getElementById('prev-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        document.getElementById('next-month').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        document.getElementById('applyForm').addEventListener('submit', (e) => {
            e.preventDefault();
            // Example: Show an alert or send data via AJAX
            const formData = new FormData(e.target);
            const name = formData.get('name');
            alert(`Thank you, ${name}! Your application for the training on ${document.getElementById('modal-date').textContent} has been submitted. We will contact you soon.`);
            var applyModal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
            applyModal.hide();
            e.target.reset(); // Reset the form after submission
        });

        renderCalendar(); // Initial render
    </script>

</body>
</html>