<?php
require_once __DIR__ . '/header.php';

// Helper function to build sorting links
function sort_link($column, $text, $current_column, $current_order, $search_term) {
    $order = ($column == $current_column && $current_order == 'ASC') ? 'DESC' : 'ASC';
    $arrow = ($column == $current_column) ? ($current_order == 'ASC' ? ' &uarr;' : ' &darr;') : '';
    $query_params = http_build_query(array_merge($_GET, ['sort' => $column, 'order' => $order]));
    return '<a href="?' . $query_params . '">' . $text . $arrow . '</a>';
}
?>

<h1 class="mt-4">Products</h1>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <div class="mb-2 mb-md-0">
        <a href="index.php?action=add" class="btn btn-primary mb-2">Add New Product</a>
        <div class="btn-group mb-2" role="group" aria-label="View mode">
            <a href="?action=products&view=list" class="btn btn-secondary <?php echo ($view_mode === 'list') ? 'active' : ''; ?>">List View</a>
            <a href="?action=products&view=categorized" class="btn btn-secondary <?php echo ($view_mode === 'categorized') ? 'active' : ''; ?>">Categorized View</a>
        </div>
        <a href="?action=export_products_csv&<?php echo http_build_query(array_intersect_key($_GET, array_flip(['search', 'sort', 'order']))); ?>" class="btn btn-info mb-2">Export to CSV</a>
    </div>
    <div class="btn-toolbar mb-2 mb-md-0">
        <form action="index.php" method="get" class="form-inline">
            <input type="hidden" name="action" value="products">
            <?php if (isset($_GET['view'])): ?>
            <input type="hidden" name="view" value="<?php echo htmlspecialchars($_GET['view']); ?>">
            <?php endif; ?>
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>" aria-label="Search">
                <button type="submit" class="btn btn-outline-success"><i class="fas fa-search"></i></button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<?php if ($view_mode === 'categorized'): ?>
    <?php
    $current_category = null;
    $row_number = 0;
    if (!empty($products)):
        foreach ($products as $product):
            $category_name = $product['category_name'] ?? 'Uncategorized';
            if ($category_name !== $current_category):
                if ($current_category !== null):
                    echo '</tbody></table></div>'; // Close previous table and card
                endif;
                $current_category = $category_name;
                $row_number = 0; // Reset counter for new category
    ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h3><?php echo htmlspecialchars($current_category); ?></h3>
                    </div>
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%;">#</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
            <?php endif; ?>
                        <?php $row_number++; ?>
                        <tr>
                            <td><?php echo $row_number; ?></td>
                            <td>
                                <?php if (!empty($product['image'])): ?>
                                    <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['description']); ?></td>
                            <td><?php echo htmlspecialchars($product['price']); ?></td>
                            <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                            <td>
                                <a href="index.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="index.php?action=history&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-info" title="History"><i class="fas fa-history"></i></a>
                                <a href="index.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
        <?php endforeach; ?>
        <?php
            echo '</tbody></table></div>'; // Close the last table
        else: ?>
            <div class="alert alert-info">No products found.</div>
        <?php endif; ?>

<?php else: // Default List View ?>

<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Image</th>
                <th><?php echo sort_link('name', 'Name', $sort_column, $sort_order, $search_term); ?></th>
                <th>Description</th>
                <th>Category</th>
                <th><?php echo sort_link('price', 'Price', $sort_column, $sort_order, $search_term); ?></th>
                <th><?php echo sort_link('quantity', 'Quantity', $sort_column, $sort_order, $search_term); ?></th>
                <th><?php echo sort_link('date_updated', 'Last Updated', $sort_column, $sort_order, $search_term); ?></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($products)): ?>
                <?php $row_number = $offset ?? 0; ?>
                <?php foreach ($products as $product): $row_number++; ?>
                <tr>
                    <td><?php echo $row_number; ?></td>
                    <td>
                        <?php if (!empty($product['image'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['date_updated']); ?></td>
                    <td class="table-actions">
                        <a href="index.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="index.php?action=history&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-info" title="History"><i class="fas fa-history"></i></a>
                        <a href="index.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No products found. <?php if (!empty($search_term)) echo 'Matching your search for "' . htmlspecialchars($search_term) . '". <a href="index.php?action=products">Clear search</a>.'; ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination Controls -->
<?php if ($view_mode === 'list'): ?>
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($total_pages > 1): ?>
            <!-- Previous Page Link -->
            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">Previous</a>
            </li>

            <!-- Page Number Links -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <!-- Next Page Link -->
            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
<?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/footer.php'; ?>