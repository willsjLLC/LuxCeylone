<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Luxceylone - Mobile Navigation</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="icon" href="{{ siteLogo() }}" sizes="32x32" type="image/png">
    <link rel="icon" href="{{ siteLogo() }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ siteLogo() }}">
    <link rel="apple-touch-icon" href="{{ siteLogo() }}">

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
            background: #1a1a1a;
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
            background: #f5f5f531;
            color: #f5f5f531;
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

        /* Header */
        .luxury-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            z-index: 1000;
            padding: 15px 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            z-index: 1001;
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

        /* Desktop Navigation */
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
            display: flex;
            gap: 15px;
        }

        .nav-cta a {
            background: var(--primary-gold);
            color: var(--dark-primary);
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .nav-cta a:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-gold);
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
            z-index: 1001;
            background: none;
            border: none;
        }

        .mobile-menu-toggle span {
            width: 25px;
            height: 3px;
            background: var(--primary-gold);
            margin: 3px 0;
            transition: var(--transition);
            border-radius: 2px;
        }

        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
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
            background: var(--dark-primary);
            color: var(--text-light);
            padding: 80px 0 30px;
            border-top: 1px solid var(--glass-border);
        }

        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 30px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 60px;
        }

        .footer-brand {
            max-width: 300px;
            margin: 0 auto;
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
            justify-content: center;
        }

        .social-link {
            width: 45px;
            height: 45px;
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

        /* Responsive */
        @media (max-width: 992px) {
            .footer-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .footer-section {
                text-align: center !important;
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

            .footer-logo {
                font-size: 1.8rem;
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

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
            z-index: 1001;
            background: none;
            border: none;
        }

        .mobile-menu-toggle span {
            width: 25px;
            height: 3px;
            background: var(--primary-gold);
            margin: 3px 0;
            transition: var(--transition);
            border-radius: 2px;
        }

        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        /* Mobile Sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 300px;
            height: 100vh;
            background: var(--dark-primary);
            backdrop-filter: blur(20px);
            border-left: 1px solid var(--glass-border);
            z-index: 999;
            transition: var(--transition);
            overflow-y: auto;
        }

        .mobile-sidebar.active {
            right: 0;
        }

        .mobile-sidebar-content {
            padding: 80px 30px 30px;
        }

        .mobile-nav-menu {
            list-style: none;
            padding: 0;
            margin: 0 0 40px 0;
        }

        .mobile-nav-menu li {
            margin-bottom: 10px;
        }

        .mobile-nav-menu a {
            display: block;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            padding: 15px 20px;
            border-radius: 10px;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
        }

        .mobile-nav-menu a:hover,
        .mobile-nav-menu a.active {
            color: var(--primary-gold);
            background: var(--glass-bg);
            border-color: var(--glass-border);
            transform: translateX(5px);
        }

        .mobile-cta-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .mobile-cta-buttons a {
            background: var(--primary-gold);
            color: var(--dark-primary);
            padding: 15px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .mobile-cta-buttons a:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
        }

        .mobile-cta-buttons .btn-secondary {
            background: transparent;
            color: var(--primary-gold);
            border: 2px solid var(--primary-gold);
        }

        .mobile-cta-buttons .btn-secondary:hover {
            background: var(--primary-gold);
            color: var(--dark-primary);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Design */
        @media (max-width: 992px) {

            .nav-menu,
            .nav-cta {
                display: none;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .nav-container {
                padding: 0 20px;
            }

            .logo-text {
                font-size: 1.2rem;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .mobile-sidebar {
                width: 280px;
            }

            .mobile-sidebar-content {
                padding: 70px 20px 20px;
            }

            .logo-text {
                font-size: 1.1rem;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
        }

        /* Demo Content */
        .demo-content {
            margin-top: 80px;
            padding: 40px 20px;
            text-align: center;
        }

        .demo-content h1 {
            color: var(--primary-gold);
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
        }

        .demo-content p {
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* Ensure mobile menu toggle is visible on small screens */
        @media (max-width: 992px) {

            .nav-menu,
            .nav-cta {
                display: none;
            }

            .mobile-menu-toggle {
                display: flex;
            }
        }

        /* Mobile Sidebar Styling */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 280px;
            height: 100vh;
            background: var(--dark-primary);
            backdrop-filter: blur(20px);
            border-left: 1px solid var(--glass-border);
            z-index: 999;
            transition: var(--transition);
            overflow-y: auto;
        }

        .mobile-sidebar.active {
            right: 0;
        }

        .mobile-sidebar-content {
            padding: 80px 20px 20px;
        }

        .mobile-nav-menu {
            list-style: none;
            padding: 0;
            margin: 0 0 40px 0;
        }

        .mobile-nav-menu li {
            margin-bottom: 10px;
        }

        .mobile-nav-menu a {
            display: block;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            padding: 15px 20px;
            border-radius: 10px;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
        }

        .mobile-nav-menu a:hover,
        .mobile-nav-menu a.active {
            color: var(--primary-gold);
            background: var(--glass-bg);
            border-color: var(--glass-border);
            transform: translateX(5px);
        }

        .mobile-cta-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .mobile-cta-buttons a {
            background: var(--primary-gold);
            color: var(--dark-primary);
            padding: 15px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .mobile-cta-buttons a:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
        }

        .mobile-cta-buttons .btn-secondary {
            background: transparent;
            color: var(--primary-gold);
            border: 2px solid var(--primary-gold);
        }

        .mobile-cta-buttons .btn-secondary:hover {
            background: var(--primary-gold);
            color: var(--dark-primary);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
            z-index: 1001;
            background: none;
            border: none;
        }

        .mobile-menu-toggle span {
            width: 25px;
            height: 3px;
            background: var(--primary-gold);
            margin: 3px 0;
            transition: var(--transition);
            border-radius: 2px;
        }

        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        /* Mobile Sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 280px;
            height: 100vh;
            background: var(--dark-primary);
            backdrop-filter: blur(20px);
            border-left: 1px solid var(--glass-border);
            z-index: 999;
            transition: var(--transition);
            overflow-y: auto;
        }

        .mobile-sidebar.active {
            right: 0;
        }

        .mobile-sidebar-content {
            padding: 80px 20px 20px;
        }

        .mobile-nav-menu {
            list-style: none;
            padding: 0;
            margin: 0 0 40px 0;
        }

        .mobile-nav-menu li {
            margin-bottom: 10px;
        }

        .mobile-nav-menu a {
            display: block;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            padding: 15px 20px;
            border-radius: 10px;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
        }

        .mobile-nav-menu a:hover,
        .mobile-nav-menu a.active {
            color: var(--primary-gold);
            background: var(--glass-bg);
            border-color: var(--glass-border);
            transform: translateX(5px);
        }

        .mobile-cta-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .mobile-cta-buttons a {
            background: var(--primary-gold);
            color: var(--dark-primary);
            padding: 15px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .mobile-cta-buttons a:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
        }

        .mobile-cta-buttons .btn-secondary {
            background: transparent;
            color: var(--primary-gold);
            border: 2px solid var(--primary-gold);
        }

        .mobile-cta-buttons .btn-secondary:hover {
            background: var(--primary-gold);
            color: var(--dark-primary);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Design */
        @media (max-width: 992px) {

            .nav-menu,
            .nav-cta {
                display: none;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .nav-container {
                padding: 0 20px;
            }
        }

        @media (max-width: 480px) {
            .mobile-sidebar {
                width: 280px;
            }

            .mobile-sidebar-content {
                padding: 70px 20px 20px;
            }
        }
    </style>

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

        /* Header */
        .luxury-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            z-index: 1000;
            padding: 15px 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            z-index: 1001;
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

        /* Desktop Navigation */
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
            display: flex;
            gap: 15px;
        }

        .nav-cta a {
            background: var(--primary-gold);
            color: var(--dark-primary);
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .nav-cta a:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-gold);
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
            z-index: 1001;
            background: none;
            border: none;
        }

        .mobile-menu-toggle span {
            width: 25px;
            height: 3px;
            background: var(--primary-gold);
            margin: 3px 0;
            transition: var(--transition);
            border-radius: 2px;
        }

        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        /* Mobile Sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 300px;
            height: 100vh;
            background: var(--dark-primary);
            backdrop-filter: blur(20px);
            border-left: 1px solid var(--glass-border);
            z-index: 999;
            transition: var(--transition);
            overflow-y: auto;
        }

        .mobile-sidebar.active {
            right: 0;
        }

        .mobile-sidebar-content {
            padding: 80px 30px 30px;
        }

        .mobile-nav-menu {
            list-style: none;
            padding: 0;
            margin: 0 0 40px 0;
        }

        .mobile-nav-menu li {
            margin-bottom: 10px;
        }

        .mobile-nav-menu a {
            display: block;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            padding: 15px 20px;
            border-radius: 10px;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
        }

        .mobile-nav-menu a:hover,
        .mobile-nav-menu a.active {
            color: var(--primary-gold);
            background: var(--glass-bg);
            border-color: var(--glass-border);
            transform: translateX(5px);
        }

        .mobile-cta-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .mobile-cta-buttons a {
            background: var(--primary-gold);
            color: var(--dark-primary);
            padding: 15px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .mobile-cta-buttons a:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
        }

        .mobile-cta-buttons .btn-secondary {
            background: transparent;
            color: var(--primary-gold);
            border: 2px solid var(--primary-gold);
        }

        .mobile-cta-buttons .btn-secondary:hover {
            background: var(--primary-gold);
            color: var(--dark-primary);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Design */
        @media (max-width: 992px) {

            .nav-menu,
            .nav-cta {
                display: none;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .nav-container {
                padding: 0 20px;
            }

            .logo-text {
                font-size: 1.2rem;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .mobile-sidebar {
                width: 280px;
            }

            .mobile-sidebar-content {
                padding: 70px 20px 20px;
            }

            .logo-text {
                font-size: 1.1rem;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
        }

        /* Demo Content */
        .demo-content {
            margin-top: 80px;
            padding: 40px 20px;
            text-align: center;
        }

        .demo-content h1 {
            color: var(--primary-gold);
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
        }

        .demo-content p {
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
    </style>

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

        /* Header */
        .luxury-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(20px);
            z-index: 1000;
            padding: 15px 0;
            border-bottom: 1px solid var(--glass-border);
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            z-index: 1001;
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

        /* Desktop Navigation */
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
            display: flex;
            gap: 15px;
        }

        .nav-cta a {
            background: var(--primary-gold);
            color: var(--dark-primary);
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .nav-cta a:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-gold);
        }

        /* Mobile Menu Toggle */
        .mobile-menu-toggle {
            display: none;
            flex-direction: column;
            cursor: pointer;
            padding: 5px;
            z-index: 1001;
            background: none;
            border: none;
        }

        .mobile-menu-toggle span {
            width: 25px;
            height: 3px;
            background: var(--primary-gold);
            margin: 3px 0;
            transition: var(--transition);
            border-radius: 2px;
        }

        .mobile-menu-toggle.active span:nth-child(1) {
            transform: rotate(-45deg) translate(-5px, 6px);
        }

        .mobile-menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }

        .mobile-menu-toggle.active span:nth-child(3) {
            transform: rotate(45deg) translate(-5px, -6px);
        }

        /* Mobile Sidebar */
        .mobile-sidebar {
            position: fixed;
            top: 0;
            right: -100%;
            width: 300px;
            height: 100vh;
            background: var(--dark-primary);
            backdrop-filter: blur(20px);
            border-left: 1px solid var(--glass-border);
            z-index: 9999;
            transition: var(--transition);
            overflow-y: auto;
        }

        .mobile-sidebar.active {
            right: 0;
        }

        .mobile-sidebar-content {
            padding: 80px 30px 30px;
        }

        .mobile-nav-menu {
            list-style: none;
            padding: 0;
            margin: 0 0 40px 0;
        }

        .mobile-nav-menu li {
            margin-bottom: 10px;
        }

        .mobile-nav-menu a {
            display: block;
            color: var(--text-light);
            text-decoration: none;
            font-weight: 500;
            padding: 15px 20px;
            border-radius: 10px;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            border: 1px solid transparent;
        }

        .mobile-nav-menu a:hover,
        .mobile-nav-menu a.active {
            color: var(--primary-gold);
            background: var(--glass-bg);
            border-color: var(--glass-border);
            transform: translateX(5px);
        }

        .mobile-cta-buttons {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .mobile-cta-buttons a {
            background: var(--primary-gold);
            color: var(--dark-primary);
            padding: 15px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            text-align: center;
            transition: var(--transition);
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }

        .mobile-cta-buttons a:hover {
            background: var(--gold-light);
            transform: translateY(-2px);
        }

        .mobile-cta-buttons .btn-secondary {
            background: transparent;
            color: var(--primary-gold);
            border: 2px solid var(--primary-gold);
        }

        .mobile-cta-buttons .btn-secondary:hover {
            background: var(--primary-gold);
            color: var(--dark-primary);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 998;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Design */
        @media (max-width: 992px) {

            .nav-menu,
            .nav-cta {
                display: none;
            }

            .mobile-menu-toggle {
                display: flex;
            }

            .nav-container {
                padding: 0 20px;
            }

            .logo-text {
                font-size: 1.2rem;
            }

            .logo-icon {
                width: 35px;
                height: 35px;
                font-size: 16px;
            }
        }

        @media (max-width: 480px) {
            .mobile-sidebar {
                width: 280px;
            }

            .mobile-sidebar-content {
                padding: 70px 20px 20px;
            }

            .logo-text {
                font-size: 1.1rem;
            }

            .logo-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }
        }

        /* Demo Content */
        .demo-content {
            margin-top: 80px;
            padding: 40px 20px;
            text-align: center;
        }

        .demo-content h1 {
            color: var(--primary-gold);
            margin-bottom: 20px;
            font-family: 'Playfair Display', serif;
        }

        .demo-content p {
            color: var(--text-light);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
    </style>
</head>

<body>


    @php
        $bannerContent = getContent('banner.content', true);
        $contactContent = getContent('contact_us.content', true);
        $contactElement = getContent('contact_us.element', false, null, true);
    @endphp

    <!-- Header -->
    <!-- Header -->
    <header class="luxury-header fixed-top">
        <div class="nav-container container">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="logo">
                <img src="{{ siteLogo() }}" alt="@lang('logo')" style="max-height: 60px;">
            </a>

            <!-- Desktop Navigation Menu -->
            <nav class="nav-menu">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('ads.index') }}" class="{{ request()->routeIs('ads.index') ? 'active' : '' }}">Ads</a>
                <a href="{{ route('contact') }}" class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact
                    Us</a>
            </nav>

            <!-- Desktop CTA Buttons -->
            <div class="nav-cta">
                @guest
                    <a class="btn btn--base btn--round btn--md" href="{{ route('user.login') }}">Login</a>
                    <a class="btn btn--base btn--round btn--md" href="{{ route('user.register') }}">Register</a>
                @else
                    <a class="btn btn--base btn--round btn--md" href="{{ route('user.product.index') }}">Dashboard</a>
                    <a class="btn btn--base btn--round btn--md" href="{{ route('user.logout') }}">Logout</a>
                @endguest
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>


    <!-- Mobile Sidebar -->
    <div class="mobile-sidebar" id="mobileSidebar">
        <div class="mobile-sidebar-content">
            <!-- Mobile Navigation Menu -->
            <ul class="mobile-nav-menu">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                </li>
                <li><a href="{{ route('ads.index') }}"
                        class="{{ request()->routeIs('ads.index') ? 'active' : '' }}">Ads</a></li>
                <li><a href="{{ route('contact') }}"
                        class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact Us</a></li>
            </ul>

            <!-- Mobile CTA Buttons -->
            <div class="mobile-cta-buttons">
                @guest
                    <a href="{{ route('user.login') }}">Login</a>
                    <a href="{{ route('user.register') }}" class="btn-secondary">Register</a>
                @else
                    <a href="{{ route('user.product.index') }}">Dashboard</a>
                    <a href="{{ route('user.logout') }}" class="btn-secondary">Logout</a>
                @endguest
            </div>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div class="mobile-overlay" id="mobileOverlay"></div>

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
                            {{-- <a href="#" class="view-category">
                                View Collection <i class="fas fa-arrow-right"></i>
                            </a> --}}
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
                            {{-- <a href="#" class="view-category">
                                View Collection <i class="fas fa-arrow-right"></i>
                            </a> --}}
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
                            {{-- <a href="#" class="view-category">
                                View Collection <i class="fas fa-arrow-right"></i>
                            </a> --}}
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
                            {{-- <a href="#" class="view-category">
                                View Collection <i class="fas fa-arrow-right"></i>
                            </a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                            <a href="#" class="category-link"
                                style="color: white; cursor: default; text-decoration: none;">
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
                                        {{-- <span class="view-category">
                                            View Collection <i class="fas fa-arrow-right"></i>
                                        </span> --}}
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

                <!-- Quick Links -->
                <div class="footer-section">
                    <h4 class="luxury-font">Quick Links</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('home') }}">Home</a></li>
                        <li><a href="{{ route('ads.index') }}">Ads</a></li>
                        <li>
                            <a href="{{ route('contact') }}"
                                class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                                Contact Us
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Brand Section -->
                <div class="footer-brand text-center">
                    <h2 class="footer-logo luxury-font">Luxceylone</h2>
                    <p class="footer-description">
                        Discover unparalleled luxury with our exclusive collection of premium items,
                        curated for elegance and sophistication.
                    </p>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-linkedin"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>

                <div class="footer-section text-end">
                    <h4 class="luxury-font">{{ __($contactContent->data_values->title) }}</h4>
                    <ul class="footer-links footer-contact-list">
                        @foreach ($contactElement as $contact)
                            <li>
                                @if (__($contact->data_values->title) == 'Phone')
                                    <a
                                        href="tel:{{ __($contact->data_values->content) }}">{{ __($contact->data_values->content) }}</a>

                            </li>
                        @elseif(__($contact->data_values->title) == 'Email')
                            <a
                                href="mailto:{{ __($contact->data_values->content) }}">{{ __($contact->data_values->content) }}</a>
                        @else
                            {{ __($contact->data_values->content) }}
                        @endif
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 Luxceylone. All rights reserved.</p>
            </div>
        </div>
    </footer>



    <script>
        // Wait for DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Mobile Menu Functionality
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const mobileOverlay = document.getElementById('mobileOverlay');

            // Debug logging
            console.log('Mobile Menu Elements:', {
                toggle: mobileMenuToggle,
                sidebar: mobileSidebar,
                overlay: mobileOverlay
            });

            // Check if all elements exist
            if (!mobileMenuToggle || !mobileSidebar || !mobileOverlay) {
                console.error('Mobile menu elements not found!');
                return;
            }

            function toggleMobileMenu() {
                console.log('Toggling mobile menu');
                mobileMenuToggle.classList.toggle('active');
                mobileSidebar.classList.toggle('active');
                mobileOverlay.classList.toggle('active');

                // Prevent body scroll when menu is open
                if (mobileSidebar.classList.contains('active')) {
                    document.body.style.overflow = 'hidden';
                } else {
                    document.body.style.overflow = '';
                }
            }

            function closeMobileMenu() {
                console.log('Closing mobile menu');
                mobileMenuToggle.classList.remove('active');
                mobileSidebar.classList.remove('active');
                mobileOverlay.classList.remove('active');
                document.body.style.overflow = '';
            }

            // Event Listeners
            mobileMenuToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                toggleMobileMenu();
            });

            mobileOverlay.addEventListener('click', closeMobileMenu);

            // Close menu when clicking on nav links
            document.querySelectorAll('.mobile-nav-menu a').forEach(link => {
                link.addEventListener('click', closeMobileMenu);
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileSidebar.classList.contains('active')) {
                    closeMobileMenu();
                }
            });

            // Active navigation state
            document.querySelectorAll('.mobile-nav-menu a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Remove active class from all links
                    document.querySelectorAll('.mobile-nav-menu a').forEach(l => l.classList.remove(
                        'active'));
                    // Add active class to clicked link
                    this.classList.add('active');
                });
            });
        });

        // Header background on scroll (can be outside DOMContentLoaded)
        window.addEventListener('scroll', () => {
            const header = document.querySelector('.luxury-header');
            if (header) {
                if (window.scrollY > 100) {
                    header.style.background = 'rgba(26, 26, 26, 0.98)';
                } else {
                    header.style.background = 'rgba(26, 26, 26, 0.95)';
                }
            }
        });
    </script>
</body>

</html>
