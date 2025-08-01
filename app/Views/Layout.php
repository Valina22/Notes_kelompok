<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'Catatan Harian' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background: #f8f9fa;
            padding-top: 2rem;
        }
        .container {
            max-width: 700px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4 text-center"><?= $title ?? 'Catatan Harian' ?></h1>
        <?= $this->renderSection('content') ?>
    </div>
</body>
</html>
