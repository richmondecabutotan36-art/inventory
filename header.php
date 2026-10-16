<?php require_once "auth.php"; ?>
<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Inventory Management System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style.css" rel="stylesheet">
</head><body>
<nav class="navbar navbar-dark bg-primary">
<div class="container-fluid"><a class="navbar-brand fw-bold" href="dashboard.php">Inventory System</a>
<div class="text-white">Welcome, <?=htmlspecialchars($_SESSION["username"])?> | <a class="btn btn-light btn-sm" href="logout.php">Logout</a></div>
</div></nav>
<div class="container py-4">
<div class="mb-3">
<a class="btn btn-outline-primary btn-sm" href="dashboard.php">Dashboard</a>
<a class="btn btn-outline-primary btn-sm" href="products.php">Products</a>
<a class="btn btn-outline-primary btn-sm" href="categories.php">Categories</a>
<a class="btn btn-outline-primary btn-sm" href="suppliers.php">Suppliers</a>
</div>
