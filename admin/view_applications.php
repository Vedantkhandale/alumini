<?php
session_start();
include("../includes/db.php"); // Path check kar lena apne admin folder ke hisaab se

// Query to get application details with Alumni and Job info
$query = "SELECT 
            ja.id, 
            u.full_name AS alumni_name, 
            u.email AS alumni_email,
            j.title AS job_role, 
            j.company, 
            ja.apply_time 
          FROM job_applications ja
          JOIN users u ON ja.alumni_id = u.id
          JOIN jobs j ON ja.job_id = j.id
          ORDER BY ja.apply_time DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin | Track Applications</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; padding: 40px; }
        .container { max-width: 1100px; margin: 0 auto; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        h2 { color: #0f172a; margin-bottom: 25px; font-weight: 800; border-left: 5px solid #e11d48; padding-left: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; background: #f1f5f9; padding: 15px; color: #64748b; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .user-info b { color: #0f172a; display: block; }
        .user-info span { color: #64748b; font-size: 12px; }
        .job-tag { background: #fff1f2; color: #e11d48; padding: 4px 10px; border-radius: 6px; font-weight: 700; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <h2>Job Application Tracker</h2>
    <table>
        <thead>
            <tr>
                <th>Alumni Details</th>
                <th>Applied For</th>
                <th>Company</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td class="user-info">
                        <b><?php echo htmlspecialchars($row['alumni_name']); ?></b>
                        <span><?php echo htmlspecialchars($row['alumni_email']); ?></span>
                    </td>
                    <td><span class="job-tag"><?php echo htmlspecialchars($row['job_role']); ?></span></td>
                    <td><b><?php echo htmlspecialchars($row['company']); ?></b></td>
                    <td style="color: #64748b;"><?php echo date('d M Y, h:i A', strtotime($row['apply_time'])); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4" style="text-align:center; padding: 50px; color: #94a3b8;">Abhi tak kisi ne apply nahi kiya hai.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>