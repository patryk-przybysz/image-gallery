<main>
    <form method="post">
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= $form['email'] ?>">
            <span class="errors"><?= format_errors('email', $errors) ?>
        </div>
        <div>
            <label for="login">Login:</label>
            <input type="text" id="login" name="login" value="<?= $form['login'] ?>">
            <span class="errors"><?= format_errors('login', $errors) ?>
        </div>
        <div>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" minlength=8 value="<?= $form['password'] ?>">
            <span class="errors"><?= format_errors('password', $errors) ?>
        </div>
        <div>
            <label for="repeat-password">Repeat password:</label>
            <input type="password" id="repeatPassword" name="repeatPassword" minlength=8 value="<?= $form['repeatPassword'] ?>">
            <span class="errors"><?= format_errors('repeatPassword', $errors) ?>
        </div>
        <button type="submit">Register</button>
    </form>
</main>