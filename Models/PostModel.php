<?php
require_once __DIR__ . '/../Config/database.php';
    class PostModel extends BaseModel {
        public function getPosts($keyword = '', $limit = 10, $offset = 0) {
            $sql = "SELECT * FROM posts ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
public function getPosts($keyword = '', $limit = 10, $offset = 0) {
    $sql = "SELECT * FROM posts";
    if (!empty($keyword)){
        $sql .= " WHERE title LIKE :keyword";
    }
    $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    $stmt = $this->conn->prepare($sql);
    if (!empty($keyword)){
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function getTotalPosts($keyword = ''){
    $sql = "SELECT COUNT(id) as total FROM posts";
    if (!empty($keyword)){
        $sql .= " WHERE title LIKE :keyword";
    }
    $stmt = $this->conn->prepare($sql);
    if (!empty($keyword)){
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
    }
    $stmt->execute();
    return $stmt->fetchColumn();
}
public function getPostById($id){
    $sql = "SELECT * FROM posts WHERE id = :id LIMIT 1";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
public function createPost($title, $slug, $summary, $content, $thumbnail, $meta_keywords, $meta_description){
    $sql = "INSERT INTO posts (title, slug, summary, content, thumbnail, meta_keywords, meta_description, created_at) VALUES (:title, :slug, :summary, :content, :thumbnail, :meta_keywords, :meta_description, NOW())";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
    $stmt->bindParam(':summary', $summary, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':thumbnail', $thumbnail, PDO::PARAM_STR);
    $stmt->bindParam(':meta_keywords', $meta_keywords, PDO::PARAM_STR);
    $stmt->bindParam(':meta_description', $meta_description, PDO::PARAM_STR);
    $stmt->execute();
    return $this->conn->lastInsertId();
}
public function updatePost($id, $title, $slug, $summary, $content, $thumbnail, $meta_keywords, $meta_description) {
    $sql = "UPDATE posts SET title = :title, slug = :slug, summary = :summary, content = :content, meta_keywords = :meta_keywords, meta_description = :meta_description";
    if (!empty($thumbnail)) {
        $sql .= ", thumbnail = :thumbnail";
    }
    $sql .= " WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':title', $title, PDO::PARAM_STR);
    $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
    $stmt->bindParam(':summary', $summary, PDO::PARAM_STR);
    $stmt->bindParam(':content', $content, PDO::PARAM_STR);
    $stmt->bindParam(':meta_keywords', $meta_keywords, PDO::PARAM_STR);
    $stmt->bindParam(':meta_description', $meta_description, PDO::PARAM_STR);
    if (!empty($thumbnail)) {
        $stmt->bindParam(':thumbnail', $thumbnail, PDO::PARAM_STR);
    }
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
public function deletePost($i){
    $sql = "DELETE FROM posts WHERE id = :id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    return $stmt->execute();
}
?>