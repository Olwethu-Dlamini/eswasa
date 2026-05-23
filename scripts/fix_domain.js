// Sweep all PHP files: replace `swasa.co.sz` with `eswasa.co.sz`,
// only where it stands alone (preceded by non-word char like @ . / : <space>).
// Uses \b word boundary so `eswasa.co.sz` is untouched.
const fs = require('fs');
const path = require('path');

function walk(dir, files = []) {
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    if (entry.isDirectory()) {
      if (['node_modules', '.git', 'rs-plugin'].includes(entry.name)) continue;
      walk(path.join(dir, entry.name), files);
    } else if (entry.name.endsWith('.php')) {
      files.push(path.join(dir, entry.name));
    }
  }
  return files;
}

const ROOT = path.join(__dirname, '..');
const files = walk(ROOT);
const re = /\bswasa\.co\.sz/g;

let total = 0;
const changes = [];
for (const fp of files) {
  const before = fs.readFileSync(fp, 'utf8');
  const after = before.replace(re, 'eswasa.co.sz');
  if (after !== before) {
    const n = (before.match(re) || []).length;
    fs.writeFileSync(fp, after, 'utf8');
    changes.push({ file: path.relative(ROOT, fp), count: n });
    total += n;
  }
}
console.log(`Replaced ${total} occurrences across ${changes.length} files:`);
for (const c of changes) console.log(`  ${String(c.count).padStart(3)}  ${c.file}`);
