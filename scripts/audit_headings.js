// Playwright audit: enumerate every visible heading (h1-h6) + canonical
// subtitle/lead classes across all public pages, and flag off-spec sizes.
//
// Canonical ESWASA typography:
//   - Section title (h2 inside main content)  : 32px
//   - Section subtitle / .section-subtitle / .sub-title / .lead : 16px (subtitle pattern)
//   - h1 (page hero/banner title)             : context-specific (allow 28-64px)
//   - h3                                       : 24-28px
//   - h4                                       : 18-22px
//   - h5                                       : 16-20px
//   - h6                                       : 15-16px
//
// Writes report to .audit-screenshots/headings-audit.json
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

async function auditPage(page, url) {
  await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
  await page.waitForTimeout(400);

  return await page.evaluate(() => {
    const headings = [];
    const subtitles = [];

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

    function visible(el) {
      const rect = el.getBoundingClientRect();
      if (rect.width === 0 || rect.height === 0) return false;
      const cs = window.getComputedStyle(el);
      if (cs.visibility === 'hidden' || cs.display === 'none') return false;
      return true;
    }

    function ownText(el) {
      let t = '';
      for (const n of el.childNodes) if (n.nodeType === 3) t += n.nodeValue;
      // also pull text of inline children (links/spans/em) so headings still read
      t = el.textContent || t;
      return t.replace(/\s+/g, ' ').trim();
    }

    // Headings
    const hs = document.querySelectorAll('h1,h2,h3,h4,h5,h6');
    hs.forEach(el => {
      if (!visible(el)) return;
      const txt = ownText(el).slice(0, 80);
      if (!txt) return;
      const cs = window.getComputedStyle(el);
      headings.push({
        tag: el.tagName.toLowerCase(),
        selector: selectorOf(el),
        fontSize: cs.fontSize,
        fontWeight: cs.fontWeight,
        text: txt
      });
    });

    // Subtitles / lead text
    const subSelectors = [
      '.section-subtitle', '.sub-title', '.lead',
      '.section__title .sub-title', '.section__title .desc',
      '.section-heading p', '.main_title p',
      '.cta-subtitle', '.intro-subtitle'
    ];
    document.querySelectorAll(subSelectors.join(',')).forEach(el => {
      if (!visible(el)) return;
      const txt = ownText(el).slice(0, 80);
      if (!txt) return;
      const cs = window.getComputedStyle(el);
      subtitles.push({
        tag: el.tagName.toLowerCase(),
        selector: selectorOf(el),
        fontSize: cs.fontSize,
        text: txt
      });
    });

    return { headings, subtitles };
  });
}

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1366, height: 900 } });
  const page = await ctx.newPage();

  const ALLOWED = {
    h1: ['32px', '40px', '48px', '50px'],
    h2: ['32px'],
    h3: ['24px', '26px', '28px'],
    h4: ['18px', '20px', '22px'],
    h5: ['16px', '18px'],
    h6: ['15px', '16px']
  };

  const flaggedHeadings = {};  // page -> [off-spec headings]
  const flaggedSubtitles = {}; // page -> [off-spec subtitles]
  const allHeadings = {};      // size -> count
  const allSubtitles = {};     // size -> count

  for (const slug of PAGES) {
    const url = BASE + slug;
    try {
      const { headings, subtitles } = await auditPage(page, url);
      flaggedHeadings[slug] = [];
      flaggedSubtitles[slug] = [];
      for (const h of headings) {
        const ok = (ALLOWED[h.tag] || []).includes(h.fontSize);
        const key = `${h.tag}:${h.fontSize}`;
        allHeadings[key] = (allHeadings[key] || 0) + 1;
        if (!ok) flaggedHeadings[slug].push(h);
      }
      for (const s of subtitles) {
        const ok = (s.fontSize === '16px' || s.fontSize === '15px');
        const key = `${s.tag}:${s.fontSize}`;
        allSubtitles[key] = (allSubtitles[key] || 0) + 1;
        if (!ok) flaggedSubtitles[slug].push(s);
      }
      const hOff = flaggedHeadings[slug].length;
      const sOff = flaggedSubtitles[slug].length;
      console.log(`${slug.padEnd(28)}  headings_off=${hOff}  subtitles_off=${sOff}  (${headings.length}h / ${subtitles.length}sub)`);
    } catch (e) {
      console.log(`${slug}  FAILED  ${e.message}`);
    }
  }

  const outFile = path.join(OUT_DIR, 'headings-audit.json');
  fs.writeFileSync(outFile, JSON.stringify({ flaggedHeadings, flaggedSubtitles, allHeadings, allSubtitles }, null, 2));
  console.log('\nWrote ' + outFile);

  console.log('\nGlobal heading-size buckets:');
  Object.entries(allHeadings).sort((a,b)=>b[1]-a[1]).forEach(([k,v]) => console.log(`  ${k.padEnd(14)} x${v}`));

  console.log('\nGlobal subtitle-size buckets:');
  Object.entries(allSubtitles).sort((a,b)=>b[1]-a[1]).forEach(([k,v]) => console.log(`  ${k.padEnd(14)} x${v}`));

  // Aggregate off-spec headings by (tag,size,class)
  const offAgg = {};
  for (const [slug, arr] of Object.entries(flaggedHeadings)) {
    for (const h of arr) {
      const key = `${h.tag} @ ${h.fontSize}  ${h.selector}`;
      if (!offAgg[key]) offAgg[key] = { tag: h.tag, fontSize: h.fontSize, selector: h.selector, sample: h.text, pages: new Set(), count: 0 };
      offAgg[key].pages.add(slug); offAgg[key].count++;
    }
  }
  const offRows = Object.values(offAgg).map(o => ({
    tag: o.tag, fontSize: o.fontSize, selector: o.selector, count: o.count,
    pageCount: o.pages.size, sample: o.sample
  })).sort((a,b)=>b.count-a.count);

  console.log('\nTop off-spec heading patterns:');
  offRows.slice(0, 40).forEach(r => {
    console.log(`  ${r.tag} ${r.fontSize.padEnd(8)} x${String(r.count).padStart(3)}  on ${r.pageCount}p  ${r.selector}  | "${r.sample}"`);
  });

  // Aggregate off-spec subtitles
  const subAgg = {};
  for (const [slug, arr] of Object.entries(flaggedSubtitles)) {
    for (const s of arr) {
      const key = `${s.fontSize}  ${s.selector}`;
      if (!subAgg[key]) subAgg[key] = { fontSize: s.fontSize, selector: s.selector, sample: s.text, pages: new Set(), count: 0 };
      subAgg[key].pages.add(slug); subAgg[key].count++;
    }
  }
  const subRows = Object.values(subAgg).map(o => ({
    fontSize: o.fontSize, selector: o.selector, count: o.count,
    pageCount: o.pages.size, sample: o.sample
  })).sort((a,b)=>b.count-a.count);

  console.log('\nOff-spec subtitle patterns:');
  subRows.slice(0, 40).forEach(r => {
    console.log(`  ${r.fontSize.padEnd(8)} x${String(r.count).padStart(3)}  on ${r.pageCount}p  ${r.selector}  | "${r.sample}"`);
  });

  await browser.close();
})();
