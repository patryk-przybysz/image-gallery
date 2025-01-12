<main>
    <form method="post">
        <div>
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" value="<?= $form['login'] ?>">
            <span class="errors"><?= format_errors('login', $errors) ?>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" value="<?= $form['password'] ?>">
            <span class="errors"><?= format_errors('password', $errors) ?>
        </div>
        <button type="submit">Login</button>
    </form>
</main>