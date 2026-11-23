<?php

abstract class BaseController
{
    protected $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    protected function requireAdmin()
    {
        if (empty($_SESSION['user_id'])) {
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        require_once __DIR__ . '/../Models/UserModel.php';
        $userModel = new UserModel($this->db);
        $user = $userModel->findById($_SESSION['user_id']);

        if (!$user || (int) $user['status'] !== 1) {
            $_SESSION = [];
            session_destroy();
            header('Location: index.php?controller=auth&action=login');
            exit;
        }

        if ((int) $user['role'] !== 0) {
            http_response_code(403);
            exit('Bạn không có quyền truy cập chức năng quản trị.');
        }

        $_SESSION['role'] = (int) $user['role'];
        $_SESSION['fullname'] = $user['fullname'];

        return $user;
    }

    protected function csrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    protected function hasValidCsrfToken()
    {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && is_string($_POST['csrf_token'])
            && is_string($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }

    protected function postedString($name, $default = '')
    {
        $value = $_POST[$name] ?? $default;

        return is_string($value) ? trim($value) : '';
    }

    protected function requirePost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Phương thức yêu cầu không được hỗ trợ.');
        }
    }

    protected function findPage()
    {
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);
        return $page !== false && $page > 0 ? $page : 1;
    }

    protected function pagination($totalItems, $page, $perPage)
    {
        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $page = min($page, $totalPages);

        return [
            'page' => $page,
            'perPage' => $perPage,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'offset' => ($page - 1) * $perPage,
        ];
    }

    protected function consumeFlash()
    {
        $message = $_SESSION['content_flash'] ?? null;
        unset($_SESSION['content_flash']);

        return $message;
    }

    protected function redirectWithMessage($url, $message)
    {
        $_SESSION['content_flash'] = $message;
        header('Location: ' . $url);
        exit;
    }

    protected function sanitizeRichText($html)
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previousErrorMode = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="rich-content">' . $html . '</div>'
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previousErrorMode);

        if (!$loaded) {
            throw new RuntimeException('Không thể xử lý nội dung định dạng.');
        }

        $wrapper = null;
        foreach ($document->getElementsByTagName('div') as $element) {
            if ($element->getAttribute('id') === 'rich-content') {
                $wrapper = $element;
                break;
            }
        }

        if (!$wrapper) {
            throw new RuntimeException('Không tìm thấy vùng nội dung cần xử lý.');
        }

        $this->sanitizeChildren($wrapper);
        $cleanHtml = '';
        foreach ($wrapper->childNodes as $child) {
            $cleanHtml .= $document->saveHTML($child);
        }

        return $cleanHtml;
    }

    private function sanitizeChildren(DOMNode $parent)
    {
        $allowedTags = [
            'a', 'b', 'blockquote', 'br', 'code', 'del', 'em', 'h1', 'h2', 'h3',
            'h4', 'i', 'li', 'ol', 'p', 'pre', 's', 'strong', 'sub', 'sup', 'u', 'ul',
        ];
        $removeWithContent = ['iframe', 'object', 'script', 'style', 'svg'];

        for ($node = $parent->firstChild; $node !== null; ) {
            $nextNode = $node->nextSibling;

            if ($node instanceof DOMElement) {
                $tagName = strtolower($node->tagName);

                if (in_array($tagName, $removeWithContent, true)) {
                    $parent->removeChild($node);
                    $node = $nextNode;
                    continue;
                }

                $this->sanitizeChildren($node);

                if (!in_array($tagName, $allowedTags, true)) {
                    while ($node->firstChild) {
                        $parent->insertBefore($node->firstChild, $node);
                    }
                    $parent->removeChild($node);
                    $node = $nextNode;
                    continue;
                }

                $safeHref = null;
                if ($tagName === 'a' && $node->hasAttribute('href')) {
                    $href = trim($node->getAttribute('href'));
                    $parts = parse_url($href);
                    $scheme = is_array($parts) ? strtolower($parts['scheme'] ?? '') : '';

                    if ($parts !== false
                        && strpos($href, '//') !== 0
                        && ($scheme === '' || in_array($scheme, ['http', 'https', 'mailto'], true))) {
                        $safeHref = $href;
                    }
                }

                while ($node->attributes->length > 0) {
                    $node->removeAttributeNode($node->attributes->item(0));
                }

                if ($safeHref !== null) {
                    $node->setAttribute('href', $safeHref);
                    $node->setAttribute('rel', 'nofollow noopener noreferrer');
                }
            }

            $node = $nextNode;
        }
    }
}
