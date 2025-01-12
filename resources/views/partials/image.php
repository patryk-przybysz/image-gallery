<a href="<?= $image->paths['full'] ?>">
    <div class="card">
        <img src="<?= $image->paths['thumbnail'] ?>" alt="<?= $image->title ?>">
        <p>
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                    <path d="M21 7.5V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h3.5M16 2v4M8 2v4m-5 4h5m9.5 7.5L16 16.25V14" />
                    <path d="M22 16a6 6 0 1 1-12 0a6 6 0 0 1 12 0" />
                </g>
            </svg>
            <?= format_time($image->createdAt) ?>
        </p>
        <?php if ($image->author) : ?>
            <p class="truncatable">
                <svg xmlns=" http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <path d="M18 20a6 6 0 0 0-12 0" />
                        <circle cx="12" cy="10" r="4" />
                        <circle cx="12" cy="12" r="10" />
                    </g>
                </svg>
                <?= $image->author ?>
            </p>
        <?php endif; ?>
        <?php if ($image->visibility === "private") : ?>
            <p>
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                        <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </g>
                </svg>
                Private
            </p>
        <?php endif; ?>
    </div>
</a>