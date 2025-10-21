<?php
require_once __DIR__ . '/header.php';

// Helper function to build sorting links
function sort_sales_link($column, $text, $current_column, $current_order, $search_term) {
    $order = ($column == $current_column && $current_order == 'ASC') ? 'DESC' : 'ASC';
    $arrow = ($column == $current_column) ? ($current_order == 'ASC' ? ' &uarr;' : ' &darr;') : '';
    $query_params = http_build_query(array_merge($_GET, ['sort' => $column, 'order' => $order]));
    return '<a href="?' . $query_params . '">' . $text . $arrow . '</a>';
}
?>

<h1 class="mt-4">Sales History</h1>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="btn-group mb-2">
            <a href="index.php?action=record_sale" class="btn btn-success">Record New Sale</a>
            <a href="?action=export_sales_csv&<?php echo http_build_query(array_intersect_key($_GET, array_flip(['search', 'start_date', 'end_date']))); ?>" class="btn btn-secondary">Export to CSV</a>
        </div>
    </div>
    <div class="col-md-12">
        <form action="index.php" method="get" class="form-inline float-md-right">
            <input type="hidden" name="action" value="sales_list">
            <label for="start_date" class="mr-2">From:</label>
            <input type="date" name="start_date" id="start_date" class="form-control mr-sm-2" value="<?php echo htmlspecialchars($start_date ?? ''); ?>">
            
            <label for="end_date" class="mr-2">To:</label>
            <input type="date" name="end_date" id="end_date" class="form-control mr-sm-2" value="<?php echo htmlspecialchars($end_date ?? ''); ?>">

            <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search..." value="<?php echo htmlspecialchars($search_term ?? ''); ?>" aria-label="Search">
            
            <button type="submit" class="btn btn-outline-success">Search</button>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead class="thead-light">
        <tr>
            <th>Sale ID</th>
            <th><?php echo sort_sales_link('product_name', 'Product', $sort_column, $sort_order, $search_term); ?></th>
            <th><?php echo sort_sales_link('quantity_sold', 'Quantity', $sort_column, $sort_order, $search_term); ?></th>
            <th>Price/Item</th>
            <th><?php echo sort_sales_link('total_price', 'Total Price', $sort_column, $sort_order, $search_term); ?></th>
            <th>Sold By</th>
            <th>Actions</th>
            <th><?php echo sort_sales_link('sale_date', 'Date', $sort_column, $sort_order, $search_term); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($sales)): ?>
            <?php foreach ($sales as $sale): ?>
            <tr>
                <td><?php echo htmlspecialchars($sale['id']); ?></td>
                <td><?php echo htmlspecialchars($sale['product_name']); ?></td>
                <td><?php echo htmlspecialchars($sale['quantity_sold']); ?></td>
                <td>$<?php echo number_format($sale['price_per_item'], 2); ?></td>
                <td>$<?php echo number_format($sale['total_price'], 2); ?></td>
                <td><?php echo htmlspecialchars($sale['user_name']); ?></td>
                <td>
                    <a href="index.php?action=invoice&id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-info" target="_blank">Invoice</a>
                </td>
                <td><?php echo htmlspecialchars($sale['sale_date']); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center">No sales found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Pagination Controls -->
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($total_pages > 1): ?>
            <li class="page-item <?php echo ($current_page <= 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php echo ($current_page >= $total_pages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<?php require_once __DIR__ . '/footer.php'; ?>