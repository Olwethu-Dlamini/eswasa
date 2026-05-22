// Audit the index page's text styling to understand the reference:
//   - What's blue (exact color values)?
//   - What's bold (font-weights other than 400)?
const { chromium } = require('playwright');

(async () => {
  const b = await chromium.launch();
  const p = await b.newPage({ viewport: { width: 1366, height: 900 } });
  await p.goto('http://eswasa.test/index.php', { waitUntil: 'domcontentloaded' });
  await p.waitForTimeout(600);

  const data = await p.evaluate(() => {
    const colorCount = {};
    const weightByTag = {};
    const boldNonHeadingExamples = [];

    function visible(el) {
      const r = el.getBoundingClientRect();
      if (r.width === 0 || r.height === 0) return false;
      const cs = window.getComputedStyle(el);
      return cs.visibility !== 'hidden' && cs.display !== 'none';
    }

    document.body.querySelectorAll('*').forEach(el => {
      if (['SCRIPT','STYLE','NOSCRIPT','SVG','PATH'].includes(el.tagName)) return;
      if (!visible(el)) return;
      // only count text-bearing elements (own text node)
      let ownText = '';
      for (const n of el.childNodes) if (n.nodeType === 3) ownText += n.nodeValue;
      ownText = ownText.replace(/\s+/g, ' ').trim();
      if (!ownText) return;

      const cs = window.getComputedStyle(el);
      const c = cs.color;
      const w = cs.fontWeight;
      colorCount[c] = (colorCount[c] || 0) + 1;

      const tag = el.tagName.toLowerCase();
      if (!weightByTag[tag]) weightByTag[tag] = {};
      weightByTag[tag][w] = (weightByTag[tag][w] || 0) + 1;

      // Find non-heading non-link bold text
      const isHeading = /^h[1-6]$/.test(tag);
      const isLink = tag === 'a' || el.closest('a');
      const numW = parseInt(w, 10) || (w === 'bold' ? 700 : 400);
      if (numW >= 600 && !isHeading && !isLink) {
        if (boldNonHeadingExamples.length < 30) {
          boldNonHeadingExamples.push({ tag, weight: w, text: ownText.slice(0, 70) });
        }
      }
    });

    return { colorCount, weightByTag, boldNonHeadingExamples };
  });
  await b.close();

  console.log('=== Top text colors on index.php ===');
  Object.entries(data.colorCount).sort((a,b)=>b[1]-a[1]).slice(0,12).forEach(([c,n]) => {
    console.log(`  x${String(n).padStart(4)}  ${c}`);
  });

  console.log('\n=== Font weights by tag ===');
  for (const [tag, weights] of Object.entries(data.weightByTag)) {
    const parts = Object.entries(weights).map(([w,n]) => `${w}:${n}`).join('  ');
    console.log(`  ${tag.padEnd(8)} ${parts}`);
  }

  console.log('\n=== Bold non-heading non-link examples on index ===');
  data.boldNonHeadingExamples.forEach(e => {
    console.log(`  <${e.tag}> w=${e.weight}  "${e.text}"`);
  });
})();
