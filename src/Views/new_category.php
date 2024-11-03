<header>
    <?php if (!empty($message)): ?>
        <h2><?php print $message; ?></h2>
    <?php endif; ?>
</header>

<main>
    <h1>Add new category</h1>
    <form action="/new/category" method="POST">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required><br>
        <button type="submit">Add</button>
    </form>
</main>
