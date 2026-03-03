<?php
// admin/manage_products.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';

$current_cat = isset($_GET['cat']) ? $_GET['cat'] : 'All';

// =========================================
// ১. Delete Logic
// =========================================
if (isset($_GET['delete'])) {
    try {
        $id = intval($_GET['delete']);
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        if($stmt->execute([$id])){
            $_SESSION['success'] = "Product Deleted Successfully!";
        } else {
            $_SESSION['error'] = "Failed to delete product!";
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Database Error: " . $e->getMessage();
    }
    header("Location: manage_products.php?cat=" . urlencode($current_cat));
    exit;
}

// =========================================
// ২. Edit Category Logic
// =========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_category_submit'])) {
    try {
        $old_cat = $_POST['old_category'];
        $new_cat = trim($_POST['new_category']);
        
        if(!empty($new_cat)) {
            $stmt = $pdo->prepare("UPDATE products SET category = ? WHERE category = ?");
            $stmt->execute([$new_cat, $old_cat]);
            $_SESSION['success'] = "Category Renamed Successfully!";
            header("Location: manage_products.php?cat=" . urlencode($new_cat));
            exit;
        }
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header("Location: manage_products.php?cat=" . urlencode($current_cat));
        exit;
    }
}

// =========================================
// ৩. Add Product Logic
// =========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    try {
        $title = trim($_POST['title']);
        $category = trim($_POST['category']);
        $price = floatval($_POST['price']); // Data type fix
        $stock = intval($_POST['stock']);   // Data type fix
        $image = trim($_POST['image']); 
        $description = trim($_POST['description']);

        $gallery_arr = isset($_POST['gallery']) ? array_filter(array_map('trim', $_POST['gallery'])) : [];
        $gallery_str = implode(',', $gallery_arr);

        $stmt = $pdo->prepare("INSERT INTO products (title, category, price, stock, image, gallery, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$title, $category, $price, $stock, $image, $gallery_str, $description])) {
            $_SESSION['success'] = "Product Added Successfully!";
        } else {
            $_SESSION['error'] = "Failed to add product!";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database Error: " . $e->getMessage();
    }
    header("Location: manage_products.php?cat=" . urlencode($category));
    exit;
}

// =========================================
// ৪. Update Product Logic (Edit Fix)
// =========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_product'])) {
    try {
        $id = intval($_POST['product_id']);
        $title = trim($_POST['title']);
        $category = trim($_POST['category']);
        $price = floatval($_POST['price']); // 100% Fix for update bug
        $stock = intval($_POST['stock']);   // 100% Fix for update bug
        $image = trim($_POST['image']); 
        $description = trim($_POST['description']);

        $gallery_arr = isset($_POST['gallery']) ? array_filter(array_map('trim', $_POST['gallery'])) : [];
        $gallery_str = implode(',', $gallery_arr);

        $stmt = $pdo->prepare("UPDATE products SET title=?, category=?, price=?, stock=?, image=?, gallery=?, description=? WHERE id=?");
        if ($stmt->execute([$title, $category, $price, $stock, $image, $gallery_str, $description, $id])) {
            $_SESSION['success'] = "Product Updated Successfully!";
        } else {
            $err = $stmt->errorInfo();
            $_SESSION['error'] = "Update Failed: " . $err[2];
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Database Error: " . $e->getMessage();
    }
    header("Location: manage_products.php?cat=" . urlencode($category));
    exit;
}

// =========================================
// Data Fetching
// =========================================
$cat_stmt = $pdo->query("SELECT DISTINCT category FROM products WHERE category != ''");
$categories = $cat_stmt->fetchAll(PDO::FETCH_COLUMN);

if ($current_cat === 'All') {
    $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll();
} else {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
    $stmt->execute([$current_cat]);
    $products = $stmt->fetchAll();
}

