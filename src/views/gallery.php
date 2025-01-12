<main>
    <form>
        <label>
            Images per page:
            <input type="number" name="per_page" id="per_page" min="0" value="<?= $perPage ?>">
        </label>
        <label>
            Total items: <?= $totalCount ?>
        </label>
    </form>
    <div class="gallery">
        <?php foreach ($images as $image) {
            echo render_image($image);
        } ?>
    </div>
    <nav>
        <?php if ($currentPage > 1) : ?>
            <a href="/?page=<?= $currentPage - 1 ?>&per_page=<?= $perPage ?>">Previous</a>
        <?php endif; ?>

        Page <?= $currentPage ?> of <?= $totalPages ?>

        <?php if ($currentPage < $totalPages) : ?>
            <a href="/?page=<?= $currentPage + 1 ?>&per_page=<?= $perPage ?>">Next</a>
        <?php endif; ?>
    </nav>
</main>