// Playwright audit: find every visible body-text element whose computed
// font-size is NOT 15px. Writes a JSON report to .audit-screenshots/text-size-audit.json
// Usage:  node scripts/audit_text_size.js  [pageList.txt]
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
if (!fs.existsSync(OUT_DIR)) fs.mkdirSync(OUT_DIR, { recursive: true });

// Tags that are intentionally non-body-size (headings, branding, controls).
const SKIP_TAGS = new Set([
  'H1','H2','H3','H4','H5','H6',
  'STYLE','SCRIPT','NOSCRIPT','SVG','PATH','CODE','PRE',
  'BUTTON','INPUT','TEXTAREA','SELECT','OPTION','LABEL'
]);

async function auditPage(page, url) {
  await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
  // give a moment for webfonts/CSS to settle
  await page.waitForTimeout(400);

  return await page.evaluate((skipTagsArr) => {
    const SKIP = new Set(skipTagsArr);
    const offenders = [];
    const counts = {};   // fontSize -> count
    const samples = {};  // selectorKey -> {fontSize, sampleText, count}

    function selectorOf(el) {
      const parts = [];
      let cur = el;
      let depth = 0;
      while (cur && cur.nodeType === 1 && depth < 4) {
        let p = cur.tagName.toLowerCase();
        if (cur.id) { p += '#' + cur.id; parts.unshift(p); break; }
        if (cur.className && typeof cur.className === 'string') {
          const cls = cur.className.trim().split(/\s+/).slice(0, 2).join('.');
          if (cls) p += '.' + cls;
        }
        parts.unshift(p);
        cur = cur.parentElement;
        depth++;
      }
      return parts.join(' > ');
    }

    const all = document.body ? document.body.querySelectorAll('*') : [];
    all.forEach(el => {
      if (SKIP.has(el.tagName)) return;
      // visibility check
      const rect = el.getBoundingClientRect();
      if (rect.width === 0 || rect.height === 0) return;
      const cs = window.getComputedStyle(el);
      if (cs.visibility === 'hidden' || cs.display === 'none') return;

      // direct text content (own text nodes, not descendants)
      let ownText = '';
      for (const n of el.childNodes) {
        if (n.nodeType === 3) ownText += n.nodeValue;
      }
      ownText = ownText.replace(/\s+/g, ' ').trim();
      if (!ownText || ownText.length < 3) return;

      const fs = cs.fontSize;
      if (fs === '15px') return; // good
      // We care about anything that's body copy and not 15px.
      // Don't flag values < 14px (small print) or > 20px (clearly heading-ish).
      const px = parseFloat(fs);
      if (px < 14 || px > 20) return;

      offenders.push({
        tag: el.tagName.toLowerCase(),
        selector: selectorOf(el),
        fontSize: fs,
        text: ownText.slice(0, 80)
      });
      counts[fs] = (counts[fs] || 0) + 1;
      const key = selectorOf(el) + '|' + fs;
      if (!samples[key]) samples[key] = { fontSize: fs, selector: selectorOf(el), count: 0, sample: ownText.slice(0, 80) };
      samples[key].count++;
    });

    return { offenders, counts, samples: Object.values(samples) };
  }, [...SKIP_TAGS]);
}

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1366, height: 900 } });
  const page = await ctx.newPage();
  const report = {};
  const globalSelectorAgg = {};

  for (const slug of PAGES) {
    const url = BASE + slug;
    try {
      const res = await auditPage(page, url);
      report[slug] = res;
      console.log(`${slug.padEnd(28)}  offenders=${res.offenders.length}  buckets=${JSON.stringify(res.counts)}`);
      for (const s of res.samples) {
        const key = s.selector + ' :: ' + s.fontSize;
        if (!globalSelectorAgg[key]) globalSelectorAgg[key] = { selector: s.selector, fontSize: s.fontSize, count: 0, sample: s.sample, pages: new Set() };
        globalSelectorAgg[key].count += s.count;
        globalSelectorAgg[key].pages.add(slug);
      }
    } catch (e) {
      console.log(`${slug}  FAILED  ${e.message}`);
      report[slug] = { error: String(e.message) };
    }
  }

  // Sort global aggregate by count desc
  const flat = Object.values(globalSelectorAgg).map(o => ({
    selector: o.selector, fontSize: o.fontSize, totalCount: o.count,
    pageCount: o.pages.size, sample: o.sample
  })).sort((a, b) => b.totalCount - a.totalCount);

  const outFile = path.join(OUT_DIR, 'text-size-audit.json');
  fs.writeFileSync(outFile, JSON.stringify({ perPage: report, aggregate: flat }, null, 2));
  console.log('\nWrote ' + outFile);

  console.log('\nTop 40 offenders (selector :: size, count, pages):');
  flat.slice(0, 40).forEach(r => {
    console.log(`  ${r.fontSize}  x${String(r.totalCount).padStart(4)}  on ${r.pageCount} pages  ${r.selector}  | "${r.sample}"`);
  });

  await browser.close();
})();
