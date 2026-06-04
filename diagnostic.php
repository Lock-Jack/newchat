<?php
header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>LocalChat — Diagnostic</title>
<style>
  body { font-family: monospace; background: #1a1a1a; color: #e8e8e8; padding: 30px; }
  h1   { color: #e74c3c; margin-bottom: 24px; }
  .check { padding: 10px 16px; margin: 6px 0; border-radius: 6px; display: flex; gap: 12px; align-items: center; }
  .ok   { background: #1e3a1e; border-left: 3px solid #2ecc71; }
  .err  { background: #3a1e1e; border-left: 3px solid #e74c3c; }
  .warn { background: #3a301e; border-left: 3px solid #f39c12; }
  .fix  { background: #2a2a2a; border: 1px solid #444; border-radius: 6px; padding: 12px 16px; margin: 6px 0 16px; font-size: 13px; color: #ccc; }
  h2   { color: #888; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin: 24px 0 8px; }
  code { background: #333; padding: 2px 6px; border-radius: 3px; color: #e74c3c; }
</style>
</head>
<body>
<h1>🔍 LocalChat — Diagnostic</h1>

<?php
$root = __DIR__;
$issues = 0;

function ok($msg)   { echo "<div class='check ok'>✅ $msg</div>"; }
function err($msg)  { echo "<div class='check err'>❌ $msg</div>"; global $issues; $issues++; }
function warn($msg) { echo "<div class='check warn'>⚠️ $msg</div>"; }
function fix($msg)  { echo "<div class='fix'>💡 $msg</div>"; }

echo "<h2>PHP</h2>";
$phpver = phpversion();
if (version_compare($phpver, '7.4', '>=')) ok("PHP $phpver");
else { err("PHP $phpver — version trop ancienne (7.4+ requis)"); }

echo "<h2>Extensions PHP</h2>";
if (extension_loaded('sqlite3')) ok("Extension SQLite3 chargée");
else { err("Extension SQLite3 <b>non chargée</b>"); fix("Activez <code>extension=sqlite3</code> dans votre <code>php.ini</code> et redémarrez Apache."); }

if (extension_loaded('fileinfo')) ok("Extension fileinfo chargée");
else warn("Extension fileinfo absente (les types MIME des fichiers envoyés ne seront pas détectés)");

echo "<h2>Fichiers & Dossiers</h2>";
$files = ['init_db.php', 'index.html', 'api/index.php'];
foreach ($files as $f) {
    if (file_exists("$root/$f")) ok("$f présent");
    else err("$f <b>manquant</b>");
}

echo "<h2>Permissions d'écriture</h2>";
// Test écriture racine (pour chat.db)
if (is_writable($root)) ok("Dossier racine accessible en écriture (pour chat.db)");
else { err("Dossier racine <b>non accessible en écriture</b> — impossible de créer <code>chat.db</code>"); fix("Faites : <code>chmod 775 .</code> dans le dossier <code>localchat/</code>, ou accordez les droits à l'utilisateur Apache (www-data)."); }

// Dossier uploads
$udir = "$root/uploads";
if (!is_dir($udir)) {
    if (@mkdir($udir, 0775, true)) ok("Dossier <code>uploads/</code> créé automatiquement");
    else { err("Dossier <code>uploads/</code> <b>inexistant et impossible à créer</b>"); fix("Créez manuellement le dossier <code>uploads/</code> avec <code>mkdir uploads && chmod 775 uploads</code>"); }
} elseif (is_writable($udir)) ok("Dossier <code>uploads/</code> accessible en écriture");
else { err("Dossier <code>uploads/</code> <b>non accessible en écriture</b>"); fix("Faites : <code>chmod 775 uploads/</code>"); }

echo "<h2>Base de données SQLite</h2>";
if (extension_loaded('sqlite3')) {
    try {
        $dbpath = "$root/chat.db";
        $db = new SQLite3($dbpath);
        $db->exec("CREATE TABLE IF NOT EXISTS _test (id INTEGER PRIMARY KEY)");
        $db->exec("DROP TABLE _test");
        ok("SQLite3 fonctionne — <code>chat.db</code> créé/accessible à <code>$dbpath</code>");
        $db->close();
    } catch (Exception $e) {
        err("SQLite3 erreur : " . $e->getMessage());
        fix("Vérifiez que le dossier a les droits d'écriture.");
    }
}

echo "<h2>Test de l'API</h2>";
$apipath = "$root/api/index.php";
if (file_exists($apipath)) {
    ok("api/index.php trouvé");
    // Tenter un include minimal
    ob_start();
    try {
        // Juste vérifier la syntaxe
        $syntax = shell_exec("php -l " . escapeshellarg($apipath) . " 2>&1");
        if ($syntax && strpos($syntax, 'No syntax errors') !== false) ok("api/index.php — syntaxe PHP valide");
        elseif ($syntax) warn("Vérification syntaxe : $syntax");
    } catch(Exception $e) {}
    ob_end_clean();
}

echo "<h2>Résumé</h2>";
if ($issues === 0) {
    echo "<div class='check ok' style='font-size:15px;font-weight:bold'>🎉 Tout est correct ! Allez sur <a href='index.html' style='color:#e74c3c'>index.html</a> pour utiliser le chat.</div>";
} else {
    echo "<div class='check err' style='font-size:15px;font-weight:bold'>$issues problème(s) détecté(s). Corrigez-les ci-dessus puis rechargez cette page.</div>";
}
?>

<p style="margin-top:30px;color:#555;font-size:12px">Supprimez ce fichier <code>diagnostic.php</code> une fois que tout fonctionne.</p>
</body>
</html>
