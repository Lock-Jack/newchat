<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Token');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit; }

// Afficher les erreurs PHP en JSON pour faciliter le débogage
set_error_handler(function($errno, $errstr) {
    echo json_encode(['ok' => false, 'error' => "PHP Error: $errstr"]);
    exit;
});

require_once __DIR__ . '/../init_db.php';

// Créer le dossier uploads s'il n'existe pas
$uploadsDir = __DIR__ . '/../uploads';
if (!is_dir($uploadsDir)) {
    @mkdir($uploadsDir, 0775, true);
}

try {
    $db = getDB();
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => 'Impossible d\'ouvrir la base de données : ' . $e->getMessage()]);
    exit;
}

// S'assurer que la colonne ip_address existe
@$db->exec("ALTER TABLE users ADD COLUMN ip_address TEXT");

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$token = $_SERVER['HTTP_X_TOKEN'] ?? $_GET['token'] ?? '';

// Helper: get user by token
function getUserByToken($db, $token) {
    if (!$token) return null;
    $stmt = $db->prepare("SELECT * FROM users WHERE token = :token");
    $stmt->bindValue(':token', $token);
    $res = $stmt->execute();
    return $res->fetchArray(SQLITE3_ASSOC) ?: null;
}

// Helper: update last_seen
function touchUser($db, $user_id) {
    $stmt = $db->prepare("UPDATE users SET last_seen = :t, ip_address = :ip WHERE id = :id");
    $stmt->bindValue(':t', time());
    $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
    $stmt->bindValue(':id', $user_id, SQLITE3_INTEGER);
    $stmt->execute();
}

