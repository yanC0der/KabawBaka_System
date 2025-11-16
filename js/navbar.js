// KabawBaka Navbar Management Module

class NavbarManager {
    constructor() {
        this.init();
    }

    init() {
        this.checkLoginStatus();
    }

    async checkLoginStatus() {
        try {
            const response = await fetch('php/check_session.php');
            const data = await response.json();
            this.updateNavbar(data);
        } catch (error) {
            console.error('Error checking login status:', error);
        }
    }

    updateNavbar(data) {
        const userMenu = document.getElementById('userMenu');
        if (!userMenu) return;

        if (data.logged_in) {
            userMenu.innerHTML = `
                <a href="user_dashboard.html" class="btn-login">Dashboard</a>
                <a href="#" id="logoutBtn" class="btn-login">Logout</a>
            `;

            // Logout functionality
            const logoutBtn = document.getElementById('logoutBtn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.logout();
                });
            }
        } else {
            userMenu.innerHTML = '<a href="login.html" class="btn-login">Login</a>';
        }
    }

    async logout() {
        try {
            await fetch('php/logout.php');
            window.location.reload();
        } catch (error) {
            console.error('Error logging out:', error);
            window.location.reload();
        }
    }
}

// Initialize navbar when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    new NavbarManager();
});
