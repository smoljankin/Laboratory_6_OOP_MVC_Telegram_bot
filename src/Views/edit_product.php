<header>
    <?php if (!empty($message)): ?>
        <h2><?php print $message; ?></h2>
    <?php endif; ?>
</header>

<main>
    <h1>Edit product(<?php print $product['id']; ?>)</h1>
    <form action="/products" method="POST">
        <p>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php print $product['name']; ?>" required>
        </p>

        <p>
            <label for="description">Description:</label>
            <textarea name="description" id="description"><?php print $product['description']; ?></textarea>
        </p>

        <p>
            <label for="price">Price:</label>
            <input type="number" name="price" id="price" required min="0" value="<?php print $product['price']; ?>">
        </p>

        <input type="hidden" name="action" value="put">
        <input type="hidden" name="category_id" value="<?php print $product['category_id']; ?>">
        <input type="hidden" name="id" value="<?php print $product['id']; ?>">

        <p>
            <button type="submit">Save</button>
        </p>
    </form>
</main>
