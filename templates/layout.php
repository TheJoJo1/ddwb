<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?= csrf_meta() ?>
    
    <title><?= e($title ?? 'DingeDieWirBesitzen - DDWB') ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    
    <!-- Theme -->
    <script>
        // Initialize theme from localStorage
        (function() {
            const theme = localStorage.getItem('ddwb-theme') || 
                         (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    
    <!-- Mobile viewport for scanner -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
</head>
<body class="<?= $bodyClass ?? '' ?>">
    <!-- Skip to main content for accessibility -->
    <a href="#main-content" class="skip-link">Zum Hauptinhalt springen</a>
    
    <!-- Header -->
    <?= $this->renderPartial('templates/header') ?>
    
    <!-- Navigation -->
    <?= $this->renderPartial('templates/navigation') ?>
    
    <!-- Sidebar -->
    <?= $this->renderPartial('templates/sidebar') ?>
    
    <!-- Main Content -->
    <main id="main-content" class="main-content">
        <!-- Flash Messages -->
        <?= $this->renderPartial('templates/flash') ?>
        
        <!-- Page Content -->
        <?= $viewContent ?? '' ?>
    </main>
    
    <!-- Footer -->
    <?= $this->renderPartial('templates/footer') ?>
    
    <!-- JavaScript -->
    <script src="<?= asset('js/app.js') ?>"></script>
    
    <!-- Scanner Library (loaded locally) -->
    <script src="<?= asset('js/vendor/zxing.js') ?>"></script>
    
    <!-- Additional JavaScript -->
    <?= $scripts ?? '' ?>
</body>
</html>
