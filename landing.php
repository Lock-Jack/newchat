<div id="website-landing-pages">
    <nav>
        <div class="nav-logo">
            <div class="logo-ring"></div>
            <span><?= $app_name ?></span>
        </div>
        <ul>
            <li><a href="#features">Fonctionnalités</a></li>
            <li><a href="#app">L'App</a></li>
            <li><a href="#login">Connexion</a></li>
            <li><a href="#login" class="nav-cta">Rejoindre</a></li>
        </ul>
    </nav>

    <section class="hero">
        <div class="hero-bg"></div>
        <div class="hero-badge">✦ La Plateforme Audio de Nouvelle Génération</div>
        <h1>
            <span class="line-white">CONNECT</span>
            <span class="line-gold">YOUR</span>
            <span class="line-white">SOUND.</span>
        </h1>
        <p class="hero-sub"><?= $tagline ?></p>
        <div class="hero-actions">
            <a href="#login" class="btn-primary">Commencer gratuitement</a>
            <a href="#features" class="btn-secondary">Découvrir →</a>
        </div>
    </section>

    <div class="stats">
        <div class="stat-item">
            <span class="stat-num">100K+</span>
            <span class="stat-label">Utilisateurs</span>
        </div>
        <div class="stat-item">
            <span class="stat-num">0</span>
            <span class="stat-label">Publicités</span>
        </div>
        <div class="stat-item">
            <span class="stat-num">256-bit</span>
            <span class="stat-label">Chiffrement</span>
        </div>
        <div class="stat-item">
            <span class="stat-num">∞</span>
            <span class="stat-label">Liberté</span>
        </div>
    </div>

    <section class="features" id="features">
        <p class="section-label">// Pourquoi SyncNet</p>
        <h2 class="section-title">MESSAGERIE<br><span style="color: var(--red)">REDÉFINIE.</span></h2>

        <div class="features-grid">
            <?php foreach ($features as $i => $f): ?>
            <div class="feature-card <?= $f['theme'] ?> reveal" style="transition-delay: <?= $i * 0.1 ?>s">
                <span class="feature-icon"><?= $f['icon'] ?></span>
                <span class="feature-badge"><?= $f['badge'] ?></span>
                <h3 class="feature-title"><?= $f['title'] ?></h3>
                <p class="feature-subtitle"><?= $f['subtitle'] ?></p>
                <p class="feature-desc"><?= $f['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mockup-section" id="app">
        <div class="mockup-inner">
            <div class="mockup-text reveal">
                <p class="section-label">// Interface</p>
                <h2 class="section-title">UNE APP.<br>TOUS VOS<br><span style="color:var(--gold-light)">SONS.</span></h2>
                <p>SyncNet réunit chat, groupes, partage de fichiers et voice chat dans une interface épurée.</p>
                <ul class="feature-list">
                    <li>Chat individuel et groupes thématiques</li>
                    <li>Partage de fichiers audio et médias</li>
                    <li>Voice Chat intégré</li>
                </ul>
            </div>

            <div class="app-mockup reveal">
                <div class="app-titlebar">
                    <div class="dot r"></div><div class="dot y"></div><div class="dot g"></div>
                    <span style="font-size:0.7rem;color:var(--muted);margin-left:0.8rem"><?= $app_name ?></span>
                </div>
                <div class="app-body">
                    <div class="app-sidebar">
                        <div class="sidebar-icon active">💬</div>
                        <div class="sidebar-icon">👥</div>
                    </div>
                    <div class="app-main">
                        <div class="chat-search">🔍 <span>Rechercher...</span></div>
                        <?php foreach ($chats as $c): ?>
                        <div class="chat-item <?= $c['active'] ? 'active' : '' ?>">
                            <div class="chat-avatar" style="background:<?= $c['color'] ?>22;color:<?= $c['color'] ?>"><?= $c['initials'] ?></div>
                            <div class="chat-info">
                                <div class="chat-name"><?= $c['name'] ?></div>
                                <div class="chat-preview"><?= $c['preview'] ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="login">
        <div class="login-section">
            <div class="login-text reveal">
                <p class="section-label">// Accès</p>
                <h2 class="section-title">REJOIGNEZ<br>LA<br><span style="color:var(--red)">COMMUNAUTÉ.</span></h2>
            </div>

            <div class="login-card reveal">
                <div class="login-logo-wrap">
                    <div class="login-logo-icon"></div>
                    <span class="login-app-name"><?= strtoupper($app_name) ?></span>
                </div>

                <form class="login-form" onsubmit="handleLogin(event)">
                    <div class="input-group">
                        <label>Email ou nom d'utilisateur</label>
                        <input type="text" id="login-username" placeholder="ex: SoundMaster" required>
                    </div>
                    <div class="input-group">
                        <label>Mot de passe sécurisé</label>
                        <input type="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="login-btn">Connexion</button>
                </form>
            </div>
        </div>
    </section>
</div>