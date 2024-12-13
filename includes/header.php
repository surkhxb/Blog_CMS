<header>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            padding-top: 70px; /* Height of the navbar */
        }

        header {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
            height: 70px;
        }

        .logo a {
            font-size: 24px;
            font-weight: bold;
            color: #6a2c8f; /* Dark purple */
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .logo a:hover {
            color: #9c4dcc; /* Medium purple */
        }

        .nav-links {
            display: flex;
            gap: 20px;
            list-style: none;
        }

        .nav-links li a {
            text-decoration: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-links li a::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -5px;
            width: 0;
            height: 2px;
            background: #6a2c8f;
            transition: width 0.3s ease-in-out;
        }

        .nav-links li a:hover {
            color: #6a2c8f;
        }

        .nav-links li a:hover::after {
            width: 100%;
        }

        .burger {
            display: none;
            cursor: pointer;
        }

        .burger div {
            width: 25px;
            height: 3px;
            background: #333;
            margin: 5px;
            transition: all 0.3s ease;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .nav-links {
                position: absolute;
                top: 70px;
                right: 0;
                background: rgba(255, 255, 255, 0.95);
                height: 100vh;
                width: 100%;
                flex-direction: column;
                align-items: center;
                gap: 30px;
                transform: translateX(100%);
                transition: transform 0.3s ease-in-out;
            }

            .nav-links.active {
                transform: translateX(0);
            }

            .burger {
                display: block;
            }

            .burger.toggle .line1 {
                transform: rotate(-45deg) translate(-5px, 6px);
            }

            .burger.toggle .line2 {
                opacity: 0;
            }

            .burger.toggle .line3 {
                transform: rotate(45deg) translate(-5px, -6px);
            }
        }
    </style>

    <nav>
        <div class="logo">
            <a href="index.php">Blog Content Management System</a>
        </div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/dashboard.php">Dashboard</a></li>
                <?php endif; ?>
                <li><a href="posts/create.php">Create Post</a></li>
                <li><a href="scripts/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="scripts/login.php">Login</a></li>
                <li><a href="scripts/auth.php">Register</a></li>
            <?php endif; ?>
        </ul>
        <div class="burger">
            <div class="line1"></div>
            <div class="line2"></div>
            <div class="line3"></div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const burger = document.querySelector('.burger');
            const navLinks = document.querySelector('.nav-links');

            burger.addEventListener('click', () => {
                navLinks.classList.toggle('active');
                burger.classList.toggle('toggle');
            });
        });
    </script>
</header>
