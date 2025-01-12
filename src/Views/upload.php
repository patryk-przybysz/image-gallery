<main>
    <form enctype="multipart/form-data" method="post">
        <div>
            <label for="title">Title:</label>
            <?= format_errors('title', $errors) ?>
            <input type="text" id="title" name="title" value="<?= $form['title'] ?>">
        </div>
        <div>
            <label for="watermark">Watermark:</label>
            <?= format_errors('watermark', $errors) ?>
            <input type="text" id="watermark" name="watermark" value="<?= $form['watermark'] ?>">
        </div>
        <div>
            <label for="author">Author:</label>
            <?= format_errors('author', $errors) ?>
            <input type="text" id="author" name="author" value="<?= $form['author'] ?>">
        </div>
        <div>
            <label for="visibility">Visibility:</label>
            <input type="radio" title="public" name="visibility" value="public" <?php if ($form['visibility'] !== 'private') echo 'checked' ?>>Public</input>
            <input type="radio" title="private" name="visibility" value="private" <?php if ($form['visibility'] === 'private') echo 'checked' ?>>Private</input>
            <?= format_errors('visibility', $errors) ?>
        </div>
        <div>
            <label for="file">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m14-7l-5-5l-5 5m5-5v12" />
                </svg>
                Choose Photo <span id="selected-file" />
                <?= format_errors('file', $errors) ?></label>
            <input type="file" id="file" name="file" accept="image/png, image/jpeg">
        </div>
        <button type="submit">Upload</button>
    </form>
    <script src="/static/js/fileUpload.js"></script>
</main>