<div id="social-network-app">
    <div class="app-container">
        <aside class="app-left-nav">
            <a href="#" class="nav-item active">🔥 Découvrir</a>
            <a href="#" class="nav-item">🎧 Mon Flux</a>
            <a href="#" class="nav-item">💬 Messages</a>
            <a href="#" class="nav-item" onclick="location.reload()" style="margin-top: 2rem; color: var(--red);">Déconnexion</a>
        </aside>

        <main class="feed-center">
            <div class="create-post-box">
                <div class="create-post-header">
                    <div class="user-avatar" id="app-user-avatar">U</div>
                    <textarea placeholder="Quoi de neuf dans votre studio ? Décrivez votre son..."></textarea>
                </div>
                <div class="create-post-actions">
                    <div class="upload-audio-btn">🎵 Enregistrer un fichier Audio</div>
                    <button class="btn-primary" style="padding: 0.5rem 1.2rem; font-size: 0.85rem;">Publier</button>
                </div>
            </div>

            <div class="audio-post-card">
                <div class="post-user-info">
                    <div class="user-avatar" style="background: var(--red); color: white;">JM</div>
                    <div class="post-user-meta">
                        <div class="username">Jean Mirion</div>
                        <div class="time">Il y a 12 minutes</div>
                    </div>
                </div>
                <div class="post-text">J'ai terminé la structure rythmique de mon prochain morceau Cyberpunk ! 🔥</div>
                <div class="post-audio-player">
                    <button class="play-track-btn" onclick="playTrack('Cyberpunk Drum Structure', 'Jean Mirion', '🎧')">▶</button>
                    <div class="waveform-container">
                        <?php for($b=0; $b<40; $b++): ?><div class="wave-bar"></div><?php endfor; ?>
                    </div>
                </div>
                <div class="post-interactions">
                    <div class="interact-btn" onclick="toggleLike(this)">❤️ <span>24</span></div>
                    <div class="interact-btn">💬 <span>7</span></div>
                </div>
            </div>
        </main>

        <aside class="app-right-sidebar">
            <div class="sidebar-widget">
                <h3 class="widget-title">Tendances Audio</h3>
                <div class="trending-list">
                    <div class="trending-item"><span class="trend-tag">#Synthwave2026</span><span class="trend-count">1.2k</span></div>
                    <div class="trending-item"><span class="trend-tag">#MixingTips</span><span class="trend-count">530</span></div>
                </div>
            </div>
        </aside>
    </div>
</div>

<div class="global-audio-player" id="global-player">
    <div class="player-track-info">
        <div class="player-avatar" id="p-avatar">🎵</div>
        <div class="player-meta">
            <div class="track-title" id="p-title">Aucun morceau</div>
            <div class="track-author" id="p-author">Choisissez une piste</div>
        </div>
    </div>
    <div class="player-controls">
        <button class="btn-ctrl btn-main-play" id="p-play-toggle" onclick="toggleGlobalPlay()">⏸</button>
    </div>
</div>