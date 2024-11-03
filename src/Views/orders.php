<nav>
    <ul><li>Admin panel</li></ul>

    <ul>
        <li>
            <a href="/">Home</a>
        </li>
    </ul>
</nav>

<header>
    <?php if (!empty($message)): ?>
        <h2><?php print $message; ?></h2>
    <?php endif; ?>
</header>

<main>
    <h1>Orders</h1>

    <?php if (!empty($orders)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>UserName</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($orders as $order): ?>
                    <tr>
                        <td><?php print $order['id']; ?></td>
                        <td><?php print $order['user_email']; ?></td>
                        <td><?php print $order['user_name']; ?></td>
                        <td><?php print $order['user_address']; ?></td>
                        <td><?php print $order['user_phone']; ?></td>
                        <td><a href="/orders?id=<?php print $order['id']; ?>">Details</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h2>No orders at the moment</h2>
    <?php endif; ?>
</main>
