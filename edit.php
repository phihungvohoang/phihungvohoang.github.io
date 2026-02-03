<?php
$file = './json/data.json';
$data = json_decode(file_get_contents($file), true);
$sku = isset($_GET['sku']) ? $_GET['sku'] : null;

// --- XỬ LÝ XÓA SẢN PHẨM ---
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $data = array_filter($data, function ($item) use ($delete_id) {
        return $item['id'] != $delete_id;
    });
    // Reset lại index mảng để JSON đẹp hơn
    $data = array_values($data);
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: edit.php");
    exit;
}

// --- XỬ LÝ CẬP NHẬT ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    if (empty($_POST['id']) || empty($_POST['original_sku'])) {
        header("Location: edit.php?error=empty_sku");
        exit;
    }
    $target_sku = $_POST['original_sku'];
    foreach ($data as $key => $item) {
        if ($item['id'] == $target_sku) {
            $data[$key] = [
                "id" => $_POST['id'],
                "name" => $_POST['name'],
                "description" => $_POST['description'],
                "price" => (int)$_POST['price'],
                "qty" => (int)$_POST['qty'],
                "tag" => [
                    "type" => $_POST['tag_type'],
                    "category" => $_POST['tag_category'],
                    "brand" => $_POST['tag_brand'],
                    "color" => $_POST['tag_color']
                ],
                "img1" => $_POST['img1'],
                "img2" => $_POST['img2'],
                "img3" => $_POST['img3'],
                "img4" => $_POST['img4'],
                "img5" => $_POST['img5']
            ];
            break;
        }
    }
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: edit.php");
    exit;
}

$p = null;
if ($sku) {
    foreach ($data as $item) {
        if ($item['id'] == $sku) {
            $p = $item;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý sản phẩm - Hazel Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

    <style>
        .product-img-td {
            width: 50px;
            height: 50px;
            object-fit: contain;
            background: #fff;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0;
        }
    </style>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.html">
                HAZEL <span style="color: #ffcc00;">ADMIN</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <div class="navbar-nav me-auto">
                    <a class="nav-link active" href="edit.php"><i class="fa-solid fa-boxes-stacked me-1"></i> Sản
                        phẩm</a>
                </div>

                <div class="d-flex gap-2">
                    <a href="view_orders.php" class="btn btn-warning btn-sm fw-bold">
                        <i class="fa-solid fa-cart-shopping me-1"></i> Đơn hàng
                    </a>

                    <a href="add.php" class="btn btn-success btn-sm fw-bold">
                        <i class="fa fa-plus me-1"></i> Thêm sản phẩm
                    </a>

                    <a href="index.html" class="btn btn-outline-light btn-sm">
                        <i class="fa-solid fa-house"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container pb-5">
        <?php if (!$sku): ?>
            <div class="card shadow-sm border-0 p-3">
                <table id="productTable" class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Ảnh</th>
                            <th>SKU</th>
                            <th>Tên</th>
                            <th>Giá</th>
                            <th>Kho</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $item): ?>
                            <tr>
                                <td><img data-src="<?= $item['img1'] ?>" class="lazy product-img-td rounded border"
                                        src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7">
                                </td>
                                <td><code><?= $item['id'] ?></code></td>
                                <td class="fw-bold small"><?= $item['name'] ?></td>
                                <td class="text-danger"><?= number_format((float)$item['price']) ?>đ</td>
                                <td><?= $item['qty'] ?></td>
                                <td>
                                    <a href="edit.php?sku=<?= $item['id'] ?>" class="btn btn-primary btn-sm"><i
                                            class="fa fa-edit"></i></a>
                                    <a href="edit.php?delete=<?= $item['id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Xóa?')"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php elseif ($p): ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white d-flex justify-content-between py-3">
                            <span class="fw-bold">Chỉnh sửa: <?= $p['name'] ?></span>
                            <a href="edit.php" class="text-white text-decoration-none small">Hủy & Quay lại</a>
                        </div>
                        <div class="card-body p-4">
                            <form method="POST">
                                <input type="hidden" name="original_sku" value="<?= $p['id'] ?>">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Mã SKU (ID)</label>
                                        <input type="text" name="id" class="form-control" value="<?= $p['id'] ?>"
                                            required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Tên xe</label>
                                        <input type="text" name="name" class="form-control" value="<?= $p['name'] ?>"
                                            required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold small">Mô tả</label>
                                        <textarea name="description" class="form-control"
                                            rows="3"><?= $p['description'] ?></textarea>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Giá bán</label>
                                        <input type="number" name="price" class="form-control"
                                            value="<?= (int)$p['price'] ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small">Số lượng tồn</label>
                                        <input type="number" name="qty" class="form-control"
                                            value="<?= (int)$p['qty'] ?>">
                                    </div>

                                    <div class="col-12 py-2 border-top border-bottom bg-light">
                                        <span class="text-primary fw-bold">Phân loại (Tags)</span>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Type</label>
                                        <input type="text" name="tag_type" class="form-control"
                                            value="<?= $p['tag']['type'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Loại xe</label>
                                        <input type="text" name="tag_category" class="form-control"
                                            value="<?= $p['tag']['category'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Màu sắc</label>
                                        <input type="text" name="tag_color" class="form-control"
                                            value="<?= $p['tag']['color'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Brand</label>
                                        <input type="text" name="tag_brand" class="form-control"
                                            value="<?= $p['tag']['brand'] ?? '' ?>">
                                    </div>
                                    <div class="col-12 py-2 border-bottom bg-light mb-3">
                                        <span class="text-primary fw-bold">Hình ảnh (Links)</span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <div class="border rounded d-flex align-items-center justify-content-center bg-white"
                                                style="height: 300px; overflow: hidden;">
                                                <img id="preview-img-1"
                                                    src="<?= !empty($p['img1']) ? $p['img1'] : 'https://cf.shopee.vn/file/vn-11134207-7ras8-m581uu1hoawmbf' ?>"
                                                    alt="Preview"
                                                    style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <div class="mb-2">
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text" style="width: 80px;">Ảnh
                                                            <?= $i ?></span>
                                                        <input type="text" name="img<?= $i ?>" id="input-img-<?= $i ?>"
                                                            class="form-control" placeholder="Dán link hình ảnh vào đây..."
                                                            value="<?= $p['img' . $i] ?? '' ?>"
                                                            oninput="<?= $i === 1 ? 'updatePreview(this.value)' : '' ?>">
                                                    </div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4 text-end">
                                        <button type="submit" name="update_product"
                                            class="btn btn-primary px-5 fw-bold">LƯU THAY ĐỔI</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.8.3/dist/lazyload.min.js"></script>
    <script>
        $(document).ready(function() {
            var myLazyLoad = new LazyLoad({
                elements_selector: ".lazy"
            });
            var table = $('#productTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json"
                }
            });
            table.on('draw', () => myLazyLoad.update());
        });

        function updatePreview(url) {
            const preview = document.getElementById('preview-img-1');
            const placeholder = 'https://via.placeholder.com/300?text=Preview+picture+1';

            if (url.trim() !== "") {
                preview.src = url;
                // Xử lý trường hợp link ảnh lỗi
                preview.onerror = function() {
                    this.src = 'https://via.placeholder.com/300?text=Link+Image+Error';
                };
            } else {
                preview.src = placeholder;
            }
        }
    </script>

</body>

</html>