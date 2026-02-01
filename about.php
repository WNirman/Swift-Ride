<?php require_once 'includes/auth.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - SwiftRide System</title>
    <?php include 'includes/header.php'; ?>
</head>

<body class="bg-light">
    <?php include 'includes/navigation.php'; ?>

    <div class="main-content">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="glass p-5 rounded-4 animate-up">
                        <div class="text-center mb-5">
                            <h1 class="display-4 fw-bold mb-3 text-primary">About SwiftRide</h1>
                            <p class="lead text-muted mx-auto" style="max-width: 700px;">
                                Redefining urban mobility with a premium vehicle rental experience. Simple, safe, and
                                sophisticated.
                            </p>
                        </div>

                        <div class="row g-4 mb-5">
                            <div class="col-md-4">
                                <div class="text-center p-4 h-100 rounded-4 transition-hover">
                                    <div class="icon-box-primary mb-3 mx-auto">
                                        <i class="fas fa-car-side fa-2x"></i>
                                    </div>
                                    <h5 class="fw-bold">Extensive range</h5>
                                    <p class="text-muted small">From agile bicycles to spacious vans, our fleet is
                                        curated for every journey.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-4 h-100 rounded-4 transition-hover">
                                    <div class="icon-box-primary mb-3 mx-auto">
                                        <i class="fas fa-shield-alt fa-2x"></i>
                                    </div>
                                    <h5 class="fw-bold">Safe & Reliable</h5>
                                    <p class="text-muted small">Pristine maintenance and rigorous safety checks for your
                                        peace of mind.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-4 h-100 rounded-4 transition-hover">
                                    <div class="icon-box-primary mb-3 mx-auto">
                                        <i class="fas fa-headset fa-2x"></i>
                                    </div>
                                    <h5 class="fw-bold">24/7 Support</h5>
                                    <p class="text-muted small">Our dedicated team is always ready to assist you,
                                        whenever and wherever.</p>
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center mb-5 gx-5">
                            <div class="col-md-6">
                                <h3 class="fw-bold mb-4">Who We Are?</h3>
                                <p class="text-muted mb-4">
                                    SwiftRide is a premium mobility platform designed for the modern traveller. We
                                    bridge the gap between convenience and elegance, offering a seamless booking
                                    experience that gets you on the road in minutes.
                                </p>
                                <div class="glass p-4 rounded-4 border-primary-start">
                                    <p class="font-italic mb-0">"Our mission is to make travel effortless, accessible,
                                        and enjoyable for everyone, everywhere."</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-primary rounded-4 p-5 text-white animate-up shadow-lg">
                                    <h3 class="fw-bold mb-4">Why Choose Us?</h3>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-3 d-flex align-items-center">
                                            <i class="fas fa-check-circle me-3 opacity-75"></i> Competitive &
                                            Transparent Pricing
                                        </li>
                                        <li class="mb-3 d-flex align-items-center">
                                            <i class="fas fa-check-circle me-3 opacity-75"></i> Flexible Rental Terms
                                        </li>
                                        <li class="mb-3 d-flex align-items-center">
                                            <i class="fas fa-check-circle me-3 opacity-75"></i> Multi-Location
                                            Pickup/Drop
                                        </li>
                                        <li class="d-flex align-items-center">
                                            <i class="fas fa-check-circle me-3 opacity-75"></i> Premium Customer Service
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .icon-box-primary {
            width: 64px;
            height: 64px;
            background: var(--primary-glow);
            color: var(--primary-color);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .transition-hover:hover .icon-box-primary {
            transform: scale(1.1);
            background: var(--primary-color);
            color: white;
        }

        .border-primary-start {
            border-left: 4px solid var(--primary-color);
        }
    </style>

    <?php include 'includes/footer.php'; ?>
</body>

</html>