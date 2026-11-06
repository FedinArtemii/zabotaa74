import fs from 'node:fs';
const assets='theme/zabota74/assets';
fs.mkdirSync(`${assets}/fonts`,{recursive:true});
for (const [family,slug] of [['Manrope:wght@400..800','manrope'],['Prata','prata']]) {
  const response=await fetch(`https://fonts.googleapis.com/css2?family=${family}&display=swap`,{headers:{'User-Agent':'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36'}});
  if(!response.ok) throw Error('Font download failed');
  const css=await response.text();
  fs.writeFileSync(`${assets}/fonts/${slug}-source.css`,css);
  const license=await fetch(`https://raw.githubusercontent.com/google/fonts/main/ofl/${slug}/OFL.txt`);
  if(!license.ok) throw Error(`Font license missing: ${slug}`);
  fs.writeFileSync(`${assets}/fonts/${slug}-OFL.txt`,await license.text());
  for (const subset of ['cyrillic','latin']) {
    const escaped=subset.replace('-','\\-');
    const block=css.match(new RegExp('/\\* '+escaped+' \\*/([\\s\\S]*?)(?=/\\*|$)'))?.[1];
    const url=block?.match(/url\(([^)]+)\)/)?.[1];
    if(!url) throw Error(`Font subset missing: ${slug}/${subset}`);
    const file=await fetch(url);
    fs.writeFileSync(`${assets}/fonts/${slug}-${subset}.woff2`,Buffer.from(await file.arrayBuffer()));
  }
}
// Own vector placeholders. These intentionally do not depict the real premises or residents.
const palettes=[['#b79887','#d8c5ad','#6e6f55','#a3634e'],['#c4ae97','#e9d6b7','#84866b','#815044'],['#a89f83','#d4c9af','#697157','#a57a56'],['#b8a298','#dfc9b4','#7c7b63','#a76552'],['#b9aa96','#eee0c7','#7f8265','#9a614b'],['#b7b5a0','#d8d7bc','#6d735a','#ab7964']];
for(let i=0;i<6;i++) {
  const [bg,light,green,terra]=palettes[i];
  const sun=i%2===0?'<circle cx="325" cy="230" r="78" fill="'+light+'" opacity=".75"/>':'<circle cx="290" cy="190" r="55" fill="'+light+'" opacity=".8"/>';
  const svg=`<svg xmlns="http://www.w3.org/2000/svg" width="800" height="900" viewBox="0 0 800 900"><defs><linearGradient id="bg" x1="0" y1="0" x2="1" y2="1"><stop stop-color="${bg}"/><stop offset="1" stop-color="${terra}" stop-opacity=".55"/></linearGradient><linearGradient id="window" x1="0" y1="0" x2="0" y2="1"><stop stop-color="${light}"/><stop offset="1" stop-color="${bg}"/></linearGradient><pattern id="grain" width="7" height="7" patternUnits="userSpaceOnUse"><circle cx="1" cy="1" r=".6" fill="#fff" opacity=".08"/><circle cx="4" cy="5" r=".55" fill="#271711" opacity=".06"/></pattern></defs><path fill="url(#bg)" d="M0 0h800v900H0z"/><path d="M180 690V300c0-245 360-245 360 0v390Z" fill="url(#window)"/><path d="M210 680V305c0-205 300-205 300 0v375Z" fill="${green}" opacity=".28"/>${sun}<path d="M210 470c100-95 210-70 300-170v380H210Z" fill="${green}" opacity=".38"/><path d="M210 535c115-80 200-42 300-82v227H210Z" fill="${green}" opacity=".4"/><path d="M360 120v560M180 403h360" stroke="${light}" stroke-width="7" opacity=".7"/><path d="M0 700h800v200H0Z" fill="${terra}" opacity=".38"/><path d="m0 900 210-200m590 200L510 700M360 700v200" stroke="${light}" opacity=".13"/><ellipse cx="625" cy="760" rx="95" ry="19" fill="#36221d" opacity=".16"/><path d="m565 650 23 101h77l23-101Z" fill="${terra}"/><path d="M626 666V439m0 96c-65-24-70-84-58-99 48 6 67 55 58 99Zm0 62c61-31 69-77 56-95-42 2-65 54-56 95Zm0-111c53-24 54-68 42-82-38 2-50 47-42 82Z" stroke="${green}" fill="${green}" stroke-width="5"/><path fill="url(#grain)" d="M0 0h800v900H0z"/><g fill="#3f3028" opacity=".65" font-family="Arial,sans-serif" text-anchor="middle"><path d="M360 775v20m-10-10h20" stroke="#3f3028" stroke-width="1"/><text x="360" y="819" font-size="10" letter-spacing="3">МЕСТО ДЛЯ ФОТОГРАФИИ</text><text x="360" y="840" font-size="8" letter-spacing="2">ПАНСИОНАТ «ЗАБОТА»</text></g></svg>`;
  fs.writeFileSync(`${assets}/placeholder-${i+1}.svg`,svg);
}
fs.writeFileSync(`${assets}/favicon.svg`,'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><rect width="64" height="64" rx="14" fill="#351a1c"/><path d="M32 52C32 36 12 43 12 25C12 10 32 13 32 29C32 13 52 10 52 25C52 43 32 36 32 52Z" fill="none" stroke="#e8d6ca" stroke-width="2"/></svg>');
console.log('Local fonts and 6 original vector placeholders prepared.');
