    <?php
    $password = "RahasiaAdmin123!"; // Ganti dengan password yang Anda pilih
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    echo "Hashed Password: " . $hashed_password;
    ?>
    