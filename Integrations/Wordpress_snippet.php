<?php
// Add this code to your theme's functions.php or a custom plugin

function enqueue_custom_scripts() {
    ?>
    <script>
    (function() {
        // Generate or retrieve a unique user ID
        function getUserID() {
            var userID = getCookie('user_id');
            if (!userID) {
                userID = 'user_' + Math.random().toString(36).substr(2, 9);
                setCookie('user_id', userID, 365);
            }
            return userID;
        }

        // Set a cookie
        function setCookie(name, value, days) {
            var expires = "";
            if (days) {
                var date = new Date();
                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                expires = "; expires=" + date.toUTCString();
            }
            document.cookie = name + "=" + (value || "") + expires + "; path=/";
        }

        // Get a cookie
        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') c = c.substring(1, c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
            }
            return null;
        }

        // Log visit to the server
        function logVisit(userID, page) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "https://yourdomain.com/track_visit", true);
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log("Visit logged successfully:", xhr.responseText);
                }
            };
            var data = JSON.stringify({
                user_id: userID,
                page: page,
                visit_time: new Date().toISOString().slice(0, 19).replace('T', ' ')
            });
            xhr.send(data);
        }

        // Get the current page URL
        var pageURL = window.location.href;
        var userID = getUserID();

        // Log the visit
        logVisit(userID, pageURL);
    })();
    </script>
    <?php
}

add_action('wp_footer', 'enqueue_custom_scripts');
