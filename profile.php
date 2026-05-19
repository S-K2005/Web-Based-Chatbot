<?php
session_start();
include('db_connect.php');

if(!isset($_SESSION['id'])){
    echo "Not logged in!";
    exit();
}

$student_name  = $_SESSION['Fullname'] ?? '';
$student_email = $_SESSION['Emaill'] ?? '';
$student_phone = $_SESSION['Phone'] ?? '';
$student_pass  = $_SESSION['Pass'] ?? '';
$student_psid  = $_SESSION['psid'] ?? '';
?>

<style>
/* Profile Container */
#profile-form {
    max-width: 500px;
    margin: 30px auto;
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    font-family: 'Poppins', sans-serif;
    transition: 0.3s;
}
#profile-form:hover { transform: translateY(-5px); }
#profile-form h2 { text-align: center; margin-bottom: 25px; color: #333; font-size: 26px; }
#profile-form label { display: block; margin-bottom: 6px; font-weight: 500; color: #555; }
#profile-form input {
    width: 100%;
    padding: 12px 15px;
    margin-bottom: 20px;
    border: 1.5px solid #ddd;
    border-radius: 10px;
    transition: 0.3s;
    font-size: 15px;
}
#profile-form input:focus {
    border-color: #4caf50;
    box-shadow: 0 0 8px rgba(76,175,80,0.3);
    outline: none;
}
#profile-form button {
    width: 100%;
    padding: 12px;
    background: #4caf50;
    color: #fff;
    border: none;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: 0.3s;
}
#profile-form button:hover { background: #45a049; transform: scale(1.02); }
#profile-msg { text-align: center; margin-top: 15px; font-weight: 500; font-size: 15px; }
</style>

<h2>Profile</h2>
<form id="profile-form">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($student_name); ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($student_email); ?>" required>

    <label>Phone:</label>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($student_phone); ?>" required>

    <button type="submit">Update Profile</button>
</form>

<div id="profile-msg"></div>

<script>
// Submit profile via AJAX
document.getElementById('profile-form').onsubmit = function(e){
    e.preventDefault();
    let formData = new FormData(this);

    fetch('update_profile.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById('profile-msg').innerHTML = data;
    });
};
</script>
