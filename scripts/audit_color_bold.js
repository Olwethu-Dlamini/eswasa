// Audit every visible text-bearing element across all pages for:
//   - Non-blue text color (anything not in the brand-blue family)
//   - Bold non-heading non-link text (font-weight >= 600 in <p>, <li>, <span>, <div>, <strong>, <b>, etc.)
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const PAGES = [
  'index.php', 'about-us.php', 'announcements.php', 'board.php',
  'Calibration.php', 'Certification.php', 'certification-status.php',
  'contact.php', 'contactcalibration.php', 'customer-feedback.php',
  'disclaimer.php', 'event-details.php', 'events.php', 'faq.php',
  'ingelo.php', 'managementsystems.php', 'Meetourteam.php', 'news.php',
  'policies.php', 'privacy.php', 'product.php', 'publications.php',
  'purchase.php', 'qoute.php', 'qoute_calibration.php',
  'qoute_certification.php', 'qoute_training.php', 'service-charter.php',
  'services.php', 'Standards.php', 'tcp.php', 'terms.php',
  'training-about.php', 'training-calendar.php', 'vacancies.php',
  'work.php'
];

const BASE = 'http://eswasa.test/';
const OUT_DIR = path.join(__dirname, '..', '.audit-screenshots');

function isBlueFamily(c) {
  // Accept rgb(43,51,136) and small variants, and rgba forms with that base.
  const m = c.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/);
  if (!m) return false;
  const r = +m[1], g = +m[2], b = +m[3];
  // accept anything in the navy-blue band
  if (b >= 100 && b <= 200 && r < 90 && g < 90) return true;
  // also accept the existing var --tg-body-color #39557E (57,85,126) — but we'll flag and replace it
  return false;
}

async function auditPage(page, url) {
  await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
  await page.waitForTimeout(400);

  return await page.evaluate(() => {
    function visible(el) {
      const r = el.getBoundingClientRect();
      if (r.width === 0 || r.height === 0) return false;
      const cs = window.getComputedStyle(el);
      return cs.visibility !== 'hidden' && cs.display !== 'none';
    }
    function selectorOf(el) {
      const parts = [];
      let cur = el; let d = 0;
      while (cur && cur.nodeType === 1 && d < 4) {
        let p = cur.tagName.toLowerCase();
        if (cur.id) { p += '#' + cur.id; parts.unshift(p); break; }
        if (cur.className && typeof cur.className === 'string') {
          const cls = cur.className.trim().split(/\s+/).slice(0, 2).join('.');
          if (cls) p += '.' + cls;
        }
        parts.unshift(p); cur = cur.parentElement; d++;
      }
      return parts.join(' > ');
    }

    const colors = [];
    const bolds = [];
    document.body.querySelectorAll('*').forEach(el => {
      if (['SCRIPT','STYLE','NOSCRIPT','SVG','PATH'].includes(el.tagName)) return;
      if (!visible(el)) return;
      let ownText = '';
      for (const n of el.childNodes) if (n.nodeType === 3) ownText += n.nodeValue;
      ownText = ownText.replace(/\s+/g, ' ').trim();
      if (!ownText) return;

      const cs = window.getComputedStyle(el);
      const tag = el.tagName.toLowerCase();
      const isHeading = /^h[1-6]$/.test(tag);
      const isLink = tag === 'a' || el.closest('a');
      const isFooter = el.closest('footer, #footer, .footer-area');

      // Color: skip footer (white-on-blue), skip links (can have brand variants), skip headings
      if (!isFooter && !isLink && !isHeading) {
        colors.push({ tag, color: cs.color, selector: selectorOf(el), text: ownText.slice(0, 60) });
      }

      // Bold: flag bold non-heading non-link non-footer text
      const w = cs.fontWeight;
      const numW = parseInt(w, 10) || (w === 'bold' ? 700 : 400);
      if (numW >= 600 && !isHeading && !isLink) {
        bolds.push({ tag, weight: w, selector: selectorOf(el), text: ownText.slice(0, 60) });
      }
    });

    return { colors, bolds };
  });
}

(async () => {
  const b = await chromium.launch();
  const ctx = await b.newContext({ viewport: { width: 1366, height: 900 } });
  const page = await ctx.newPage();
  const colorAgg = {};
  const boldAgg = {};
  const perPage = {};
  for (const slug of PAGES) {
    try {
      const r = await auditPage(page, BASE + slug);
      perPage[slug] = r;
      r.colors.forEach(c => {
        const key = `${c.color}|${c.selector}`;
        if (!colorAgg[key]) colorAgg[key] = { color: c.color, selector: c.selector, sample: c.text, pages: new Set(), count: 0 };
        colorAgg[key].pages.add(slug); colorAgg[key].count++;
      });
      r.bolds.forEach(bd => {
        const key = `${bd.tag}|${bd.weight}|${bd.selector}`;
        if (!boldAgg[key]) boldAgg[key] = { tag: bd.tag, weight: bd.weight, selector: bd.selector, sample: bd.text, pages: new Set(), count: 0 };
        boldAgg[key].pages.add(slug); boldAgg[key].count++;
      });
      console.log(`${slug.padEnd(28)}  colors=${r.colors.length}  bolds=${r.bolds.length}`);
    } catch (e) { console.log(`${slug}  FAILED  ${e.message}`); }
  }

  // Filter & sort
  function isBlueFamily(c) {
    const m = c.match(/rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/);
    if (!m) return false;
    const r = +m[1], g = +m[2], b = +m[3];
    return (b >= 100 && b <= 200 && r < 90 && g < 90);
  }

  const nonBlue = Object.values(colorAgg).filter(c => !isBlueFamily(c.color)).sort((a,b)=>b.count-a.count);
  const bolds = Object.values(boldAgg).sort((a,b)=>b.count-a.count);

  fs.writeFileSync(path.join(OUT_DIR, 'color-bold-audit.json'), JSON.stringify({
    nonBlue: nonBlue.map(c => ({...c, pages: [...c.pages]})),
    bolds:   bolds.map(b => ({...b, pages: [...b.pages]}))
  }, null, 2));

  console.log('\n=== Non-blue text colors (top 40) ===');
  nonBlue.slice(0, 40).forEach(c => {
    console.log(`  x${String(c.count).padStart(4)}  on ${c.pages.size}p  ${c.color.padEnd(28)}  ${c.selector}  | "${c.sample}"`);
  });

  console.log('\n=== Bold non-heading non-link text (top 40) ===');
  bolds.slice(0, 40).forEach(b => {
    console.log(`  ${b.tag.padEnd(8)} w=${String(b.weight).padStart(3)} x${String(b.count).padStart(4)}  on ${b.pages.size}p  ${b.selector}  | "${b.sample}"`);
  });

  await b.close();
})();
