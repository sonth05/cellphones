<?php if (!empty($_SESSION['flash'])): ?>
    <div class="flash">
        <?php foreach ($_SESSION['flash'] as $type => $messages): ?>
            <?php foreach ((array)$messages as $message): ?>
                <div class="flash-<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($message) ?></div>
            <?php endforeach; ?>
        <?php endforeach; unset($_SESSION['flash']); ?>
    </div>
<?php endif; ?>


