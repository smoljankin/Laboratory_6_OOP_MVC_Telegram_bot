<header>
    <?php if (!empty($message)): ?>
        <h2><?php print $message; ?></h2>
    <?php endif; ?>
</header>

<main>
    <h1>Add new product</h1>
    <form action="/new/product" method="POST">
        <p>
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>
        </p>

        <p>
            <label for="description">Description:</label>
            <textarea name="description" id="description"></textarea>
        </p>

        <p>
            <label for="price">Price:</label>
            <input type="number" name="price" id="price" required min="0">
        </p>

        <input type="hidden" name="category" value="<?php print $category; ?>">

        <p>
            <button type="submit">Add</button>
        </p>
    </form>
</main>
