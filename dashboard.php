<?php
require "header.php";
require "config.php";
$total = $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()["c"];
$stock = $conn->query("SELECT COALESCE(SUM(quantity),0) c FROM products")->fetch_assoc()["c"];
$low = $conn->query("SELECT COUNT(*) c FROM products WHERE quantity <= reorder_level AND quantity > 0")->fetch_assoc()["c"];
$out = $conn->query("SELECT COUNT(*) c FROM products WHERE quantity=0")->fetch_assoc()["c"];
?>
<h2 class="mb-4">Dashboard</h2>
<div class="row g-3">
<div class="col-md-3"><div class="card stat"><div class="card-body"><h6>Total Products</h6><h2><?=$total?></h2></div></div></div>
<div class="col-md-3"><div class="card stat"><div class="card-body"><h6>Total Stock</h6><h2><?=$stock?></h2></div></div></div>
<div class="col-md-3"><div class="card stat"><div class="card-body"><h6>Low Stock</h6><h2><?=$low?></h2></div></div></div>
<div class="col-md-3"><div class="card stat"><div class="card-body"><h6>Out of Stock</h6><h2><?=$out?></h2></div></div></div>
</div>
<div class="card mt-4 shadow-sm"><div class="card-body">
<h5>Inventory Management System</h5>
<p class="text-muted mb-0">Use the menu above to manage products, categories, and suppliers.</p>
</div></div>
<?php require "footer.php"; ?>