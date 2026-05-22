// Take focused screenshots of every section title + its divider on a sample
// of pages, so we can visually verify the 60x2 brand-blue underline renders
// consistently. Saves to .audit-screenshots/titles/<page>__<n>.png
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const PAGES = [
  'index.php', 'about-us.php', 'board.php', 'Certification.php',
  'contact.php', 'managementsystems.php', 'product.php', 'ingelo.php',
  'service-charter.php', 'tcp.php', 'vacancies.php', 'publications.php',
  'disclaimer.php', 'privacy.php', 'terms.php', 'faq.php',
  'Calibration.php', 'Standards.php'
];

const BASE = 'http://eswasa.test/';
const OUT_DIR = path.join(__dirname, '..', '.audit-screenshots', 'titles');
if (!fs.existsSync(OUT_DIR)) fs.mkdirSync(OUT_DIR, { recursive: true });
// clean prior runs
for (const f of fs.readdirSync(OUT_DIR)) {
  if (f.endsWith('.png')) fs.unlinkSync(path.join(OUT_DIR, f));
}

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1366, height: 900 }, deviceScaleFactor: 1 });
  const page = await ctx.newPage();

  for (const slug of PAGES) {
    await page.goto(BASE + slug, { waitUntil: 'domcontentloaded', timeout: 30000 });
    await page.waitForTimeout(500);

    // Find every visible h2 not in accordion/footer/slider
    const h2s = await page.evaluate(() => {
      const list = [];
      document.querySelectorAll('h2').forEach((h, i) => {
        if (h.closest('footer, #footer, .footer-area, .tp-caption, .rev_slider, .slider-area, .accordion-item')) return;
        if (h.classList.contains('accordion-header')) return;
        const r = h.getBoundingClientRect();
        if (r.width === 0 || r.height === 0) return;
        const cs = window.getComputedStyle(h);
        if (cs.visibility === 'hidden' || cs.display === 'none') return;
        const text = (h.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 60);
        if (!text) return;
        list.push({ idx: i, text });
      });
      return list;
    });

    console.log(`${slug}  -> ${h2s.length} h2 titles`);

    for (let i = 0; i < h2s.length; i++) {
      const { text } = h2s[i];
      try {
        // Find the visible h2 by text and screenshot a box around it + 100px below
        const handle = await page.evaluateHandle((needle) => {
          const arr = Array.from(document.querySelectorAll('h2'));
          return arr.find(h => (h.textContent || '').replace(/\s+/g, ' ').trim().startsWith(needle));
        }, text.slice(0, 40));
        if (!handle) continue;
        const el = handle.asElement();
        if (!el) continue;

        // Scroll into view, then compute a clip including ~80px below the h2
        await el.evaluate((node) => node.scrollIntoView({ block: 'center', behavior: 'instant' }));
        await page.waitForTimeout(150);
        const box = await el.boundingBox();
        if (!box) continue;
        const padTop = 20, padBottom = 80, padX = 40;
        const clip = {
          x: Math.max(0, box.x - padX),
          y: Math.max(0, box.y - padTop),
          width: Math.min(1366 - Math.max(0, box.x - padX), box.width + padX * 2),
          height: Math.min(900, box.height + padTop + padBottom)
        };
        const safeText = text.replace(/[^a-z0-9]+/gi, '_').slice(0, 40).toLowerCase();
        const fname = `${slug.replace('.php','')}__${String(i+1).padStart(2,'0')}__${safeText}.png`;
        await page.screenshot({ path: path.join(OUT_DIR, fname), clip });
      } catch (e) {
        console.log(`  ! ${text}: ${e.message}`);
      }
    }
  }

  await browser.close();
  console.log('\nScreenshots written to .audit-screenshots/titles/');
})();
