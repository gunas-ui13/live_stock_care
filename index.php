<?php include('header.php'); ?>
<?php
// db.php - This file will handle database connection
require_once 'db.php'; // Update this path to your database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>My Website</title>
    <style>
        /* Global styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: lightgoldenrodyellow;
            color: #333;
            overflow-x: hidden;
        }

        header {
            background: lavenderblush;
            color: black;
            padding: 5px 0;
            text-align: center;
            margin-top: 0;
        }

        nav {
            display: flex;
            justify-content: flex-end;
            background: lightgray;
            padding: 8px;
        }

        nav a {
            color: #fff;
            padding: 12px 16px;
            text-decoration: none;
            font-weight: bold;
            margin-left: 2cm;
            background-color: green;
            transition: background-color 0.3s, color 0.3s;
        }

        nav a:hover {
            background: #f5c71a;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 0 auto;
        }

        section {
            padding: 20px 0;
        }

        h2 {
            color: green;
            text-align: center;
            font-size: 50px;
        }

        .about-content, .services {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
        }

        .about-content div, .services div {
            width: 30%;
            padding: 12px;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .service-card {
            background-color: #fafafa;
            margin-bottom: 10px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .service-card h3 {
            font-size: 1.2rem;
            color: #333;
        }

        footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 15px;
            position: relative;
            bottom: 0;
            width: 100%;
        }

        /* Blog Section Styles */
        #blog {
            padding: 15px 0;
        }

        .blog-posts {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .blog-post {
            background: #fafafa;
            padding: 15px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .blog-post h3 {
            font-size: 1.5rem;
            color: #333;
        }

        .blog-post p {
            color: #555;
        }

        .blog-post a {
            color: #f5c71a;
            text-decoration: none;
        }

        .blog-post a:hover {
            text-decoration: underline;
        }

        /* Contact Section Styles */
        #contact {
            padding: 20px 0;
        }

        .contact-info {
            display: flex;
            justify-content: space-between;
        }

        .contact-info div {
            width: 30%;
            padding: 15px;
            background: #fff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 8px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .contact-form button {
            width: 100%;
            padding: 10px;
            background: #333;
            color: #fff;
            border: none;
            border-radius: 5px;
        }

        .contact-form button:hover {
            background: #f5c71a;
        }

        /* Home Section */
        .main-home {
            padding: 1rem 7%;
            width: 100%;
            min-height: 100vh;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin-bottom: 0;
            font-size: 12pt;
        }
       #about{
        margin-top: 0;
       }
        .home {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-top: 0; /* Remove top margin */
            margin-bottom: 0; 
            padding: 0;
        }

        .home .home-left-content {
            flex: 1 1 40rem;
            text-align: center;
            margin-top: 0;
            padding-left: 0;
            margin-left: 0; 
        }

        .home-left-content span {
            font-size: 2rem;
            color: var(--maincolor);
            padding: 1rem 0;
            font-weight: bolder;
        }

        .home-left-content h2 {
            font-size: 40px;
            margin-top: 0;
        }

        .home-left-content p {
            font-size: 1.5rem;
            color: var(--textcolor);
            line-height: 2.5rem;
            padding-left: 0; /* 1cm gap from the left border */
            padding-right: 2rem;
            margin-bottom: 0;
            text-align: justify; 
        }

        .home-btn {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .home-btn a {
            display: inline-block;
            padding: 1rem 2rem;
            font-size: 1.5rem;
            color: black;
            transition: 0.5s ease;
        }

        .home-btn a:hover {
            padding: 1rem 1.5rem;
        }

        .homebtnsec {
            background: transparent !important;
            color: var(--textcolor) !important;
            border: 1px solid var(--maincolor);
        }

        .homebtnsec:hover {
            background-color: var(--secondcolor) !important;
            color: black;
        }

        .home .home-right-content {
            flex: 1 1 50rem;
            padding: 0;
            margin-bottom: 0;
        }

        .home .home-right-content img {
            width: 100%;
            max-width: 900px;
            height: auto;
            margin-bottom: 0;
            margin-top: 0;
        }
       
        /* About Us Section Styles */
        .about-us {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0;
            padding:0;
        }

        .about-us .about-left-content img {
            flex: 1;
            max-width:  80%;
            margin-left: 0;
        }

        .about-us .about-right-content {
            flex: 1;
            max-width: 45%;
            font-size: 1.5rem;
            text-align: justify;
        }

        span {
            text-align: center;
        }
        .about-right-content {
    
    margin-top: 0px;
    font-size: 18px;
    line-height: 1.8;
    color: #555;
    padding: 20px;
    
}

.about-section {
    margin-bottom: 20px;
}

.about-section h3 {
    font-size: 20px;
    color: #333;
    margin-bottom: 10px;
    border-bottom: 2px solid #007b3c; /* Green underline */
    padding-bottom: 5px;
}

.about-section p {
    font-size: 16px;
    color: #666;
    line-height: 1.6;
    text-align: justify; /* Justify the text for a more formal look */
}

.about-section p b {
    font-weight: bold;
    color: #007b3c; /* Highlighting "VetCare" in green */
}

.about-right-content {
    margin-top: 0px;
}

@media only screen and (max-width: 768px) {
    .about-right-content {
        padding: 15px;
    }

    .about-section h3 {
        font-size: 22px;
    }

    .about-section p {
        font-size: 14px;
    }
}

/* Default styles (for larger screens) */

/* Mobile view: screens up to 768px */
@media screen and (max-width: 768px) {
  body {
    font-size: 14px;
  }

  .navbar {
    flex-direction: column;
  }

  .navbar a {
    padding: 10px;
    text-align: center;
    display: block;
  }

  .hero {
    padding: 20px;
    text-align: center;
  }

  /* Adjust other elements as needed */
}
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    text-align: center;
}

