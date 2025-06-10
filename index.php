<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SportsPro Technical Support</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0077ff;
            --secondary: #00d4ff;
            --bg-gradient: linear-gradient(135deg, #0077ff, #00d4ff);
            --card-bg: rgba(255, 255, 255, 0.9);
            --text-dark: #222;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            width: 90%;
            max-width: 400px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.8s ease-out;
        }

        h1 {
            font-size: 2rem;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
        }

        .links {
            list-style: none;
            display: grid;
            gap: 1rem;
        }

        .links a {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: 600;
            color: var(--text-dark);
            background: #fff;
            padding: 0.75rem;
            border-radius: 10px;
            transition: transform .2s, box-shadow .2s;
        }

        .links a:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .icon {
            font-size: 1.2rem;
            margin-right: 0.5rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="card">
        <h1>Welcome to SportsPro</h1>
        <ul class="links">
            <li><a href="admin/login.php"><span class="icon">🛡️</span>Admin Login</a></li>
            <li><a href="technician/login.php"><span class="icon">🔧</span>Technician Login</a></li>
            <li><a href="customer/login.php"><span class="icon">👤</span>Customer Login</a></li>
        </ul>
    </div>
</body>

</html>