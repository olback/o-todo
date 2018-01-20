<?php

    require(__DIR__.'/api/functions.php');
        
    if(!loggedIn()) {
        header('Location: login.php');
        die();
    }

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $_COOKIE['username']; ?> | o-todo</title>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#ffcc00" />
        <meta name="mobile-web-app-capable" content="yes">
        <link href="assets/css/main.min.css" rel="stylesheet" />
        <link href="assets/css/font-awesome.min.css" rel="stylesheet"/>
        <link rel="shortcut icon" href="assets/icons/icon-96.png" />
        <link rel="manifest" href="manifest.json" />
        <script src="assets/main.js"></script>
    </head>
    <body>

        <!-- Top navigation -->
        <nav>
            <div id="menu-button" class="header nav-button"><i class="fa fa-bars" aria-hidden="true"></i></div>
            <h1 class="title">o-ToDo</h1>
            <div class="header nav-button add-note-button"><i class="fa fa-plus" aria-hidden="true"></i></div>
        </nav>

        <!-- Main section -->
        <main id="main">

            <h1>Your ToDo-list</h1>

            <!-- Scoll list -->
            <section id="list">
                <div id="hint">
                    <h3 id="hint-title">Add a note</h3>
                    <p id="hint-body">Click the <i class="fa fa-plus" aria-hidden="true"></i> icon at the top of the screen or via the sidemenu.</p>
                </div>
                <!-- Notes! -->
            </section>

        </main>
        
        <!-- Side nav -->
        <aside id="side-menu">

            <div class="top">
                <figure>
                    <img src="assets/images/profile_img.png" alt="Profile image"/>
                    <figcaption><?php echo $_COOKIE['username']; ?></figcaption>
                </figure>
            </div>

            <ul>
                <li class="add-note-button"><i class="fa fa-plus" aria-hidden="true"></i> <span>Add note</span></li>
                <li id="refresh"><i class="fa fa-refresh" aria-hidden="true"></i> <span>Reload</span></li>
                <li id="profile-button"><i class="fa fa-user-circle-o" aria-hidden="true"></i> <span>Profile</span></li>
                <!-- (yet to be implemented) <li id="admin-button"><i class="fa fa-wrench" aria-hidden="true"></i> <span>Admin</span></li>-->
                <li id="logout-button"><i class="fa fa-sign-out" aria-hidden="true"></i> <span>Log out</span></li>
            </ul>

            <!-- Footer -->
            <footer>
                olback &copy; <span class="current-year"></span> <span id="version">v0.1</span><br>
                Please contribute on <a href="https://github.com/olback/o-todo" target="_blank" class="contribute">GitHub</a>!
            </footer>

        </aside>

        <div id="timed-status">
            <span id="timed-status-text">Hello gais</span>
        </div>
        <div id="timed-status-bar"></div>

        <!-- Displayed when JavaScript is disabled. -->
        <noscript>
            <div class="container">
                <div class="inner">
                    <h1>JavaScript is required!</h1>
                    <p>This application/site requires JavaScript to function properly.</p>
                </div>
            </div>
        </noscript>

        <!-- Add note-->
        <div class="modal" id="add-note">
            <div class="inner">
                <i class="fa fa-close close-modal" aria-hidden="true"></i>
                <h1>Add note</h1><br>
                <form>
                    <label for="new-note-title">Title</label>
                    <input type="text" id="new-note-title">
                    <label for="new-note-body">Note</label>
                    <textarea id="new-note-body" style="height: 80px;"></textarea>
                    <label for="new-note-due-date">Due date</label>
                    <input type="date" id="new-note-due-date">
                    <label for="new-note-importance">Importance</label>
                    <input type="number" id="new-note-importance" min="0" value="0" max="100">
                    <input type="date" id="new-note-create-date" class="hidden" />
                    <button type="button" class="clear" id="new-note-submit" style="margin:auto;display:block;">Add note</button>
                    <p id="new-note-status"></p>
                </form>
            </div>
        </div>

        <!-- Edit note-->
        <div class="modal" id="edit-note">
            <div class="inner">
                <i class="fa fa-close close-modal" aria-hidden="true"></i>
                <h1>Edit note</h1><br>
                <form>
                    <label for="edit-note-title">Title</label>
                    <input type="text" name="edit-note-title" id="edit-note-title">
                    <label for="edit-note-note">Note</label>
                    <textarea name="edit-note-note" id="edit-note-note" style="height: 80px;"></textarea>
                    <label for="edit-note-due-date">Due date</label>
                    <input type="date" name="edit-note-due-date" id="edit-note-due-date">
                    <label for="edit-note-importance">Importance</label>
                    <input type="number" name="edit-note-importance" id="edit-note-importance" min="0" value="0" max="100">
                    <input type="text" name="edit-note-id" id="edit-note-id" class="hidden">
                    <div class="buttons">
                        <button class="clear" type="button" id="edit-mark-done">Mark as done</button>
                        <!--<input disabled="disabled" type="button" name="edit-note-submit" value="Update note">-->
                    </div>
                </form>
            </div>
        </div>

        <!-- Profile -->
        <div class="modal" id="profile">
            <div class="inner">
                <i class="fa fa-close close-modal" aria-hidden="true"></i>
                <h1>Profile</h1><br>
                <figure>
                    <img src="assets/images/profile_img.png" alt="Profile image"/>
                    <figcaption><?php echo $_COOKIE['username']; ?></figcaption>
                </figure>
                <form>
                    <label for="api-key">API Key</label>
                    <input type="text" id="api-key" readonly="readonly" value="<?php echo $_COOKIE['api_key']; ?>">
                    <div class="buttons">
                        <button type="button" class="clear">Reset API Key</button>
                        <!--<input type="submit" value="Save" style="width: 100px;">-->
                    </div>
                </form>
            </div>
        </div>

        <!-- App settings (yet to be implemented)-->
        <!--<div class="modal" id="admin">
            <div class="inner">
                <i class="fa fa-close close-modal" aria-hidden="true"></i>
                <h1>Admin</h1>
            </div>
        </div>-->

    </body>
</html>
