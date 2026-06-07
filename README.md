# LocalChat — Chat Réseau Local (Offline)

Application de chat **offline et en ligne** fonctionnant sur réseau local (WiFi, LAN).  
Design rouge & gris foncé. Aucun compte requis.


---

## Prérequis

- **PHP 7.4+** avec l'extension SQLite3 activée
- Serveur web : **XAMPP**, **WAMP**, **Laragon** (Windows) ou **Apache/Nginx** (Linux/macOS)

---

## Installation

### Option A — XAMPP / WAMP / Laragon
1. Copiez le dossier `localchat/` dans votre répertoire web :
   - XAMPP  → `C:/xampp/htdocs/localchat/`
   - WAMP   → `C:/wamp64/www/localchat/`
   - Laragon → `C:/laragon/www/localchat/`
2. Démarrez Apache depuis le panneau de contrôle.
3. Ouvrez votre navigateur : `http://localhost/localchat/`

### Option B — Serveur intégré PHP (développement)
```bash
cd localchat
php -S 0.0.0.0:8080
```
Accès depuis un autre appareil du même réseau : `http://[VOTRE_IP]:8080`

---

## Accès depuis d'autres appareils (même WiFi)

1. Trouvez l'IP de votre machine hébergeante :
   - Windows : `ipconfig` → "Adresse IPv4"
   - Linux/macOS : `ip addr` ou `ifconfig`
2. Les autres appareils du réseau ouvrent : `http://[IP_HÔTE]/localchat/`

> **Exemple** : `http://192.168.1.42/localchat/`

---

## Fonctionnalités Clés

- **Mode En Ligne / Hors Ligne** : Un interrupteur dans l'interface utilisateur permet de basculer l'application en mode "hors ligne". Dans ce mode, l'application cesse d'envoyer des signaux de présence et de recevoir des messages, vous rendant invisible pour les autres utilisateurs.
- **Visibilité des Adresses IP** : La liste des utilisateurs en ligne affiche désormais l'adresse IP locale de chaque appareil connecté, permettant d'identifier plus facilement les machines.
- **Page d'Administration** : Une interface dédiée (`admin.html`) fournit des statistiques en temps réel sur l'activité du chat :
    - Nombre d'utilisateurs actuellement en ligne.
    - Nombre total d'utilisateurs enregistrés.
    - Nombre total de messages échangés.
    - Liste des 15 derniers utilisateurs actifs avec leur statut (en ligne/hors ligne) et leur adresse IP.

## Accès à la Page d'Administration

Pour accéder au panneau d'administration, ouvrez simplement `http://[IP_HÔTE]/localchat/admin.html` dans votre navigateur.

---



## Structure des fichiers



---

## Sécurité (réseau local)

- La base de données `chat.db` est inaccessible directement (bloquée par `.htaccess`)
- Les scripts PHP sont interdits dans le dossier `uploads/`
- Fichiers autorisés : jpg, jpeg, png, gif, webp, pdf, txt, zip, mp4, mp3
- Taille maximale : **20 Mo** par fichier

---

## Dépannage

| Problème                     | Solution                                          |
|------------------------------|---------------------------------------------------|
| "Extension SQLite3 manquante" | Activez `extension=sqlite3` dans `php.ini`       |
| Pas d'accès depuis le réseau | Vérifiez le pare-feu Windows / Linux              |
| Uploads ne fonctionnent pas  | Donnez les droits en écriture au dossier `uploads/` : `chmod 775 uploads/` |
| Serveur PHP ne démarre pas   | Port occupé ? Essayez `php -S 0.0.0.0:8081`      |
