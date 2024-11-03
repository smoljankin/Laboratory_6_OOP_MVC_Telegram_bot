<nav>
    <ul>
        <li><strong>Admin panel</strong></li>
    </ul>
    <ul>
        <li>
            <a href="/category">Categories</a>
        </li>
        <li>
            <a href="/orders">Orders</a>
        </li>
        <li>
            <a href="/warehouse">Warehouse</a>
        </li>
        <li>
            <a href="/logout">Logout</a>
        </li>
    </ul>
</nav>

<header>
    <?php if (!empty($message)): ?>
        <h2><?php print $message; ?></h2>
    <?php endif; ?>
</header>

<main>
    <p>
        Here you can manage products through the categories, warehouse, and review orders.
    </p>
</main>
