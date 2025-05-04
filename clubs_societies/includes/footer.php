 <!-- Dashboard Content ends here -->
 </div>
            
            </div>
        </div>
    
    
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            // Toggle sidebar functionality
            document.getElementById('toggleSidebar').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('show');
                document.getElementById('overlay').classList.toggle('show');
            });
            
            document.getElementById('overlay').addEventListener('click', function() {
                document.getElementById('sidebar').classList.remove('show');
                document.getElementById('overlay').classList.remove('show');
            });
        </script>
    </body>
    </html>