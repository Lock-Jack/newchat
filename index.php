<?php
// index.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $app_name ?> — <?= $tagline ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php 
// Inclusion des différentes sections de l'application
require_once 'landing.php'; 
require_once 'app.php'; 
?>

<script>
    // Animations au scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    // Gestion de la transition après connexion
    function handleLogin(e) {
        e.preventDefault();
        const usernameInput = document.getElementById('login-username').value || 'User';
        const btn = e.target.querySelector('.login-btn');
        
        btn.textContent = 'Connexion...';
        btn.style.opacity = '0.7';
        
        setTimeout(() => {
            btn.textContent = '✓ Connecté !';
            btn.style.background = 'linear-gradient(135deg, #28a745, #34d058)';
            
            setTimeout(() => {
                const landing = document.getElementById('website-landing-pages');
                landing.style.transition = 'opacity 0.5s ease';
                landing.style.opacity = '0';
                
                setTimeout(() => {
                    landing.style.display = 'none';
                    document.getElementById('app-user-avatar').textContent = usernameInput.substring(0, 2).toUpperCase();
                    
                    const appView = document.getElementById('social-network-app');
                    appView.style.display = 'block';
                    window.scrollTo(0, 0);
                }, 500);
            }, 1000);
        }, 1200);
    }

    // Lecteur Audio Global
    let isPlaying = false;
    function playTrack(title, author, avatar) {
        document.getElementById('p-title').textContent = title;
        document.getElementById('p-author').textContent = author;
        document.getElementById('p-avatar').textContent = avatar;
        document.getElementById('global-player').classList.add('active');
        isPlaying = true;
        document.getElementById('p-play-toggle').textContent = "⏸";
    }

    function toggleGlobalPlay() {
        const toggleBtn = document.getElementById('p-play-toggle');
        if (isPlaying) {
            toggleBtn.textContent = "▶";
            isPlaying = false;
        } else {
            toggleBtn.textContent = "⏸";
            isPlaying = true;
        }
    }

    function toggleLike(element) {
        element.classList.toggle('like-active');
        const span = element.querySelector('span');
        let count = parseInt(span.textContent);
        if (element.classList.contains('like-active')) {
            element.style.color = 'var(--red-bright)';
            span.textContent = count + 1;
        } else {
            element.style.color = 'var(--muted)';
            span.textContent = count - 1;
        }
    }
</script>
</body>
</html>