$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $edit_stmt->execute([$_GET['edit_id']]);
    $edit_data = $edit_stmt->fetch();
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .form-row { display: flex; gap: 20px; margin-bottom: 15px; flex-wrap: wrap; }
    .form-row .form-group { flex: 1; min-width: 200px; }
    .form-group label { display: block; margin-bottom: 8px; color: #ccc; font-size: 14px; font-weight: 600; }
    .form-group input, .form-group select, .form-group textarea { 
        width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); 
        color: #fff; border-radius: 8px; outline: none; transition: 0.3s; font-family: 'Poppins', sans-serif;
    }
    .form-group input:focus, .form-group textarea:focus { border-color: var(--accent-orange); box-shadow: 0 0 10px rgba(255,115,0,0.2); }
    
    .btn-primary { background: var(--accent-orange); color: #fff; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; font-size: 15px; width: 100%; }
    .btn-primary:hover { background: #e66800; box-shadow: 0 0 15px rgba(255, 115, 0, 0.4); }
    
    .btn-action { background: rgba(255,255,255,0.1); color: #fff; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; transition: 0.3s; margin-right: 5px; border: 1px solid rgba(255,255,255,0.2); cursor: pointer;}
    .btn-action:hover { background: var(--accent-orange); border-color: var(--accent-orange); }
    
    .btn-danger { background: rgba(231, 76, 60, 0.2); color: #e74c3c; border: 1px solid #e74c3c; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; font-weight: bold; transition: 0.3s; cursor: pointer; display: flex; align-items: center; justify-content: center; }
    .btn-danger:hover { background: #e74c3c; color: #fff; }
    
    .product-img-preview { width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2); }

    .category-tabs { display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px; align-items: center; }
    .cat-btn { padding: 10px 22px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 30px; color: #fff; text-decoration: none; font-size: 14px; transition: 0.3s; font-weight: 600; }
    .cat-btn:hover { background: rgba(255,115,0,0.2); border-color: var(--accent-orange); }
    .cat-btn.active { background: var(--accent-orange); color: #fff; box-shadow: 0 0 15px rgba(255,115,0,0.4); border-color: var(--accent-orange); }
    
    .action-cat-btn { background: none; border: 1px dashed var(--accent-orange); color: var(--accent-orange); cursor: pointer; }
    .action-cat-btn:hover { background: var(--accent-orange); color: #fff; border-style: solid; }

    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(5px); z-index: 2000; display: none; justify-content: center; align-items: center; }
    .modal-overlay.active { display: flex; }
    .modal-box { background: #111; padding: 30px; border-radius: 15px; border: 1px solid rgba(255,255,255,0.1); width: 400px; max-width: 90%; position: relative; }
    .close-modal { position: absolute; top: 15px; right: 20px; font-size: 20px; cursor: pointer; color: #aaa; transition: 0.3s; }
    .close-modal:hover { color: #ff4757; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 style="font-weight: 800;">MANAGE <span style="color: var(--accent-orange);">PRODUCTS</span></h1>
</div>

<div class="category-tabs">
    <a href="manage_products.php?cat=All" class="cat-btn <?= $current_cat == 'All' ? 'active' : '' ?>">
        <i class="fa-solid fa-layer-group"></i> All Products
    </a>
    
    <?php foreach($categories as $c): ?>
        <a href="manage_products.php?cat=<?= urlencode($c) ?>" class="cat-btn <?= $current_cat == $c ? 'active' : '' ?>">
            <i class="fa-solid fa-folder"></i> <?= htmlspecialchars($c) ?>
        </a>
    <?php endforeach; ?>
    
    <button class="cat-btn action-cat-btn" onclick="openModal('addCatModal')">
        <i class="fa-solid fa-plus"></i> Add Category
    </button>

    <?php if($current_cat !== 'All' && in_array($current_cat, $categories)): ?>
        <button class="cat-btn action-cat-btn" style="border-color: #3498db; color: #3498db;" onclick="openModal('editCatModal')">
            <i class="fa-solid fa-pen-to-square"></i> Edit Category
        </button>
    <?php endif; ?>
</div>

<div class="glass-card" style="margin-bottom: 40px; border-top: 4px solid var(--accent-orange);">
    <h3 style="margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
        <?php if($edit_data): ?>
            <i class="fa-solid fa-pen-to-square" style="color: var(--accent-orange);"></i> Update Product
        <?php else: ?>
            <i class="fa-solid fa-plus-circle" style="color: var(--accent-orange);"></i> Add New Product 
            <?= $current_cat !== 'All' ? 'under "'.htmlspecialchars($current_cat).'"' : '' ?>
        <?php endif; ?>
    </h3>
    
    <form action="manage_products.php?cat=<?= urlencode($current_cat) ?>" method="POST">
        <?php if($edit_data): ?>
            <input type="hidden" name="product_id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Product Title</label>
                <input type="text" name="title" value="<?= $edit_data ? htmlspecialchars($edit_data['title']) : '' ?>" placeholder="e.g. Premium Smart Watch" required <?= !$edit_data ? 'autofocus' : '' ?>>
            </div>
            <div class="form-group">
                <label>Category</label>
                <?php if ($current_cat !== 'All' && !$edit_data): ?>
                    <input type="text" name="category" value="<?= htmlspecialchars($current_cat) ?>" readonly style="background: rgba(255,115,0,0.1); color: #ff7300; border-color: #ff7300; font-weight: bold;">
                <?php else: ?>
                    <input type="text" name="category" value="<?= $edit_data ? htmlspecialchars($edit_data['category']) : '' ?>" placeholder="e.g. Electronics, Shoes" required>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Price (৳)</label>
                <input type="number" name="price" step="0.01" value="<?= $edit_data ? $edit_data['price'] : '' ?>" placeholder="e.g. 2500" required>
            </div>
            <div class="form-group">
                <label>Stock Quantity</label>
                <input type="number" name="stock" value="<?= $edit_data ? $edit_data['stock'] : '' ?>" placeholder="e.g. 50" required>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 15px;">
            <label>Main Image URL</label>
            <div style="display: flex; gap: 15px; align-items: center;">
                <input type="text" name="image" id="main_image_input" value="<?= $edit_data ? htmlspecialchars($edit_data['image']) : '' ?>" placeholder="Paste main image link here" required oninput="previewImage(this.value)">
                <img id="main_image_preview" src="<?= $edit_data ? htmlspecialchars($edit_data['image']) : 'https://via.placeholder.com/60x60?text=No+Img' ?>" alt="Preview" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; border: 1px solid rgba(255,255,255,0.2);">
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px; background: rgba(0,0,0,0.2); padding: 15px; border-radius: 10px; border: 1px dashed rgba(255,255,255,0.1);">
            <label style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                <span>Gallery Images (Optional Design Symbols)</span>
                <button type="button" class="btn-action" style="background:#3498db; border-color:#3498db; padding:6px 12px;" onclick="addGalleryField()">
                    <i class="fa-solid fa-plus"></i> Add Image Link
                </button>
            </label>
            
            <div id="gallery_container">
                <?php 
                $existing_gallery = [];
                if($edit_data && !empty($edit_data['gallery'])) {
                    $existing_gallery = explode(',', $edit_data['gallery']);
                }
                
                if(count($existing_gallery) > 0) {
                    foreach($existing_gallery as $g_link) {
                        if(trim($g_link) !== "") {
                            echo '<div style="display:flex; gap:10px; margin-bottom:10px;" class="gallery-row">';
                            echo '<input type="text" name="gallery[]" value="'.htmlspecialchars(trim($g_link)).'" placeholder="Paste gallery image link here">';
                            echo '<button type="button" class="btn-danger" style="padding: 0 15px;" onclick="this.parentElement.remove()"><i class="fa-solid fa-trash"></i></button>';
                            echo '</div>';
                        }
                    }
                } else {
                    echo '<div style="display:flex; gap:10px; margin-bottom:10px;" class="gallery-row">';
                    echo '<input type="text" name="gallery[]" placeholder="Paste gallery image link here">';
                    echo '<button type="button" class="btn-danger" style="padding: 0 15px;" onclick="this.parentElement.remove()"><i class="fa-solid fa-trash"></i></button>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label>Product Description</label>
            <textarea name="description" rows="4" required><?= $edit_data ? htmlspecialchars($edit_data['description']) : '' ?></textarea>
        </div>

        <div style="display: flex; gap: 15px;">
            <button type="submit" name="<?= $edit_data ? 'update_product' : 'add_product' ?>" class="btn-primary">
                <?= $edit_data ? 'Update Product' : 'Upload Product' ?> <i class="fa-solid <?= $edit_data ? 'fa-save' : 'fa-upload' ?>"></i>
            </button>
            <?php if($edit_data): ?>
                <a href="manage_products.php?cat=<?= urlencode($current_cat) ?>" class="btn-primary" style="background: #333; text-align: center; text-decoration: none;">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="glass-card">
    <h3 style="margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px;">
        <i class="fa-solid fa-list" style="color: var(--accent-orange);"></i> 
        <?= $current_cat == 'All' ? 'All Products' : htmlspecialchars($current_cat).' Products' ?> List
    </h3>

    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($products as $product): ?>
                    <?php $img_src = filter_var($product['image'], FILTER_VALIDATE_URL) ? $product['image'] : '../assets/images/products/'.$product['image']; ?>
                    <tr>
                        <td><img src="<?= $img_src ?>" class="product-img-preview" alt="img" onerror="this.src='https://via.placeholder.com/50x50?text=No+Img'"></td>
                        <td style="font-weight: bold;"><?= htmlspecialchars($product['title']) ?></td>
                        <td><span style="background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 15px; font-size: 12px;"><?= htmlspecialchars($product['category']) ?></span></td>
                        <td style="color: var(--accent-orange); font-weight: bold;">৳ <?= number_format($product['price'], 2) ?></td>
                        <td>
                            <?php if($product['stock'] > 0): ?>
                                <span style="color: #2ecc71; font-weight: bold;"><?= $product['stock'] ?> in stock</span>
                            <?php else: ?>
                                <span style="color: #e74c3c; font-weight: bold;">Out of stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="manage_products.php?cat=<?= urlencode($current_cat) ?>&edit_id=<?= $product['id'] ?>" class="btn-action">
                                <i class="fa-solid fa-pen"></i> Edit
                            </a>
                            <button type="button" class="btn-danger" style="display:inline-block;" onclick="confirmDelete(<?= $product['id'] ?>, '<?= urlencode($current_cat) ?>')">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if(count($products) == 0): ?>
                    <tr><td colspan="6" style="text-align: center; color: #aaa; padding: 30px;">No products found!</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div> 

<div class="modal-overlay" id="addCatModal">
    <div class="modal-box">
        <i class="fa-solid fa-xmark close-modal" onclick="closeModal('addCatModal')"></i>
        <h3 style="margin-bottom: 20px; color: var(--accent-orange);">Add New Category</h3>
        <div class="form-group">
            <label>Category Name</label>
            <input type="text" id="new_category_name" placeholder="Enter category name...">
        </div>
        <button class="btn-primary" onclick="submitNewCategory()" style="margin-top: 10px;">Proceed to Add Product</button>
    </div>
</div>

<div class="modal-overlay" id="editCatModal">
    <div class="modal-box">
        <i class="fa-solid fa-xmark close-modal" onclick="closeModal('editCatModal')"></i>
        <h3 style="margin-bottom: 20px; color: #3498db;">Rename Category</h3>
        <form action="" method="POST">
            <input type="hidden" name="old_category" value="<?= htmlspecialchars($current_cat) ?>">
            <div class="form-group">
                <label>Current Name: <b><?= htmlspecialchars($current_cat) ?></b></label>
                <input type="text" name="new_category" value="<?= htmlspecialchars($current_cat) ?>" required>
            </div>
            <button type="submit" name="edit_category_submit" class="btn-primary" style="background: #3498db; margin-top: 10px;">Update Category</button>
        </form>
    </div>
</div>

<script>
    // Sweet Alert Success/Error Messages
    <?php if(isset($_SESSION['success'])): ?>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '<?= $_SESSION['success'] ?>',
            background: '#111',
            color: '#fff',
            confirmButtonColor: 'var(--accent-orange)'
        });
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?= $_SESSION['error'] ?>',
            background: '#111',
            color: '#fff',
            confirmButtonColor: '#e74c3c'
        });
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    // SweetAlert Delete Confirmation
    function confirmDelete(id, cat) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            background: '#111',
            color: '#fff',
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#333',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "manage_products.php?cat=" + cat + "&delete=" + id;
            }
        });
    }

    // Category Modal Functions
    function openModal(id) { document.getElementById(id).classList.add('active'); }
    function closeModal(id) { document.getElementById(id).classList.remove('active'); }

    function submitNewCategory() {
        var catName = document.getElementById('new_category_name').value.trim();
        if(catName !== "") {
            window.location.href = "manage_products.php?cat=" + encodeURIComponent(catName);
        } else {
            Swal.fire('Warning', 'Please enter a category name!', 'warning');
        }
    }

    // Dynamic Gallery Input Field
    function addGalleryField() {
        const container = document.getElementById('gallery_container');
        const div = document.createElement('div');
        div.style.cssText = 'display:flex; gap:10px; margin-bottom:10px;';
        div.innerHTML = `
            <input type="text" name="gallery[]" placeholder="Paste gallery image link here" style="width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.2); color: #fff; border-radius: 8px; outline: none;">
            <button type="button" class="btn-danger" style="padding: 0 15px;" onclick="this.parentElement.remove()"><i class="fa-solid fa-trash"></i></button>
        `;
        container.appendChild(div);
    }

    // Live Image Preview Function
    function previewImage(url) {
        const img = document.getElementById('main_image_preview');
        if(url.trim() !== '') {
            img.src = url;
        } else {
            img.src = 'https://via.placeholder.com/60x60?text=No+Img';
        }
    }
</script>

</body>
</html>