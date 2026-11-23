<?php
public function getCommentsByPostId($post_id){
    $sql = "SELECT * FROM comments WHERE post_id = :post_id AND status = 1 ORDER BY created_at DESC";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function createComment($post_id, $user_id, $content){
    $status = 1;
    $sql = "INSERT INTO comments (post_id, user_id, content, status, created_at) VALUES (:post_id, :user_id, :content, :status, NOW())";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    $stmt->execute();
    return $this->conn->lastInsertId();
}
public function getAllComments($limit, $offset){
    $sql = "SELECT comments.*, posts.title as post_title FROM comments JOIN posts ON comments.post_id = posts.id ORDER BY comments.created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function updateCommentStatus($id, $status){
    $sql = "UPDATE comments SET status = :status WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    return $stmt->execute();
}
public function deleteComment($id){
    $sql = "DELETE FROM comments WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
?>