 // Mobile menu toggle
 document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('cu-menuToggle');
    const navMenu = document.getElementById('navMenu');
    
    menuToggle.addEventListener('click', function() {
        navMenu.classList.toggle('active');
    });
 });  
    // Session Calendar button
    const sessionBtn = document.querySelector('.cu-session-btn');
    sessionBtn.addEventListener('click', function() {
        alert('Opening Session Calendar');
    });
    
    // More Details buttons
    const detailsBtns = document.querySelectorAll('.cu-details-btn');
    detailsBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const sessionName = this.parentElement.querySelector('.session-name').textContent;
            alert('Showing details for: ' + sessionName);
        });
    });
    
    // Logout button
    const logoutBtn = document.querySelector('.cu-logout-btn');
    logoutBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to log out?')) {
            alert('You have been logged out successfully!');
            
        }
    });
    
    
    // Add active class to current navigation item
    const currentLocation = window.location.href;
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(function(link) {
        if (link.href === currentLocation) {
            link.classList.add('active');
        }
    });

// Show appointment modal
function showAppointmentModal() {
    new bootstrap.Modal(document.getElementById('appointmentModal')).show();
}

// Handle appointment form submission
document.getElementById('appointmentForm').onsubmit = function(event) {
    event.preventDefault();
    alert('Appointment request received. We will confirm your appointment shortly!');
    bootstrap.Modal.getInstance(document.getElementById('appointmentModal')).hide();
    event.target.reset();
};

// CV Upload functionality
const cvPreview = document.getElementById('cvPreview');
const cvFile = document.getElementById('cvFile');
const uploadProgress = document.getElementById('cu-uploadProgress');
const uploadStatus = document.getElementById('uploadStatus');

// Drag and drop functionality
cvPreview.addEventListener('dragover', (e) => {
    e.preventDefault();
    cvPreview.classList.add('drag-over');
});

cvPreview.addEventListener('dragleave', () => {
    cvPreview.classList.remove('drag-over');
});

cvPreview.addEventListener('drop', (e) => {
    e.preventDefault();
    cvPreview.classList.remove('drag-over');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        cvFile.files = files;
        handleFileSelect(files[0]);
    }
});

// File input change handler
cvFile.addEventListener('change', (e) => {
    if (e.target.files.length > 0) {
        handleFileSelect(e.target.files[0]);
    }
});

// Handle file selection
function handleFileSelect(file) {
    const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    
    if (!allowedTypes.includes(file.type)) {
        showUploadStatus('Please upload a PDF, DOC, or DOCX file.', 'danger');
        return;
    }

    // Show file name in preview
    cvPreview.innerHTML = `
        <div class="p-4">
            <h5>Selected File:</h5>
            <p>${file.name}</p>
            <p>Size: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
        </div>
    `;
}

// Handle CV upload
function handleCVUpload(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    // Show progress bar
    uploadProgress.style.display = 'block';
    let progress = 0;
    
    // Simulate upload progress
    const progressInterval = setInterval(() => {
        progress += 10;
        uploadProgress.querySelector('.progress-bar').style.width = `${progress}%`;
        
        if (progress >= 100) {
            clearInterval(progressInterval);
            uploadProgress.style.display = 'none';
            showUploadStatus('CV uploaded successfully! Our team will review it shortly.', 'success');
            event.target.reset();
            cvPreview.innerHTML = '<div class="text-center p-4">Drag and drop your CV here or use the file input above</div>';
        }
    }, 200);

    return false;
}

// Show upload status message
function showUploadStatus(message, type) {
    uploadStatus.className = `alert alert-${type}`;
    uploadStatus.textContent = message;
    uploadStatus.classList.remove('d-none');
    
    setTimeout(() => {
        uploadStatus.classList.add('d-none');
    }, 5000);
}