.success {
    background-color: #4CAF50;
    color: white;
}

.error {
    background-color: #f44336;
    color: white;
}

        @media only screen and (max-width: 480px) {
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
                overflow-x: hidden;
            }

            .about-us {
                flex-direction: column;
                text-align: center;
                margin-top: 0;
            }

            .about-us .about-left-content, .about-us .about-right-content {
                max-width: 100%;
            }

            .home {
                flex-direction: column;
                align-items: center;
            }

            .home .home-left-content, .home .home-right-content {
                flex: 1 1 100%;
            }

            .contact-info {
                flex-direction: column;
                align-items: center;
            }

            .contact-info div {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        @media only screen and (max-width: 900px) and (min-width: 481px) {
            .about-us {
                flex-direction: column;
            }

            .home {
                flex-direction: column;
            }

            .contact-info {
                flex-direction: column;
            }

            .contact-info div {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
<header>
    <h1>Welcome to Our Cattle Clinic</h1>
</header>
<nav>
    <a href="#home">Home</a>
    <a href="#about">About Us</a>
    <a href="#services">Services</a>
    <a href="#blog">Blog</a>
    <a href="#contact">Contact</a>
    <a href="login1.php">Login</a>
</nav>

<!-- Home Section -->
<div class="main-home">
    <div class="home">
        <div class="home-left-content">
            <h2>We take care of our <br> Livestock Healths</h2>
            <p class="lorem"> Inspiring Livestock Excellence since 2024. Where Livestock champions are born.We provide the most full medical services, so every livestock could have the opportunity 
            to receive qualitative medical help. At VetCare, we believe pets deserve the same quality healthcare as humans. <br>our mission is to provide holistic and compassionate advanced care for pets across India, making it accessible and stress-free.
We’re here to transform the way pets are treated, ensuring every furry friend gets the love, care and medical attention they need to thrive.
</p>
        </div>
        <div class="home-right-content">
            <img src="images/hero2.png" alt="">
        </div>
    </div>
</div>

<!-- About Us Section -->
<section id="about">
    <div class="container">
        <h2>About Us</h2>
        <div class="about-us">
            <div class="about-left-content">
                <img src="images/about1.png" alt="About Us Image" style="width: 100%;">
            </div>
            <div class="about-right-content">
                <h3>Our Mission</h3>
                <p>We are committed to providing excellent veterinary care for all kinds of pets, ensuring they remain in the best condition.At <b>VetCare</b>, our mission is to ensure the health, safety, and well-being of livestock through preventive care, emergency services, and specialized treatments. We believe in providing high-quality care while focusing on animal welfare and supporting the livelihood of farmers and agricultural communities.</p>
                <h3>Our Vision</h3>
                <p>To be the leading veterinary clinic in the area, known for our compassionate care and state-of-the-art facilities.</p>
                </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="services">
    <div class="container">
        <h2>Our Services</h2>
        <div class="services">
            <div class="service-card">
                <h3>Veterinary Checkups</h3>
                <p>Comprehensive health checkups for pets to ensure they remain in the best condition.</p>
            </div>
            <div class="service-card">
                <h3>Vaccinations</h3>
                <p>We provide all necessary vaccinations to protect your pets from common diseases.</p>
            </div>
            <div class="service-card">
                <h3>Surgery</h3>
                <p>State-of-the-art surgical services for pets with expert veterinarians.</p>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section id="blog">
    <div class="container">
        <h2>Our Blog</h2>
        <div class="blog-posts">
            <div class="blog-post">
                <h3>Latest Post Title</h3>
                <p>Summary of the latest blog post goes here. It can be a brief introduction to the post content.</p>
                <a href="#">Read More</a>
            </div>
            <div class="blog-post">
                <h3>Previous Post Title</h3>
                <p>Summary of a previous blog post goes here. It can be a brief introduction to the post content.</p>
                <a href="#">Read More</a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section id="contact">
    <div class="container">
        <h2>Contact Us</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">
                Message sent successfully! Thank you for contacting us.
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="alert error">
                There was an error sending your message. Please try again.
            </div>
        <?php endif; ?>

        <div class="contact-info">
            <div>
                <h3>Address</h3>
                <p>123 Main Street, City, Country</p>
            </div>
            <div>
                <h3>Phone</h3>
                <p>(123) 456-7890</p>
            </div>
            <div>
                <h3>Email</h3>
                <p>contact@clinic.com</p>
            </div>
        </div>

        <form class="contact-form" action="" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" placeholder="Your Message" required></textarea>
            <button type="submit">Send Message</button>
        </form>
    </div>
</section>


<footer>
    <p>&copy; 2025 Livestock Care Clinic. All Rights Reserved.</p>
</footer>

</body>
</html>
