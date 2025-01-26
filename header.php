<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Project</title>

    <!-- Google Translate Script -->
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement({
                pageLanguage: 'en', // Set your default language
                includedLanguages: 'hi,bn,gu,kn,ml,mr,ne,or,pa,ta,te,ur,as,si', // Indian language codes
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <!-- Add other meta tags, stylesheets, or JS files here if necessary -->
</head>
<body>
    <!-- Your header content goes here, like navigation menus -->
    
    <?php if(basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
        <!-- Google Translate Widget is visible only on index.php -->
        <div id="google_translate_element"></div>
    <?php endif; ?>

    <!-- Your page content goes here -->
</body>
</html>
