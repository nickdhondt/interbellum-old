<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Interbellum</title>
    <link rel="stylesheet" href="css/screen.css"/>
    <script src="script/game.js"></script>
    <meta name=viewport content="width=device-width, initial-scale=1">
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
</head>
<body>
<header>
    <nav>
        <ul id="nav">
            <li id="0">Mail</li>
            <li id="1">Profile</li>
            <li id="4">Ranking</li>
            <li id="2">Settings</li>
            <li>Map</li>
            <li id="a">Browser</li>
        </ul>
    </nav>
</header>
<main>
    <div id="browser" class="window"><div>
            Browser
            <div></div><div></div>
        </div>
        <div class="static">
            <div id="browser_bar">
                <input id="url" type="text" placeholder="Voer een adres in" />
            </div>
            <iframe></iframe>
        </div>
    </div>
    <div id="mail" class="window"><div>
            Mail
            <div></div><div></div>
        </div>
        <div>
            <div id="3" class="button">New message</div>
            <h2>Messages</h2>
        </div>
    </div>
    <div id="profile" class="window"><div>
            Profile
            <div></div><div></div>
        </div>
        <div>
        </div>
    </div>
    <div id="settings" class="window"><div>
            Settings
            <div></div><div></div>
        </div>
        <div>
            <section>
                <h2>Change password</h2>
                <form>
                    <ul>
                        <li>
                            <input type="password" placeholder="Current password"/>
                        </li>
                        <li>
                            <input type="password" placeholder="New password"/>
                        </li>
                        <li>
                            <input type="password" placeholder="Repeat password"/>
                        </li>
                        <li>
                            <input type="button" value="Change"/>
                        </li>
                    </ul>
                </form>
            </section>
        </div>
    </div>
    <div id="new_message" class="window"><div>
            New message
            <div></div><div></div>
        </div>
        <div>
            <form>
                <ul>
                    <li>
                        <input type="text" placeholder="Recipient(s)" name="message_recipients"/>
                        <div id="message_proposed_usernames"></div>
                    </li>
                    <li>
                        <input type="text" placeholder="Subject"/>
                    </li>
                    <li>
                        <textarea placeholder="Message"></textarea>
                    </li>
                    <li>
                        <input type="button" value="Send" name="send_message"/>
                    </li>
                </ul>
            </form>
        </div>
    </div>
    <div id="ranking" class="window"><div>
            Ranking
            <div></div><div></div>
        </div>
        <div></div>
    </div>
</main>
</body>
</html>