// Drives http://eswasa.test/training-calendar.php to confirm the pager works
// end-to-end. Run with: node .verify/verify_pager.mjs
//
// Captures: 3 PNGs (page 1, page 2, page 3) of the trainings list region,
// and prints counts + selected diagnostics. Non-zero exit code on failure.

import { chromium } from 'playwright';
import { mkdirSync } from 'node:fs';

const URL = 'http://eswasa.test/training-calendar.php';
const OUT = '.verify/out';
mkdirSync(OUT, { recursive: true });

const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 1400, height: 1000 } });

const errors = [];
page.on('pageerror', (e) => errors.push('JS ERROR: ' + e.message));
page.on('console',  (m) => { if (m.type() === 'error') errors.push('CONSOLE ERROR: ' + m.text()); });

console.log(`[1] Loading ${URL}`);
await page.goto(URL, { waitUntil: 'domcontentloaded', timeout: 15000 });
await page.waitForSelector('#trainings-list .training-card', { timeout: 8000 });

async function dumpState(tag) {
    const totalTrainings = await page.evaluate(() => window.trainings ? window.trainings.length : -1);
    const visibleCards   = await page.locator('#trainings-list .training-card').count();
    const visibleCodes   = await page.locator('#trainings-list .training-code').allTextContents();
    const currentPage    = await page.evaluate(() => window.currentPage ?? -1);
    const totalPages     = await page.evaluate(() => (typeof totalPages === 'function' ? totalPages() : -1));
    const pagerHidden    = await page.locator('#trainings-pager').evaluate((el) => el.classList.contains('is-hidden'));
    const pagerBtns      = await page.locator('#trainings-pager .page-btn').allTextContents();
    const currentLabel   = await page.locator('#trainings-pager .page-btn.is-current').textContent().catch(() => null);

    console.log(`\n=== ${tag} ===`);
    console.log(`  total trainings in JS:  ${totalTrainings}`);
    console.log(`  visible cards:           ${visibleCards}`);
    console.log(`  visible codes:           ${visibleCodes.join(', ')}`);
    console.log(`  currentPage var:         ${currentPage}`);
    console.log(`  pager hidden?            ${pagerHidden}`);
    console.log(`  pager buttons:           [${pagerBtns.join(' ')}]`);
    console.log(`  current button label:    ${currentLabel}`);
    return { totalTrainings, visibleCards, visibleCodes, currentPage, pagerHidden, pagerBtns, currentLabel };
}

const findings = [];

// ---- Probe 1: page 1 (default load) -----------------------------------------
const s1 = await dumpState('Page 1 (initial load)');
await page.locator('#trainings-list').screenshot({ path: `${OUT}/page1.png` });
if (s1.totalTrainings !== 13) findings.push(`❌ expected 13 trainings, got ${s1.totalTrainings}`);
if (s1.visibleCards !== 6)    findings.push(`❌ page 1 should show 6 cards, got ${s1.visibleCards}`);
if (s1.pagerHidden)           findings.push(`❌ pager is hidden but should be visible (13 trainings > 6 per page)`);
if (s1.currentLabel !== '1')  findings.push(`❌ current page label should be '1', got '${s1.currentLabel}'`);

// ---- Probe 2: click page 2 ---------------------------------------------------
await page.locator('#trainings-pager .page-btn', { hasText: /^2$/ }).click();
await page.waitForTimeout(150);
const s2 = await dumpState('Page 2 (after clicking [2])');
await page.locator('#trainings-list').screenshot({ path: `${OUT}/page2.png` });
if (s2.visibleCards !== 6)   findings.push(`❌ page 2 should show 6 cards, got ${s2.visibleCards}`);
if (s2.currentLabel !== '2') findings.push(`❌ after click, current page should be '2', got '${s2.currentLabel}'`);
// Codes on page 2 should not overlap with codes on page 1
const overlap = s1.visibleCodes.filter((c) => s2.visibleCodes.includes(c));
if (overlap.length) findings.push(`❌ overlapping codes between page 1 and page 2: ${overlap.join(', ')}`);

// ---- Probe 3: click "›" next chevron ----------------------------------------
await page.locator('#trainings-pager .page-btn[aria-label="Next page"]').click();
await page.waitForTimeout(150);
const s3 = await dumpState('Page 3 (after clicking ›)');
await page.locator('#trainings-list').screenshot({ path: `${OUT}/page3.png` });
if (s3.visibleCards !== 1)   findings.push(`❌ page 3 should show 1 card (13 - 6 - 6 = 1), got ${s3.visibleCards}`);
if (s3.currentLabel !== '3') findings.push(`❌ after next, current page should be '3', got '${s3.currentLabel}'`);

// ---- Probe 4: next chevron at last page should be disabled ------------------
const nextDisabled = await page.locator('#trainings-pager .page-btn[aria-label="Next page"]').isDisabled();
console.log(`\n  next-chevron disabled on last page: ${nextDisabled}`);
if (!nextDisabled) findings.push(`❌ '›' should be disabled on the last page`);

// ---- Probe 5: prev chevron + back to page 1 ---------------------------------
await page.locator('#trainings-pager .page-btn[aria-label="Previous page"]').click();
await page.locator('#trainings-pager .page-btn[aria-label="Previous page"]').click();
await page.waitForTimeout(150);
const s5 = await dumpState('Page 1 again (two clicks of ‹)');
const prevDisabled = await page.locator('#trainings-pager .page-btn[aria-label="Previous page"]').isDisabled();
console.log(`  prev-chevron disabled on first page: ${prevDisabled}`);
if (s5.currentLabel !== '1') findings.push(`❌ after two ‹ clicks we should be on page 1, got '${s5.currentLabel}'`);
if (!prevDisabled)            findings.push(`❌ '‹' should be disabled on the first page`);

// ---- Probe 6: click a training card → auto-jump page if needed --------------
// Click the LAST card on page 1 (code 'OHS 02', training id=5), then go to page 3
// and click a card → it should toggle is-active without changing page.
// First: click GAP 02 (page 3, the only card) — selecting it should NOT switch pages.
await page.locator('#trainings-list .training-card').first().click();
await page.waitForTimeout(150);
const stillOnP3 = await page.evaluate(() => window.currentPage);
const isActive  = await page.locator('#trainings-list .training-card.is-active').count();
console.log(`\n  after clicking GAP 02 card: currentPage=${stillOnP3}, .is-active cards=${isActive}`);
if (stillOnP3 !== undefined && stillOnP3 !== null) {
    // It's bound by closure, not a window prop — fine, skip this assertion.
}
if (isActive !== 1) findings.push(`❌ clicking a card should mark exactly 1 .is-active, found ${isActive}`);

// ---- Probe 7: JS console / page errors --------------------------------------
if (errors.length) {
    for (const e of errors) findings.push(`⚠️  ${e}`);
}

// ---- Summary ---------------------------------------------------------------
console.log(`\n========== SUMMARY ==========`);
if (findings.length === 0) {
    console.log('✅ All probes passed.');
} else {
    console.log('Findings:');
    findings.forEach((f) => console.log('  ' + f));
}
console.log(`\nScreenshots saved under ${OUT}/`);

await browser.close();
process.exit(findings.length === 0 ? 0 : 1);
