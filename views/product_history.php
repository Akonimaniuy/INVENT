<?php require_once __DIR__ . '/header.php'; ?>

<h1 class="mt-4">Stock Movement History</h1>
<h3 class="text-muted"><?php echo htmlspecialchars($product['name']); ?></h3>
<a href="index.php?action=products" class="btn btn-secondary mb-3">Back to Product List</a>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
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
                                        $type = $movement['movement_type'];
                                        $badge_class = 'badge-secondary';
                                        if (in_array($type, ['initial_stock', 'manual_add'])) $badge_class = 'badge-success';
                                        if (in_array($type, ['sale', 'manual_remove'])) $badge_class = 'badge-danger';
                                        echo '<span class="badge ' . $badge_class . '">' . htmlspecialchars(ucwords(str_replace('_', ' ', $type))) . '</span>';
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
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>