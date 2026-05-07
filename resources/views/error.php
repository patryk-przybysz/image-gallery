<main>
    <div class="error">
        <img src="/static/<?= $code === 404 ? "404.svg" : "error.svg" ?>" alt="Error illustration">
        <h1><?= e($message) ?></h1>
    </div>
</main>
