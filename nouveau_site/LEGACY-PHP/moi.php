<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil — Clément Hanot</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="style_moi.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="moi-main">
        <div class="moi-hero">
            <div class="moi-eyebrow" data-reveal>— À propos</div>
            <h1 class="moi-title" data-reveal>
                Derrière<br>
                <em>la caméra,</em> <span class="moi-stroke">moi.</span>
            </h1>
        </div>

        <div class="moi-wrapper">
            <!-- Colonne gauche : texte + CVs -->
            <div class="moi-left">
                <p class="moi-lead" data-reveal>
                    Je m'appelle <strong>Clément Hanot</strong>, j'ai grandi en Île-de-France et je vis aujourd'hui
                    à La Crau, dans le Var. J'étudie à l'université de Toulon en
                    <em>Métiers du Multimédia et de l'Internet</em>.
                </p>

                <p class="moi-description" data-reveal style="--delay:.1s;">
                    Mon terrain de jeu : tout ce qui se voit. La photographie d'abord, parce qu'elle m'a appris
                    à regarder. Puis la vidéo, le montage et la couleur, parce qu'elles m'ont appris à raconter.
                    Aujourd'hui je cherche un stage pour appliquer tout ça à des projets qui ont du sens.
                </p>

                <!-- Compétences clés -->
                <div class="moi-skills" data-reveal style="--delay:.15s;">
                    <h3>— Boîte à outils</h3>
                    <ul class="moi-skills-list">
                        <li>DaVinci&nbsp;Resolve</li>
                        <li>Premiere&nbsp;Pro</li>
                        <li>After&nbsp;Effects</li>
                        <li>Photoshop</li>
                        <li>Lightroom</li>
                        <li>Figma</li>
                        <li>HTML&nbsp;/&nbsp;CSS&nbsp;/&nbsp;JS</li>
                        <li>PHP&nbsp;&amp;&nbsp;MySQL</li>
                    </ul>
                </div>

                <!-- CVs -->
                <div class="cv-list" data-reveal style="--delay:.2s;">
                    <h3 class="cv-section-title">— Curriculum vidéo</h3>

                    <div class="cv-item">
                        <div class="cv-meta">
                            <span class="cv-year">Juin 2025</span>
                            <span class="cv-lang">FR</span>
                        </div>
                        <div class="cv-title">CV vidéo français</div>
                        <a class="cv-pill" href="https://youtu.be/4NYaDi_x_7Y" target="_blank" rel="noopener noreferrer">
                            Visionner sur YouTube <span aria-hidden="true">→</span>
                        </a>
                    </div>

                    <div class="cv-item">
                        <div class="cv-meta">
                            <span class="cv-year">Octobre 2025</span>
                            <span class="cv-lang">EN</span>
                        </div>
                        <div class="cv-title">CV vidéo anglais</div>
                        <a class="cv-pill" href="https://youtu.be/Z25kletljqA" target="_blank" rel="noopener noreferrer">
                            Watch on YouTube <span aria-hidden="true">→</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : photo -->
            <aside class="moi-right" data-reveal="right">
                <div class="moi-photo-frame">
                    <img src="img/Photo_pro_noir.jpg" alt="Portrait de Clément Hanot" class="moi-photo">
                    <span class="moi-photo-caption">Clément Hanot · 2025</span>
                </div>
            </aside>
        </div>

        <!-- Citation / signature -->
        <section class="moi-quote" data-reveal>
            <blockquote>
                <span class="quote-mark">“</span>
                La photographie m'a appris à <em>regarder</em>. La vidéo m'a appris à <em>raconter</em>.
                <span class="quote-mark-end">”</span>
            </blockquote>
            <cite>— Clément Hanot</cite>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
