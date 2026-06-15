/* =============================================================
   PROJECTS.JS — chargement statique des projets
   - Index : rend la grille + sidebar de filtres + boutons Acad/Pro
   - Projet : rend la page détail à partir de ?id=X
   ============================================================= */
(function () {
    'use strict';

    const JSON_URL = 'data/projects.json';
    const JSON_URL_FROM_SUB = 'data/projects.json'; // fonctionne aussi depuis projet.html (même répertoire)

    /* Petits helpers */
    const $ = (sel, ctx) => (ctx || document).querySelector(sel);
    const $$ = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));
    const slugify = (s) => (s || '').toString().toLowerCase()
        .normalize('NFD').replace(/[̀-ͯ]/g, '')
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/(^-|-$)/g, '');

    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    async function loadProjects() {
        try {
            const res = await fetch(JSON_URL, { cache: 'no-store' });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return await res.json();
        } catch (err) {
            console.error('Impossible de charger les projets :', err);
            return [];
        }
    }

    /* =========================================================
       INDEX — grille + sidebar + boutons Académique/Professionnel
       ========================================================= */
    async function initIndex() {
        const grid = $('#projects-grid');
        if (!grid) return;

        const projects = await loadProjects();
        const sidebar = $('#filters-sidebar');
        const projectsCount = $('#projects-count');
        const typeBtns = $$('.filter-buttons .filter-btn');

        const state = {
            type: 'Professionnel',
            domain: '',
            year: '',
            skills: [],
            poles: []
        };

        function getCounts(field) {
            const counts = {};
            projects.forEach(p => {
                const v = p[field];
                if (!v) return;
                if (Array.isArray(v)) {
                    v.forEach(x => { counts[x] = (counts[x] || 0) + 1; });
                } else {
                    counts[v] = (counts[v] || 0) + 1;
                }
            });
            return counts;
        }

        function buildSidebar() {
            const domainCounts = getCounts('domain');
            const yearCounts = getCounts('year');
            const skillCounts = getCounts('skills');
            const poleCounts = getCounts('poles');

            const years = Object.keys(yearCounts).sort((a, b) => b.localeCompare(a));
            const skills = Object.keys(skillCounts).sort();
            const poles = Object.keys(poleCounts).sort();

            const domainsOrder = ['Audiovisuel', 'Communication', 'Developpement Web'];
            const domains = Array.from(new Set([...domainsOrder, ...Object.keys(domainCounts)]));

            sidebar.innerHTML = `
                <div class="filters-group">
                    <h3>Domaine</h3>
                    <ul class="filters-list">
                        <li><a href="#" class="filter-link domain-btn active" data-domain=""><span>Tous</span><span class="count">${projects.length}</span></a></li>
                        ${domains.map(d => `
                            <li><a href="#" class="filter-link domain-btn" data-domain="${escapeHtml(d)}">
                                <span>${escapeHtml(d.replace('Developpement', 'Développement'))}</span>
                                <span class="count">${domainCounts[d] || 0}</span>
                            </a></li>
                        `).join('')}
                    </ul>
                </div>

                <div class="filters-group">
                    <h3>Année</h3>
                    <ul class="filters-list">
                        <li><a href="#" class="filter-link year-btn active" data-year=""><span>Toutes</span><span class="count">${projects.length}</span></a></li>
                        ${years.map(y => `
                            <li><a href="#" class="filter-link year-btn" data-year="${escapeHtml(y)}">
                                <span>${escapeHtml(y)}</span>
                                <span class="count">${yearCounts[y] || 0}</span>
                            </a></li>
                        `).join('')}
                    </ul>
                </div>

                ${skills.length ? `
                <div class="filters-group">
                    <h3>Compétence</h3>
                    <ul class="filters-list">
                        ${skills.map(s => `
                            <li><label class="filter-link">
                                <span><input type="checkbox" class="skill-check" value="${escapeHtml(s)}"> ${escapeHtml(s)}</span>
                                <span class="count">${skillCounts[s]}</span>
                            </label></li>
                        `).join('')}
                    </ul>
                </div>` : ''}

                ${poles.length ? `
                <div class="filters-group">
                    <h3>Pôles</h3>
                    <ul class="filters-list">
                        ${poles.map(p => `
                            <li><label class="filter-link">
                                <span><input type="checkbox" class="pole-check" value="${escapeHtml(p)}"> ${escapeHtml(p)}</span>
                                <span class="count">${poleCounts[p]}</span>
                            </label></li>
                        `).join('')}
                    </ul>
                </div>` : ''}

                <a href="#" class="filter-reset" id="filterReset">↺ Réinitialiser</a>
            `;
        }

        function renderGrid() {
            const filtered = projects.filter(p => {
                if (state.type && p.type !== state.type) return false;
                if (state.domain && p.domain !== state.domain) return false;
                if (state.year && String(p.year) !== state.year) return false;
                if (state.skills.length) {
                    const has = (p.skills || []).map(s => s.toLowerCase());
                    if (!state.skills.every(s => has.includes(s.toLowerCase()))) return false;
                }
                if (state.poles.length) {
                    const has = (p.poles || []).map(s => s.toLowerCase());
                    if (!state.poles.every(s => has.includes(s.toLowerCase()))) return false;
                }
                return true;
            });

            if (projectsCount) projectsCount.textContent = `(${String(filtered.length).padStart(2, '0')} projets)`;

            if (!filtered.length) {
                grid.innerHTML = `<div class="projects-empty">Aucun projet ne correspond à ces filtres.</div>`;
                return;
            }

            grid.innerHTML = filtered.map(p => {
                const tagClass = 'tag-' + slugify(p.domain || '');
                const domainLabel = escapeHtml((p.domain || '').replace('Developpement', 'Développement'));
                return `
                <article class="project-card"
                         data-href="projet.html?id=${encodeURIComponent(p.id)}"
                         data-type="${escapeHtml(p.type || '')}"
                         data-domain="${escapeHtml(p.domain || '')}"
                         data-year="${escapeHtml(p.year || '')}">
                    <div class="card-bg" style="background-image:url('${escapeHtml(p.image)}');"></div>
                    <span class="vf-corner vf-tl"></span>
                    <span class="vf-corner vf-tr"></span>
                    <span class="vf-corner vf-bl"></span>
                    <span class="vf-corner vf-br"></span>
                    <div class="vf-rec"><span class="vf-rec-dot"></span>REC</div>
                    <div class="vf-meta">
                        <div class="vf-meta-item">
                            <span class="vf-meta-label">Année</span>
                            <span class="vf-meta-val">${escapeHtml(p.year || '—')}</span>
                        </div>
                        <div class="vf-meta-item vf-meta-center">
                            <span class="vf-meta-label">Titre</span>
                            <span class="vf-meta-val">${escapeHtml(p.title)}</span>
                        </div>
                        <div class="vf-meta-item vf-meta-right">
                            <span class="vf-meta-label">Thème</span>
                            <a href="#" class="vf-meta-val project-tag ${tagClass}" data-filter-domain="${escapeHtml(p.domain || '')}">${domainLabel}</a>
                        </div>
                    </div>
                </article>`;
            }).join('');

            attachCardHandlers();
        }

        function attachCardHandlers() {
            // click sur la carte -> page projet (sauf si on clique sur le tag domaine)
            $$('.project-card', grid).forEach(card => {
                card.addEventListener('click', (e) => {
                    const tag = e.target.closest('.project-tag');
                    if (tag) {
                        e.preventDefault();
                        const d = tag.getAttribute('data-filter-domain') || '';
                        state.domain = d;
                        syncSidebarActive();
                        renderGrid();
                        // scroll vers la grille pour montrer le résultat
                        const layout = $('.projects-layout');
                        if (layout) layout.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        return;
                    }
                    const href = card.dataset.href;
                    if (!href) return;
                    const trans = $('.page-transition');
                    if (trans) {
                        trans.classList.add('active');
                        setTimeout(() => { window.location.href = href; }, 450);
                    } else {
                        window.location.href = href;
                    }
                });
            });
        }

        function syncSidebarActive() {
            $$('.domain-btn', sidebar).forEach(b => b.classList.toggle('active', (b.dataset.domain || '') === state.domain));
            $$('.year-btn', sidebar).forEach(b => b.classList.toggle('active', (b.dataset.year || '') === state.year));
            typeBtns.forEach(b => b.classList.toggle('active', b.dataset.type === state.type));
        }

        function bindSidebar() {
            sidebar.addEventListener('click', (e) => {
                const dBtn = e.target.closest('.domain-btn');
                const yBtn = e.target.closest('.year-btn');
                const reset = e.target.closest('#filterReset');
                if (dBtn) {
                    e.preventDefault();
                    state.domain = dBtn.dataset.domain || '';
                    syncSidebarActive();
                    renderGrid();
                } else if (yBtn) {
                    e.preventDefault();
                    state.year = yBtn.dataset.year || '';
                    syncSidebarActive();
                    renderGrid();
                } else if (reset) {
                    e.preventDefault();
                    state.domain = '';
                    state.year = '';
                    state.skills = [];
                    state.poles = [];
                    $$('.skill-check', sidebar).forEach(c => c.checked = false);
                    $$('.pole-check', sidebar).forEach(c => c.checked = false);
                    syncSidebarActive();
                    renderGrid();
                }
            });

            sidebar.addEventListener('change', (e) => {
                if (e.target.classList.contains('skill-check')) {
                    state.skills = $$('.skill-check', sidebar).filter(i => i.checked).map(i => i.value);
                    renderGrid();
                } else if (e.target.classList.contains('pole-check')) {
                    state.poles = $$('.pole-check', sidebar).filter(i => i.checked).map(i => i.value);
                    renderGrid();
                }
            });
        }

        function bindTypeButtons() {
            typeBtns.forEach(b => b.addEventListener('click', () => {
                state.type = b.dataset.type;
                syncSidebarActive();
                renderGrid();
            }));
        }

        buildSidebar();
        syncSidebarActive();
        bindSidebar();
        bindTypeButtons();
        renderGrid();
    }

    /* =========================================================
       PROJET — page détail (lit ?id=X)
       ========================================================= */
    async function initProjectPage() {
        const container = $('#project-detail');
        if (!container) return;

        const params = new URLSearchParams(window.location.search);
        const id = params.get('id');
        if (!id) {
            container.innerHTML = `<div class="container" style="padding:160px 6%;"><p style="font-family:var(--font-mono);color:var(--muted);">Aucun projet sélectionné.</p></div>`;
            return;
        }

        const projects = await loadProjects();
        const project = projects.find(p => String(p.id) === String(id));
        if (!project) {
            container.innerHTML = `<div class="container" style="padding:160px 6%;"><p style="font-family:var(--font-mono);color:var(--muted);">Projet introuvable.</p></div>`;
            return;
        }

        document.title = `${project.title} — Clément Hanot`;
        const skills = project.skills || [];
        const logiciels = project.logiciels || [];

        container.innerHTML = `
            <section class="project-hero" style="background-image:url('${escapeHtml(project.image)}');">
                <div class="hero-overlay"></div>
                <div class="hero-inner">
                    <div class="eyebrow" data-reveal>${escapeHtml(project.type || 'Projet')} · ${escapeHtml(project.year || '')}</div>
                    <h1 class="project-title-big" data-reveal style="--delay:.1s;">${escapeHtml(project.title)}</h1>
                    <div class="project-subtags" data-reveal style="--delay:.2s;">
                        ${project.domain ? `<span class="tag">${escapeHtml(project.domain.replace('Developpement','Développement'))}</span>` : ''}
                        ${skills.map(s => `<span class="tag">${escapeHtml(s)}</span>`).join('')}
                    </div>
                </div>
            </section>

            <main class="project-main">
                <div class="container">
                    <a href="index.html#projects" class="project-back" data-reveal>← Tous les projets</a>
                    <div class="project-meta-bar" data-reveal>
                        <span class="project-year">${escapeHtml(project.year || '')}</span>
                        ${project.type ? `<span>· ${escapeHtml(project.type)}</span>` : ''}
                        ${project.domain ? `<span>· ${escapeHtml(project.domain.replace('Developpement','Développement'))}</span>` : ''}
                    </div>

                    <div class="project-grid">
                        <div class="project-left" data-reveal>
                            <div class="box description-box">
                                <h3>— Description</h3>
                                <div class="box-content">${escapeHtml(project.description || '').replace(/\n/g, '<br>')}</div>
                            </div>
                        </div>
                        <aside class="project-right" data-reveal="right" style="--delay:.15s;">
                            <div class="box softwares-box">
                                <h4>— Logiciels</h4>
                                <div class="box-content">
                                    ${logiciels.length ? `<ul class="software-list">${logiciels.map(l => `<li>${escapeHtml(l)}</li>`).join('')}</ul>` : 'Aucun logiciel renseigné.'}
                                </div>
                            </div>
                            <div class="box apprentissage-box">
                                <h4>— Apprentissage critique</h4>
                                <div class="box-content">${project.apprentissages ? escapeHtml(project.apprentissages).replace(/\n/g, '<br>') : 'Aucun apprentissage renseigné.'}</div>
                            </div>
                        </aside>
                    </div>

                    ${project.link ? `
                    <div class="project-bottom-links">
                        <div class="box links-full">
                            <div class="box-content"><strong>Liens :</strong><a href="${escapeHtml(project.link)}" target="_blank" rel="noopener noreferrer">${escapeHtml(project.link)}</a></div>
                        </div>
                    </div>` : ''}
                </div>
            </main>
        `;
    }

    /* Init */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => { initIndex(); initProjectPage(); });
    } else {
        initIndex(); initProjectPage();
    }
})();
