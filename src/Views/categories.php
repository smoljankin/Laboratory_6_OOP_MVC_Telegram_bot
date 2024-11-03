<nav>
    <ul>
        <li><strong>Admin panel</strong></li>
    </ul>
    <ul>
        <li><a href="/">Home</a></li>
        <li><a href="/new/category">Add new category</a></li>
    </ul>
</nav>

<header>
    <?php if (!empty($message)): ?>
        <h2><?php print $message; ?></h2>
    <?php endif; ?>
</header>

<main>
    <h1>Categories</h1>

    <?php if (!empty($categories)): ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Edit</th>
                    <th>Delete</th>
                    <th>Show products</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($categories as $category): ?>
                    <tr>
                        <td><?php print $category['id']; ?></td>
                        <td><?php print $category['name']; ?></td>
                        <td><a href="/category?id=<?php print $category['id']; ?>">Edit</a></td>
                        <td>
                            <form action="/category" method="POST">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php print $category['id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                        <td>
                            <a href="/products?category=<?php print $category['id']; ?>">Show products</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h2>No categories at the moment</h2>
    <?php endif; ?>
</main>
