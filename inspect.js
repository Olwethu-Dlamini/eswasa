const { chromium } = require('playwright');
(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage({ viewport: { width: 1920, height: 1080 } });
    await page.goto('http://localhost/eswasa/index.php');
    await page.waitForTimeout(2000);

    // Screenshot just the Discover section zoomed in
    const section = await page.$('.courses-area');
    await section.screenshot({ path: 'screenshot_cards_closeup.png' });

    // Get card styles
    const cardStyles = await page.$$eval('.coursesSlider .swiper-slide .blog__post-item', els =>
        els.map((el, i) => {
            const style = getComputedStyle(el);
            const rect = el.getBoundingClientRect();
            return {
                card: i + 1,
                width: Math.round(rect.width),
                height: Math.round(rect.height),
                borderRadius: style.borderRadius,
                boxShadow: style.boxShadow,
                background: style.background,
                marginBottom: style.marginBottom,
                overflow: style.overflow
            };
        })
    );
    console.log('CARD STYLES:');
    cardStyles.forEach(c => console.log(JSON.stringify(c)));

    // Get content styles
    const contentStyles = await page.$$eval('.coursesSlider .swiper-slide .blog__post-content', els =>
        els.map((el, i) => {
            const style = getComputedStyle(el);
            const rect = el.getBoundingClientRect();
            return {
                card: i + 1,
                width: Math.round(rect.width),
                height: Math.round(rect.height),
                padding: style.padding
            };
        })
    );
    console.log('CONTENT STYLES:');
    contentStyles.forEach(c => console.log(JSON.stringify(c)));

    // Check section overflow
    const sectionStyle = await page.$eval('.courses-area', el => {
        const style = getComputedStyle(el);
        return {
            overflow: style.overflow,
            height: Math.round(el.getBoundingClientRect().height),
            paddingTop: style.paddingTop,
            paddingBottom: style.paddingBottom
        };
    });
    console.log('SECTION:', JSON.stringify(sectionStyle));

    await browser.close();
})();
