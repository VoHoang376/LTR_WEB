<?php
$adminJsVer = file_exists(__DIR__ . '/../../../Public/js/admin.js')
    ? filemtime(__DIR__ . '/../../../Public/js/admin.js')
    : 1;
?>
    </main>
</div>

<script src="Public/js/admin.js?v=<?= (int) $adminJsVer ?>"></script>
</body>
</html>
