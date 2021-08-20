<!DOCTYPE html>

<head>
    <title>Pusher Test</title>
    <script src="./pusher.min.js"></script>
    <script>
        // Enable pusher logging - don't include this in production
        Pusher.logToConsole = true;

        var pusher = new Pusher('3d43b2d8585b9a3e590c', {
            cluster: 'eu'
        });

        pusher.connection.bind('connected', function() {
            console.log(pusher.connection.socket);
            document.getElementById('status').innerText = "Connected";
        });


        var channel = pusher.subscribe('my-channel');
        channel.bind('my-event', function(data) {
            alert(JSON.stringify(data));
        });
    </script>
</head>

<body>
    <div id="status"></div>
    <h1>Pusher Test</h1>
    <p>
        Try publishing an event to channel <code>my-channel</code>
        with event name <code>my-event</code>.
    </p>
</body>