<?php

function prompt($prompt_msg) {
    echo $prompt_msg . ": ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    return trim($line);
}

function downloadCRUDGenerator() {
    $url = "https://raw.githubusercontent.com/ramabhadrarao/mycrudgen/main/CRUDGeneratorv9.php";
    $generatorDir = __DIR__ . "/generator";
    if (!is_dir($generatorDir)) {
        mkdir($generatorDir, 0755, true);
    }
    $filePath = $generatorDir . "/CRUDGeneratorv9.php";
    file_put_contents($filePath, file_get_contents($url));
    require_once $filePath;
}

function createDirectories() {
    $directories = [
        "../pages",
        "../js",
        "../actions",
        "../css",
        "../images",
        "../includes"
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

// Database credentials
$servername = prompt("Enter the database host");
$username = prompt("Enter the database username");
$password = prompt("Enter the database password");
$dbname = prompt("Enter the database name");

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists\n";
} else {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Set charset
$conn->set_charset("utf8mb4");

// Define the SQL for creating tables
$sql = "
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(50) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(50) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(50) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `submenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `submenu_name` varchar(50) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`menu_id`) REFERENCES `menu`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permission_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permission_group_permissions` (
  `group_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`, `permission_id`),
  FOREIGN KEY (`group_id`) REFERENCES `permission_groups`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_permission_groups` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `group_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`group_id`) REFERENCES `permission_groups`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";

// Execute multi-query
if ($conn->multi_query($sql) === TRUE) {
    echo "Tables created successfully\n";
} else {
    echo "Error creating tables: " . $conn->error . "\n";
}

// Wait for multi_query to finish
while ($conn->next_result()) {
    if ($result = $conn->store_result()) {
        $result->free();
    }
}

// Insert default data
$default_data_sql = "
-- Inserting some sample permissions
INSERT INTO `permissions` (`permission_name`) VALUES 
('create_add_page'), ('read_add_page'), ('update_add_page'), ('delete_add_page'),
('create_manage_menu'), ('read_manage_menu'), ('update_manage_menu'), ('delete_manage_menu'),
('create_manage_submenu'), ('read_manage_submenu'), ('update_manage_submenu'), ('delete_manage_submenu'),
('create_manage_users'), ('read_manage_users'), ('update_manage_users'), ('delete_manage_users'),
('create_manage_roles'), ('read_manage_roles'), ('update_manage_roles'), ('delete_manage_roles'),
('create_manage_permissions'), ('read_manage_permissions'), ('update_manage_permissions'), ('delete_manage_permissions'),
('create_manage_role_permissions'), ('read_manage_role_permissions'), ('update_manage_role_permissions'), ('delete_manage_role_permissions');

-- Inserting some sample roles
INSERT INTO `roles` (`role_name`) VALUES 
('Admin'), ('User'), ('Student'), ('Faculty');

-- Inserting some sample permission groups
INSERT INTO `permission_groups` (`group_name`) VALUES 
('Admin Group'), ('User Group'), ('Student Group'), ('Faculty Group');

-- Associating permissions with permission groups
INSERT INTO `permission_group_permissions` (`group_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, NOW(), NOW()), (1, 2, NOW(), NOW()), (1, 3, NOW(), NOW()), (1, 4, NOW(), NOW()), -- Admin Group permissions for add_page
(1, 5, NOW(), NOW()), (1, 6, NOW(), NOW()), (1, 7, NOW(), NOW()), (1, 8, NOW(), NOW()), -- Admin Group permissions for manage_menu
(1, 9, NOW(), NOW()), (1, 10, NOW(), NOW()), (1, 11, NOW(), NOW()), (1, 12, NOW(), NOW()), -- Admin Group permissions for manage_submenu
(1, 13, NOW(), NOW()), (1, 14, NOW(), NOW()), (1, 15, NOW(), NOW()), (1, 16, NOW(), NOW()), -- Admin Group permissions for manage_users
(1, 17, NOW(), NOW()), (1, 18, NOW(), NOW()), (1, 19, NOW(), NOW()), (1, 20, NOW(), NOW()), -- Admin Group permissions for manage_roles
(1, 21, NOW(), NOW()), (1, 22, NOW(), NOW()), (1, 23, NOW(), NOW()), (1, 24, NOW(), NOW()), -- Admin Group permissions for manage_permissions
(1, 25, NOW(), NOW()), (1, 26, NOW(), NOW()), (1, 27, NOW(), NOW()), (1, 28, NOW(), NOW()), -- Admin Group permissions for manage_role_permissions
(2, 2, NOW(), NOW()), (2, 6, NOW(), NOW()), (2, 10, NOW(), NOW()), (2, 14, NOW(), NOW()), (2, 18, NOW(), NOW()), (2, 22, NOW(), NOW()), (2, 26, NOW(), NOW()), -- User Group read permissions
(3, 2, NOW(), NOW()), (3, 6, NOW(), NOW()), (3, 10, NOW(), NOW()), (3, 14, NOW(), NOW()), (3, 18, NOW(), NOW()), (3, 22, NOW(), NOW()), (3, 26, NOW(), NOW()), -- Student Group read permissions
(4, 2, NOW(), NOW()), (4, 6, NOW(), NOW()), (4, 10, NOW(), NOW()), (4, 14, NOW(), NOW()), (4, 18, NOW(), NOW()), (4, 22, NOW(), NOW()), (4, 26, NOW(), NOW()); -- Faculty Group read permissions

-- Creating default admin user
INSERT INTO `users` (`username`, `password`, `role_id`, `created_at`, `updated_at`) VALUES ('admin', '" . password_hash('nihita1981', PASSWORD_DEFAULT) . "', 1, NOW(), NOW());

-- Associating default admin user with Admin Group
INSERT INTO `user_permission_groups` (`user_id`, `group_id`, `created_at`, `updated_at`) VALUES (1, 1, NOW(), NOW());
";

if ($conn->multi_query($default_data_sql) === TRUE) {
    echo "Default data inserted successfully\n";
} else {
    echo "Error inserting default data: " . $conn->error . "\n";
}

// Wait for multi_query to finish
while ($conn->next_result()) {
    if ($result = $conn->store_result()) {
        $result->free();
    }
}

// Create necessary directories
createDirectories();

// Download and require CRUDGenerator
downloadCRUDGenerator();

$tables = [
    'menu' => ['id', 'menu_name', 'page_id'],
    'pages' => ['id', 'page_name'],
    'permissions' => ['id', 'permission_name'],
    'roles' => ['id', 'role_name'],
    'role_permissions' => ['role_id', 'permission_id'],
    'submenu' => ['id', 'submenu_name', 'menu_id', 'page_id'],
    'users' => ['id', 'username', 'password', 'role_id'],
    'permission_groups' => ['id', 'group_name'],
    'permission_group_permissions' => ['group_id', 'permission_id'],
    'user_permission_groups' => ['user_id', 'group_id']
];

$foreignKeys = [
    'role_permissions' => [
        'role_id' => ['table' => 'roles', 'key' => 'id', 'field' => 'role_name'],
        'permission_id' => ['table' => 'permissions', 'key' => 'id', 'field' => 'permission_name']
    ],
    'submenu' => [
        'menu_id' => ['table' => 'menu', 'key' => 'id', 'field' => 'menu_name'],
        'page_id' => ['table' => 'pages', 'key' => 'id', 'field' => 'page_name']
    ],
    'users' => [
        'role_id' => ['table' => 'roles', 'key' => 'id', 'field' => 'role_name']
    ],
    'permission_group_permissions' => [
        'group_id' => ['table' => 'permission_groups', 'key' => 'id', 'field' => 'group_name'],
        'permission_id' => ['table' => 'permissions', 'key' => 'id', 'field' => 'permission_name']
    ],
    'user_permission_groups' => [
        'user_id' => ['table' => 'users', 'key' => 'id', 'field' => 'username'],
        'group_id' => ['table' => 'permission_groups', 'key' => 'id', 'field' => 'group_name']
    ]
];

foreach ($tables as $table => $columns) {
    $generator = new CRUDGenerator($table, $columns, $foreignKeys[$table] ?? []);
    $generator->generateFiles();
}

echo "Admin user created with default username 'admin' and password 'nihita1981'.\n";
echo "CRUD files generated successfully.\n";

$conn->close();
?>
