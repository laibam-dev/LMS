<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Polymath Path</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .stat-card { border: none; border-radius: 15px; transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .bg-polymath { background-color: #003366; color: white; }
        .sidebar-fixed {
            background: #003366; color: #FFD700; min-height: 100vh; width: 220px; position: fixed; top: 56px; left: 0; z-index: 1030;
            display: flex; flex-direction: column; padding: 2rem 1rem 1rem 1rem;
        }
        .sidebar-fixed a {
            color: #FFD700; font-weight: 500; margin-bottom: 0.5rem; border-radius: 8px; transition: background 0.2s, color 0.2s;
            padding: 0.75rem 1rem; text-decoration: none;
        }
        .sidebar-fixed a.active, .sidebar-fixed a:hover {
            background: #FFD700; color: #003366;
        }
        .main-content {
            margin-left: 220px; padding: 2rem 2rem 2rem 2rem;
        }
        @media (max-width: 991px) {
            .sidebar-fixed { position: static; width: 100%; min-height: auto; }
            .main-content { margin-left: 0; padding: 1rem; }
        }
    </style>
</head>
<body>
