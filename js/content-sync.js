(function () {
  fetch('/api/visit', { method: 'POST' }).catch(() => {});

  fetch('/api/content')
    .then(r => r.json())
    .then(data => {
      if (!data.success || !data.content) return;
      applyContent(data.content);
    })
    .catch(() => {});

  function applyContent(c) {
    if (c.site?.title) document.title = c.site.title;
    if (c.site?.description) {
      let meta = document.querySelector('meta[name="description"]');
      if (meta) meta.content = c.site.description;
    }

    setText('.eyebrow', c.hero?.eyebrow);
    setText('.hero-sub', c.hero?.subtitle);
    setText('.photo-tag', c.hero?.photo_tag);
    setText('#role-text', c.hero?.default_role);
    if (c.hero?.photo) setAttr('.photo-frame img', 'src', c.hero.photo);

    if (c.site?.cv_file) {
      document.querySelectorAll('a.btn-ghost[download]').forEach(a => {
        a.href = c.site.cv_file;
      });
    }

    if (c.about?.title) setText('.bento-bio h3', c.about.title);
    if (c.about?.bio) setText('.bento-bio p', c.about.bio);
    if (c.about?.photo) setAttr('.bento-photo img', 'src', c.about.photo);
    if (c.about?.stat1_value) setText('.bento-stat:nth-of-type(1) strong', c.about.stat1_value);
    if (c.about?.stat1_label) setText('.bento-stat:nth-of-type(1) span', c.about.stat1_label);
    if (c.about?.stat2_value) setText('.bento-stat:nth-of-type(2) strong', c.about.stat2_value);
    if (c.about?.stat2_label) setText('.bento-stat:nth-of-type(2) span', c.about.stat2_label);
    if (c.about?.location) setText('.bento-location strong', c.about.location);

    if (c.roles?.length) window.PORTFOLIO_ROLES = c.roles;

    if (c.contact?.email) {
      const mail = document.querySelector('.contact-details a[href^="mailto:"]');
      if (mail) {
        mail.href = 'mailto:' + c.contact.email;
        mail.innerHTML = `<i class='bx bx-envelope'></i> ${c.contact.email}`;
      }
    }

    if (c.footer || c.site) {
      const footer = document.querySelector('.footer p');
      if (footer) footer.textContent = `© ${c.site?.footer_year || '2026'} ${c.site?.footer_name || 'Naveed Ali'}`;
    }
  }

  function setText(sel, val) {
    if (!val) return;
    const el = document.querySelector(sel);
    if (el) el.textContent = val;
  }

  function setAttr(sel, attr, val) {
    if (!val) return;
    const el = document.querySelector(sel);
    if (el) el.setAttribute(attr, val);
  }
})();
