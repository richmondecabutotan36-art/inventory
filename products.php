<?php
require "header.php";
require "config.php";

// DELETE PRODUCT
if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: products.php");
    exit;
}

// ADD OR EDIT PRODUCT
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    $name = trim($_POST["product_name"] ?? "");
    $cat = (int) ($_POST["category_id"] ?? 0);
    $price = (float) ($_POST["price"] ?? 0);
    $qty = (int) ($_POST["quantity"] ?? 0);
    $reorder = (int) ($_POST["reorder_level"] ?? 10);
    $supplier = (int) ($_POST["supplier_id"] ?? 0);

    if (
        $name !== "" &&
        $cat > 0 &&
        $supplier > 0 &&
        $price >= 0 &&
        $qty >= 0 &&
        $reorder >= 0
    ) {
        if ($action === "edit") {
            $id = (int) ($_POST["id"] ?? 0);

            $stmt = $conn->prepare(
                "UPDATE products
                 SET product_name = ?, category_id = ?, price = ?,
                     quantity = ?, reorder_level = ?, supplier_id = ?
                 WHERE id = ?"
            );

            $stmt->bind_param(
                "sidiiii",
                $name,
                $cat,
                $price,
                $qty,
                $reorder,
                $supplier,
                $id
            );

            $stmt->execute();
        } elseif ($action === "add") {
            $stmt = $conn->prepare(
                "INSERT INTO products
                (product_name, category_id, price, quantity,
                 reorder_level, supplier_id)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sidiii",
                $name,
                $cat,
                $price,
                $qty,
                $reorder,
                $supplier
            );

            $stmt->execute();
        }
    }

    header("Location: products.php");
    exit;
}

// DROPDOWN DATA
$cats = $conn->query("SELECT * FROM categories ORDER BY name");
$sups = $conn->query("SELECT * FROM suppliers ORDER BY name");

// SEPARATE RESULTS FOR EDIT MODAL
$editCats = $conn->query("SELECT * FROM categories ORDER BY name");
$editSups = $conn->query("SELECT * FROM suppliers ORDER BY name");

// PRODUCT LIST
$products = $conn->query(
    "SELECT p.*, c.name AS category, s.name AS supplier
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     LEFT JOIN suppliers s ON p.supplier_id = s.id
     ORDER BY p.id DESC"
);
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Products</h2>

    <button class="btn btn-primary"
            data-bs-toggle="modal"
            data-bs-target="#addProduct">
        + Add Product
    </button>
</div>

<div class="table-responsive card shadow-sm">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Product</th>
                <th>Category</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($p = $products->fetch_assoc()): ?>
            <tr>
                <td><?= (int) $p["id"] ?></td>

                <td>
                    <?= htmlspecialchars($p["product_name"]) ?>
                </td>

                <td>
                    <?= htmlspecialchars($p["category"] ?? "") ?>
                </td>

                <td>
                    ₱<?= number_format((float) $p["price"], 2) ?>
                </td>

                <td><?= (int) $p["quantity"] ?></td>

                <td>
                    <?= htmlspecialchars($p["supplier"] ?? "") ?>
                </td>

                <td>
                    <?php if ($p["quantity"] == 0): ?>
                        <span class="badge bg-danger">Out</span>
                    <?php elseif ($p["quantity"] <= $p["reorder_level"]): ?>
                        <span class="badge bg-warning text-dark">Low</span>
                    <?php else: ?>
                        <span class="badge bg-success">Available</span>
                    <?php endif; ?>
                </td>

                <td class="text-nowrap">
                    <!-- EDIT BUTTON -->
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary edit-btn"
                        data-bs-toggle="modal"
                        data-bs-target="#editProduct"
                        data-id="<?= (int) $p["id"] ?>"
                        data-name="<?= htmlspecialchars($p["product_name"], ENT_QUOTES, 'UTF-8') ?>"
                        data-category="<?= (int) $p["category_id"] ?>"
                        data-price="<?= htmlspecialchars((string) $p["price"], ENT_QUOTES, 'UTF-8') ?>"
                        data-quantity="<?= (int) $p["quantity"] ?>"
                        data-reorder="<?= (int) $p["reorder_level"] ?>"
                        data-supplier="<?= (int) $p["supplier_id"] ?>">
                        Edit
                    </button>

                    <!-- DELETE BUTTON -->
                    <a
                        class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('Delete this product?')"
                        href="?delete=<?= (int) $p["id"] ?>">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- ADD PRODUCT MODAL -->
