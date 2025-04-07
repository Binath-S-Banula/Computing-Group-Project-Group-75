// JavaScript for NCare Medical Center

document.addEventListener('DOMContentLoaded', function() {
    // More Details Button
    document.getElementById('moreDetailsBtn').addEventListener('click', function() {
        alert('Notice details will be shown here. This functionality can be expanded as needed.');
        // You can replace this with code to show a modal or redirect to another page
        // Example with modal:
        // $('#noticeModal').modal('show');
        
        // Example with redirection:
        // window.location.href = "notice-details.html";
    });
    
    // Check Schedule Button
    document.getElementById('checkScheduleBtn').addEventListener('click', function() {
        // Navigate to doctor schedule section or page
        window.location.href = "#doctorSchedule";
        alert('Doctor\'s schedule will be displayed here. You can modify this to redirect to a specific page or display a modal.');
        
        // Example to load schedule via AJAX:
        /*
        fetch('api/doctor-schedule.json')
            .then(response => response.json())
            .then(data => {
                // Process and display schedule data
                console.log(data);
                displaySchedule(data);
            })
            .catch(error => {
                console.error('Error fetching schedule:', error);
            });
        */
    });
    
    // Book Appointment Button
    document.getElementById('bookAppointmentBtn').addEventListener('click', function() {
        // Navigate to appointment booking section or page
        window.location.href = "#bookAppointment";
        alert('Appointment booking form will be displayed here. You can modify this to redirect to a specific page or display a modal.');
        
        // Example to show a booking form modal:
        /*
        $('#appointmentModal').modal('show');
        */
        
        // Example to redirect to a booking page:
        /*
        window.location.href = "book-appointment.html";
        */
    });
    
    // Example function to display doctor schedule (for use with AJAX example above)
    function displaySchedule(scheduleData) {
        // Code to render schedule data to the page
        const scheduleContainer = document.getElementById('scheduleContainer');
        
        if (!scheduleContainer) return;
        
        let scheduleHTML = '<div class="schedule-wrapper">';
        
        scheduleData.forEach(doctor => {
            scheduleHTML += `
                <div class="doctor-schedule">
                    <h3>${doctor.name}</h3>
                    <p><strong>Specialty:</strong> ${doctor.specialty}</p>
                    <div class="schedule-times">
                        ${doctor.schedule.map(slot => `
                            <div class="time-slot">
                                <div class="day">${slot.day}</div>
                                <div class="time">${slot.startTime} - ${slot.endTime}</div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        });
        
        scheduleHTML += '</div>';
        scheduleContainer.innerHTML = scheduleHTML;
    }
    
    // Example function for form validation (for appointment booking)
    function validateAppointmentForm() {
        const name = document.getElementById('patientName').value;
        const email = document.getElementById('patientEmail').value;
        const date = document.getElementById('appointmentDate').value;
        
        let isValid = true;
        let errorMessage = '';
        
        if (!name.trim()) {
            errorMessage += 'Name is required.\n';
            isValid = false;
        }
        
        if (!email.trim()) {
            errorMessage += 'Email is required.\n';
            isValid = false;
        } else if (!/^\S+@\S+\.\S+$/.test(email)) {
            errorMessage += 'Email format is invalid.\n';
            isValid = false;
        }
        
        if (!date) {
            errorMessage += 'Appointment date is required.\n';
            isValid = false;
        }
        
        if (!isValid) {
            alert('Please correct the following errors:\n' + errorMessage);
        }
        
        return isValid;
    }
    
    // You can add the form submission handler when you implement the form
    /*
    const appointmentForm = document.getElementById('appointmentForm');
    if (appointmentForm) {
        appointmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (validateAppointmentForm()) {
                // Submit the form via AJAX or other method
                submitAppointmentForm();
            }
        });
    }
    
    function submitAppointmentForm() {
        // Get form data
        const formData = new FormData(document.getElementById('appointmentForm'));
        
        // Send data to server
        fetch('api/book-appointment', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Appointment booked successfully!');
                // Reset form or redirect
                document.getElementById('appointmentForm').reset();
            } else {
                alert('Error booking appointment: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while booking your appointment.');
        });
    }
    */
});