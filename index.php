<?php
session_start();
require './inc/db.php'; // assumes $conn or $mysqli is defined here

$token = bin2hex(random_bytes(32));

if (empty($_SESSION['username']) || empty($_SESSION['compcode'])) {
    header("Location: ./login/?isloged=false&mode=tns&redirect=true&token={$token}");
    exit();
} else {
    // Fetch company details
    $compcode = $_SESSION['compcode'];

    $stmt = $conn->prepare("SELECT id, comp_code, comp_name, comp_address1, comp_address2, comp_address3 FROM company_master WHERE comp_code = ?");
    $stmt->bind_param("s", $compcode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['company_id']          = $row['id'];
        $_SESSION['company_comp_code']   = $row['comp_code'];
        $_SESSION['company_comp_name']   = $row['comp_name'];
        $_SESSION['company_address1']    = $row['comp_address1'];
        $_SESSION['company_address2']    = $row['comp_address2'];
        $_SESSION['company_address3']    = $row['comp_address3'];
    } else {
        // Optional: Handle if company not found
        $_SESSION['company_fetch_error'] = 'Company details not found';
    }

    $stmt->close();

    header("Location: ./dashboard/?isloged=true&mode=tns&redirect=true&token={$token}");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zynex WMS</title>

    <link rel="stylesheet" href="./inc/global.css">
</head>
<body>
    
</body>
</html>