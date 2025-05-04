           document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const toggleBtn = document.getElementById('toggleSidebar');
            const overlay = document.getElementById('overlay');
            
            // Function to check screen size and adjust sidebar
            function checkScreenSize() {
                if (window.innerWidth < 992) {
                    // Mobile view
                    sidebar.classList.remove('collapsed');
                    content.classList.remove('expanded');
                } else {
                    // Desktop view - keep previous state
                }
            }
            
            // Toggle sidebar
            toggleBtn.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    // Mobile view
                    sidebar.classList.toggle('mobile-active');
                    overlay.classList.toggle('active');
                } else {
                    // Desktop view
                    sidebar.classList.toggle('collapsed');
                    content.classList.toggle('expanded');
                }
            });
            
            // Close sidebar when clicking on overlay
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-active');
                overlay.classList.remove('active');
            });
            
            // Check screen size on load and resize
            checkScreenSize();
            window.addEventListener('resize', checkScreenSize);
        });
   