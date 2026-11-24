<?php
/** Khung dưới khu quản trị. */
?>
    </main>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container"></div>

<?php
$adminJsVer = file_exists(__DIR__ . '/../../../Public/js/admin.js')
    ? filemtime(__DIR__ . '/../../../Public/js/admin.js')
    : 1;
?>
<script src="Public/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="Public/js/validate.js"></script>
<script src="Public/js/admin.js?v=<?= (int) $adminJsVer ?>"></script>
</body>
</html>
