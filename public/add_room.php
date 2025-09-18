<?php
require 'config.php';
$user = current_user($pdo);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hotel_id = (int)$_POST['hotel_id'];
    $type = $_POST['type'];
    $price = (float)$_POST['price'];
    $availability = (int)$_POST['availability'];

    $stmt = $pdo->prepare('INSERT INTO rooms (hotel_id, type, price, availability) VALUES (?, ?, ?, ?)');
    if ($stmt->execute([$hotel_id, $type, $price, $availability])) {
        echo "<p class='success'>Room added successfully!</p>";
    } else {
        echo "<p class='error'>Failed to add room.</p>";
    }
}

// Fetch hotels for the dropdown
$hotels = $pdo->query('SELECT id, name FROM hotels')->fetchAll();
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Add Room</title>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap">
<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --accent-color: #4cc9f0;
    --success-color: #4ad66d;
    --error-color: #f72585;
    --text-color: #2b2d42;
    --card-bg: #fff;
}

body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    color: var(--text-color);
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 50px 0;
}

.container {
    background: var(--card-bg);
    padding: 2rem 2.5rem;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    width: 400px;
    animation: fadeSlideUp 0.6s ease forwards;
}

h2 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    text-align: center;
}

form {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

label {
    font-weight: 600;
    margin-bottom: 0.3rem;
}

input, select {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    transition: border 0.3s;
}

input:focus, select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(67,97,238,0.2);
}

button {
    padding: 0.9rem;
    background: var(--primary-color);
    border: none;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
}

button:hover {
    background: var(--secondary-color);
    transform: translateY(-2px);
}

.success {color: var(--success-color); font-weight: 600; margin-bottom: 1rem;}
.error {color: var(--error-color); font-weight: 600; margin-bottom: 1rem;}

@keyframes fadeSlideUp {
    from {opacity: 0; transform: translateY(20px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>
</head>
<body>

<div class="container">
    <h2>Add New Room</h2>
    <form method="post">
        <label>Hotel</label>
        <select name="hotel_id" required>
            <?php foreach ($hotels as $h): ?>
                <option value="<?= $h['id'] ?>"><?= htmlspecialchars($h['name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Room Type</label>
        <input type="text" name="type" required>

        <label>Price</label>
        <input type="number" step="0.01" name="price" required>

        <label>Availability</label>
        <input type="number" name="availability" required>

        <button type="submit">Add Room</button>
    </form>
</div>

</body>
</html>
