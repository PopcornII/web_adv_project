<!-- resources/views/landing.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant POS Landing Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        .hero-section {
            position: relative;
            height: 100vh;
            background-image: url({{ asset('assets/img/food.jpg') }});
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-align: center;
            padding: 0 20px;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-content h1 {
            font-size: 4rem;
            color: #ffcc00;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero-content p {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: #ffcc00;
            border: none;
            padding: 15px 30px;
            font-size: 1.25rem;
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn-primary:hover {
            background-color: #e6b800;
        }
    </style>
</head>

<body>
    <div class="hero-section">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Welcome to Restaurant POS</h1>
            <p>Manage your operations with ease and efficiency.</p>
            <a href="{{ route('login') }}" class="btn btn-primary">Get Started</a>
        </div>
    </div>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>
