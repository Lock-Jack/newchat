<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../init_db.php';

try {
    $db = getDB();
    
    // Statistiques globales
    $total_users = $db->querySingle("SELECT COUNT(*) FROM users");
    $total_msgs  = $db->querySingle("SELECT COUNT(*) FROM messages");
    
    // Utilisateurs en ligne (dernières 30 secondes)
    $cutoff = time() - 30;
    $online_users = $db->querySingle("SELECT COUNT(*) FROM users WHERE last_seen > $cutoff");

    // Liste des 15 derniers actifs
    $res = $db->query("SELECT pseudo, last_seen, ip_address, avatar_color FROM users ORDER BY last_seen DESC LIMIT 15");
    $recent = [];
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $recent[] = $row;
    }

    echo json_encode([
        'ok' => true,
        'stats' => [
            'total_users' => $total_users,
            'total_messages' => $total_msgs,
            'online_users' => $online_users
        ],
        'recent_users' => $recent
    ]);

} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
exit;