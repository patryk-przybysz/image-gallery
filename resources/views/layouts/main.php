<?php

use App\Models\User;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Patryk Przybysz">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/static/css/style.css">
    <title>Image Gallery</title>
</head>

<body>
    <header>
        <div class="logo">
            <a href="/">
                <h1><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                        <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                            <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3z" />
                            <circle cx="12" cy="13" r="3" />
                        </g>
                    </svg>Image Gallery</h1>
            </a>
        </div>
        <nav>
            <ul>
                <li>
                    <h2>
                        <a href="/upload">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4m14-7l-5-5l-5 5m5-5v12" />
                            </svg>
                            Upload
                        </a>
                    </h2>
                </li>
                <li>
                    <h2>
                        <a href="/search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                    <circle cx="11" cy="11" r="8" />
                                    <path d="m21 21l-4.3-4.3" />
                                </g>
                            </svg>
                            Search
                        </a>
                    </h2>
                </li>
                <?php if (User::current()) : ?>
                    <li>
                        <h2>Logged in as: <span id="login-display" class="truncatable">@<?= e(User::getName()) ?></span></h2>
                    </li>
                    <li>
                        <h2>
                            <a href="/auth/logout">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4m7 14l5-5l-5-5m5 5H9" />
                                </svg>
                                Logout
                            </a>
                        </h2>
                    </li>
                <?php else : ?>
                    <li>
                        <h2>
                            <a href="/auth/register">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                    <g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M19 8v6m3-3h-6" />
                                    </g>
                                </svg>
                                Register
                            </a>
                        </h2>
                    </li>
                    <li>
                        <h2>
                            <a href="/auth/login">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m-5-4l5-5l-5-5m5 5H3" />
                                </svg>
                                Login
                            </a>
                        </h2>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <?= $_content ?>
    <footer>&copy; Patryk Przybysz</footer>
</body>

</html>
