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

.hero-bg-container {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    overflow: hidden;
    z-index: 1;
}
.hero-bg {
    position: absolute;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-size: cover;
    background-position: center;
    opacity: 0;
    transition: opacity 1.5s ease-in-out;
}
.hero-bg.show {
    opacity: 0.8;
    z-index: 1;
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

<!-- Hero Section -->
<div class="hero" id="hero">
    <div class="hero-content">
        <h1 class="hero-text" id="hero-text">Find Your Perfect Ride</h1>
        <p class="hero-subtext">Rent cars, bikes, vans & more. Anywhere. Anytime.</p>
    </div>
    <!-- Background slideshow images -->
    <div class="hero-bg-container">
        <div class="hero-bg fade show" style="background-image: url('assets/img/bg1.webp');"></div>
        <div class="hero-bg fade" style="background-image: url('assets/img/bg2.webp');"></div>
        <div class="hero-bg fade" style="background-image: url('assets/img/bg3.webp');"></div>
        <div class="hero-bg fade" style="background-image: url('assets/img/bg4.webp');"></div>
    </div>
    <!-- Navigation dots -->
    <div class="hero-dots" id="hero-dots"></div>
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
  width: 60px; height: 60px;
  border-radius: 50%;
  background: #007bff;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,0,0,0.3);
  z-index: 9999;
}
#chatBtn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  width: 320px; height: 450px;
  border-radius: 16px;
  background: #fff;
  display: none;
  flex-direction: column;
  padding: 10px;
  box-shadow: 0 8px 20px rgba(0,0,0,0.3);
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
  flex: 1; overflow-y: auto; margin-top: 10px; font-size: 14px; color: #444;
}
#chatInput { display: flex; margin-top: 10px; }
#chatInput input {
  flex: 1; padding: 5px; border: 1px solid #ccc; border-radius: 6px 0 0 6px;
}
#chatInput button {
  background: #007bff; border: none; color: white;
  padding: 5px 10px; border-radius: 0 6px 6px 0; cursor: pointer;
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
      headers: {"Content-Type": "application/json"},
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
