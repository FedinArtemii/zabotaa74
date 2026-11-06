(() => {
  const toggle = document.querySelector('.menu-toggle');
  const nav = document.querySelector('#primary-nav');
  const setMenu = open => {
    toggle?.setAttribute('aria-expanded', String(open));
    nav?.classList.toggle('is-open', open);
    const label = toggle?.querySelector('.sr-only');
    if (label) label.textContent = open ? 'Закрыть меню' : 'Открыть меню';
  };
  toggle?.addEventListener('click', () => setMenu(toggle.getAttribute('aria-expanded') !== 'true'));
  nav?.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMenu(false)));
  document.addEventListener('keydown', e => { if (e.key === 'Escape') setMenu(false); });
  const contact = document.querySelector('#contact-dialog');
  document.querySelectorAll('[data-contact], a[href="#consultation"]').forEach(button => button.addEventListener('click', event => {
    event.preventDefault(); contact?.showModal();
  }));
  document.querySelectorAll('dialog').forEach(dialog => {
    dialog.querySelector('.dialog-close')?.addEventListener('click', () => dialog.close());
    dialog.addEventListener('click', e => { if(e.target === dialog) {
      const rect = dialog.getBoundingClientRect();
      if (e.clientX < rect.left || e.clientX > rect.right || e.clientY < rect.top || e.clientY > rect.bottom) dialog.close();
    }});
  });
  const gallery = document.querySelector('#gallery-dialog');
  document.querySelectorAll('[data-gallery]').forEach(button => button.addEventListener('click', () => {
    const img = button.querySelector('img');
    const large = document.querySelector('#gallery-large');
    large.src = img.src; large.alt = img.alt;
    document.querySelector('#gallery-dialog-title').textContent = button.dataset.gallery;
    gallery.showModal();
  }));
  const filters = [...document.querySelectorAll('[data-filter]')];
  filters.forEach(button => button.addEventListener('click', () => {
    filters.forEach(b => { b.classList.remove('active'); b.setAttribute('aria-pressed','false'); });
    button.classList.add('active'); button.setAttribute('aria-pressed','true');
    document.querySelectorAll('[data-category]').forEach(item => { item.hidden = button.dataset.filter !== 'all' && item.dataset.category !== button.dataset.filter; });
    const count = document.querySelector('#gallery-count');
    if(count) {
      const total=[...document.querySelectorAll('[data-category]')].filter(item=>!item.hidden).length;
      const word={one:'фотография',few:'фотографии',many:'фотографий',other:'фотографии'}[new Intl.PluralRules('ru').select(total)];
      count.textContent = `${total} ${word}`;
    }
  }));
  const tabs = [...document.querySelectorAll('[data-schedule-tab]')];
  tabs.forEach(button => button.addEventListener('click', () => {
    tabs.forEach(b => { b.classList.remove('active'); b.setAttribute('aria-selected','false'); });
    button.classList.add('active'); button.setAttribute('aria-selected','true');
    document.querySelectorAll('[data-schedule]').forEach(panel => {panel.hidden=panel.dataset.schedule!==button.dataset.scheduleTab;});
  }));
  document.querySelectorAll('[data-tablist]').forEach(list => list.addEventListener('keydown', event => {
    if(!['ArrowLeft','ArrowRight','Home','End'].includes(event.key)) return;
    const buttons=[...list.querySelectorAll('[role="tab"]')];
    const index=buttons.indexOf(document.activeElement);
    const next=event.key==='Home'?0:event.key==='End'?buttons.length-1:(index+(event.key==='ArrowRight'?1:-1)+buttons.length)%buttons.length;
    event.preventDefault();buttons[next].focus();buttons[next].click();
  }));
})();
