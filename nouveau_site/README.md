# Portfolio — Clément Hanot

Site portfolio statique pour valoriser mes projets académiques et professionnels (audiovisuel, photographie, communication, développement web).

**Technologies :** HTML / CSS / JavaScript pur — aucun framework, aucune base de données.
**Hébergement :** prêt pour GitHub Pages, Netlify, Vercel ou tout serveur statique.

---

## Structure du projet

```
nouveau_site/
├── index.html              # Page d'accueil (hero + grille projets + timeline)
├── moi.html                # Page profil "À propos"
├── projet.html             # Template page projet (utilise ?id=X)
├── styles.css              # CSS principal
├── style_moi.css           # CSS de la page profil
│
├── data/
│   └── projects.json       # ★ TES PROJETS sont ici ★
│
├── js/
│   ├── main.js             # Curseur, animations scroll, transitions
│   └── projects.js         # Chargement JSON, grille, filtres, page projet
│
├── img/
│   ├── image_pro.jpg       # Photo principale (hero)
│   ├── Photo_pro_noir.jpg  # Portrait page profil
│   └── projets/            # Mets ici tes images de projets
│
├── uploads/                # (ancien dossier d'upload, utilisable pour les images)
│
├── README.md               # Ce fichier
└── .gitignore
```

---

## Ajouter un nouveau projet — 3 étapes

### 1. Ajoute l'image du projet

Place l'image dans le dossier `img/projets/` (ou `uploads/`).
Format recommandé : **JPG ou PNG, format carré 1200×1200 px minimum.**

### 2. Édite `data/projects.json`

Ajoute un nouvel objet dans le tableau. Copie ce template :

```json
{
  "id": 7,
  "slug": "nom-de-ton-projet",
  "title": "Titre du projet",
  "type": "Professionnel",
  "domain": "Audiovisuel",
  "year": "2026",
  "image": "img/projets/mon-projet.jpg",
  "description": "Description complète du projet, ce que tu as fait, le contexte…",
  "skills": ["cadrage", "montage", "colorimetrie"],
  "logiciels": ["DaVinci Resolve", "Premiere Pro"],
  "apprentissages": "Ce que ce projet t'a appris, les compétences acquises…",
  "link": "https://lien-externe.com",
  "poles": ["pole1"]
}
```

**Champs :**

| Champ | Valeurs acceptées |
|---|---|
| `id` | Nombre unique (incrémente le plus grand actuel) |
| `slug` | identifiant url-friendly (lettres-tirets) |
| `title` | Titre du projet |
| `type` | `"Académique"` ou `"Professionnel"` |
| `domain` | `"Audiovisuel"`, `"Communication"` ou `"Developpement Web"` |
| `year` | `"2024"`, `"2025"`, `"2026"`… |
| `image` | Chemin relatif vers l'image (`img/projets/...` ou `uploads/...`) |
| `description` | Texte libre (les `\n` font des retours à la ligne) |
| `skills` | Tableau de compétences (`["cadrage", "montage"]`) |
| `logiciels` | Tableau des logiciels utilisés |
| `apprentissages` | Texte libre |
| `link` | URL externe (vidéo, site, etc.) — laisse `""` si aucun |
| `poles` | Tableau des pôles MMI (`["pole1", "pole2"]`) |

### 3. Sauvegarde et recharge

Sauvegarde le fichier, recharge la page → ton projet apparaît dans la grille et a sa propre page via `projet.html?id=7`.

---

## Tester en local

⚠️ Le site charge `data/projects.json` via `fetch()` — il faut donc un **serveur HTTP** local, pas un simple double-clic sur `index.html`.

**Option 1 — XAMPP** (déjà installé chez toi)
1. Place le dossier dans `C:\xampp\htdocs\portfolio\`
2. Démarre Apache
3. Ouvre `http://localhost/portfolio/`

**Option 2 — Python** (rapide)
```bash
cd portfolio
python -m http.server 8000
# puis ouvre http://localhost:8000
```

**Option 3 — VS Code**
Installe l'extension *Live Server*, clique droit sur `index.html` → "Open with Live Server".

---

## Déployer sur GitHub Pages

1. **Crée un repo GitHub** (par exemple `portfolio`).

2. **Pousse le code :**
   ```bash
   cd nouveau_site
   git init
   git add .
   git commit -m "Initial portfolio"
   git branch -M main
   git remote add origin https://github.com/TON_USER/portfolio.git
   git push -u origin main
   ```

3. **Active GitHub Pages :**
   - Va sur `https://github.com/TON_USER/portfolio/settings/pages`
   - **Source :** Deploy from a branch
   - **Branch :** `main` · `/ (root)`
   - Sauvegarde → ton site sera disponible à `https://TON_USER.github.io/portfolio/` en 1 à 2 minutes.

4. **Pour mettre à jour le site** plus tard, il suffit de :
   ```bash
   git add data/projects.json img/projets/
   git commit -m "Nouveau projet : XXX"
   git push
   ```
   GitHub Pages redéploie automatiquement.

---

## Direction artistique

- **Typo titres :** [DM Serif Display](https://fonts.google.com/specimen/DM+Serif+Display)
- **Typo corps :** [Outfit](https://fonts.google.com/specimen/Outfit)
- **Typo mono :** JetBrains Mono
- **Palette :**
  - `#BA2D0B` — rouge brique (accent)
  - `#D5F2E3` — menthe pâle (fond clair)
  - `#73BA9B` — sage (accent doux)
  - `#01110A` — noir profond (texte / fond sombre)

---

## Crédits

Inspirations : [workbygabin.com](https://workbygabin.com/) (hero), [noah-condamines.fr](https://noah-condamines.fr/) (grille projets).

© 2026 — Clément Hanot
