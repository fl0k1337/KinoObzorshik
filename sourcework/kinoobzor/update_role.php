<?php
include 'db.php';
session_start();

if (!isset($_SESSION['role_name']) || $_SESSION['role_name'] !== 'admin') {
    die(json_encode(['success' => false, 'error' => 'Доступ запрещен']));
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $data['user_id'] ?? null;
$role_id = $data['role_id'] ?? null;

try {
    // Обновляем роль в базе
    $stmt = $db->prepare("UPDATE users SET role_id = ? WHERE id = ?");
    $stmt->execute([$role_id, $user_id]);
    
    // Если обновляем свою собственную роль - обновляем сессию
    if ($user_id == $_SESSION['user_id']) {
        $stmt = $db->prepare("SELECT roles.name FROM roles WHERE id = ?");
        $stmt->execute([$role_id]);
        $new_role = $stmt->fetchColumn();
        
        $_SESSION['role_name'] = $new_role;
        $_SESSION['role_id'] = $role_id;
    }
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}