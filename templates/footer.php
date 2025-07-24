<!-- Back to Top Button -->
<button id="topBtn" class="btn" aria-label="Back to top" title="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Footer -->
<footer class="bg-dark text-white py-4 mt-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 text-center text-md-start">
                <p class="mb-0">Primary School Educational Management System</p>
            </div>
            <div class="col-md-4 text-center">
                <p class="mb-0">&copy; <?= date('Y') ?></p>
            </div>
            <div class="col-md-4 text-center text-md-end">
                <p class="mb-0">All rights reserved. <span class="d-none d-md-inline">|</span> <a href="privacy.php" class="text-white text-decoration-none">Privacy Policy</a></p>
            </div>
        </div>
    </div>
</footer>

<!-- JavaScript Resources -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
        crossorigin="anonymous"></script>

<script nonce="<?= $scriptNonce ?>">
// Back to Top Button - Enhanced Implementation
document.addEventListener('DOMContentLoaded', function() {
    const topBtn = document.getElementById('topBtn');
    
    // Show/hide button based on scroll position
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            topBtn.classList.add('visible');
        } else {
            topBtn.classList.remove('visible');
        }
    });

    // Smooth scroll to top
    topBtn.addEventListener('click', function(e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

            // Card interaction handlers
            document.querySelectorAll('.card a').forEach(link => {
                link.addEventListener('click', handleCardLinkClick);
            });

            document.querySelectorAll('.card').forEach(card => {
                card.addEventListener('keydown', handleCardKeyboardNav);
                if (!card.hasAttribute('tabindex')) {
                    card.setAttribute('tabindex', '0');
                }
            });

            // Dashboard search functionality
            const searchInput = document.getElementById('dashboardSearch');
            if (searchInput) {
                searchInput.addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    document.querySelectorAll('.col .card').forEach(card => {
                        const cardText = card.textContent.toLowerCase();
                        card.style.display = cardText.includes(searchTerm) ? 'block' : 'none';
                    });
                });
            }

            // Live system status updates
            if (document.querySelector('.progress-bar[aria-label="Storage"]')) {
                updateSystemStatus(); // Initial load
                setInterval(updateSystemStatus, 120000); // Update every 2 minutes
            }
        });

        // ===== FUNCTION DEFINITIONS =====
        
        function handleCardLinkClick(e) {
            // Only handle internal links
            if (this.href && this.href.startsWith(window.location.origin)) {
                e.preventDefault();
                const card = this.closest('.card');
                const originalContent = card.innerHTML;
                
                // Show loading state
                card.innerHTML = `
                    <div class="card-body text-center py-4 card-loading">
                        <div class="spinner-border text-light" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 mb-0">Loading content...</p>
                    </div>
                `;
                
                setTimeout(() => {
                    window.location.href = this.href;
                }, 500);
                
                setTimeout(() => {
                    if (window.location.href !== this.href) {
                        card.innerHTML = originalContent;
                        alert('Navigation failed. Please try again.');
                    }
                }, 3000);
            }
        }

        function handleCardKeyboardNav(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                const link = this.querySelector('a');
                if (link) {
                    e.preventDefault();
                    link.click();
                }
            }
        }

        function updateSystemStatus() {
            fetch('api/system_status.php')
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const storageBar = document.querySelector('.progress-bar[aria-label="Storage"]');
                    const usersBar = document.querySelector('.progress-bar[aria-label="Active Users"]');
                    
                    if (storageBar) storageBar.style.width = data.storage + '%';
                    if (usersBar) usersBar.style.width = data.active_users + '%';
                })
                .catch(error => {
                    console.error('Failed to fetch system status:', error);
                });
        }
    </script>