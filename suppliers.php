<?php
require "header.php";
require "config.php";

// DELETE SUPPLIER
if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];

    $stmt = $conn->prepare("DELETE FROM suppliers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: suppliers.php");
    exit;
}

// ADD OR UPDATE SUPPLIER
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    $name = trim($_POST["name"]);
    $contact = trim($_POST["contact"]);
    $email = trim($_POST["email"]);

    if ($id > 0) {
        // UPDATE
        $stmt = $conn->prepare(
            "UPDATE suppliers SET name=?, contact=?, email=? WHERE id=?"
        );
        $stmt->bind_param("sssi", $name, $contact, $email, $id);
        $stmt->execute();
    } else {
        // INSERT
        $stmt = $conn->prepare(
            "INSERT INTO suppliers(name, contact, email) VALUES(?,?,?)"
        );
        $stmt->bind_param("sss", $name, $contact, $email);
        $stmt->execute();
    }

    header("Location: suppliers.php");
    exit;
}

// GET SUPPLIER FOR EDITING
$editSupplier = null;

if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];

    $stmt = $conn->prepare("SELECT * FROM suppliers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $editSupplier = $stmt->get_result()->fetch_assoc();
}

$rows = $conn->query("SELECT * FROM suppliers ORDER BY id DESC");
?>

<div class="d-flex justify-content-between mb-3">
    <h2>Suppliers</h2>
    <button class="btn btn-primary"
        data-bs-toggle="modal"
        data-bs-target="#add">
        + Add Supplier
    </button>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($r = $rows->fetch_assoc()): ?>
                <tr>
                    <td><?= (int)$r["id"] ?></td>
                    <td><?= htmlspecialchars($r["name"]) ?></td>
                    <td><?= htmlspecialchars($r["contact"]) ?></td>
                    <td><?= htmlspecialchars($r["email"] ?? "") ?></td>
                    <td>
                        <a class="btn btn-sm btn-warning"
                           href="suppliers.php?edit=<?= (int)$r["id"] ?>">
                            Edit
                        </a>

                        <a class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this supplier?')"
                           href="suppliers.php?delete=<?= (int)$r["id"] ?>">
                            Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ADD SUPPLIER MODAL -->
<div class="modal fade" id="add" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Add Supplier</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input class="form-control mb-2"
                        name="name" placeholder="Supplier name" required>

                    <input class="form-control mb-2"
                        name="contact" placeholder="Contact number" required>

                    <input class="form-control"
                        type="email" name="email" placeholder="Email">
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

<?php if ($editSupplier): ?>
<!-- EDIT SUPPLIER MODAL -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Supplier</h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id"
                        value="<?= (int)$editSupplier["id"] ?>">

                    <input class="form-control mb-2"
                        name="name"
                        value="<?= htmlspecialchars($editSupplier["name"]) ?>"
                        placeholder="Supplier name" required>

                    <input class="form-control mb-2"
                        name="contact"
                        value="<?= htmlspecialchars($editSupplier["contact"]) ?>"
                        placeholder="Contact number" required>

                    <input class="form-control"
                        type="email" name="email"
                        value="<?= htmlspecialchars($editSupplier["email"] ?? "") ?>"
                        placeholder="Email">
                </div>

                <div class="modal-footer">
                    <a href="suppliers.php" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Update Supplier
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