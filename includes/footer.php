<?php
// Fetch the Google Analytics ID (set in Admin → Site Settings). Defensive:
// only runs if a DB connection is in scope, never breaks the page if absent.
$ga4_id = '';
if (isset($conn) && $conn instanceof mysqli) {
    if ($res = @$conn->query("SELECT content FROM page_content WHERE page_key='site_ga4_id' LIMIT 1")) {
        if ($row = $res->fetch_assoc()) {
            $ga4_id = trim((string)$row['content']);
        }
    }
}
// Only emit a well-formed id, so nothing odd can be injected into the page.
if (!preg_match('/^(G|UA|GTM)-[A-Z0-9-]+$/i', $ga4_id)) {
    $ga4_id = '';
}
?>
<footer style="
    background-color: #2B3388;
    color: #fff; 
    padding: 24px 0 16px; 
    font-family: Arial, Helvetica, sans-serif;
    font-size: 0.9rem;
">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-4 text-center text-md-start">

            <!-- Logo + 4 small quality badges -->
            <div class="d-flex align-items-center flex-wrap gap-3">
                <a href="index.html">
                    <img src="assets/img/logo/ESWASA2 LOGO.png" alt="ESWASA" height="120">
                </a>
                <div class="d-flex gap-2">
                    <img src="assets/img/quality/management-mark.png" alt="Management Systems Certification Mark" width="36">
                    <img src="assets/img/quality/product-certification.png" alt="Product Certification Mark" width="36">
                    <img src="assets/img/quality/compulsory-standards.png" alt="Compulsory Standards Quality Mark" width="36">
                    <img src="assets/img/quality/ingelo-certification.png" alt="Ingelo Certification Scheme Mark" width="36">
                </div>
            </div>

            <!-- Center links with | separators (exactly like SABS) -->
            <div class="d-flex flex-wrap justify-content-center gap-3 flex-grow-1">
                <a href="disclaimer.php" class="text-white text-decoration-none">Disclaimer</a>
                <span class="text-white opacity-75">|</span>
                <a href="terms.php" class="text-white text-decoration-none">Terms & Conditions</a>
                <span class="text-white opacity-75">|</span>
                <a href="privacy.php" class="text-white text-decoration-none">Privacy Policy</a>
                <span class="text-white opacity-75">|</span>
                <a href="Certification.php" class="text-white text-decoration-none">Certification</a>
                <span class="text-white opacity-75">|</span>
                <a href="Standards.php" class="text-white text-decoration-none">Standards</a>
                <span class="text-white opacity-75">|</span>
                <a href="faq.php" class="text-white text-decoration-none">FAQs</a>
            </div>

            <!-- Social icons on the right -->
            <div class="d-flex gap-3">
                <a href="https://www.facebook.com/eswasaupdates" target="_blank" rel="noopener" aria-label="Facebook" class="text-white" style="font-size: 20px;"><i class="fab fa-facebook-f"></i></a>
                <a href="https://www.linkedin.com/company/eswatini-standards-authority/" target="_blank" rel="noopener" aria-label="LinkedIn" class="text-white" style="font-size: 20px;"><i class="fab fa-linkedin-in"></i></a>
                <a href="https://wa.me/26878780415" target="_blank" rel="noopener" aria-label="WhatsApp" class="text-white" style="font-size: 20px;"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>

        <!-- Copyright line (single centered line) -->
        <div class="text-center mt-3 pt-3 border-top" 
             style="border-color: rgba(255,255,255,0.15); font-size: 0.85rem; opacity: 0.8;">
            Copyright © <?php echo date("Y"); ?> Eswatini Standards Authority. 
            Developed by 
            <a href="https://www.realimageservices.com" target="_blank" 
               class="text-white text-decoration-underline">Real Image Internet</a>
        </div>
    </div>
</footer>
<?php if ($ga4_id !== ''): ?>
<!-- Google Analytics 4 — configured in Admin → Site Settings -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= rawurlencode($ga4_id) ?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?= htmlspecialchars($ga4_id, ENT_QUOTES) ?>');
</script>
<?php endif; ?>