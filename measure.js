const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage({ viewport: { width: 1920, height: 1080 } });
    await page.goto('http://localhost/eswasa/index.php');
    await page.waitForTimeout(2000);

    const cards = await page.$$eval('.coursesSlider .swiper-slide .blog__post-item', els =>
        els.map((el, i) => {
            const rect = el.getBoundingClientRect();
            return { card: i+1, width: Math.round(rect.width), height: Math.round(rect.height), visible: rect.width > 0 };
        })
    );
    console.log('CARD DIMENSIONS:');
    cards.forEach(c => console.log('Card ' + c.card + ': ' + c.width + 'x' + c.height + ' visible=' + c.visible));

    const section = await page.$eval('.courses-area', el => {
        const rect = el.getBoundingClientRect();
        const style = getComputedStyle(el);
        return {
            totalHeight: Math.round(rect.height),
            paddingTop: style.paddingTop,
            paddingBottom: style.paddingBottom
        };
    });
    console.log('SECTION: height=' + section.totalHeight + ' pt=' + section.paddingTop + ' pb=' + section.paddingBottom);

    const titleWrap = await page.$eval('.courses-area .section__title-wrap', el => {
        const style = getComputedStyle(el);
        return { marginBottom: style.marginBottom, height: Math.round(el.getBoundingClientRect().height) };
    });
    console.log('TITLE WRAP: height=' + titleWrap.height + ' mb=' + titleWrap.marginBottom);

    await browser.close();
})();
