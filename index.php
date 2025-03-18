<?php
// List of allowed IP addresses - modify this with your actual IPs
$allowedIPs = [
    '123.456.789.101',
    '234.567.890.123',
    '345.678.901.234'
    // Add more IPs as needed
];

// Get the visitor's IP address
$visitorIP = $_SERVER['REMOTE_ADDR'];

// Check if behind a proxy (common with Cloudflare, etc.)
if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    // The value can contain multiple IPs - get the first one
    $forwardedIPs = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
    $visitorIP = trim($forwardedIPs[0]);
}

// Check if the visitor's IP is allowed
$isAllowed = in_array($visitorIP, $allowedIPs);

// If not allowed, show access denied and exit
if (!$isAllowed) {
    header('HTTP/1.1 403 Forbidden');
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Access Denied</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    </head>
    <body class="bg-gray-50 font-sans">
        <div class="container mx-auto px-4 py-20 text-center">
            <h1 class="text-4xl font-bold text-red-600 mb-6">Access Denied</h1>
            <p class="text-xl mb-4">You are not authorized to view this website.</p>
            <p class="text-gray-600">Your IP: ' . htmlspecialchars($visitorIP) . '</p>
        </div>
    </body>
    </html>';
    exit;
}

// If allowed, continue with the regular content

// Function to get all directories in the current folder
function getDirectories() {
    $dirs = [];
    $items = scandir('.');
    
    foreach ($items as $item) {
        if ($item[0] !== '.' && is_dir($item)) {
            $dirs[] = $item;
        }
    }
    
    sort($dirs);
    return $dirs;
}

// Function to check if a background image exists for a folder
function getBackgroundImageForFolder($folderName) {
    $backgroundPath = $folderName . '/background.jpg';
    
    if (file_exists($backgroundPath)) {
        return $backgroundPath;
    }
    
    // Use the default background
    $defaultBackgroundPath = '.github/img/background.jpg';
    if (file_exists($defaultBackgroundPath)) {
        return $defaultBackgroundPath;
    }
    
    // If no background found, return a placeholder
    return 'data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22250%22%20height%3D%22150%22%20viewBox%3D%220%200%20250%20150%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_18cc3c8ac91%20text%20%7B%20fill%3Argba(255%2C255%2C255%2C.75)%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A13pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_18cc3c8ac91%22%3E%3Crect%20width%3D%22250%22%20height%3D%22150%22%20fill%3D%22%23777%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2285%22%20y%3D%2280%22%3ENot%20Found%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E';
}

// Function to get custom folder names from config
function getFolderNameBg($folderName) {
    $configPath = 'subjects-names.json';
    
    if (file_exists($configPath)) {
        try {
            $namesConfig = json_decode(file_get_contents($configPath), true);
            if (isset($namesConfig[$folderName])) {
                return $namesConfig[$folderName];
            }
        } catch (Exception $e) {
            // If there's an error, just return the folder name
        }
    }
    
    // If no name found in config, return the folder name itself
    return $folderName;
}

// Get all directories
$directories = getDirectories();

// Format current date
$date = date('j.n.Y');

// Generate folder links HTML
$folderLinksHtml = '';
foreach ($directories as $dir) {
    $bgImage = getBackgroundImageForFolder($dir);
    $folderNameBg = getFolderNameBg($dir);
    
    $folderLinksHtml .= '
    <a href="' . htmlspecialchars($dir) . '/" class="folder-link relative h-[150px] w-[250px] bg-cover bg-center text-white shadow-lg rounded-lg overflow-hidden" style="background-image: url(\'' . htmlspecialchars($bgImage) . '\');">
        <div class="absolute inset-0 bg-black/25 hover:bg-black/40 transition-all duration-300"></div>
        <span class="absolute bottom-2 left-0 w-full text-center text-lg ">' . htmlspecialchars($folderNameBg) . '</span>
    </a>';
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tsvetelina Kaneva's 5 min exercises</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>
    <h1 class="text-4xl text-center font-bold my-[40px]">Tsvetelina Kaneva's 5 min questions</h1>
    
    <div class="flex flex-wrap justify-center gap-4 p-4">
        <?php echo $folderLinksHtml; ?>
    </div>
    
    <footer class="text-center text-gray-500 mt-10 text-sm">
        Auto-generated on <?php echo $date; ?>
    </footer>
</body>
</html>