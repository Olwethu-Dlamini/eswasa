// Playwright audit: for every visible h2 section title, check whether it is
// followed by (or contains) a `.section-divider` element. Flag titles missing
// the divider. Writes report to .audit-screenshots/divider-audit.json
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

    // Only check h2 (canonical "section title" — h1 is page hero,
    // h3-h6 are sub-section headings).
    const results = [];
    document.querySelectorAll('h2').forEach(h => {
      if (!visible(h)) return;
      const text = (h.textContent || '').replace(/\s+/g, ' ').trim().slice(0, 80);
      if (!text) return;
      const fs = window.getComputedStyle(h).fontSize;

      // Skip clearly non-section titles: footer h2, slider captions, accordion items
      const inFooter = h.closest('footer, #footer, .footer-area');
      const inSlider = h.closest('.tp-caption, .rev_slider, .slider-area, .swiper, .slick-slider');
      const inAccordion = h.closest('.accordion-item') || h.classList.contains('accordion-header');
      if (inFooter || inSlider || inAccordion) return;

      // Accept a ::after/::before pseudo-underline as a valid divider too
      // (e.g. .cta-title::after with width ≥ 40 looks like our 60×2 brand line).
      const pseudoHasUnderline = (el) => {
        for (const pseudo of ['::after', '::before']) {
          const cs = window.getComputedStyle(el, pseudo);
          if (!cs || cs.content === 'none') continue;
          const w = parseFloat(cs.width);
          const h2px = parseFloat(cs.height);
          if (cs.content !== '' && w >= 40 && w <= 120 && h2px > 0 && h2px <= 6) return true;
        }
        return false;
      };

      // Look for .section-divider nearby OR a ::after/::before pseudo underline:
      //   - h2 itself with ::after/::before
      //   - inside h2
      //   - immediately next sibling
      //   - inside the same parent within first ~3 children after h2
      //   - inside an .section-heading / .main_title / .team-header / .section-title wrapper
      let foundDivider = false;
      if (pseudoHasUnderline(h)) foundDivider = true;
      if (!foundDivider && h.querySelector('.section-divider')) foundDivider = true;
      if (!foundDivider) {
        let sib = h.nextElementSibling, hops = 0;
        while (sib && hops < 3) {
          if (sib.matches('.section-divider') || sib.querySelector('.section-divider')) { foundDivider = true; break; }
          sib = sib.nextElementSibling; hops++;
        }
      }
      if (!foundDivider) {
        const parent = h.parentElement;
        if (parent && parent.querySelector('.section-divider')) {
          // confirm divider is within reasonable distance (same wrapper)
          foundDivider = true;
        }
      }

      results.push({
        text, fontSize: fs, selector: selectorOf(h), hasDivider: foundDivider
      });
    });
    return results;
  });
}

(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1366, height: 900 } });
  const page = await ctx.newPage();
  const report = {};
  let totalMissing = 0;
  for (const slug of PAGES) {
    try {
      const rows = await auditPage(page, BASE + slug);
      report[slug] = rows;
      const missing = rows.filter(r => !r.hasDivider);
      totalMissing += missing.length;
      console.log(`${slug.padEnd(28)}  h2_total=${rows.length}  missing_divider=${missing.length}`);
      missing.forEach(m => console.log(`    [no-divider]  ${m.fontSize.padEnd(7)} ${m.selector}  | "${m.text}"`));
    } catch (e) {
      console.log(`${slug}  FAILED  ${e.message}`);
    }
  }
  console.log(`\nTotal h2 titles missing divider: ${totalMissing}`);
  fs.writeFileSync(path.join(OUT_DIR, 'divider-audit.json'), JSON.stringify(report, null, 2));
  await browser.close();
})();
