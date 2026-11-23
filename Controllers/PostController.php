<?php
require_once 'models/PostModel.php';
class PostController{
    private $postModel;
    public function __construct() {
        $this->postModel = new PostModel();
    }
    public function index(){
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 5;
        $offset = ($page - 1) * $limit;
        $posts = $this->postModel->getPosts($limit, $offset);
        $totalPosts = $this->postModel->getTotalPosts();
        $totalPages = ceil($totalPosts / $limit);
        require_once 'views/posts/index.php';
    }
}
public function create(){
    require_once 'views/posts/create.php';
}
public function store(){
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        $title = $_POST['title'];
        $slug = $_POST['slug'];
        $summary = $_POST['summary'];
        $content = $_POST['content'];
        $thumbnail = $_POST['thumbnail'];
        $meta_keywords = $_POST['meta_keywords'];
        $meta_description = $_POST['meta_description'];
        $this->postModel->createPost($title, $slug, $summary, $content, $thumbnail, $meta_keywords, $meta_description);
        header("Location: index.php?controller=post&action=index");
        exit();
    } 
}
public function edit($id){
    $post = $this->postModel->getPostById($id);
    if (!$post){
        die("This content do not existed");
    }
    require_once 'views/posts/edit.php';
}
public function update($id){
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $title = $_POST['title'];
        $slug = $_POST['slug'];
        $summary = $_POST['summary'];
        $content = $_POST['content'];
        $thumbnail = $_POST['thumbnail'];
        $meta_keywords = $_POST['meta_keywords'];
        $meta_description = $_POST['meta_description'];
        $this->postModel->updatePost($id, $title, $slug, $summary, $content, $thumbnail, $meta_keywords, $meta_description);
        header("Location: index.php?controller=post&action=index");
        exit();
    }
}
public function delete($id){
    $this->postModel->deletePost($id);
    header("Location: index.php?controller=post&action=index");
    exit();
}
?>