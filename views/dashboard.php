<?php require_once __DIR__ . '/header.php'; ?>

<h1 class="mt-4">Dashboard</h1>
<p class="lead">A quick overview of your inventory.</p>

<!-- Stat Cards -->
<div class="row">
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card text-white bg-primary stat-card">
            <div class="card-body">
                <div class="card-icon"><i class="fas fa-boxes"></i></div>
                <h5 class="card-title">Total Products</h5>
                <p class="card-text display-4"><?php echo htmlspecialchars($stats['total_products']); ?></p>
                <p class="card-text small">Unique items in inventory.</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card text-white bg-success stat-card">
            <div class="card-body">
                <div class="card-icon"><i class="fas fa-coins"></i></div>
                <h5 class="card-title">Total Stock Value</h5>
                <p class="card-text display-4">$<?php echo number_format($stats['total_value'], 2); ?></p>
                <p class="card-text small">Sum of (price × quantity).</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card text-white bg-danger stat-card">
            <div class="card-body">
                <div class="card-icon"><i class="fas fa-exclamation-triangle"></i></div>
                <h5 class="card-title">Low Stock Items</h5>
                <p class="card-text display-4"><?php echo count($low_stock_products); ?></p>
                <p class="card-text small">Items with quantity &lt; 10.</p>
            </div>
        </div>
    </div>
</div>

<!-- Low Stock Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bell"></i> Low Stock Alerts (Quantity &lt; 10)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <tbody>
                            <?php if (!empty($low_stock_products)): ?>
                                <?php foreach ($low_stock_products as $product): ?>
                                    <tr>
                                        <td><img src="uploads/<?php echo htmlspecialchars($product['image'] ?? 'default.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 40px; height: 40px; object-fit: cover;"></td>
                                        <td class="align-middle"><a href="index.php?action=edit&id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a></td>
                                        <td><span class="badge badge-danger">Only <?php echo htmlspecialchars($product['quantity']); ?> left</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td class="text-center">No items are currently low on stock.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Sales and Top Selling Products -->
<div class="row mt-4">
    <!-- Recent Sales -->
    <div class="col-lg-7 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-receipt"></i> Recent Sales
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless table-sm">
                        <tbody>
                            <?php if (!empty($recent_sales)): ?>
                                <?php foreach ($recent_sales as $sale): ?>
                                    <tr>
                                        <td class="align-middle"><strong><?php echo htmlspecialchars($sale['product_name']); ?></strong></td>
                                        <td>Sold <?php echo htmlspecialchars($sale['quantity_sold']); ?> unit(s)</td>
                                        <td class="text-success">$<?php echo number_format($sale['total_price'], 2); ?></td>
                                        <td class="text-muted"><?php echo date('M d, Y', strtotime($sale['sale_date'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td class="text-center">No recent sales to display.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="col-lg-5 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-fire"></i> Top Selling Products
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php if (!empty($top_selling_products)): ?>
                        <?php foreach ($top_selling_products as $product): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <img src="uploads/<?php echo htmlspecialchars($product['product_image'] ?? 'default.png'); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" style="width: 30px; height: 30px; object-fit: cover; margin-right: 10px;">
                                    <?php echo htmlspecialchars($product['product_name']); ?>
                                </div>
                                <span class="badge badge-primary badge-pill"><?php echo htmlspecialchars($product['total_quantity_sold']); ?> sold</span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-center">No sales data available to determine top products.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>