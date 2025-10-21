<?php require_once __DIR__ . '/header.php'; ?>

<h1 class="mt-4">Record a Sale</h1>
<p class="lead">Select a product and enter the quantity sold to update stock levels.</p>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <strong>Please correct the following errors:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <form action="index.php?action=record_sale" method="POST">
            <div class="form-group">
                <label for="product_id">Product</label>
                <select name="product_id" id="product_id" class="form-control" required>
                    <option value="">-- Select a Product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" <?php echo (isset($_POST['product_id']) && $_POST['product_id'] == $product['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($product['name']); ?> (In Stock: <?php echo $product['quantity']; ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Quantity Sold</label>
                <input type="number" name="quantity" id="quantity" class="form-control" min="1" required value="<?php echo htmlspecialchars($_POST['quantity'] ?? '1'); ?>">
            </div>
            <button type="submit" class="btn btn-success">Record Sale</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>