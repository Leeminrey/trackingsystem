<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Access Denied Page">
    <title>Access Denied</title>
    <style>
        :root {
            --background-color: #f4f4f4;
            --container-background: white;
            --box-shadow: rgba(0, 0, 0, 0.1);
            --header-color: #d9534f;
            --text-color: #666;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: var(--background-color);
        }
        .container {
            text-align: center;
            background: var(--container-background);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 0 20px var(--box-shadow);
            width: 80%;
            max-width: 600px;
        }
        .container img {
            width: 225px;
            height: 225px;
        }
        .container h1 {
            color: var(--header-color);
            margin: 20px 0 20px;
            font-size: 2    .5em;
        }
        .container p {
            color: var(--text-color);
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <main class="container">
        <img src="images/warning.png" alt="Access Denied Icon">
        <h1>Access Denied - Forbidden</h1>
        <p>You are not authorized to view this page.<br>Please check your credentials and try again.</p>
    </main>
</body>
</html>