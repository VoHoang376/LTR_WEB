<?php

/**
 * Các hàm dùng chung cho toàn ứng dụng.
 *
 * Mọi hàm đều được bọc trong function_exists() để có thể ghép an toàn với
 * phần "công việc chung" của nhóm nếu sau này bổ sung trùng tên.
 */

if (!function_exists('e')) {
    /**
     * In ra HTML an toàn. Dùng cho mọi giá trị lấy từ DB/người dùng,
     * trừ nội dung WYSIWYG đã được lọc theo whitelist thẻ.
     */
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /** Lấy (hoặc sinh mới) token CSRF của phiên hiện tại. */
    function csrf_token()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    /** Trả về thẻ <input hidden> chứa token CSRF để chèn vào mọi form POST. */
    function csrf_field()
    {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('csrf_verify')) {
    /** Kiểm tra token CSRF gửi lên có khớp với token trong phiên hay không. */
    function csrf_verify()
    {
        return isset($_POST['csrf_token'], $_SESSION['csrf_token'])
            && is_string($_POST['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    }
}

if (!function_exists('redirect')) {
    /** Chuyển hướng và dừng chương trình. */
    function redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('flash_set')) {
    /** Ghi thông báo một lần (sống qua một lần redirect). */
    function flash_set($key, $message, $type = 'success')
    {
        $_SESSION['_flash'][$key] = ['message' => $message, 'type' => $type];
    }
}

if (!function_exists('flash_get')) {
    /** Đọc và xoá thông báo một lần. Trả về mảng ['message','type'] hoặc null. */
    function flash_get($key)
    {
        if (empty($_SESSION['_flash'][$key])) {
            return null;
        }
        $flash = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);

        return $flash;
    }
}

if (!function_exists('format_price')) {
    /** Định dạng tiền tệ Việt Nam: 2590000 => "2.590.000 đ". */
    function format_price($amount)
    {
        $amount = (float) $amount;
        $decimals = (floor($amount) === $amount) ? 0 : 2;

        return number_format($amount, $decimals, ',', '.') . ' đ';
    }
}

if (!function_exists('format_datetime')) {
    /** Định dạng ngày giờ theo kiểu Việt Nam: 2026-11-21 09:30:00 => 21/11/2026 09:30. */
    function format_datetime($value)
    {
        if (empty($value)) {
            return '—';
        }
        $timestamp = strtotime((string) $value);

        return $timestamp === false ? '—' : date('d/m/Y H:i', $timestamp);
    }
}

if (!function_exists('slugify')) {
    /** Chuyển chuỗi tiếng Việt có dấu thành slug: "Máy in Laser" => "may-in-laser". */
    function slugify($text)
    {
        $text = trim((string) $text);
        $text = mb_strtolower($text, 'UTF-8');

        $map = [
            'a' => 'áàảãạăắằẳẵặâấầẩẫậäåā',
            'd' => 'đð',
            'e' => 'éèẻẽẹêếềểễệëē',
            'i' => 'íìỉĩịîïī',
            'o' => 'óòỏõọôốồổỗộơớờởỡợöō',
            'u' => 'úùủũụưứừửữựûüū',
            'y' => 'ýỳỷỹỵÿ',
            'c' => 'ç',
            'n' => 'ñ',
        ];
        foreach ($map as $latin => $chars) {
            $letters = preg_split('//u', $chars, -1, PREG_SPLIT_NO_EMPTY);
            $text = str_replace($letters, $latin, $text);
        }

        $text = preg_replace('/[^a-z0-9]+/', '-', $text);
        $text = trim((string) $text, '-');
        $text = preg_replace('/-{2,}/', '-', (string) $text);

        return $text !== '' ? $text : 'san-pham';
    }
}

if (!function_exists('upload_image')) {
    /**
     * Lưu một ảnh tải lên vào Public/uploads/<thư mục con>.
     *
     * Chỉ chấp nhận JPG/PNG/WebP, tối đa 2 MB, kiểm tra thật bằng finfo + getimagesize
     * (không tin phần mở rộng hay Content-Type do trình duyệt gửi).
     *
     * @param array|null $file    Phần tử trong $_FILES.
     * @param string     $folder  Thư mục con trong Public/uploads (mặc định "products").
     * @param string|null $error  Nhận thông báo lỗi nếu có.
     * @return string|null Tên tệp đã lưu, hoặc null nếu không có tệp / có lỗi.
     */
    function upload_image($file, $folder = 'products', &$error = null)
    {
        $error = null;

        if ($file === null || !is_array($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error = 'Tải ảnh lên thất bại (mã lỗi ' . (int) $file['error'] . ').';

            return null;
        }
        if ($file['size'] <= 0 || $file['size'] > 2 * 1024 * 1024) {
            $error = 'Ảnh không được vượt quá 2 MB.';

            return null;
        }
        if (!is_uploaded_file($file['tmp_name'])) {
            $error = 'Tệp tải lên không hợp lệ.';

            return null;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($extensions[$mimeType])) {
            $error = 'Chỉ chấp nhận ảnh JPG, PNG hoặc WebP.';

            return null;
        }

        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false || ($imageInfo['mime'] ?? '') !== $mimeType) {
            $error = 'Tệp tải lên không phải là ảnh hợp lệ.';

            return null;
        }

        $directory = dirname(__DIR__) . '/Public/uploads/' . trim($folder, '/');
        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            $error = 'Không thể tạo thư mục lưu ảnh.';

            return null;
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extensions[$mimeType];
        if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $filename)) {
            $error = 'Không thể lưu ảnh lên máy chủ.';

            return null;
        }

        return $filename;
    }
}

if (!function_exists('delete_upload_image')) {
    /** Xoá một ảnh đã lưu (chỉ nhận tên tệp, chặn đường dẫn ../). */
    function delete_upload_image($filename, $folder = 'products')
    {
        if (empty($filename) || basename($filename) !== $filename) {
            return false;
        }

        $path = dirname(__DIR__) . '/Public/uploads/' . trim($folder, '/') . '/' . $filename;

        return is_file($path) && unlink($path);
    }
}

if (!function_exists('upload_url')) {
    /** Sinh URL công khai cho ảnh đã lưu, có ảnh mặc định nếu trống. */
    function upload_url($filename, $folder = 'products')
    {
        // Ảnh mặc định nằm trong Public/img, không nằm trong thư mục uploads.
        if (empty($filename) || $filename === 'no-image.svg' || basename($filename) !== $filename) {
            return 'Public/img/no-image.svg';
        }

        return 'Public/uploads/' . trim($folder, '/') . '/' . rawurlencode($filename);
    }
}

if (!function_exists('query_url')) {
    /**
     * Sinh URL từ query string hiện tại, ghi đè một số tham số.
     * Giá trị null hoặc '' sẽ bị loại bỏ.
     */
    function query_url(array $overrides = [], $base = 'index.php')
    {
        $params = $_GET;
        foreach ($overrides as $key => $value) {
            if ($value === null || $value === '') {
                unset($params[$key]);
            } else {
                $params[$key] = $value;
            }
        }
        unset($params['page']);
        if (isset($overrides['page'])) {
            $params['page'] = $overrides['page'];
        }

        $query = http_build_query($params);

        return $query === '' ? $base : $base . '?' . $query;
    }
}

if (!function_exists('paginate')) {
    /**
     * Tính toán thông tin phân trang.
     *
     * @return array{page:int,perPage:int,total:int,totalPages:int,offset:int,from:int,to:int}
     */
    function paginate($total, $perPage, $currentPage)
    {
        $total = max(0, (int) $total);
        $perPage = max(1, (int) $perPage);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, (int) $currentPage), $totalPages);
        $offset = ($page - 1) * $perPage;

        return [
            'page' => $page,
            'perPage' => $perPage,
            'total' => $total,
            'totalPages' => $totalPages,
            'offset' => $offset,
            'from' => $total === 0 ? 0 : $offset + 1,
            'to' => min($offset + $perPage, $total),
        ];
    }
}

