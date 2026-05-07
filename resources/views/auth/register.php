<main id="main-content">
    <h1>Create an account</h1>
    <form method="post" novalidate>
        <div>
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($form['email']) ?>" autocomplete="email" required>
            <?= format_errors('email', $errors) ?>
        </div>
        <div>
            <label for="login">Login</label>
            <input type="text" id="login" name="login" value="<?= e($form['login']) ?>" autocomplete="username" required>
            <?= format_errors('login', $errors) ?>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" id="password" name="password" minlength="8" autocomplete="new-password" required>
            <?= format_errors('password', $errors) ?>
        </div>
        <div>
            <label for="repeatPassword">Repeat password</label>
            <input type="password" id="repeatPassword" name="repeatPassword" minlength="8" autocomplete="new-password" required>
            <?= format_errors('repeatPassword', $errors) ?>
        </div>
        <button type="submit">Register</button>
    </form>
</main>
