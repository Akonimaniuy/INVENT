<?php require_once __DIR__ . '/header.php'; ?>

<h1 class="mt-4">Edit Product</h1>
<form action="index.php?action=edit&id=<?php echo htmlspecialchars($product['id']); ?>" method="post" enctype="multipart/form-data">
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

    <div class="form-group">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? $product['name']); ?>" required>
    </div>
    <div class="form-group">
        <label for="category_id">Category</label>
        <select name="category_id" id="category_id" class="form-control">
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php echo (($_POST['category_id'] ?? $product['category_id']) == $category['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea name="description" id="description" class="form-control"><?php echo htmlspecialchars($_POST['description'] ?? $product['description']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="price">Price</label>
        <input type="number" name="price" id="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($_POST['price'] ?? $product['price']); ?>" required>
    </div>
    <div class="form-group">
        <label for="quantity">Quantity</label>
        <input type="number" name="quantity" id="quantity" class="form-control" value="<?php echo htmlspecialchars($_POST['quantity'] ?? $product['quantity']); ?>" required>
    </div>
    <div class="form-group">
        <label for="image">New Product Image</label>
        <input type="file" name="image" id="image" class="form-control-file">
        <?php if (!empty($product['image'])): ?>
            <p class="mt-2">Current Image: <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 100px;"></p>
        <?php endif; ?>
    </div>
    <button type="submit" class="btn btn-primary">Update Product</button>
    <a href="index.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require_once __DIR__ . '/footer.php'; ?>