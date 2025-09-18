<?php
$host = '127.0.0.1';
$db   = 'hotel_reservation';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];
$pdo = new PDO($dsn, $user, $pass, $options);

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    $imageName = $_FILES['image']['name'];
    $imageTmp = $_FILES['image']['tmp_name'];
    $uploadDir = "uploads/";

    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $targetFile = $uploadDir . basename($imageName);

    if (move_uploaded_file($imageTmp, $targetFile)) {
        $stmt = $pdo->prepare("INSERT INTO hotels (name, location, description, image) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $location, $description, $imageName]);
        $message = "Hotel added successfully!";
    } else {
        $message = "Error uploading image!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Hotel</title>
<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --accent-color: #4cc9f0;
        --text-color: #2b2d42;
        --light-color: #f8f9fa;
        --dark-color: #212529;
    }
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins',sans-serif; }
    body { background: #f5f7fa; color: var(--text-color); display:flex; flex-direction:column; align-items:center; min-height:100vh; padding:2rem; }
    .form-container {
        background: white;
        padding: 2rem 3rem;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        max-width: 500px;
        width: 100%;
        animation: fadeSlideUp 0.8s ease forwards;
    }
    h2 { color: var(--primary-color); margin-bottom: 1.5rem; text-align:center; }
    form { display:flex; flex-direction:column; gap:1rem; }
    input[type="text"], textarea, input[type="file"] {
        padding: 0.9rem 1rem;
        font-size: 1rem;
        border-radius: 10px;
        border: 1px solid #ccc;
        outline:none;
        transition: all 0.3s ease;
        width:100%;
    }
    input[type="text"]:focus, textarea:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 8px rgba(67,97,238,0.3);
    }
    textarea { resize: vertical; min-height: 120px; }
    button {
        padding: 1rem;
        font-size: 1.1rem;
        border:none;
        border-radius: 10px;
        background: var(--primary-color);
        color:white;
        font-weight:600;
        cursor:pointer;
        transition: all 0.3s ease;
    }
    button:hover { background: var(--secondary-color); transform: translateY(-2px); }
    .message { text-align:center; font-weight:600; color:green; margin-bottom:1rem; }
    @keyframes fadeSlideUp {
        0% {opacity:0; transform:translateY(20px);}
        100% {opacity:1; transform:translateY(0);}
    }
</style>
</head>
<body>

<div class="form-container">
    <h2>Add New Hotel</h2>
    <?php if($message): ?>
        <div class="message"><?=htmlspecialchars($message)?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="Hotel Name" required>
        <input type="text" name="location" placeholder="Location" required>
        <textarea name="description" placeholder="Description" required></textarea>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Add Hotel</button>
    </form>
</div>

</body>
</html>
