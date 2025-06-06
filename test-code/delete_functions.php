<?php
function deleteProduct($pdo, $id) {
    $sql = 'DELETE FROM products WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->rowCount();
}
