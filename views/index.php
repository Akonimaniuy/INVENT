<?php require_once __DIR__ . '/header.php'; ?>

<h1 class="mt-4">Product Categories</h1>
<div class="mb-3">
    <a href="index.php?action=add_category" class="btn btn-success">Add New Category</a>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Name</th>
                    <th style="width: 15%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $row_number = 0; ?>
                <?php foreach ($categories as $category): $row_number++; ?>
                    <tr>
                        <td><?php echo $row_number; ?></td>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td>
                            <a href="index.php?action=edit_category&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="index.php?action=delete_category&id=<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>