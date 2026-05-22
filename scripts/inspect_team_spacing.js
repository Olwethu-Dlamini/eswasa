const { chromium } = require('playwright');
(async () => {
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 1366, height: 900 } });
  await p.goto('http://eswasa.test/Meetourteam.php', { waitUntil: 'domcontentloaded' });
  await p.waitForTimeout(700);

  const data = await p.evaluate(() => {
    const sections = [];
    document.querySelectorAll('h2.section-title').forEach(h => {
      const r = h.getBoundingClientRect();
      const divider = h.nextElementSibling && h.nextElementSibling.classList.contains('section-divider') ? h.nextElementSibling : null;
      const dRect = divider ? divider.getBoundingClientRect() : null;
      let firstCard = null;
      let cur = h.parentElement;
      while (cur && !firstCard) {
        firstCard = cur.querySelector('.team-card, .staff-photo-container, .eswasa-staff-content');
        cur = cur.nextElementSibling;
      }
      const fRect = firstCard ? firstCard.getBoundingClientRect() : null;

      // CSS spacing values
      const cs = getComputedStyle(h);
      const dcs = divider ? getComputedStyle(divider) : null;
      const fcs = firstCard ? getComputedStyle(firstCard) : null;
      sections.push({
        title: h.textContent.trim(),
        h2_margin: { top: cs.marginTop, bottom: cs.marginBottom, padding: cs.padding },
        divider_margin: dcs ? { top: dcs.marginTop, bottom: dcs.marginBottom } : null,
        first_card_margin: fcs ? { top: fcs.marginTop, bottom: fcs.marginBottom } : null,
        gap_px: fRect && dRect ? Math.round(fRect.top - dRect.bottom) : (fRect ? Math.round(fRect.top - r.bottom) : null),
        first_card_class: firstCard ? firstCard.className : null
      });
    });
    return sections;
  });
  console.log(JSON.stringify(data, null, 2));

  const titles = await p.$$('h2.section-title');
  for (let i = 0; i < titles.length; i++) {
    await titles[i].evaluate(n => n.scrollIntoView({ block: 'start' }));
    await p.waitForTimeout(250);
    const box = await titles[i].boundingBox();
    await p.screenshot({ path: '.audit-screenshots/_team_sec_'+i+'.png', clip: { x: 0, y: Math.max(0, box.y - 30), width: 1366, height: 700 } });
  }
  await b.close();
})();
