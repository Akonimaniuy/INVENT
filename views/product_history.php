<?php require_once __DIR__ . '/header.php'; ?>

<h1 class="mt-4">Stock Movement History</h1>
<h3 class="text-muted"><?php echo htmlspecialchars($product['name']); ?></h3>
<a href="index.php?action=products" class="btn btn-secondary mb-3">Back to Product List</a>

<table class="table table-bordered table-striped">
    <thead class="thead-light">
        <tr>
            <th>Date</th>
            <th>Movement Type</th>
            <th>Quantity Change</th>
            <th>New Quantity</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($history)): ?>
            <?php foreach ($history as $movement): ?>
                <tr>
                    <td><?php echo htmlspecialchars($movement['created_at']); ?></td>
                    <td>
                        <?php 
                            $type = ucwords(str_replace('_', ' ', $movement['movement_type']));
                            echo htmlspecialchars($type);
                        ?>
                    </td>
                    <td>
                        <?php
                            $change = $movement['quantity_change'];
                            $class = $change > 0 ? 'text-success' : 'text-danger';
                            $prefix = $change > 0 ? '+' : '';
                            echo "<strong class='{$class}'>" . $prefix . htmlspecialchars($change) . "</strong>";
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($movement['new_quantity']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No movement history found for this product.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/footer.php'; ?>