switch ($action) {

    // ── JOIN ──────────────────────────────────────────────
    case 'join':
        $pseudo = trim($_POST['pseudo'] ?? '');
        if (!$pseudo || strlen($pseudo) < 2 || strlen($pseudo) > 20) {
            echo json_encode(['ok' => false, 'error' => 'Pseudonyme invalide (2-20 caractères)']);
            exit;
        }
        $pseudo = htmlspecialchars($pseudo, ENT_QUOTES);
        $colors = ['#c0392b','#e74c3c','#922b21','#a93226','#cb4335','#b03a2e'];
        $color  = $colors[array_rand($colors)];

        // Upsert: if pseudo exists, return existing token
        $stmt = $db->prepare("SELECT * FROM users WHERE pseudo = :p");
        $stmt->bindValue(':p', $pseudo);
        $row = $stmt->execute()->fetchArray(SQLITE3_ASSOC);

        if ($row) {
            touchUser($db, $row['id']);
            echo json_encode(['ok' => true, 'token' => $row['token'], 'pseudo' => $row['pseudo'], 'color' => $row['avatar_color'], 'id' => $row['id']]);
        } else {
            $token_new = bin2hex(random_bytes(16));
            $stmt2 = $db->prepare("INSERT INTO users (pseudo, token, avatar_color, last_seen, ip_address) VALUES (:p, :t, :c, :ls, :ip)");
            $stmt2->bindValue(':p', $pseudo);
            $stmt2->bindValue(':t', $token_new);
            $stmt2->bindValue(':c', $color);
            $stmt2->bindValue(':ls', time(), SQLITE3_INTEGER);
            $stmt2->bindValue(':ip', $_SERVER['REMOTE_ADDR']);
            $stmt2->execute();
            $id = $db->lastInsertRowID();
            echo json_encode(['ok' => true, 'token' => $token_new, 'pseudo' => $pseudo, 'color' => $color, 'id' => $id]);
        }
        exit;

    // ── PING (keep alive) ─────────────────────────────────
    case 'ping':
        $user = getUserByToken($db, $token);
        if (!$user) { echo json_encode(['ok' => false]); exit; }
        touchUser($db, $user['id']);
        echo json_encode(['ok' => true]);
        exit;

    // ── ONLINE USERS ──────────────────────────────────────
    case 'online':
        $user = getUserByToken($db, $token);
        if (!$user) { echo json_encode(['ok' => false]); exit; }
        touchUser($db, $user['id']);
        $cutoff = time() - 30;
        $res = $db->query("SELECT id, pseudo, avatar_color, ip_address FROM users WHERE last_seen > $cutoff ORDER BY pseudo ASC");
        $users = [];
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) $users[] = $row;
        echo json_encode(['ok' => true, 'users' => $users]);
        exit;

    // ── SEND MESSAGE ──────────────────────────────────────
    case 'send':
        $user = getUserByToken($db, $token);
        if (!$user) { echo json_encode(['ok' => false, 'error' => 'Non authentifié']); exit; }
        touchUser($db, $user['id']);

        $content     = trim($_POST['content'] ?? '');
        $receiver_id = intval($_POST['receiver_id'] ?? 0) ?: null;
        $room        = $receiver_id ? null : 'general';
        $type        = 'text';
        $file_path   = null;
        $file_name   = null;
        $file_type   = null;

        // File upload
        if (!empty($_FILES['file']['tmp_name'])) {
            $orig     = basename($_FILES['file']['name']);
            $ext      = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
            $allowed  = ['jpg','jpeg','png','gif','webp','pdf','txt','zip','mp4','mp3'];
            if (!in_array($ext, $allowed)) {
                echo json_encode(['ok' => false, 'error' => 'Type de fichier non autorisé']); exit;
            }
            if ($_FILES['file']['size'] > 20 * 1024 * 1024) {
                echo json_encode(['ok' => false, 'error' => 'Fichier trop lourd (max 20 Mo)']); exit;
            }
            $safe_name = uniqid() . '.' . $ext;
            $dest      = __DIR__ . '/../uploads/' . $safe_name;
            move_uploaded_file($_FILES['file']['tmp_name'], $dest);
            $file_path = 'uploads/' . $safe_name;
            $file_name = $orig;
            $file_type = $_FILES['file']['type'];
            $type      = 'file';
            if (!$content) $content = $orig;
        }

        if (!$content && !$file_path) {
            echo json_encode(['ok' => false, 'error' => 'Message vide']); exit;
        }

        $content = htmlspecialchars($content, ENT_QUOTES);
        $stmt = $db->prepare("INSERT INTO messages (sender_id, receiver_id, room, content, file_path, file_name, file_type, type, created_at)
            VALUES (:sid, :rid, :room, :content, :fp, :fn, :ft, :type, :ca)");
        $stmt->bindValue(':sid',     $user['id'], SQLITE3_INTEGER);
        $stmt->bindValue(':rid',     $receiver_id, $receiver_id ? SQLITE3_INTEGER : SQLITE3_NULL);
        $stmt->bindValue(':room',    $room,    $room    ? SQLITE3_TEXT : SQLITE3_NULL);
        $stmt->bindValue(':content', $content);
        $stmt->bindValue(':fp',      $file_path,  $file_path  ? SQLITE3_TEXT : SQLITE3_NULL);
        $stmt->bindValue(':fn',      $file_name,  $file_name  ? SQLITE3_TEXT : SQLITE3_NULL);
        $stmt->bindValue(':ft',      $file_type,  $file_type  ? SQLITE3_TEXT : SQLITE3_NULL);
        $stmt->bindValue(':type',    $type);
        $stmt->bindValue(':ca',      time(), SQLITE3_INTEGER);
        $stmt->execute();
        echo json_encode(['ok' => true, 'id' => $db->lastInsertRowID()]);
        exit;

    // ── FETCH MESSAGES ────────────────────────────────────
    case 'messages':
        $user = getUserByToken($db, $token);
        if (!$user) { echo json_encode(['ok' => false]); exit; }
        touchUser($db, $user['id']);

        $since       = intval($_GET['since'] ?? 0);
        $receiver_id = intval($_GET['receiver_id'] ?? 0) ?: null;

        if ($receiver_id) {
            $uid = $user['id'];
            $stmt = $db->prepare("
                SELECT m.*, u.pseudo as sender_pseudo, u.avatar_color as sender_color
                FROM messages m JOIN users u ON m.sender_id = u.id
                WHERE m.created_at > :since
                  AND m.receiver_id IS NOT NULL
                  AND ((m.sender_id = :uid AND m.receiver_id = :rid)
                    OR (m.sender_id = :rid2 AND m.receiver_id = :uid2))
                ORDER BY m.id ASC LIMIT 200
            ");
            $stmt->bindValue(':since', $since, SQLITE3_INTEGER);
            $stmt->bindValue(':uid',   $uid,   SQLITE3_INTEGER);
            $stmt->bindValue(':rid',   $receiver_id, SQLITE3_INTEGER);
            $stmt->bindValue(':rid2',  $receiver_id, SQLITE3_INTEGER);
            $stmt->bindValue(':uid2',  $uid,   SQLITE3_INTEGER);
        } else {
            $stmt = $db->prepare("
                SELECT m.*, u.pseudo as sender_pseudo, u.avatar_color as sender_color
                FROM messages m JOIN users u ON m.sender_id = u.id
                WHERE m.created_at > :since AND m.room = 'general'
                ORDER BY m.id ASC LIMIT 200
            ");
            $stmt->bindValue(':since', $since, SQLITE3_INTEGER);
        }

        $res = $stmt->execute();
        $msgs = [];
        while ($row = $res->fetchArray(SQLITE3_ASSOC)) $msgs[] = $row;
        echo json_encode(['ok' => true, 'messages' => $msgs, 'server_time' => time()]);
        exit;

    default:
        echo json_encode(['ok' => false, 'error' => 'Action inconnue']);
}
