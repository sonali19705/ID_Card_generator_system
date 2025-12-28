<?php
include 'db_connect.php';
session_start();
$user_email = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New ID Request - Live Preview</title>
<link rel="stylesheet" href="styles.css">
<style>
/* --- ID Card Preview Styles --- */
.preview-container {
    display: flex;
    gap: 20px;
    justify-content: center;
    margin-top: 30px;
    flex-wrap: wrap;
}

.id-card {
    width: 300px;
    height: 180px;
    border-radius: 10px;
    overflow: hidden;
    position: relative;
    background: #ffffff;
    color: #000;
    box-shadow: 0 4px 15px rgba(0,0,0,0.3);
    padding: 10px;
    font-family: Arial, sans-serif;
}

.id-front {
    background: linear-gradient(135deg, #0044cc, #3399ff);
    color: #fff;
}

.id-header {
    text-align: center;
    font-weight: bold;
    font-size: 18px;
    margin-bottom: 8px;
}

.id-photo {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #fff;
    margin: 0 auto 8px;
    overflow: hidden;
}

.id-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.id-details {
    font-size: 14px;
    text-align: center;
}

.id-back {
    background: #f5f5f5;
    padding: 15px;
    font-size: 12px;
    color: #333;
}

.barcode {
    width: 100%;
    height: 40px;
    background: repeating-linear-gradient(
        90deg,
        black 0px,
        black 2px,
        white 2px,
        white 4px
    );
    margin-top: 20px;
}
/* Keep your previous preview styles here */
.preview-container { display:flex; gap:20px; flex-wrap:wrap; justify-content:center; margin-top:30px;}
.id-card { width:300px; height:180px; border-radius:10px; overflow:hidden; position:relative; background:#fff; color:#000; box-shadow:0 4px 15px rgba(0,0,0,0.3); padding:10px; font-family:Arial, sans-serif; }
.id-front { background: linear-gradient(135deg,#0044cc,#3399ff); color:#fff; }
.id-header { text-align:center; font-weight:bold; font-size:18px; margin-bottom:8px; }
.id-photo { width:80px; height:80px; border-radius:50%; background:#fff; margin:0 auto 8px; overflow:hidden; }
.id-photo img { width:100%; height:100%; object-fit:cover; }
.id-details { font-size:14px; text-align:center; }
.id-back { background:#f5f5f5; padding:15px; font-size:12px; color:#333; }
.barcode { width:100%; height:40px; background:repeating-linear-gradient(90deg,black 0px,black 2px,white 2px,white 4px); margin-top:20px; }
</style>
</head>
<body>

<h2 style="text-align:center;">New ID Request</h2>

<div class="request-preview-container" style="display:flex; gap:40px; justify-content:center; flex-wrap:wrap;">

    <!-- Form Section -->
    <form id="newIdForm" style="max-width:400px;">
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" id="studentName" placeholder="Enter full name" required>
        </div>
        <div class="form-group">
            <label>Designation</label>
            <select id="designation">
                <option value="Student">Student</option>
                <option value="Staff">Staff</option>
                <option value="Teacher">Teacher</option>
            </select>
        </div>
        <div class="form-group">
            <label>Enrollment / ID No</label>
            <input type="text" id="enrollment" placeholder="Enter enrollment number" required>
        </div>
        <div class="form-group">
            <label>DOB</label>
            <input type="date" id="dob">
        </div>
        <div class="form-group">
            <label>Blood Group</label>
            <input type="text" id="bloodGroup" placeholder="A+, O-, etc.">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" id="email" value="<?= $user_email ?>">
        </div>
        <div class="form-group">
            <label>Mobile</label>
            <input type="tel" id="mobile">
        </div>
        <div class="form-group">
            <label>Upload Photo</label>
            <input type="file" id="photo" accept="image/*">
        </div>
    </form>

    <!-- Live Preview Section -->
    <div class="preview-container" id="idPreview">
        <!-- Front Side -->
        <div class="id-card id-front">
            <div class="id-header">Organization Name</div>
            <div class="id-photo">
                <img id="previewPhoto" src="default-photo.jpg" alt="Profile Photo">
            </div>
            <div class="id-details">
                <div id="previewName">Full Name</div>
                <div id="previewDesignation">Designation</div>
                <div id="previewEnrollment">Enrollment No</div>
            </div>
        </div>
        <!-- Back Side -->
        <div class="id-card id-back">
            <p><strong>DOB:</strong> <span id="previewDOB"></span></p>
            <p><strong>Blood Group:</strong> <span id="previewBlood"></span></p>
            <p><strong>Email:</strong> <span id="previewEmail"></span></p>
            <p><strong>Mobile:</strong> <span id="previewMobile"></span></p>
            <div class="barcode"></div>
            <p style="margin-top:8px; font-size:10px;">This card is property of the organization. Return if found.</p>
        </div>
    </div>

</div>

<script>
// Live update function
const studentName = document.getElementById('studentName');
const designation = document.getElementById('designation');
const enrollment = document.getElementById('enrollment');
const dob = document.getElementById('dob');
const bloodGroup = document.getElementById('bloodGroup');
const email = document.getElementById('email');
const mobile = document.getElementById('mobile');
const photo = document.getElementById('photo');

studentName.addEventListener('input', () => document.getElementById('previewName').innerText = studentName.value || 'Full Name');
designation.addEventListener('change', () => document.getElementById('previewDesignation').innerText = designation.value);
enrollment.addEventListener('input', () => document.getElementById('previewEnrollment').innerText = enrollment.value || 'Enrollment No');
dob.addEventListener('input', () => document.getElementById('previewDOB').innerText = dob.value);
bloodGroup.addEventListener('input', () => document.getElementById('previewBlood').innerText = bloodGroup.value);
email.addEventListener('input', () => document.getElementById('previewEmail').innerText = email.value);
mobile.addEventListener('input', () => document.getElementById('previewMobile').innerText = mobile.value);

// Live photo preview
photo.addEventListener('change', function() {
    const file = this.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            document.getElementById('previewPhoto').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>