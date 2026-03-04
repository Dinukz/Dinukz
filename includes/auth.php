<?php
require_once __DIR__ . '/../config/db.php';

function currentUser(): ?array
{
    global $pdo;
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireLogin(): array
{
    $user = currentUser();
    if (!$user) {
        header('Location: login.php?error=Please+login+first');
        exit;
    }
    return $user;
}

function requireRole(array $roles): array
{
    $user = requireLogin();
    if (!in_array($user['role'], $roles, true)) {
        http_response_code(403);
        die('Access denied.');
    }
    return $user;
}
