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
    createFile("$baseDir/settings.php", "<?php\n\n// Database settings\n\nreturn [\n    'db' => [\n        'host' => 'localhost',\n        'dbname' => 'your_database_name',\n        'user' => 'your_database_user',\n        'password' => 'your_database_password',\n    ],\n];\n");
    createFile("$baseDir/includes/dbconfig.php", "<?php\n\$config = include('../settings.php');\n\$servername = \$config['db']['host'];\n\$username = \$config['db']['user'];\n\$password = \$config['db']['password'];\n\$dbname = \$config['db']['dbname'];\n\n\$conn = new mysqli(\$servername, \$username, \$password, \$dbname);\n\nif (\$conn->connect_error) {\n    die(\"Connection failed: \" . \$conn->connect_error);\n}\n\nmysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);\n\$conn->set_charset(\"utf8mb4\");\n?>");
    createFile("$baseDir/includes/header.php", "<?php\ninclude('dbconfig.php');\ninclude('session.php');\n?>\n<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Dashboard</title>\n    <link rel=\"stylesheet\" href=\"$baseDir/css/style.css\">\n    <meta http-equiv=\"X-Content-Type-Options\" content=\"nosniff\">\n    <meta http-equiv=\"X-XSS-Protection\" content=\"1; mode=block\">\n</head>\n<body>\n<nav class=\"bg-blue-500 p-4\">\n    <div class=\"container mx-auto\">\n        <?php include('menu.php'); ?>\n    </div>\n</nav>");
    createFile("$baseDir/includes/footer.php", "<?php\n\$messages = get_flash_messages();\nif (!empty(\$messages)): ?>\n    <div class=\"fixed bottom-0 left-0 right-0 p-4 z-50\">\n        <?php foreach (\$messages as \$type => \$msgs): ?>\n            <?php foreach (\$msgs as \$msg): ?>\n                <div class=\"bg-<?= \$type == 'success' ? 'green' : 'red' ?>-100 border border-<?= \$type == 'success' ? 'green' : 'red' ?>-400 text-<?= \$type == 'success' ? 'green' : 'red' ?>-700 px-4 py-3 rounded relative mb-2 flex items-start sm:items-center transition-all duration-300 ease-in-out transform hover:scale-105\" role=\"alert\">\n                    <strong class=\"font-bold\"><?= \$type == 'success' ? 'Success!' : 'Error!' ?></strong>\n                    <span class=\"block sm:inline ml-2\"><?= htmlspecialchars(\$msg) ?></span>\n                    <button class=\"absolute top-0 bottom-0 right-0 px-4 py-3\" onclick=\"this.parentElement.style.display='none';\">\n                        <svg class=\"fill-current h-6 w-6 text-<?= \$type == 'success' ? 'green' : 'red' ?>-500\" role=\"button\" xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 20 20\">\n                            <title>Close</title>\n                            <path d=\"M14.348 14.849a1 1 0 01-1.414 0L10 11.414l-2.934 2.935a1 1 0 01-1.415-1.414l2.935-2.934-2.935-2.934a1 1 0 011.414-1.415L10 8.586l2.934-2.935a1 1 0 011.415 1.415L11.414 10l2.934 2.934a1 1 0 010 1.415z\"/>\n                        </svg>\n                    </button>\n                </div>\n            <?php endforeach; ?>\n        <?php endforeach; ?>\n    </div>\n<?php endif; ?>\n</div> <!-- Closing the flex-1 div from header.php -->\n<script src=\"../js/cdn.js\"></script>\n<script src=\"https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.2.2/cdn.js\"></script>\n</body>\n</html>");
    createFile("$baseDir/includes/menu.php", "<nav class=\"bg-blue-500 p-4 shadow-md\">\n    <div class=\"container mx-auto flex justify-between items-center\">\n        <!-- Dashboard Link -->\n        <div class=\"flex items-center space-x-4\">\n            <a href=\"$baseDir/pages/dashboard.php\" class=\"text-white font-semibold hover:text-blue-200 transition-colors duration-200\">Menu</a>\n        </div>\n        <!-- User Actions -->\n        <div class=\"flex items-center space-x-4 relative\">\n            <a href=\"$baseDir/pages/dashboard.php?page=change_password\" class=\"text-white hover:text-blue-200 transition-colors duration-200\">\n                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-6 w-6\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                    <path d=\"M12 1.75a8.25 8.25 0 0 0-8.25 8.25v3.5H4.75a1 1 0 0 1 0 2H7.5a.25.25 0 0 0 .25-.25v-8.25a7.25 7.25 0 1 1 14.5 0v8.25c0 .138.112.25.25.25h2.75a1 1 0 0 1 0 2H19.5v3.5a1 1 0 0 1-1 1H14.75v-4.75a2.25 2.25 0 0 0-4.5 0v4.75H5.5a1 1 0 0 1-1-1v-3.5H4.75a1 1 0 0 1 0-2H6v-3.5a8.25 8.25 0 0 0-8.25-8.25V10H4v-.25a7.25 7.25 0 0 1 14.5 0v3.5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-3.5a7.25 7.25 0 1 1 14.5 0V14H19.5a.25.25 0 0 0 .25-.25v-8.25a8.25 8.25 0 0 0-8.25-8.25zM10.25 18v3.75a1.25 1.25 0 1 0 2.5 0V18h-2.5z\"/>\n                </svg>\n            </a>\n            <a href=\"$baseDir/pages/logout.php\" class=\"text-white hover:text-blue-200 transition-colors duration-200\">\n                <svg xmlns=\"http://www.w3.org/2000/svg\" class=\"h-6 w-6\" viewBox=\"0 0 24 24\" fill=\"none\" stroke=\"currentColor\" stroke-width=\"2\" stroke-linecap=\"round\" stroke-linejoin=\"round\">\n                    <path d=\"M16 17l4-4-4-4M12 12h8\"/>\n                    <path d=\"M2 12h10\"/>\n                </svg>\n            </a>\n            <div class=\"relative\" x-data=\"{ open: false }\" @mouseover=\"open = true\" @mouseleave=\"open = false\">\n                <button class=\"flex items-center space-x-2 text-white\">\n                    <img src=\"$baseDir/images/user-icon.gif\" alt=\"User Icon\" class=\"h-8 w-8 rounded-full\">\n                </button>\n            </div>\n        </div>\n    </div>\n</nav>");
    createFile("$baseDir/includes/session.php", "<?php\nsession_start();\nsession_regenerate_id(true);\n\nif (!isset(\$_SESSION['username'])) {\n    header(\"Location: $baseDir/pages/login.php\");\n    exit();\n}\n\nfunction check_permission(\$permission) {\n    global \$conn;\n    \$role_id = \$_SESSION['role_id'];\n    \$sql = \"SELECT * FROM role_permissions rp \n            JOIN permissions p ON rp.permission_id = p.id \n            WHERE rp.role_id = ? AND p.permission_name = ?\";\n    \$stmt = \$conn->prepare(\$sql);\n    \$stmt->bind_param(\"is\", \$role_id, \$permission);\n    \$stmt->execute();\n    \$result = \$stmt->get_result();\n    return \$result->num_rows > 0;\n}\n\nfunction set_flash_message(\$type, \$message) {\n    \$_SESSION['flash'][\$type][] = \$message;\n}\n\nfunction get_flash_messages() {\n    \$messages = \$_SESSION['flash'] ?? [];\n    unset(\$_SESSION['flash']);\n    return \$messages;\n}\n?>");
    createFile("$baseDir/generator/CRUDGenerator.php", "<?php\n\n// CRUD Generator\n\n?>");
    createFile("$baseDir/css/tailwind.css", "/* Tailwind CSS compiled file */\n@tailwind base;\n@tailwind components;\n@tailwind utilities;\n");
    createFile("$baseDir/README.md", "# $projectName\n\nProject description.\n");
    createFile("$baseDir/pages/dashboard.php", "<?php\ninclude('../includes/header.php');\n?>\n\n<div class=\"container mx-auto mt-8\">\n<h1 class=\"text-xl mb-6 text-gray-800 bg-gray-200 font-bold border-b border-dashed border-gray-400 px-4 py-2 rounded-lg\">Dashboard</h1>\n  \n    \n    <?php\n    if (isset(\$_GET['page'])) {\n        \$page = \$_GET['page'];\n        switch (\$page) {\n            case 'change_password':\n                include('change_password.php');\n                break;\n           \n            default:\n                echo \"<p>Page not found.</p>\";\n        }\n    } else {\n        include('../includes/usermenu.php'); \n    }\n    ?>\n</div>\n\n<?php include('../includes/footer.php'); ?>\n</body>\n</html>");
    createFile("$baseDir/pages/login.php", "<?php\ninclude('../includes/dbconfig.php');\nsession_start();\n\nif (\$_SERVER['REQUEST_METHOD'] == 'POST') {\n    \$username = \$_POST['username'];\n    \$password = \$_POST['password'];\n\n    \$sql = \"SELECT * FROM users WHERE username = ?\";\n    \$stmt = \$conn->prepare(\$sql);\n    \$stmt->bind_param(\"s\", \$username);\n    \$stmt->execute();\n    \$result = \$stmt->get_result();\n    if (\$result->num_rows > 0) {\n        \$user = \$result->fetch_assoc();\n        if (password_verify(\$password, \$user['password'])) {\n            \$_SESSION['username'] = \$user['username'];\n            \$_SESSION['role_id'] = \$user['role_id'];\n            header(\"Location: $baseDir/pages/dashboard.php\");\n            exit();\n        } else {\n            \$error = \"Invalid password.\";\n        }\n    } else {\n        \$error = \"No user found with that username.\";\n    }\n}\n?>\n\n<!DOCTYPE html>\n<html lang=\"en\">\n<head>\n    <meta charset=\"UTF-8\">\n    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n    <title>Login</title>\n    <link rel=\"stylesheet\" href=\"$baseDir/css/style.css\">\n    <link rel=\"stylesheet\" href=\"https://kit-pro.fontawesome.com/releases/v5.15.1/css/pro.min.css\" />\n    <!-- <meta http-equiv=\"Content-Security-Policy\" content=\"default-src 'self' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline';\"> -->\n    <!-- <meta http-equiv=\"X-Content-Type-Options\" content=\"nosniff\">\n    <meta http-equiv=\"X-Frame-Options\" content=\"DENY\">\n    <meta http-equiv=\"X-XSS-Protection\" content=\"1; mode=block\"> -->\n</head>\n<body class=\"min-h-screen flex flex-col items-center justify-center bg-gray-300\">\n    <div class=\"flex flex-col bg-white shadow-md px-4 sm:px-6 md:px-8 lg:px-10 py-8 rounded-md w-full max-w-md\">\n        <div class=\"font-medium self-center text-xl sm:text-2xl uppercase text-gray-800\">Login To Your Account</div>\n        <!-- <div class=\"relative mt-10 h-px bg-gray-300\">\n            <div class=\"absolute left-0 top-0 flex justify-center w-full -mt-2\">\n                <span class=\"bg-white px-4 text-xs text-gray-500 uppercase\">Or Login With Email</span>\n            </div>\n        </div> -->\n        <div class=\"mt-10\">\n            <form method=\"POST\">\n                <?php if (isset(\$error)): ?>\n                    <p class=\"text-red-500\"><?php echo htmlspecialchars(\$error); ?></p>\n                <?php endif; ?>\n                <div class=\"flex flex-col mb-6\">\n                    <label for=\"username\" class=\"mb-1 text-xs sm:text-sm tracking-wide text-gray-600\">Username:</label>\n                    <div class=\"relative\">\n                        <div class=\"inline-flex items-center justify-center absolute left-0 top-0 h-full w-10 text-gray-400\">\n                            <svg class=\"h-6 w-6\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">\n                                <path d=\"M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207\" />\n                            </svg>\n                        </div>\n                        <input id=\"username\" type=\"text\" name=\"username\" class=\"text-sm sm:text-base placeholder-gray-500 pl-10 pr-4 rounded-lg border border-gray-400 w-full py-2 focus:outline-none focus:border-blue-400\" placeholder=\"Username\" required />\n                    </div>\n                </div>\n                <div class=\"flex flex-col mb-6\">\n                    <label for=\"password\" class=\"mb-1 text-xs sm:text-sm tracking-wide text-gray-600\">Password:</label>\n                    <div class=\"relative\">\n                        <div class=\"inline-flex items-center justify-center absolute left-0 top-0 h-full w-10 text-gray-400\">\n                            <span>\n                                <svg class=\"h-6 w-6\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">\n                                    <path d=\"M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\" />\n                                </svg>\n                            </span>\n                        </div>\n                        <input id=\"password\" type=\"password\" name=\"password\" class=\"text-sm sm:text-base placeholder-gray-500 pl-10 pr-4 rounded-lg border border-gray-400 w-full py-2 focus:outline-none focus:border-blue-400\" placeholder=\"Password\" required />\n                    </div>\n                </div>\n                <!-- <div class=\"flex items-center mb-6 -mt-4\">\n                    <div class=\"flex ml-auto\">\n                        <a href=\"#\" class=\"inline-flex text-xs sm:text-sm text-blue-500 hover:text-blue-700\">Forgot Your Password?</a>\n                    </div>\n                </div> -->\n                <div class=\"flex w-full\">\n                    <button type=\"submit\" class=\"flex items-center justify-center focus:outline-none text-white text-sm sm:text-base bg-blue-600 hover:bg-blue-700 rounded py-2 w-full transition duration-150 ease-in\">\n                        <span class=\"mr-2 uppercase\">Login</span>\n                        <span>\n                            <svg class=\"h-6 w-6\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">\n                                <path d=\"M13 9l3 3m0 0l-3 3m3-3H8m13 0a9 9 0 11-18 0 9 9 0 0118 0z\" />\n                            </svg>\n                        </span>\n                    </button>\n                </div>\n            </form>\n        </div>\n        <!-- <div class=\"flex justify-center items-center mt-6\">\n            <a href=\"#\" target=\"_blank\" class=\"inline-flex items-center font-bold text-blue-500 hover:text-blue-700 text-xs text-center\">\n                <span>\n                    <svg class=\"h-6 w-6\" fill=\"none\" stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">\n                        <path d=\"M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z\" />\n                    </svg>\n                </span>\n                <span class=\"ml-2\">You don't have an account?</span>\n            </a>\n        </div> -->\n    </div>\n</body>\n</html>");
    createFile("$baseDir/pages/logout.php", "<?php\nsession_start();\nsession_unset();\nsession_destroy();\nheader(\"Location: login.php\");\nexit();\n?>");
    createFile("$baseDir/index.php", "<?php\nheader(\"Location: pages/login.php\");\nexit();\n?>");

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
