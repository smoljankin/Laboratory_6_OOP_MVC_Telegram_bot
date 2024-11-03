<header>
    <?php if (!empty($message)): ?>
        <h2><?php print $message; ?></h2>
    <?php endif; ?>
</header>

<main>
    <h1>Edit category (<?php print $category['id']; ?>)</h1>
    <form action="/category" method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" value="<?php print $category['name'] ?>" required>
        <input type="hidden" name="id" value="<?php print $category['id']; ?>">
        <input type="hidden" name="action" value="put">
        <p>
            <button type="submit">Save</button>
        </p>
    </form>
</main>
