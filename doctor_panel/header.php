<div style="
    background-color: #007BFF;
    color: white;
    padding: 15px 20px;
    font-size: 1.4rem;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 0 0 8px 8px;
">
    <div>
        Doctor Panel
    </div>
    <div style="font-size: 1rem;">
        Logged in as: <?= htmlspecialchars($_SESSION['doctor_data']['name']) ?>
        |
        <a href="../logout.php" style="color: #fff; text-decoration: underline;">Logout</a>
    </div>
</div>
