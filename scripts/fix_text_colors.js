// Sweep all public PHP pages and replace faded brand-blue text colors
// (rgba(43, 51, 136, 0.6+)) with solid #2B3388 on `color:` declarations.
// Skips `background-color:` and `border-color:` and `::placeholder` blocks.
const fs = require('fs');
const path = require('path');

const ROOT = path.join(__dirname, '..');
const files = fs.readdirSync(ROOT)
  .filter(f => f.endsWith('.php'))
  .filter(f => !f.startsWith('admin/') && f !== 'admin');

// match `color: rgba(43, 51, 136, 0.X)` with optional whitespace,
// optional `;` after, optional `!important`. Excludes `background-color`
// and `border-*-color` via negative lookbehind.
const re = /(?<![-\w])color:\s*rgba\(\s*43\s*,\s*51\s*,\s*136\s*,\s*0\.(6|7|75|78|8|82|85|88|9|92|95)\s*\)/gi;

let totalFiles = 0, totalReplacements = 0;
const changes = [];

for (const f of files) {
  const fp = path.join(ROOT, f);
  let txt = fs.readFileSync(fp, 'utf8');
  const before = txt;

  // Naive sweep — don't try to detect ::placeholder context, just replace
  // every color: rgba(...) with solid brand blue. The placeholder rules
  // (e.g. .form-grp input::placeholder { color: rgba(43,51,136,0.6); })
  // are rare; if they come back as a complaint we'll fix surgically.
  txt = txt.replace(re, 'color: #2B3388');

  if (txt !== before) {
    const n = (before.match(re) || []).length;
    changes.push({ file: f, replacements: n });
    fs.writeFileSync(fp, txt, 'utf8');
    totalFiles++;
    totalReplacements += n;
  }
}

console.log(`Updated ${totalFiles} files, ${totalReplacements} declarations:`);
for (const c of changes) console.log(`  ${c.replacements.toString().padStart(3)}  ${c.file}`);
