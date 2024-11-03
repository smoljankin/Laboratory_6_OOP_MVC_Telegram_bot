<nav>
    <ul><li>Admin panel</li></ul>

    <ul>
        <li>
            <a href="/">Home</a>
        </li>
        <li>
            <a href="/orders">Orders</a>
        </li>
    </ul>
</nav>

<header>
    <?php if (!empty($message)): ?>
        <h2><?php print $message; ?></h2>
    <?php endif; ?>
</header>

<main>
    <h1>Order(<?php print $order['id']; ?>) for user(<?php print $order['email']; ?>)</h1>

    <table>
        <caption>Items of order</caption>
        <thead>
            <tr>
                <th>Product name</th>
                <th>Count ordered</th>
                <th>Price per one</th>
                <th>Total price</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($order['details'] as $orderItem): ?>
                <tr>
                    <td><?php print $orderItem['product_name']; ?></td>
                    <td><?php print $orderItem['count']; ?></td>
                    <td><?php print $orderItem['price_per_one']; ?></td>
                    <td><?php print $orderItem['price']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h2>Total: <?php print $order['total']; ?></h2>
</main>
