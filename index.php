<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'includes/auth.php';
require_once 'includes/config.php';

$main_vehicle_types = [
    'Car' => ['icon' => 'fas fa-car', 'link' => 'cars.php'],
    'Bike' => ['icon' => 'fas fa-motorcycle', 'link' => 'bike.php'],
    'Van' => ['icon' => 'fas fa-shuttle-van', 'link' => 'van.php'],
    'Bicycle' => ['icon' => 'fas fa-bicycle', 'link' => 'bicycle.php'],
    'Bus' => ['icon' => 'fas fa-bus', 'link' => 'bus.php']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Find Your Ride - Rent Any Vehicle</title>
<link rel="icon" type="image/png" href="assets/img/favicon.png">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,600&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<style>
body { font-family: 'Poppins', sans-serif; }

/* Hero Section remains unchanged */
.hero {
    position: relative;
    height: 70vh;
    display: flex;
    align-items: flex-start;
    justify-content: flex-start;
    overflow: hidden;
    padding: 1rem;
    background: #000;
}
.hero-content { position: relative; z-index: 2; max-width: 45%; margin-top: 0; margin-left: 0; }
.hero-text { font-family: 'Playfair Display', serif; font-size: 3.5rem; font-weight: 700; color: #f0eaeaff; line-height: 1.2; margin-bottom: 0.5rem; transition: transform 0.1s ease, opacity 0.2s ease; }
.hero-subtext { font-family: 'Poppins', sans-serif; font-size: 1.2rem; color: #f5ededff; margin: 0; transition: transform 0.1s ease, opacity 0.2s ease; }
.hero-bg { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('assets/img/bg1.webp'); background-size: cover; background-position: center; opacity: 0.8; z-index: 1; }

/* Main Vehicle Section */
.quick-vehicles {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
    margin: 3rem 0;
}
.vehicle-box {
    background: #fff;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    width: 160px;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
}
.vehicle-box:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 25px rgba(0,0,0,0.2);
}
.vehicle-icon {
    font-size: 3rem;
    color: #007bff;
    margin-bottom: 1rem;
}
.vehicle-name {
    font-weight: 600;
    font-size: 1.2rem;
    color: #333;
}
</style>
</head>
<body class="bg-light d-flex flex-column min-vh-100">
<?php include 'includes/navigation.php'; ?>

<!-- Hero Section remains unchanged -->
<div class="hero" id="hero">
    <div class="hero-content">
        <h1 class="hero-text" id="hero-text">Find Your Perfect Ride</h1>
        <p class="hero-subtext">Rent cars, bikes, vans & more. Anywhere. Anytime.</p>
    </div>
    <div class="hero-bg" id="hero-bg"></div>
</div>

<!-- Quick Vehicle Selection Section -->
<div class="container text-center">
    <h2 class="mb-4">Choose Your Ride</h2>
    <div class="quick-vehicles">
        <?php foreach($main_vehicle_types as $type => $data): ?>
            <div class="vehicle-box" onclick="window.location.href='<?php echo $data['link']; ?>'">
                <div class="vehicle-icon"><i class="<?php echo $data['icon']; ?>"></i></div>
                <div class="vehicle-name"><?php echo $type; ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Hero parallax effect (unchanged)
const heroText = document.getElementById("hero-text");
const heroSub = document.querySelector(".hero-subtext");

document.getElementById("hero").addEventListener("mousemove", (e) => {
    const x = (e.clientX / window.innerWidth) - 0.5;
    const y = (e.clientY / window.innerHeight) - 0.5;
    heroText.style.transform = `translate(${x * 20}px, ${y * 10}px)`;
    if(heroSub) heroSub.style.transform = `translate(${x * 10}px, ${y * 5}px)`;
});

window.addEventListener("scroll", () => {
    const scrollY = window.scrollY;
    const opacity = Math.max(0.2, 1 - scrollY / 400);
    heroText.style.opacity = opacity;
    if(heroSub) heroSub.style.opacity = opacity;
});
</script>
</body>
</html>