if (!function_exists('render_pagination')) {
    /** Sinh HTML phân trang (Bootstrap 5) giữ nguyên các tham số lọc hiện tại. */
    function render_pagination(array $info)
    {
        if ($info['totalPages'] <= 1) {
            return '';
        }

        $window = 2;
        $start = max(1, $info['page'] - $window);
        $end = min($info['totalPages'], $info['page'] + $window);

        $html = '<nav class="mt-4" aria-label="Phân trang"><ul class="pagination justify-content-center flex-wrap">';

        $html .= '<li class="page-item' . ($info['page'] <= 1 ? ' disabled' : '') . '">'
            . '<a class="page-link" href="' . e(query_url(['page' => max(1, $info['page'] - 1)])) . '"'
            . ($info['page'] <= 1 ? ' tabindex="-1"' : '') . '>Trước</a></li>';

        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . e(query_url(['page' => 1])) . '">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }
        }

        for ($page = $start; $page <= $end; $page++) {
            $active = $page === $info['page'];
            $html .= '<li class="page-item' . ($active ? ' active' : '') . '">'
                . '<a class="page-link" href="' . e(query_url(['page' => $page])) . '"'
                . ($active ? ' aria-current="page"' : '') . '>' . $page . '</a></li>';
        }

        if ($end < $info['totalPages']) {
            if ($end < $info['totalPages'] - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link" href="' . e(query_url(['page' => $info['totalPages']])) . '">'
                . $info['totalPages'] . '</a></li>';
        }

        $html .= '<li class="page-item' . ($info['page'] >= $info['totalPages'] ? ' disabled' : '') . '">'
            . '<a class="page-link" href="' . e(query_url(['page' => min($info['totalPages'], $info['page'] + 1)])) . '"'
            . ($info['page'] >= $info['totalPages'] ? ' tabindex="-1"' : '') . '>Sau</a></li>';

        return $html . '</ul></nav>';
    }
}

