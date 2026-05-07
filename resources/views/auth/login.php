<main id="main-content">
    <h1>Welcome back</h1>
    <form method="post" novalidate>
        <div>
            <label for="login">Login</label>
            <input type="text" id="login" name="login" value="<?= e($form['login']) ?>" autocomplete="username" required>
            <?= format_errors('login', $errors) ?>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>
            <?= format_errors('password', $errors) ?>
        </div>
        <button type="submit">Login</button>
    </form>
</main>
