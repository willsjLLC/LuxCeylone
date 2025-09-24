<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxury Real Estate - Premium Properties</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --primary-gold: #D4AF37;
            --gold-light: #F4E4BC;
            --gold-dark: #B8860B;
            --dark-primary: #1a1a1a;
            --dark-secondary: #2d2d2d;
            --dark-tertiary: #3a3a3a;
            --text-white: #ffffff;
            --text-light: #cccccc;
            --text-muted: #999999;
            --accent-glow: rgba(212, 175, 55, 0.3);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(212, 175, 55, 0.2);
            --shadow-gold: 0 10px 40px rgba(212, 175, 55, 0.2);
            --transition: all 0.4s cubic-bezier(0.23, 1, 0.320, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-primary);
            color: var(--text-white);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .luxury-font {
            font-family: 'Playfair Display', serif;
        }

        /* Background Pattern */
        .bg-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.03;
            background-image: radial-gradient(circle at 25% 25%, var(--primary-gold) 2px, transparent 2px);
            background-size: 80px 80px;
        }

        /* Header */
        .luxury-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            z-index: 1000;
            padding: 20px 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-gold);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--dark-primary);
            font-size: 18px;
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-white);
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 40px;
        }

        .nav-menu a {
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .nav-menu a:hover,
        .nav-menu a.active {
            color: var(--primary-gold);
            background: var(--glass-bg);
        }

        .nav-cta {
            background: var(--primary-gold);
            color: var(--dark-primary);
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .nav-cta:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-gold);
        }

        /* Banner Section */
        .banner-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 120px 0 80px;
            position: relative;
            background: #1a1a1a;
        }

        .banner-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            position: relative;
            z-index: 2;
        }

        .banner__wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
        }

        .banner__content {
            max-width: 600px;
        }

        .template-badge {
            display: inline-block;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            padding: 10px 20px;
            border-radius: 30px;
            color: var(--primary-gold);
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: slideUp 1s ease 0.2s both;
        }

        .sub-heading {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            color: var(--text-light);
            margin-bottom: 20px;
            font-style: italic;
            animation: slideUp 1s ease 0.4s both;
        }

        .banner__content-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 5vw, 4.5rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 30px;
            animation: slideUp 1s ease 0.6s both;
            background: linear-gradient(135deg, var(--text-white) 0%, var(--primary-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .banner__content-title-up {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.5rem, 5vw, 4.0rem);
            font-weight: 800;
            line-height: 1.1;
            animation: slideUp 1s ease 0.6s both;
            background: linear-gradient(135deg, var(--text-white) 0%, var(--primary-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .banner__content p {
            color: var(--text-muted);
            line-height: 1.8;
            margin-bottom: 40px;
            font-size: 1rem;
            animation: slideUp 1s ease 0.8s both;
        }

        /* Job Search Form */
        .job__search {
            animation: slideUp 1s ease 1s both;
            margin-bottom: 40px;
        }

        .form--group {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 8px;
            gap: 8px !important;
        }

        .form--control {
            background: transparent;
            border: none;
            color: var(--text-white);
            padding: 16px 20px;
            font-size: 1rem;
        }

        .form--control::placeholder {
            color: var(--text-muted);
        }

        .form--control:focus {
            outline: none;
            box-shadow: none;
            background: var(--glass-bg);
        }

        .form-select {
            background: transparent;
            border: none;
            color: var(--text-white);
            padding: 16px 20px;
            min-width: 200px;
        }

        .form-select option {
            background: var(--dark-secondary);
            color: var(--text-white);
        }

        .btn--base {
            background: var(--primary-gold);
            color: var(--dark-primary);
            border: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn--base:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-gold);
            color: var(--dark-primary);
        }

        /* Mobile App Image */
        .mobile_app {
            max-width: 200px;
            margin: 30px 0;
            animation: float 3s ease-in-out infinite;
            display: block;
        }

        /* Banner Thumb */
        .banner__thumb {
            position: relative;
            animation: slideUp 1s ease 0.5s both;
        }

        .luxury-showcase {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            transform: perspective(1000px) rotateY(-15deg) rotateX(4deg);
            transition: var(--transition);

            display: flex;
            align-items: center;
            justify-content: center;
        }

        .luxury-showcase img {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
        }


        .showcase-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(26, 26, 26, 0.8) 0%, rgba(26, 26, 26, 0.4) 50%, rgba(26, 26, 26, 0.8) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .showcase-info {
            text-align: center;
            color: var(--text-white);
        }

        .showcase-price {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-gold);
            margin-bottom: 10px;
        }

        .showcase-details {
            font-size: 1.1rem;
            color: var(--text-light);
        }

        /* Golden Curves */
        .golden-curve {
            position: absolute;
            right: -100px;
            top: 50%;
            transform: translateY(-50%);
            width: 300px;
            height: 300px;
            border: 3px solid var(--primary-gold);
            border-radius: 50%;
            opacity: 0.3;
            z-index: 1;
        }

        .golden-curve::before {
            content: '';
            position: absolute;
            right: 50px;
            top: 50px;
            width: 200px;
            height: 200px;
            border: 2px solid var(--primary-gold);
            border-radius: 50%;
            opacity: 0.6;
        }

        /* Footer Info */
        .website-info {
            position: absolute;
            bottom: 40px;
            left: 40px;
            animation: slideUp 1s ease 1.2s both;
        }

        .website-url {
            color: var(--text-light);
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .social-link:hover {
            background: var(--primary-gold);
            color: var(--dark-primary);
            transform: translateY(-3px);
        }

        .company-handle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 10px;
        }

        /* Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {

            .nav-container,
            .banner-container {
                padding: 0 20px;
            }

            .banner__wrapper {
                gap: 60px;
            }

            .golden-curve {
                display: none;
            }
        }

        @media (max-width: 992px) {
            .nav-menu {
                display: none;
            }

            .banner__wrapper {
                grid-template-columns: 1fr;
                gap: 60px;
                text-align: center;
            }

            .luxury-showcase {
                transform: none;
                margin: 0 auto;
                max-width: 500px;
            }

            .website-info {
                position: relative;
                bottom: auto;
                left: auto;
                text-align: center;
                margin-top: 60px;
            }
        }

        @media (max-width: 768px) {
            .banner-section {
                padding: 100px 0 60px;
                min-height: auto;
            }

            .form--group {
                flex-direction: column;
                align-items: stretch !important;
            }

            .form--control,
            .form-select {
                margin-bottom: 8px;
            }

            .btn--base {
                margin-top: 8px;
            }

            .nav-container,
            .banner-container {
                padding: 0 20px;
            }
        }

        @media (max-width: 480px) {
            .banner__content-title {
                font-size: 2rem;
            }

            .showcase-price {
                font-size: 2rem;
            }

            .nav-container,
            .banner-container {
                padding: 0 15px;
            }

            .social-links {
                justify-content: center;
            }
        }

        /* Dynamic Content Styles */
        .dynamic-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 20px;
        }

        .btn--round {
            border-radius: 8px;
        }

        .stat-item {
            text-align: left;
        }

        .stat-number {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            animation: slideUp 1s ease 1.2s both;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            padding: 16px 32px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: var(--shadow-medium);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            padding: 16px 32px;
            border: 2px solid var(--text-primary);
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-secondary:hover {
            background: var(--text-primary);
            color: white;
        }

        /* Hero Visual */
        .hero-visual {
            position: relative;
            animation: slideUp 1s ease 0.5s both;
        }

        .luxury-showcase {
            position: relative;
            perspective: 1000px;
        }

        .main-product {
            width: 100%;
            height: 500px;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-heavy);
            transform: rotateY(-15deg) rotateX(10deg);
            transition: var(--transition);
        }

        .main-product:hover {
            transform: rotateY(-10deg) rotateX(5deg) scale(1.02);
        }

        .product-image {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 4rem;
            color: white;
        }

        .floating-products {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
        }

        .floating-item {
            position: absolute;
            width: 80px;
            height: 80px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
            box-shadow: var(--shadow-light);
            animation: float 6s ease-in-out infinite;
        }

        .floating-item:nth-child(1) {
            top: 20%;
            left: -40px;
            animation-delay: 0s;
        }

        .floating-item:nth-child(2) {
            top: 60%;
            right: -40px;
            animation-delay: 2s;
        }

        .floating-item:nth-child(3) {
            bottom: 20%;
            left: -20px;
            animation-delay: 4s;
        }

        /* Categories Section */
        .categories-section {
            padding-bottom: 120px 0;
            background: #1a1a1a;
        }

        .section-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 80px;
        }

        .section-badge {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, var(--text-white) 0%, var(--primary-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.5rem, 5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.1;
            animation: slideUp 1s ease 0.6s both;
            background: linear-gradient(135deg, var(--text-white) 0%, var(--primary-gold) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            color: rgba(180, 180, 180, 1);

        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }

        .category-card {
            background: var(--bg-white);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            position: relative;
            height: 400px;
        }

        .category-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-heavy);
        }

        .category-image {
            width: 100%;
            height: 60%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            overflow: hidden;
        }

        .category-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.6s ease;
        }

        .category-card:hover .category-image::before {
            left: 100%;
        }

        .category-content {
            padding: 30px;
        }

        .category-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 10px;
        }

        .category-prices {
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.95rem;
        }

        .original-price {
            text-decoration: line-through;
            color: #aaa;
        }

        .discount {
            background-color: #e9e9e936;
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8rem;
        }

        .selling-price {
            font-weight: bold;
            color: #fff;
            /* or your theme color */
        }


        .category-description {
            color: var(--text-secondary);
            font-size: 0.95rem;
            margin-bottom: 15px;
        }

        .category-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .item-count {
            color: var(--primary-color);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .view-category {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .view-category:hover {
            gap: 10px;
        }

        /* Features Section */
        .features-section {
            padding: 120px 0;
            background: linear-gradient(135deg, var(--bg-cream), #FFF8E7);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 50px;
            margin-top: 80px;
        }

        .feature-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            transition: var(--transition);
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-medium);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 25px;
        }

        .feature-title {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 15px;
        }

        .feature-description {
            color: var(--text-secondary);
            line-height: 1.7;
        }

        /* Footer */
        .luxury-footer {
            background: var(--secondary-color);
            color: white;
            padding: 80px 0 30px;
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }

        .footer-brand {
            max-width: 300px;
        }

        .footer-logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .footer-description {
            color: #CCCCCC;
            line-height: 1.7;
            margin-bottom: 30px;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-link {
            width: 45px;
            height: 45px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: var(--transition);
        }

        .social-link:hover {
            background: var(--primary-light);
            transform: translateY(-3px);
        }

        .footer-section h4 {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
            margin-bottom: 20px;
            font-size: 1.2rem;
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 10px;
        }

        .footer-links a {
            color: #CCCCCC;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        .footer-bottom {
            border-top: 1px solid #333;
            padding-top: 30px;
            text-align: center;
            color: #999;
        }

        /* Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {

            .nav-container,
            .hero-container,
            .section-container,
            .footer-content {
                padding: 0 20px;
            }

            .hero-content {
                gap: 60px;
            }
        }

        @media (max-width: 992px) {
            .nav-menu {
                display: none;
            }

            .hero-content {
                grid-template-columns: 1fr;
                gap: 60px;
                text-align: center;
            }

            .hero-stats {
                justify-content: center;
            }

            .categories-grid {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            }

            .features-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .footer-grid {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }
        }

        @media (max-width: 768px) {
            .hero-section {
                padding: 100px 0 60px;
            }

            .hero-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn-primary,
            .btn-secondary {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .hero-stats {
                flex-direction: column;
                gap: 20px;
                align-items: center;
            }

            .categories-section,
            .features-section {
                padding: 80px 0;
            }

            .floating-item {
                display: none;
            }
        }

        @media (max-width: 480px) {

            .nav-container,
            .hero-container,
            .section-container,
            .footer-content {
                padding: 0 15px;
            }

            .hero-section {
                padding: 80px 0 40px;
            }

            .main-product {
                height: 300px;
            }

            .product-image {
                width: 200px;
                height: 200px;
                font-size: 2.5rem;
            }
        }

        .category-link {
            text-decoration: none !important;
        }

        /* Footer Styles */
        .luxury-footer {
            background: var(--dark-primary);
            color: var(--text-light);
            padding: 80px 0 30px;
            position: relative;
            border-top: 1px solid var(--glass-border);
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }

        .footer-brand {
            max-width: 300px;
        }

        .footer-logo {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-gold);
            margin-bottom: 20px;
            animation: slideUp 1s ease 0.2s both;
        }

        .footer-description {
            color: var(--text-muted);
            line-height: 1.7;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-link {
            width: 45px;
            height: 45px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .social-link:hover {
            background: var(--primary-gold);
            color: var(--dark-primary);
            transform: translateY(-3px);
        }

        .footer-section h4 {
            font-family: 'Playfair Display', serif;
            color: var(--primary-gold);
            margin-bottom: 20px;
            font-size: 1.2rem;
            animation: slideUp 1s ease 0.4s both;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 10px;
            font-size: 0.95rem;
        }

        .footer-links a {
            color: var(--text-light);
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: var(--primary-gold);
            transform: translateX(5px);
            display: inline-block;
        }

        .footer-bottom {
            border-top: 1px solid var(--glass-border);
            padding-top: 30px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .footer-content {
                padding: 0 20px;
            }

            .footer-grid {
                gap: 40px;
            }
        }

        @media (max-width: 992px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 40px;
                text-align: center;
            }

            .footer-brand {
                max-width: 100%;
            }

            .social-links {
                justify-content: center;
            }

            .footer-links a:hover {
                transform: none;
            }
        }

        @media (max-width: 768px) {
            .luxury-footer {
                padding: 60px 0 20px;
            }

            .footer-content {
                padding: 0 15px;
            }

            .footer-grid {
                gap: 30px;
            }

            .footer-logo {
                font-size: 1.8rem;
            }

            .footer-section h4 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .footer-logo {
                font-size: 1.5rem;
            }

            .footer-description {
                font-size: 0.9rem;
            }

            .footer-section h4 {
                font-size: 1rem;
            }

            .footer-links li {
                font-size: 0.9rem;
            }

            .social-link {
                width: 40px;
                height: 40px;
            }
        }

        /* Animations */
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="bg-pattern"></div>

    @php
        $bannerContent = getContent('banner.content', true);
    @endphp

    <!-- Header -->
    <header class="luxury-header">
        <div class="nav-container">
            <a href="#" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-building"></i>
                </div>
                <div class="logo-text">Luxceylone</div>
            </a>
            <nav class="nav-menu">
                <a href="#" class="active">MENU</a>
                <a href="#about">ABOUT</a>
                <a href="#reservation">RESERVATION</a>
                <a href="#info">INFO</a>
                <a href="#contact">CONTACT</a>
            </nav>
            <a href="#" class="nav-cta">LOGIN</a>
        </div>
    </header>

    <header class="luxury-header fixed-top">
        <div class="nav-container container">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="logo">
                <div class="">
                    <a href="{{ route('home') }}">
                        <img src="{{ siteLogo() }}" alt="@lang('logo')" class="" style="max-height: 60px;">
                    </a>
                </div>
            </a>

            <!-- Navigation Menu -->
            <nav class="nav-menu">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('ads.index') }}" class="{{ request()->routeIs('ads.index') ? 'active' : '' }}">Ads</a>
                <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact
                    Us</a>
            </nav>

            <!-- CTA Buttons -->
            <div class="">
                @guest
                    <a class="btn btn--base btn--round btn--md" href="{{ route('user.login') }}">Login</a>
                    <a class="btn btn--base btn--round btn--md" href="{{ route('user.register') }}">Register</a>
                @else
                    <a class="btn btn--base btn--round btn--md" href="{{ route('user.product.index') }}">Dashboard</a>
                    <a class="btn btn--base btn--round btn--md" href="{{ route('user.logout') }}">Logout</a>
                @endguest
            </div>

            <!-- Mobile Menu Trigger -->
            <div class="header-trigger-wrapper d-flex d-lg-none align-items-center">
                <div class="header-trigger">
                    <span></span>
                </div>
            </div>
        </div>
    </header>



    <!-- Banner Section (Laravel Blade Template Structure) -->
    <section class="banner-section overflow-hidden bg_img">
        <div class="banner-container">
            <div class="banner__wrapper d-flex align-items-center">
                <!-- Dynamic Banner Thumb -->
                <div class="banner__thumb d-none d-lg-block">
                    <div class="luxury-showcase">

                        <img
                            src="{{ getImage('assets/images/frontend/banner/' . @$bannerContent->data_values->banner_image, '1315x928') }}">
                        <div class="">
                            <div class="showcase-info">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dynamic Banner Content -->
                <div class="banner__content text-center">
                    {{-- <div class="template-badge">LANDING PAGE TEMPLATE</div> --}}

                    <!-- Dynamic Heading: {{ __(@$bannerContent->data_values->heading) }} -->
                    <h2 class="sub-heading">{{ __(@$bannerContent->data_values->subheading) }}</h2>

                    <!-- Dynamic Title: {{ __(@$bannerContent->data_values->subheading) }} -->

                    <h1 class="banner__content-title-up luxury-font">
                        {{ __(@$bannerContent->data_values->heading) }}<br>
                    </h1>



                    <!-- Dynamic Description: {{ __(@$bannerContent->data_values->description) }} -->
                    <p>{{ __(@$bannerContent->data_values->description) }}</p>

                    <!-- Mobile App Image -->

                    <!-- Dynamic Search Form -->

                </div>
            </div>
        </div>

        <div class="golden-curve"></div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section" id="collections">
        <div class="section-container">
            <div class="section-header">
                <div class="section-badge">Premium Collections</div>
                <h2 class="section-title luxury-font">Exclusive Categories</h2>
                <p class="section-subtitle">
                    Discover our carefully curated luxury collections, each piece selected for its exceptional quality
                    and timeless elegance.
                </p>
            </div>

            <div class="categories-grid">
                <div class="category-card">
                    <div class="category-image">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div class="category-content">
                        <h3 class="category-title">Fine Jewelry</h3>
                        <p class="category-description">Exquisite diamonds, precious stones, and handcrafted pieces</p>
                        <div class="category-stats">
                            <span class="item-count">285+ Items</span>
                            <a href="#" class="view-category">
                                View Collection <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="category-card">
                    <div class="category-image">
                        <i class="bi bi-smartwatch"></i>
                    </div>

                    <div class="category-content">
                        <h3 class="category-title">Hand Watches</h3>
                        <p class="category-description">Exclusive wristwatches and designer hand watches</p>
                        <div class="category-stats">
                            <span class="item-count">120+ Items</span>
                            <a href="#" class="view-category">
                                View Collection <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="category-card">
                    <div class="category-image">
                        <i class="fas fa-spray-can"></i>
                    </div>
                    <div class="category-content">
                        <h3 class="category-title">Perfume</h3>
                        <p class="category-description">Luxury fragrances, exclusive scents, and designer perfumes</p>
                        <div class="category-stats">
                            <span class="item-count">80+ Items</span>
                            <a href="#" class="view-category">
                                View Collection <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="category-card">
                    <div class="category-image">
                        <i class="fas fa-tshirt"></i>
                    </div>
                    <div class="category-content">
                        <h3 class="category-title">Designer Fashion</h3>
                        <p class="category-description">Haute couture and exclusive designer pieces</p>
                        <div class="category-stats">
                            <span class="item-count">320+ Items</span>
                            <a href="#" class="view-category">
                                View Collection <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <!-- Categories Section -->
    <section class="categories-section" id="collections" style="padding-top: 120px">
        <div class="section-container">
            <div class="section-header">
                <div class="section-badge">Premium Collections</div>
                <h2 class="section-title luxury-font">Amazing Products</h2>
                <p class="section-subtitle">
                    Discover our carefully curated luxury collections, each piece selected for its exceptional quality
                    and timeless elegance.
                </p>
            </div>

            <!-- Categories Grid -->
            <div class="categories-grid">
                @if (isset($categories))
                    @foreach ($products->take(8) as $index => $product)
                        <div class="category-card" style="animation-delay: {{ $index * 0.1 }}s">
                            <a href="{{ route('ads.index', ['category' => $product->id]) ?? '#' }}"
                                class="category-link" style="color: white;">
                                <div class="category-image">
                                    <img src="{{ getImage(getFilePath('product') . '/' . $product->image_url, getFileSize('product')) }}"
                                        alt="{{ __($product->name) }}"
                                        style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="category-content">
                                    <h3 class="category-title">{{ __($product->name) }}</h3>

                                    <div class="category-prices">
                                        @if ($product->discount && $product->discount > 0)
                                            <!-- Original Price -->
                                            <span
                                                class="original-price">${{ number_format($product->original_price, 2) }}</span>

                                            <!-- Discount -->
                                            <span class="discount">-{{ $product->discount }}</span>
                                        @endif

                                        <!-- Selling Price -->
                                        <span
                                            class="selling-price">${{ number_format($product->selling_price, 2) }}</span>
                                    </div>

                                    <div class="category-stats">
                                        <span class="item-count">{{ $product->ads_count ?? rand(50, 500) }}
                                            Listings</span>
                                        <span class="view-category">
                                            View Collection <i class="fas fa-arrow-right"></i>
                                        </span>
                                    </div>
                                </div>


                            </a>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="luxury-footer">
        <div class="footer-content">
            <div class="footer-grid">
                <!-- Brand Section -->
                <div class="footer-brand">
                    <h2 class="footer-logo luxury-font">Luxceylone</h2>
                    <p class="footer-description">
                        Discover unparalleled luxury with our exclusive collection of premium properties, curated for
                        elegance and sophistication.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-linkedin"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="social-link">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
                <!-- Quick Links -->
                <div class="footer-section">
                    <h4 class="luxury-font">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#collections">Collections</a></li>
                        <li><a href="#reservation">Reservation</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <!-- Explore -->
                <div class="footer-section">
                    <h4 class="luxury-font">Explore</h4>
                    <ul class="footer-links">
                        <li><a href="#">Luxury Homes</a></li>
                        <li><a href="#">Urban Estates</a></li>
                        <li><a href="#">Beachfront Properties</a></li>
                        <li><a href="#">Exclusive Listings</a></li>
                    </ul>
                </div>
                <!-- Contact -->
                <div class="footer-section">
                    <h4 class="luxury-font">Contact Us</h4>
                    <ul class="footer-links">
                        <li><a href="mailto:info@realestate.com">info@realestate.com</a></li>
                        <li><a href="tel:+1234567890">+1 (234) 567-890</a></li>
                        <li>123 Luxury Lane, Suite 100<br>New York, NY 10001</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Luxceylone. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scrolled class to header on scroll
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.luxury-header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(26, 26, 26, 0.98)';
            } else {
                header.style.background = 'rgba(26, 26, 26, 0.95)';
            }
        });

        // Initialize select2 if available
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2').select2({
                theme: 'dark'
            });
        }
    </script>
</body>

</html>
