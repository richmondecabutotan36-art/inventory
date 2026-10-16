<?php
require "header.php";
require "config.php";

// DELETE CATEGORY
if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];

    $stmt = $conn->prepare("DELETE FROM categories WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: categories.php");
    exit;
}

// ADD OR UPDATE CATEGORY
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    $name = trim($_POST["name"]);

    if ($id > 0) {
        // UPDATE
        $stmt = $conn->prepare(
            "UPDATE categories SET name=? WHERE id=?"
        );
        $stmt->bind_param("si", $name, $id);
        $stmt->execute();
    } else {
        // INSERT
        $stmt = $conn->prepare(
            "INSERT INTO categories(name) VALUES(?)"
        );
        $stmt->bind_param("s", $name);
        $stmt->execute();
    }

    header("Location: categories.php");
    exit;
}

// GET CATEGORY FOR EDITING
$editCategory = null;

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];

    $stmt = $conn->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $editCategory = $stmt->get_result()->fetch_assoc();
}

$rows = $conn->query("SELECT * FROM categories ORDER BY id DESC");
?>

<div class="d-flex justify-content-between mb-3">
    <h2>Categories</h2>
    <button class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#add">
        + Add Category
    </button>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$r["id"] ?></td>
                    <td><?= htmlspecialchars($r["name"]) ?></td>
                    <td>
                        <a class="btn btn-sm btn-warning"
                           href="categories.php?edit=<?= (int)$r["id"] ?>">
                            Edit
                        </a>

                        <a class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this category?')"
                           href="categories.php?delete=<?= (int)$r["id"] ?>">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD CATEGORY MODAL -->
<div class="modal fade" id="add" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add Category</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input class="form-control"
                        name="name" required placeholder="Category name">
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if ($editCategory): ?>
<!-- EDIT CATEGORY MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Category</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id"
                        value="<?= (int)$editCategory["id"] ?>">

                    <input class="form-control"
                        name="name"
                        value="<?= htmlspecialchars($editCategory["name"]) ?>"
                        required placeholder="Category name">
                </div>

                <div class="modal-footer">
                    <a href="categories.php" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const modal = new bootstrap.Modal(
        document.getElementById("editModal")
    );
    modal.show();
});
</script>
<?php endif; ?>

<?php require "footer.php"; ?>