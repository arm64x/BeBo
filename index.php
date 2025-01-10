<?php
    $config = require __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeBo - Temporary File Hosting</title>
    <meta name=description content="BeBo is a simple-to-use free temporary file hosting service. It lets you share your photos, documents, music, videos and more with others online with no ads, account sign-up or tracking.">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .drop-zone {
            border: 2px dashed #ccc;
            border-radius: 4px;
            text-align: center;
            background: rgba(202, 230, 190, .75);
            margin: 0 auto 20px;
            width: 350px;
            padding: 28px 48px;
            cursor: pointer;
            font-size: 24px;
            color: #468847;
        }
        .drop-zone.dragover {
            border-color: #0d6efd;
            background: #e9ecef;
        }
        .file-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }
        .file-list {
            margin-top: 20px;
        }
        .file-item {
            margin-bottom: 10px;
        }
        .file-progress {
            height: 20px;
        }
        .copy-btn {
            cursor: pointer;
        }
        .file-status > * {
            display: none;
        }
        .file-status.uploading .upload-progress,
        .file-status.complete .file-url,
        .file-status.error .error-message {
            display: block;
        }
        .error-message {
            color: #dc3545;
        }
        .leti {
            color: inherit;
            cursor: default;
            font-family: inherit;
            font-size: 72px;
            font-weight: 700;
            line-height: 1;
            margin: 10px 0;
            text-rendering: optimizelegibility;
        }
        p.alert.alert-info.text-center {
            max-width: 840px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4 leti">BeBo</h1>
        <p class="text-center lead">Max upload size is <?php echo ($config['max_file_size'] / (1024 * 1024)); ?>&nbsp;MB &amp; files expire after <?php echo $config['default_expiry']; ?> hours.</p>
        <div class="drop-zone" id="dropZone">
            <p class="mb-0">Select or drop file(s)</p>
            <input type="file" id="fileInput" class="d-none" multiple data-max-size="<?php echo $config['max_file_size']; ?>">
        </div>

        <div class="file-list" id="fileList">
            <!-- Uploaded files will appear here -->
        </div>
    </div>

    <p class="alert alert-info text-center">
        Allow: <?php echo implode(", ", $config['allowed_extensions']); ?>
    </p>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/upload.js"></script>
</body>
</html>