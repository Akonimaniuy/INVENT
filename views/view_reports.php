<?php require_once __DIR__ . '/header.php'; ?>

<h1 class="mt-4">Inventory Reports</h1>
<p class="lead">Visual insights into your sales performance.</p>

<div class="card mb-4">
    <div class="card-body">
        <form action="index.php" method="get" class="form-inline">
            <input type="hidden" name="action" value="reports">
            <label for="start_date" class="mr-2">From:</label>
            <input type="date" name="start_date" id="start_date" class="form-control mr-sm-2" value="<?php echo htmlspecialchars($start_date ?? ''); ?>">
            <label for="end_date" class="mr-2">To:</label>
            <input type="date" name="end_date" id="end_date" class="form-control mr-sm-2" value="<?php echo htmlspecialchars($end_date ?? ''); ?>">
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>
</div>

<div class="row">
    <!-- Pie Chart: Product Distribution by Category -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                Sales Count by Category
            </div>
            <div class="card-body">
                <canvas id="productsByCategoryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bar Chart: Stock Value by Category -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header">
                Total Sales Value by Category
            </div>
            <div class="card-body">
                <canvas id="stockValueByCategoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Data from PHP
    const categoryLabels = <?php echo json_encode($category_labels); ?>;
    const categoryProductCounts = <?php echo json_encode($category_product_counts); ?>;
    const valueLabels = <?php echo json_encode($value_labels); ?>;
    const categoryValues = <?php echo json_encode($category_values); ?>;

    // Chart 1: Products by Category (Pie Chart)
    const ctx1 = document.getElementById('productsByCategoryChart').getContext('2d');
    new Chart(ctx1, {
        type: 'pie',
        data: {
            labels: categoryLabels,
            datasets: [{
                label: '# of Sales',
                data: categoryProductCounts,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d'],
                borderWidth: 1
            }]
        }
    });

    // Chart 2: Stock Value by Category (Bar Chart)
    const ctx2 = document.getElementById('stockValueByCategoryChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: valueLabels,
            datasets: [{
                label: 'Total Sales Value ($)',
                data: categoryValues,
                backgroundColor: '#28a745',
                borderColor: '#218838',
                borderWidth: 1
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>