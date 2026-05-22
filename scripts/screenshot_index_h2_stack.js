// Tighter wider screenshots of all 3 index.php section titles, side-by-side.
const { chromium } = require('playwright');
const path = require('path');

(async () => {
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 1366, height: 900 } });
  await p.goto('http://eswasa.test/index.php', { waitUntil: 'domcontentloaded' });
  await p.waitForTimeout(800);
  // Disable browser cache for safety
  await p.context().clearCookies();

  const sections = [
    { needle: 'Discover',          file: '_idx_discover.png' },
    { needle: 'Certification Marks', file: '_idx_certmarks.png' },
    { needle: 'Our Affiliations',  file: '_idx_affiliations.png' }
  ];

  for (const s of sections) {
    const handle = await p.evaluateHandle((needle) => {
      const arr = Array.from(document.querySelectorAll('h2'));
      return arr.find(h => h.textContent.includes(needle));
    }, s.needle);
    const el = handle.asElement();
    if (!el) { console.log('NOT FOUND: ' + s.needle); continue; }
    await el.evaluate((n) => n.scrollIntoView({ block: 'center' }));
    await p.waitForTimeout(200);
    const box = await el.boundingBox();
    const clip = {
      x: 0,
      y: Math.max(0, box.y - 30),
      width: 1366,
      height: 160
    };
    await p.screenshot({ path: path.join('.audit-screenshots/titles', s.file), clip });
    console.log('  ' + s.needle + ' -> ' + s.file);
  }
  await b.close();
})();