<div class="modal fade" id="addProduct" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post">
                <input type="hidden" name="action" value="add">

                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input class="form-control mb-2"
                           name="product_name"
                           placeholder="Product name"
                           required>

                    <select class="form-select mb-2"
                            name="category_id" required>
                        <option value="">Category</option>
                        <?php while ($c = $cats->fetch_assoc()): ?>
                            <option value="<?= (int) $c["id"] ?>">
                                <?= htmlspecialchars($c["name"]) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <input class="form-control mb-2"
                           type="number" step="0.01" min="0"
                           name="price" placeholder="Price" required>

                    <input class="form-control mb-2"
                           type="number" min="0"
                           name="quantity" placeholder="Quantity" required>

                    <input class="form-control mb-2"
                           type="number" min="0"
                           name="reorder_level"
                           placeholder="Reorder level"
                           value="10" required>

                    <select class="form-select"
                            name="supplier_id" required>
                        <option value="">Supplier</option>
                        <?php while ($s = $sups->fetch_assoc()): ?>
                            <option value="<?= (int) $s["id"] ?>">
                                <?= htmlspecialchars($s["name"]) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        Save Product
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- EDIT PRODUCT MODAL -->
<div class="modal fade" id="editProduct" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="post">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Product</h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Product Name</label>
                    <input class="form-control mb-2"
                           type="text"
                           name="product_name"
                           id="edit_name" required>

                    <label class="form-label">Category</label>
                    <select class="form-select mb-2"
                            name="category_id"
                            id="edit_category" required>
                        <option value="">Select Category</option>
                        <?php while ($c = $editCats->fetch_assoc()): ?>
                            <option value="<?= (int) $c["id"] ?>">
                                <?= htmlspecialchars($c["name"]) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                    <label class="form-label">Price (₱)</label>
                    <input class="form-control mb-2"
                           type="number" step="0.01" min="0"
                           name="price" id="edit_price" required>

                    <label class="form-label">Quantity</label>
                    <input class="form-control mb-2"
                           type="number" min="0"
                           name="quantity" id="edit_quantity" required>

                    <label class="form-label">Reorder Level</label>
                    <input class="form-control mb-2"
                           type="number" min="0"
                           name="reorder_level"
                           id="edit_reorder" required>

                    <label class="form-label">Supplier</label>
                    <select class="form-select"
                            name="supplier_id"
                            id="edit_supplier" required>
                        <option value="">Select Supplier</option>
                        <?php while ($s = $editSups->fetch_assoc()): ?>
                            <option value="<?= (int) $s["id"] ?>">
                                <?= htmlspecialchars($s["name"]) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        Update Product
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- POPULATE EDIT FORM -->
<script>
document.querySelectorAll(".edit-btn").forEach(function(button) {
    button.addEventListener("click", function() {
        document.getElementById("edit_id").value =
            this.dataset.id;

        document.getElementById("edit_name").value =
            this.dataset.name;

        document.getElementById("edit_category").value =
            this.dataset.category;

        document.getElementById("edit_price").value =
            this.dataset.price;

        document.getElementById("edit_quantity").value =
            this.dataset.quantity;

        document.getElementById("edit_reorder").value =
            this.dataset.reorder;

        document.getElementById("edit_supplier").value =
            this.dataset.supplier;
    });
});
</script>

<?php require "footer.php"; ?>