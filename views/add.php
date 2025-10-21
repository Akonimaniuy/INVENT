<?php require_once __DIR__ . '/header.php'; ?>

<h1 class="mt-4">Add New Category</h1>

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
        <form action="index.php?action=add_category" method="POST">
            <div class="form-group">
                <label for="name">Category Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
            <a href="index.php?action=categories" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>