if (!function_exists('cart_item_count')) {
    /** Tổng số lượng sản phẩm trong giỏ (đọc trực tiếp session, dùng cho layout). */
    function cart_item_count()
    {
        $count = 0;
        foreach ((array) ($_SESSION['cart'] ?? []) as $quantity) {
            $count += (int) $quantity;
        }

        return $count;
    }
}

if (!function_exists('site_name')) {
    /** Tên website hiển thị trên layout. Khi phần chung có setting('site_name') thì thay tại đây. */
    function site_name()
    {
        return 'LTR Technology';
    }
}

if (!function_exists('json_response')) {
    /** Trả về JSON cho các endpoint AJAX rồi dừng chương trình. */
    function json_response(array $payload, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

if (!function_exists('is_ajax_request')) {
    /** Nhận biết yêu cầu AJAX (fetch) để trả JSON thay vì chuyển trang. */
    function is_ajax_request()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}

if (!function_exists('validate_phone')) {
    /** Kiểm tra số điện thoại Việt Nam (10 số, bắt đầu bằng 0). */
    function validate_phone($phone)
    {
        return (bool) preg_match('/^0\d{9}$/', (string) $phone);
    }
}

if (!function_exists('excerpt')) {
    /** Cắt ngắn văn bản để hiển thị trong danh sách. */
    function excerpt($text, $limit = 120)
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));

        return mb_strlen($text, 'UTF-8') > $limit
            ? mb_substr($text, 0, $limit, 'UTF-8') . '…'
            : $text;
    }
}

if (!function_exists('status_label')) {
    /** Nhãn tiếng Việt cho trạng thái đơn hàng. */
    function order_status_label($status)
    {
        $labels = [
            0 => 'Chờ xác nhận',
            1 => 'Đã xác nhận',
            2 => 'Đang giao',
            3 => 'Hoàn tất',
            4 => 'Đã hủy',
        ];

        return $labels[(int) $status] ?? 'Không xác định';
    }
}

if (!function_exists('order_status_badge')) {
    /** Lớp badge Bootstrap tương ứng trạng thái đơn hàng. */
    function order_status_badge($status)
    {
        $badges = [
            0 => 'warning',
            1 => 'info',
            2 => 'primary',
            3 => 'success',
            4 => 'danger',
        ];

        return $badges[(int) $status] ?? 'secondary';
    }
}

if (!function_exists('payment_method_label')) {
    /** Nhãn phương thức thanh toán. */
    function payment_method_label($method)
    {
        $labels = [
            0 => 'Thanh toán khi nhận hàng (COD)',
            1 => 'Chuyển khoản ngân hàng',
        ];

        return $labels[(int) $method] ?? 'Không xác định';
    }
}
