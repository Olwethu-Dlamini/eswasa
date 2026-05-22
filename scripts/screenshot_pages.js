// Take full-viewport screenshots of representative pages to visually verify
// the brand-blue + no-bold policy doesn't break the design.
const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const PAGES = [
  'index.php', 'about-us.php', 'service-charter.php', 'Certification.php',
  'contact.php', 'managementsystems.php', 'qoute_calibration.php',
  'training-about.php', 'faq.php', 'board.php'
];

const BASE = 'http://eswasa.test/';
const OUT_DIR = path.join(__dirname, '..', '.audit-screenshots', 'fullpage');
if (!fs.existsSync(OUT_DIR)) fs.mkdirSync(OUT_DIR, { recursive: true });
for (const f of fs.readdirSync(OUT_DIR)) {
  if (f.endsWith('.png')) fs.unlinkSync(path.join(OUT_DIR, f));
}

(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({ viewport: { width: 1366, height: 900 } });
  const page = await ctx.newPage();
  for (const slug of PAGES) {
    await page.goto(BASE + slug, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(500);
    // Just first ~1.5 viewports — enough to see hero + intro
    await page.screenshot({ path: path.join(OUT_DIR, slug.replace('.php','') + '.png'), clip: { x: 0, y: 0, width: 1366, height: 1300 } });
    console.log('  ' + slug);
  }
  await b.close();
  console.log('Done. ' + OUT_DIR);
})();
