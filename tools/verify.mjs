import fs from 'node:fs';
import path from 'node:path';
import assert from 'node:assert/strict';
import {execFileSync} from 'node:child_process';
const root=path.resolve('docs');
const manifest=JSON.parse(fs.readFileSync(path.join(root,'export-manifest.json'),'utf8'));
let links=0;
for(const route of manifest.pages){
  const file=path.join(root,route,'index.html');
  const html=fs.readFileSync(file,'utf8');
  assert.match(html,/<html lang="ru-RU"/,'Russian language metadata');
  assert.equal((html.match(/<h1\b/g)||[]).length,1,`One H1: ${route}`);
  assert(!/127\.0\.0\.1|localhost|\.local\/|wp-config\.php/.test(html),`Local paths in ${route}`);
  assert(!/Fatal error|Warning:|Deprecated:|Parse error/.test(html),`PHP errors in ${route}`);
  assert.match(html,/noindex/,'Demo must remain noindex');
  for(const match of html.matchAll(/(?:href|src)=["']([^"']+)["']/g)){
    const href=match[1].replaceAll('&#038;','&');
    if(!href.startsWith(manifest.base))continue;
    const url=new URL(href,'https://fedinartemii.github.io');
    const local=path.join(root,decodeURIComponent(url.pathname.slice(manifest.base.length)));
    const dest=fs.existsSync(local)&&fs.statSync(local).isDirectory()?path.join(local,'index.html'):local;
    assert(fs.existsSync(dest),`Broken resource ${href} in ${route}`);links++;
    if(url.hash && dest.endsWith('.html')){
      const target=fs.readFileSync(dest,'utf8');
      assert(url.hash==='#consultation'||target.includes(`id="${url.hash.slice(1)}"`),`Missing anchor ${href}`);
    }
  }
  for(const match of html.matchAll(/<img\b[^>]*>/g))assert(/\balt=/.test(match[0]),`Missing image alt in ${route}`);
}
const css=fs.readFileSync(path.join(root,'assets/site.css'),'utf8');
for(const match of css.matchAll(/url\(['"]?([^)'"\s]+)['"]?\)/g))assert(fs.existsSync(path.join(root,'assets',match[1])),`Missing CSS asset: ${match[1]}`);
for(const dir of ['theme/zabota74','tools'])for(const file of fs.readdirSync(dir,{recursive:true}).filter(f=>f.endsWith('.php'))){
 execFileSync(process.env.PHP_BIN||'php',['-l',path.join(dir,file)],{stdio:'pipe'});
}
assert(!fs.existsSync(path.join(root,'wp-config.php')),'No WP secrets in public export');
console.log(`PASS: ${manifest.pages.length} pages, ${links} local links/assets, Russian metadata, headings, image alts, CSS fonts, PHP syntax, no local paths.`);
