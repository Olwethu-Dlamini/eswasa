// Capture every h2 on index.php and about-us.php with their computed font-size,
// weight, color and a screenshot. We want to know why they look different.
const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const OUT = path.join(__dirname, '..', '.audit-screenshots', 'compare');
if (!fs.existsSync(OUT)) fs.mkdirSync(OUT, { recursive: true });
for (const f of fs.readdirSync(OUT)) if (f.endsWith('.png')) fs.unlinkSync(path.join(OUT, f));

(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({ viewport: { width: 1366, height: 900 } });
  const page = await ctx.newPage();

  for (const slug of ['index.php', 'about-us.php']) {
    await page.goto('http://eswasa.test/' + slug, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(500);

    const h2s = await page.evaluate(() => {
      const results = [];
      document.querySelectorAll('h2').forEach((h, i) => {
        if (h.closest('footer, .footer-area, .tp-caption, .accordion-item')) return;
        if (h.classList.contains('accordion-header')) return;
        const r = h.getBoundingClientRect();
        if (r.width === 0) return;
        const cs = window.getComputedStyle(h);
        const text = (h.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 50);
        if (!text) return;
        const parentCls = (h.parentElement && h.parentElement.className) || '';
        results.push({
          idx: i, text, fontSize: cs.fontSize, fontWeight: cs.fontWeight,
          color: cs.color, classes: h.className, parentClasses: parentCls
        });
      });
      return results;
    });

    console.log('=== ' + slug + ' ===');
    h2s.forEach((h, n) => {
      console.log(`  [${n+1}] "${h.text}"`);
      console.log(`       size=${h.fontSize}  weight=${h.fontWeight}  color=${h.color}`);
      console.log(`       h2.class="${h.classes}"  parent.class="${h.parentClasses}"`);
    });

    // screenshot each h2 area
    for (let n = 0; n < h2s.length; n++) {
      const idx = h2s[n].idx;
      const handle = await page.evaluateHandle((idx) => {
        return document.querySelectorAll('h2')[idx];
      }, idx);
      const el = handle.asElement(); if (!el) continue;
      await el.evaluate(node => node.scrollIntoView({ block: 'center' }));
      await page.waitForTimeout(150);
      const box = await el.boundingBox();
      const safeText = h2s[n].text.replace(/[^a-z0-9]+/gi, '_').slice(0, 25).toLowerCase();
      const file = `${slug.replace('.php','')}__${String(n+1).padStart(2,'0')}__${safeText}.png`;
      await page.screenshot({
        path: path.join(OUT, file),
        clip: { x: 0, y: Math.max(0, box.y - 30), width: 1366, height: 160 }
      });
    }
  }
  await b.close();
  console.log('\nScreenshots: ' + OUT);
})();
