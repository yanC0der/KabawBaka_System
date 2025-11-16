<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Simple server-side navbar that outputs different links based on session
echo '<header>';
echo '  <div class="logo">';
echo '    <img src="/kabawbaka/assets/kabawbaka-logo.png" alt="KabawBaka Logo">';
echo '    <h2>KabawBaka?</h2>';
echo '  </div>';
echo '  <nav>';
echo '    <ul>';
echo '      <li><a href="/kabawbaka/index.html">Home</a></li>';
echo '      <li><a href="/kabawbaka/marketplace.html">Marketplace</a></li>';
echo '      <li><a href="/kabawbaka/kabawbaka.html">KabawBaka?</a></li>';
echo '      <li><a href="#about">About</a></li>';
echo '      <li><a href="#contact">Contact</a></li>';
echo '    </ul>';
echo '  </nav>';

echo '  <div id="userMenu">';
if (!empty($_SESSION['user_id'])) {
    $name = !empty($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Member';
    echo "<a href=\"/kabawbaka/user_dashboard.html\" class=\"btn-login\">Dashboard</a>";
    echo "<a href=\"#\" id=\"logoutBtn\" class=\"btn-login\">Logout</a>";
} else {
    echo '    <a href="/kabawbaka/login.html" class="btn-login">Login</a>';
    echo '    <a href="/kabawbaka/register.html" class="btn-login">Register</a>';
}
echo '  </div>';
echo '</header>';

?>
