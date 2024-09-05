<?php

function createDirectory($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        echo "Created directory: $path\n";
    } else {
        echo "Directory already exists: $path\n";
    }
}

function createFile($path, $content = '') {
    if (!file_exists($path)) {
        file_put_contents($path, $content);
        echo "Created file: $path\n";
    } else {
        echo "File already exists: $path\n";
    }
}

function downloadFile($url, $path) {
    $content = file_get_contents($url);
    if ($content === FALSE) {
        echo "Failed to download file: $url\n";
    } else {
        file_put_contents($path, $content);
        echo "Downloaded file: $path\n";
    }
}

function createProject($projectName) {
    $baseDir = __DIR__ . "/$projectName";

    $directories = [
        "$baseDir/actions",
        "$baseDir/css",
        "$baseDir/generator",
        "$baseDir/images",
        "$baseDir/includes",
        "$baseDir/js",
        "$baseDir/pages",
        "$baseDir/uploads"
    ];

    foreach ($directories as $directory) {
        createDirectory($directory);
    }

    // Create initial files with content
    createFile("$baseDir/settings.php", <<<EOD
<?php

// Database settings

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => '$projectName',
        'user' => 'root',
        'password' => '',
    ],
];
EOD
    );

    createFile("$baseDir/includes/dbconfig.php", <<<EOD
<?php
\$config = include('../settings.php');
\$servername = \$config['db']['host'];
\$username = \$config['db']['user'];
\$password = \$config['db']['password'];
\$dbname = \$config['db']['dbname'];

\$conn = new mysqli(\$servername, \$username, \$password, \$dbname);

if (\$conn->connect_error) {
    die("Connection failed: " . \$conn->connect_error);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
\$conn->set_charset("utf8mb4");
?>
EOD
    );

    createFile("$baseDir/includes/header.php", <<<EOD
<?php
include('dbconfig.php');
include('session.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block">
</head>
<body>
<nav class="bg-blue-500 p-4">
    <div class="container mx-auto">
        <?php include('menu.php'); ?>
    </div>
</nav>
EOD
    );

    createFile("$baseDir/includes/footer.php", <<<EOD
<?php
\$messages = get_flash_messages();
if (!empty(\$messages)): ?>
    <div class="fixed bottom-0 left-0 right-0 p-4 z-50">
        <?php foreach (\$messages as \$type => \$msgs): ?>
            <?php foreach (\$msgs as \$msg): ?>
                <div class="bg-<?= \$type == 'success' ? 'green' : 'red' ?>-100 border border-<?= \$type == 'success' ? 'green' : 'red' ?>-400 text-<?= \$type == 'success' ? 'green' : 'red' ?>-700 px-4 py-3 rounded relative mb-2 flex items-start sm:items-center transition-all duration-300 ease-in-out transform hover:scale-105" role="alert">
                    <strong class="font-bold"><?= \$type == 'success' ? 'Success!' : 'Error!' ?></strong>
                    <span class="block sm:inline ml-2"><?= htmlspecialchars(\$msg) ?></span>
                    <button class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
                        <svg class="fill-current h-6 w-6 text-<?= \$type == 'success' ? 'green' : 'red' ?>-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path d="M14.348 14.849a1 1 0 01-1.414 0L10 11.414l-2.934 2.935a1 1 0 01-1.415-1.414l2.935-2.934-2.935-2.934a1 1 0 011.414-1.415L10 8.586l2.934-2.935a1 1 0 011.415 1.415L11.414 10l2.934 2.934a1 1 0 010 1.415z"/>
                        </svg>
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<script src="../js/cdn.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.2.2/cdn.js"></script>
</body>
</html>
EOD
    );

    createFile("$baseDir/includes/menu.php", <<<EOD
<nav class="bg-blue-500 p-4 shadow-md">
    <div class="container mx-auto flex justify-between items-center">
        <!-- Dashboard Link -->
        <div class="flex items-center space-x-4">
            <a href="../pages/dashboard.php" class="text-white font-semibold hover:text-blue-200 transition-colors duration-200">Menu</a>
        </div>
        <!-- User Actions -->
        <div class="flex items-center space-x-4 relative">
            <a href="../pages/dashboard.php?page=change_password" class="text-white hover:text-blue-200 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 1.75a8.25 8.25 0 0 0-8.25 8.25v3.5H4.75a1 1 0 0 1 0 2H7.5a.25.25 0 0 0 .25-.25v-8.25a7.25 7.25 0 1 1 14.5 0v8.25c0 .138.112.25.25.25h2.75a1 1 0 0 1 0 2H19.5v3.5a1 1 0 0 1-1 1H14.75v-4.75a2.25 2.25 0 0 0-4.5 0v4.75H5.5a1 1 0 0 1-1-1v-3.5H4.75a1 1 0 0 1 0-2H6v-3.5a8.25 8.25 0 0 0-8.25-8.25V10H4v-.25a7.25 7.25 0 0 1 14.5 0v3.5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-3.5a7.25 7.25 0 1 1 14.5 0V14H19.5a.25.25 0 0 0 .25-.25v-8.25a8.25 8.25 0 0 0-8.25-8.25zM10.25 18v3.75a1.25 1.25 0 1 0 2.5 0V18h-2.5z"/>
                </svg>
            </a>
            <a href="../pages/logout.php" class="text-white hover:text-blue-200 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 17l4-4-4-4M12 12h8"/>
                    <path d="M2 12h10"/>
                </svg>
            </a>
            <div class="relative" x-data="{ open: false }" @mouseover="open = true" @mouseleave="open = false">
                <button class="flex items-center space-x-2 text-white">
                    <img src="../images/user-icon.gif" alt="User Icon" class="h-8 w-8 rounded-full">
                </button>
            </div>
        </div>
    </div>
</nav>
EOD
    );

    createFile("$baseDir/includes/session.php", <<<EOD
<?php
session_start();
session_regenerate_id(true);

if (!isset(\$_SESSION['username'])) {
    header("Location: ../pages/login.php");
    exit();
}

function check_permission(\$permission) {
    global \$conn;
    \$user_id = \$_SESSION['user_id'];
    
    \$sql = "SELECT pgp.permission_id 
            FROM user_permission_groups upg
            JOIN permission_group_permissions pgp ON upg.group_id = pgp.group_id
            JOIN permissions p ON pgp.permission_id = p.permission_id
            WHERE upg.user_id = ? AND p.permission_name = ?";
    \$stmt = \$conn->prepare(\$sql);
    \$stmt->bind_param("is", \$user_id, \$permission);
    \$stmt->execute();
    \$result = \$stmt->get_result();
    
    return \$result->num_rows > 0;
}

function set_flash_message(\$type, \$message) {
    \$_SESSION['flash'][\$type][] = \$message;
}

function get_flash_messages() {
    \$messages = \$_SESSION['flash'] ?? [];
    unset(\$_SESSION['flash']);
    return \$messages;
}
?>
EOD
    );

    createFile("$baseDir/includes/usermenu.php", <<<EOD
<?php
\$sql = "
    SELECT 
        m.menu_name AS main_menu,
        s.submenu_name AS sub_menu,
        p.page_name AS page_name
    FROM 
        menu m
    LEFT JOIN 
        submenu s ON m.menu_id = s.menu_id
    LEFT JOIN 
        pages p ON s.page_id = p.page_id
    ORDER BY 
        m.menu_name, s.submenu_name";

\$result = \$conn->query(\$sql);

if (!\$result) {
    echo "Error fetching menus: " . \$conn->error;
    exit();
}

\$menus = [];
while (\$row = \$result->fetch_assoc()) {
    \$menus[\$row['main_menu']][] = [
        'sub_menu' => \$row['sub_menu'],
        'page_name' => \$row['page_name']
    ];
}
?>

<div class="container mx-auto mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach (\$menus as \$main_menu => \$submenus): ?>
        <?php
        \$has_permission = false;
        foreach (\$submenus as \$submenu) {
            if (\$submenu['page_name'] && check_permission("read_{\$submenu['page_name']}")) {
                \$has_permission = true;
                break;
            }
        }
        ?>
        <?php if (\$has_permission): ?>
            <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200">
                <h3 class="text-2xl font-bold text-gray-800 mb-4 border-b pb-2"><?= htmlspecialchars(\$main_menu) ?></h3>
                <ul class="space-y-2">
                    <?php foreach (\$submenus as \$submenu): ?>
                        <?php if (\$submenu['page_name'] && check_permission("read_{\$submenu['page_name']}")): ?>
                            <li>
                                <a href="../pages/dashboard.php?page=<?= htmlspecialchars(\$submenu['page_name']) ?>" class="block text-blue-600 hover:text-white bg-blue-100 hover:bg-blue-600 rounded-md px-2 py-1 transition-colors duration-200">
                                    <?= htmlspecialchars(\$submenu['sub_menu']) ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
EOD
    );

    createFile("$baseDir/pages/dashboard.php", <<<EOD
<?php
include('../includes/header.php');
?>

<div class="container mx-auto mt-8">
<h1 class="text-xl mb-6 text-gray-800 bg-gray-200 font-bold border-b border-dashed border-gray-400 px-4 py-2 rounded-lg">Dashboard</h1>
  
    
    <?php
    if (isset(\$_GET['page'])) {
        \$page = \$_GET['page'];
       
     switch (\$page) {
            // case 'change_password':
            //     include('change_password.php');
            //     break;
              case 'manage_pages':
                include('manage_pages.php');
                break;
            case 'manage_users':
                include('manage_users.php');
                break;
            case 'manage_menu':
                include('manage_menu.php');
                break;
            case 'manage_roles':
                include('manage_roles.php');
                break;
            case 'manage_submenu':
                include('manage_submenu.php');
                break;
            case 'manage_permissions':
                include('manage_permissions.php');
                break;
            case 'manage_user_permission_groups':
                include('manage_user_permission_groups.php');
                break;
            case 'manage_permission_groups':
                include('manage_permission_groups.php');
                break;  
            case 'manage_permission_group_permissions':
                include('manage_permission_group_permissions.php');
                break;                            
            default:
                echo "<p>Page not found.</p>";
        }

    } else {
        include('../includes/usermenu.php'); 
    }
    ?>
</div>

<?php include('../includes/footer.php'); ?>
</body>
</html>
EOD
    );

    createFile("$baseDir/pages/login.php", <<<EOD
<?php
include('../includes/dbconfig.php');
session_start();

if (\$_SERVER['REQUEST_METHOD'] == 'POST') {
    \$username = \$_POST['username'];
    \$password = \$_POST['password'];

    \$sql = "SELECT * FROM users WHERE username = ?";
    \$stmt = \$conn->prepare(\$sql);
    \$stmt->bind_param("s", \$username);
    \$stmt->execute();
    \$result = \$stmt->get_result();
    if (\$result->num_rows > 0) {
        \$user = \$result->fetch_assoc();
        if (password_verify(\$password, \$user['password'])) {
            \$_SESSION['user_id'] = \$user['user_id'];
            \$_SESSION['username'] = \$user['username'];
            \$_SESSION['role_id'] = \$user['role_id'];
            header("Location: ../pages/dashboard.php");
            exit();
        } else {
            \$error = "Invalid password.";
        }
    } else {
        \$error = "No user found with that username.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://kit-pro.fontawesome.com/releases/v5.15.1/css/pro.min.css" />
    <!-- <meta http-equiv="Content-Security-Policy" content="default-src 'self' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline';"> -->
    <!-- <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <meta http-equiv="X-XSS-Protection" content="1; mode=block"> -->
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-gray-300">
    <div class="flex flex-col bg-white shadow-md px-4 sm:px-6 md:px-8 lg:px-10 py-8 rounded-md w-full max-w-md">
        <div class="font-medium self-center text-xl sm:text-2xl uppercase text-gray-800">Login To Your Account</div>
        <!-- <div class="relative mt-10 h-px bg-gray-300">
            <div class="absolute left-0 top-0 flex justify-center w-full -mt-2">
                <span class="bg-white px-4 text-xs text-gray-500 uppercase">Or Login With Email</span>
            </div>
        </div> -->
        <div class="mt-10">
            <form method="POST">
                <?php if (isset(\$error)): ?>
                    <p class="text-red-500"><?php echo htmlspecialchars(\$error); ?></p>
                <?php endif; ?>
                <div class="flex flex-col mb-6">
                    <label for="username" class="mb-1 text-xs sm:text-sm tracking-wide text-gray-600">Username:</label>
                    <div class="relative">
                        <div class="inline-flex items-center justify-center absolute left-0 top-0 h-full w-10 text-gray-400">
                            <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                            </svg>
                        </div>
                        <input id="username" type="text" name="username" class="text-sm sm:text-base placeholder-gray-500 pl-10 pr-4 rounded-lg border border-gray-400 w-full py-2 focus:outline-none focus:border-blue-400" placeholder="Username" required />
                    </div>
                </div>
                <div class="flex flex-col mb-6">
                    <label for="password" class="mb-1 text-xs sm:text-sm tracking-wide text-gray-600">Password:</label>
                    <div class="relative">
                        <div class="inline-flex items-center justify-center absolute left-0 top-0 h-full w-10 text-gray-400">
                            <span>
                                <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                        </div>
                        <input id="password" type="password" name="password" class="text-sm sm:text-base placeholder-gray-500 pl-10 pr-4 rounded-lg border border-gray-400 w-full py-2 focus:outline-none focus:border-blue-400" placeholder="Password" required />
                    </div>
                </div>
                <!-- <div class="flex items-center mb-6 -mt-4">
                    <div class="flex ml-auto">
                        <a href="#" class="inline-flex text-xs sm:text-sm text-blue-500 hover:text-blue-700">Forgot Your Password?</a>
                    </div>
                </div> -->
                <div class="flex w-full">
                    <button type="submit" class="flex items-center justify-center focus:outline-none text-white text-sm sm:text-base bg-blue-600 hover:bg-blue-700 rounded py-2 w-full transition duration-150 ease-in">
                        <span class="mr-2 uppercase">Login</span>
                        <span>
                            <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
        <!-- <div class="flex justify-center items-center mt-6">
            <a href="#" target="_blank" class="inline-flex items-center font-bold text-blue-500 hover:text-blue-700 text-xs text-center">
                <span>
                    <svg class="h-6 w-6" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                        <path d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </span>
                <span class="ml-2">You don't have an account?</span>
            </a>
        </div> -->
    </div>
</body>
</html>
EOD
    );

    createFile("$baseDir/pages/logout.php", <<<EOD
<?php
session_start();
session_unset();
session_destroy();
header("Location: ../pages/login.php");
exit();
?>
EOD
    );

    createFile("$baseDir/index.php", <<<EOD
<?php
header("Location: pages/login.php");
exit();
?>
EOD
    );

    // Download files from GitHub and place them in the respective directories
    downloadFile('https://raw.githubusercontent.com/ramabhadrarao/mycrudgen/main/CRUDGeneratorv11.php', "$baseDir/generator/CRUDGeneratorv11.php");
    // Download files from GitHub and place them in the respective directories
    downloadFile('https://raw.githubusercontent.com/ramabhadrarao/mycrudgen/main/create_tables.php', "$baseDir/generator/create_tables.php");
    $cssFiles = [
        'fileuploadmodel.css' => 'https://raw.githubusercontent.com/ramabhadrarao/mycrudgen/main/fileuploadmodel.css',
        'style.css' => 'https://raw.githubusercontent.com/ramabhadrarao/mycrudgen/main/style.css'
    ];
    foreach ($cssFiles as $fileName => $url) {
        downloadFile($url, "$baseDir/css/$fileName");
    }

    $jsFiles = [
        'cdn.js' => 'https://raw.githubusercontent.com/ramabhadrarao/mycrudgen/main/cdn.js',
        'image_upload_plugin.js' => 'https://raw.githubusercontent.com/ramabhadrarao/mycrudgen/main/image_upload_plugin.js'
    ];
    foreach ($jsFiles as $fileName => $url) {
        downloadFile($url, "$baseDir/js/$fileName");
    }

    downloadFile('https://raw.githubusercontent.com/ramabhadrarao/mycrudgen/main/user-icon.gif', "$baseDir/images/user-icon.gif");

    echo "Project '$projectName' created successfully.\n";
}

// Check if project name is provided
if ($argc > 1) {
    $projectName = $argv[1];
    createProject($projectName);
} else {
    echo "Usage: php create_project.php <project_name>\n";
}
?>
