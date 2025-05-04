<?php
// Include any necessary PHP files 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Booking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="cp.css">
</head>
<body>
    <header>
        <div class="logo"><img src="logo.png" alt="NSBM Green University Town"></div>
        <nav>
            <div class="menu-icon">&#9776;</div> <!-- Hamburger Icon -->
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">Timetable</a></li>
                <li><a href="#">Announcements</a></li>
                <li><a href="#">Appointments</a></li>
            </ul>
        </nav>
        <button class="logout">Log Out</button>
    </header>
    <main>
        <div class="MakeanAppointment-section">
            <div>
                <h2 class="mb-0">Make an Appointment</h2>
            </div>
        </div>
        <div class="appointment-container">
            <div class="card blue">
                <h3>Meet Your Lecturer</h3>
                <button class="btn green" onclick="window.location.href='appointment-form.php';">Book</button>
            </div>
            <div class="card green">
                <h3>Lecture Schedule</h3>
                <button class="btn blue" onclick="window.location.href='lecture-schedule.php';">Check</button>
            </div>
            <div class="card green">
                <h3>Meet Your Doctor</h3>
                <button class="btn blue" onclick="window.location.href='dc-appoint-form.php';">Check</button>
            </div>
            <div class="card blue">
                <h3>Doctor Schedule</h3>
                <button class="btn green" onclick="window.location.href='doctor-schedule.php';">Check</button>
            </div>
            <!--  View Appointments -->
            <div class="card blue">
                <h3>View Lecturer Appointments</h3>
                <button class="btn green" onclick="window.location.href='view-appointments.php';">View</button>
            </div>
            <div class="card green">
                <h3>View Doctor Appointments</h3>
                <button class="btn blue" onclick="window.location.href='view-doctor-appointments.php';">View</button>
            </div>
        </div>
    </main>
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <h3>N-Link</h3>
                <p>Communication Platform for NSBM.</p>
            </div>
            <div class="footer-section">
                <h3>Featured List</h3>
                <ul>
                    <li><a href="#">Student Dashboard</a></li>
                    <li><a href="#">Academic Dashboard</a></li>
                    <li><a href="#">Appointments</a></li>
                    <li><a href="#">Timetable</a></li>
                    <li><a href="#">Medical Center</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Mahenwaththa, Pitipana, Homagama, Sri Lanka</p>
                <p>+94 11 544 5000</p>
                <p>+94 71 244 5000</p>
                <p>inquiries@nsbm.ac.lk</p>
            </div>
            <div class="footer-section social-icons">
                <a href="#"><img src="facebook.png" alt=""></a>
                <a href="#"><img src="instagram.png" alt=""></a>
                <a href="#"><img src="whatsapp.png" alt=""></a>
            </div>
        </div>
        <p style="text-align: center; margin-top: 10px;">&copy; NSBM 2025. Designed and Developed by Group-75 NSBM</p>
    </footer>
</body>
<script src="cp.js"></script>
</html>
