import fs from 'node:fs';
import path from 'node:path';
const origin = process.env.WP_ORIGIN || 'http://127.0.0.1:8074';
const base = '/' + (process.env.PAGES_BASE || 'zabotaa74').replace(/^\/+|\/+$/g,'') + '/';
const publicOrigin = 'https://fedinartemii.github.io';
const out=path.resolve('docs');
fs.mkdirSync(out,{recursive:true});
fs.cpSync('theme/zabota74/assets',path.join(out,'assets'),{recursive:true});
// Export from the running WordPress, including any edits made in the admin editor.
const queue=['/'];const visited=new Set();
while(queue.length){
  const route=queue.shift();if(visited.has(route))continue;visited.add(route);
  const response=await fetch(origin+route,{signal:AbortSignal.timeout(20000)});
  if(!response.ok)throw Error(`${route}: HTTP ${response.status}`);
  let html=await response.text();
  for(const match of html.matchAll(/href=["']([^"']+)["']/g)){
    const url=new URL(match[1].replaceAll('&#038;','&'),origin+route);
    if(url.origin!==origin || url.search || url.pathname.startsWith('/wp-') || /\.[a-z\d]{2,5}$/i.test(url.pathname) || url.pathname==='/xmlrpc.php')continue;
    if(!visited.has(url.pathname)&&!queue.includes(url.pathname))queue.push(url.pathname);
  }
  html=html.replace(/<link[^>]+rel=['"](?:EditURI|shortlink)['"][^>]*>/gi,'');
  html=html.replaceAll(`${origin}/wp-content/themes/zabota74/assets/`,`${base}assets/`);
  html=html.replaceAll(origin+'/',base);
  html=html.replace(/(<link[^>]+rel=['"]canonical['"][^>]+href=['"])([^'"]+)/gi,(_,prefix,url)=>prefix+publicOrigin+url);
  const dest=path.join(out,route.replace(/^\//,''),'index.html');
  fs.mkdirSync(path.dirname(dest),{recursive:true});fs.writeFileSync(dest,html);
  console.log(`Exported ${route}`);
}
let notFound=await (await fetch(origin+'/missing-page-for-static-export/')).text();
notFound=notFound.replaceAll(`${origin}/wp-content/themes/zabota74/assets/`,`${base}assets/`).replaceAll(origin+'/',base);
notFound=notFound.replace(/<link[^>]+rel=['"](?:EditURI|shortlink)['"][^>]*>/gi,'');
fs.writeFileSync(path.join(out,'404.html'),notFound);
fs.writeFileSync(path.join(out,'.nojekyll'),'');
fs.writeFileSync(path.join(out,'robots.txt'),'User-agent: *\nDisallow: /\n');
fs.writeFileSync(path.join(out,'export-manifest.json'),JSON.stringify({source:'Local WordPress',base,pages:[...visited]},null,2)+'\n');
console.log(`Exported ${visited.size} WordPress pages → docs${base}`);
