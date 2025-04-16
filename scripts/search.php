<?php
header('Content-Type: application/json');
require_once '../scripts/db.php';


if (isset($_GET['query']) && !empty($_GET['query'])) {
    $search = trim($_GET['query']);
    $stmt = $pdo->prepare("SELECT id, title, description, image FROM products WHERE title LIKE ? OR description LIKE ?");
    $stmt->execute(["%$search%", "%$search%"]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Убедитесь, что возвращаем правильный JSON
    echo json_encode($results);
    exit; // Завершаем выполнение после отправки JSON
} else {
    echo json_encode([]);
    exit;
}
?>