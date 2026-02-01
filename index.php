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
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@1,600&family=Poppins:wght@300;400;600&display=swap"
    rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }

    .hero {
      position: relative;
      height: 85vh;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      padding: 2rem;
      background: #000;
    }

    .hero::after {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.7) 100%);
      z-index: 2;
    }

    .hero-content {
      position: relative;
      z-index: 3;
      max-width: 800px;
      text-align: center;
    }

    .hero-text {
      font-family: 'Playfair Display', serif;
      font-size: 4.5rem;
      font-weight: 700;
      color: #fff;
      line-height: 1.1;
      margin-bottom: 1.5rem;
      text-shadow: 0 5px 20px rgba(0,0,0,0.5);
      transition: transform 0.1s ease, opacity 0.2s ease;
    }

    .hero-subtext {
      font-family: 'Poppins', sans-serif;
      font-size: 1.5rem;
      color: rgba(255,255,255,0.9);
      margin: 0;
      text-shadow: 0 2px 10px rgba(0,0,0,0.5);
      transition: transform 0.1s ease, opacity 0.2s ease;
    }

    .hero-bg-container {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      overflow: hidden;
      z-index: 1;
    }

    .hero-bg {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-size: cover;
      background-position: center;
      opacity: 0;
      transition: opacity 1.5s ease-in-out;
      transform: scale(1);
    }

    .hero-bg.show {
      opacity: 1 !important;
      z-index: 1;
      animation: kenBurns 30s linear infinite alternate;
    }

    @keyframes kenBurns {
      0% {
        transform: scale(1) translate(0, 0);
      }

      100% {
        transform: scale(1.1) translate(-1%, -1%);
      }
    }

    .hero-dots {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 10px;
      z-index: 3;
    }

    .hero-dots span {
      width: 12px;
      height: 12px;
      background-color: rgba(255, 255, 255, 0.5);
      border-radius: 50%;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .hero-dots span.active {
      background-color: #ffffff;
    }

    /* Quick Vehicle Section */
    .quick-vehicles {
      display: flex;
      justify-content: center;
      gap: 2rem;
      flex-wrap: wrap;
      margin: 3rem 0;
    }

    .vehicle-box {
      background: #fff;
      border-radius: 20px;
      padding: 2.5rem;
      text-align: center;
      width: 200px;
      transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s;
      cursor: pointer;
      border: 1px solid rgba(0, 0, 0, 0.05);
      perspective: 1000px;
      transform-style: preserve-3d;
    }

    .vehicle-box:hover {
      transform: translateY(-10px) rotateX(5deg) rotateY(5deg);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .vehicle-icon {
      font-size: 3.5rem;
      color: var(--primary-color);
      margin-bottom: 1.5rem;
      transition: transform 0.3s;
      display: inline-block;
    }

    .vehicle-box:hover .vehicle-icon {
      transform: translateZ(30px);
    }

    .vehicle-name {
      font-weight: 700;
      font-size: 1.4rem;
      color: #333;
    }
  </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
  <?php include 'includes/navigation.php'; ?>

  <!-- Hero Section -->
  <div class="hero" id="hero">
    <div class="hero-content animate-up">
      <h1 class="hero-text" id="hero-text">Find Your Perfect Ride</h1>
      <p class="hero-subtext">Rent cars, bikes, vans & more. Anywhere. Anytime.</p>
    </div>
    <!-- Background slideshow images -->
    <div class="hero-bg-container">
      <div class="hero-bg show fade" style="background-image: url('assets/img/bg1.webp');"></div>
      <div class="hero-bg fade" style="background-image: url('assets/img/bg2.webp');"></div>
      <div class="hero-bg fade" style="background-image: url('assets/img/bg3.webp');"></div>
      <div class="hero-bg fade" style="background-image: url('assets/img/bg4.webp');"></div>
    </div>
    <!-- Dark overlay is now handled via CSS ::after -->
    <!-- Navigation dots -->
    <div class="hero-dots" id="hero-dots"></div>
  </div>

  <!-- Quick Vehicle Selection Section -->
  <div class="container text-center py-5">
    <h2 class="mb-5 fw-bold animate-up">Choose Your Ride</h2>
    <div class="quick-vehicles animate-up" style="animation-delay: 0.2s;">
      <?php foreach ($main_vehicle_types as $type => $data): ?>
        <div class="vehicle-box" onclick="window.location.href='<?php echo $data['link']; ?>'">
          <div class="vehicle-icon"><i class="<?php echo $data['icon']; ?>"></i></div>
          <div class="vehicle-name"><?php echo $type; ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Featured Fleet Section -->
  <?php
  $conn = Connect();
  // Fetch random available vehicles
  $sql_featured = "SELECT * FROM Vehicles WHERE vehicle_availability = 'yes' AND is_deleted = 0 ORDER BY RAND() LIMIT 3";
  $res_featured = $conn->query($sql_featured);
  ?>
  <div class="container py-5 mb-5">
    <div class="d-flex justify-content-between align-items-end mb-5 animate-up">
      <div>
        <h2 class="fw-bold mb-2">Featured Fleet</h2>
        <p class="text-muted mb-0">Discover our most popular rides selected for you</p>
      </div>
      <a href="cars.php" class="btn btn-outline-primary rounded-pill px-4">View All Vehicles <i
          class="fas fa-arrow-right ms-2"></i></a>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4 animate-up" style="animation-delay: 0.3s;">
      <?php if ($res_featured && $res_featured->num_rows > 0): ?>
        <?php while ($v = $res_featured->fetch_assoc()): ?>
          <div class="col">
            <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden vehicle-card-modern">
              <?php $img = !empty($v['vehicle_image']) ? $v['vehicle_image'] : 'assets/img/default-car.jpg'; ?>
              <div class="position-relative">
                <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" style="height: 240px; object-fit: cover;"
                  alt="<?= htmlspecialchars($v['vehicle_model']) ?>">
                <div class="position-absolute top-0 end-0 m-3">
                  <span class="badge glass text-dark py-2 px-3 rounded-pill fw-bold">Rs. <?= number_format($v['price']) ?> /
                    day</span>
                </div>
              </div>
              <div class="card-body p-4">
                <h5 class="fw-bold mb-3"><?= htmlspecialchars($v['vehicle_brand'] . ' ' . $v['vehicle_model']) ?></h5>
                <div class="d-flex gap-3 mb-4 text-muted small">
                  <span><i class="fas fa-gas-pump text-primary me-1"></i>
                    <?= htmlspecialchars($v['fuel_type'] ?? 'Petrol') ?></span>
                  <span><i class="fas fa-user-friends text-primary me-1"></i>
                    <?= htmlspecialchars($v['seating_capacity'] ?? '5') ?> Seats</span>
                  <span><i class="fas fa-cog text-primary me-1"></i> Auto</span>
                </div>
                <a href="my_bookings.php?vehicle_id=<?= $v['vehicle_id'] ?>"
                  class="btn btn-primary w-100 rounded-pill py-2 shadow-sm">Reserve Now</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="col-12 text-center text-muted py-5">
          <div class="glass p-5 rounded-4">
            <p class="mb-0">Explore our selection by choosing a category above.</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <?php $conn->close(); ?>

  <?php include 'includes/footer.php'; ?>

  <!-- Parallax & Slide Show -->
  <script>
    const heroText = document.getElementById("hero-text");
    const heroSub = document.querySelector(".hero-subtext");
    const slides = document.querySelectorAll('.hero-bg');
    const dotsContainer = document.getElementById('hero-dots');
    let currentSlide = 0;
    let dots = [];

    // Create dots
    slides.forEach((_, i) => {
      const dot = document.createElement('span');
      dot.addEventListener('click', () => showSlide(i));
      dotsContainer.appendChild(dot);
      dots.push(dot);
    });

    function showSlide(index) {
      slides[currentSlide].classList.remove('show');
      dots[currentSlide].classList.remove('active');
      currentSlide = index;
      slides[currentSlide].classList.add('show');
      dots[currentSlide].classList.add('active');
    }

    function nextSlide() {
      let nextIndex = (currentSlide + 1) % slides.length;
      showSlide(nextIndex);
    }

    // Init
    showSlide(0);
    setInterval(nextSlide, 4000);

    // Parallax Effect
    document.getElementById("hero").addEventListener("mousemove", (e) => {
      const x = (e.clientX / window.innerWidth) - 0.5;
      const y = (e.clientY / window.innerHeight) - 0.5;
      heroText.style.transform = `translate(${x * 30}px, ${y * 15}px)`;
      if (heroSub) heroSub.style.transform = `translate(${x * 15}px, ${y * 8}px)`;
    });

    // Tilt Effect for vehicle boxes
    document.querySelectorAll('.vehicle-box').forEach(box => {
      box.addEventListener('mousemove', (e) => {
        const rect = box.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const centerX = rect.width / 2;
        const centerY = rect.height / 2;
        const rotateX = (centerY - y) / 10;
        const rotateY = (x - centerX) / 10;

        box.style.transform = `translateY(-10px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
      });

      box.addEventListener('mouseleave', () => {
        box.style.transform = `translateY(0) rotateX(0) rotateY(0)`;
      });
    });

    window.addEventListener("scroll", () => {
      const scrollY = window.scrollY;
      const opacity = Math.max(0.2, 1 - scrollY / 400);
      heroText.style.opacity = opacity;
      if (heroSub) heroSub.style.opacity = opacity;
    });
  </script>

  <!-- Chatbot Floating Button -->
  <div id="chatBtnCircle">Swift</div>
  <div id="chatBtn">
    <div id="chatHeader">
      <span><strong>Swift Guide</strong></span>
      <span id="closeBtn" style="cursor:pointer;">✖</span>
    </div>
    <div id="chatMessages">
      <div><b>Bot:</b> Hi! I’m your travel guide 🤖. How many days will you travel?</div>
    </div>
    <div id="chatInput">
      <input id="chatbot-input" type="text" placeholder="Type here...">
      <button id="sendBtn">Send</button>
    </div>
  </div>
  <style>
    #chatBtnCircle {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: #007bff;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
      z-index: 9999;
    }

    #chatBtn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 320px;
      height: 450px;
      border-radius: 16px;
      background: #fff;
      display: none;
      flex-direction: column;
      padding: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      z-index: 9999;
    }

    #chatHeader {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-bottom: 8px;
      border-bottom: 1px solid #ddd;
    }

    #chatMessages {
      flex: 1;
      overflow-y: auto;
      margin-top: 10px;
      font-size: 14px;
      color: #444;
    }

    #chatInput {
      display: flex;
      margin-top: 10px;
    }

    #chatInput input {
      flex: 1;
      padding: 5px;
      border: 1px solid #ccc;
      border-radius: 6px 0 0 6px;
    }

    #chatInput button {
      background: #007bff;
      border: none;
      color: white;
      padding: 5px 10px;
      border-radius: 0 6px 6px 0;
      cursor: pointer;
    }
  </style>
  <script>
    let step = 0;
    let userData = {};

    const chatBtn = document.getElementById('chatBtn');
    const chatBtnCircle = document.getElementById('chatBtnCircle');
    const closeBtn = document.getElementById('closeBtn');
    const sendBtn = document.getElementById('sendBtn');
    const chatMessages = document.getElementById('chatMessages');
    const userInput = document.getElementById('chatbot-input');

    // Open chat
    chatBtnCircle.addEventListener('click', () => {
      chatBtn.style.display = 'flex';
      chatBtnCircle.style.display = 'none';
    });

    // Close chat
    closeBtn.addEventListener('click', () => {
      chatBtn.style.display = 'none';
      chatBtnCircle.style.display = 'flex';
    });

    // Add message
    function addMessage(sender, text) {
      const msg = document.createElement("div");
      msg.innerHTML = `<b>${sender}:</b> ${text}`;
      chatMessages.appendChild(msg);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Send message
    function sendMessage() {
      const userText = userInput.value.trim();
      if (!userText) return;
      addMessage("You", userText);
      userInput.value = "";

      if (step === 0) {
        userData.days = parseInt(userText);
        step++;
        addMessage("Bot", "Great! 👌 How many people are traveling?");
      } else if (step === 1) {
        userData.people = parseInt(userText);
        step++;
        addMessage("Bot", "Got it. From which city will you pick up the vehicle?");
      } else if (step === 2) {
        userData.city = userText;
        step++;
        fetch("chatbot.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(userData)
        })
          .then(res => res.json())
          .then(data => addMessage("Bot", data.reply));
      } else {
        addMessage("Bot", "Would you like to book now? 🚀");
      }
    }
    sendBtn.addEventListener('click', sendMessage);
    userInput.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') sendMessage();
    });
  </script>

</body>

</html>