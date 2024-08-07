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
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_name` varchar(50) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`menu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(50) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`page_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permissions` (
  `permission_id` int(11) NOT NULL AUTO_INCREMENT,
  `permission_name` varchar(50) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`permission_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `submenu` (
  `submenu_id` int(11) NOT NULL AUTO_INCREMENT,
  `submenu_name` varchar(50) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `page_id` int(11) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`submenu_id`),
  FOREIGN KEY (`menu_id`) REFERENCES `menu`(`menu_id`) ON DELETE CASCADE,
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`page_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permission_groups` (
  `permission_group_id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`permission_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `permission_group_permissions` (
  `group_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`group_id`, `permission_id`),
  FOREIGN KEY (`group_id`) REFERENCES `permission_groups`(`permission_group_id`) ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`permission_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `user_permission_groups` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `group_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`group_id`) REFERENCES `permission_groups`(`permission_group_id`) ON DELETE CASCADE
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
-- Inserting sample pages
INSERT INTO `pages` (`page_name`) VALUES 
('manage_pages'), ('manage_menu'), ('manage_submenu'), ('manage_users'), ('manage_roles'), ('manage_permissions'), ('manage_role_permissions');

-- Inserting sample menu
INSERT INTO `menu` (`menu_name`, `page_id`) VALUES
('Master Settings', NULL);

-- Inserting sample submenu
INSERT INTO `submenu` (`submenu_name`, `menu_id`, `page_id`) VALUES
('Add Page', 1, 1), 
('Manage Menu', 1, 2), 
('Manage Submenu', 1, 3), 
('Manage Users', 1, 4), 
('Manage Roles', 1, 5), 
('Manage Permissions', 1, 6), 
('Manage Role Permissions', 1, 7);

-- Inserting sample permissions
INSERT INTO `permissions` (`permission_name`) VALUES 
('create_manage_pages'), ('read_manage_pages'), ('update_manage_pages'), ('delete_manage_pages'),
('create_manage_menu'), ('read_manage_menu'), ('update_manage_menu'), ('delete_manage_menu'),
('create_manage_submenu'), ('read_manage_submenu'), ('update_manage_submenu'), ('delete_manage_submenu'),
('create_manage_users'), ('read_manage_users'), ('update_manage_users'), ('delete_manage_users'),
('create_manage_roles'), ('read_manage_roles'), ('update_manage_roles'), ('delete_manage_roles'),
('create_manage_permissions'), ('read_manage_permissions'), ('update_manage_permissions'), ('delete_manage_permissions'),
('create_manage_role_permissions'), ('read_manage_role_permissions'), ('update_manage_role_permissions'), ('delete_manage_role_permissions');

-- Inserting sample roles
INSERT INTO `roles` (`role_name`) VALUES 
('Admin'), ('User'), ('Student'), ('Faculty');

-- Inserting sample permission groups
INSERT INTO `permission_groups` (`group_name`) VALUES 
('Admin Group'), ('User Group'), ('Student Group'), ('Faculty Group');

-- Associating permissions with permission groups
INSERT INTO `permission_group_permissions` (`group_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), -- Admin Group permissions for manage_pages
(1, 5), (1, 6), (1, 7), (1, 8), -- Admin Group permissions for manage_menu
(1, 9), (1, 10), (1, 11), (1, 12), -- Admin Group permissions for manage_submenu
(1, 13), (1, 14), (1, 15), (1, 16), -- Admin Group permissions for manage_users
(1, 17), (1, 18), (1, 19), (1, 20), -- Admin Group permissions for manage_roles
(1, 21), (1, 22), (1, 23), (1, 24), -- Admin Group permissions for manage_permissions
(1, 25), (1, 26), (1, 27), (1, 28), -- Admin Group permissions for manage_role_permissions
(2, 2), (2, 6), (2, 10), (2, 14), (2, 18), (2, 22), (2, 26), -- User Group read permissions
(3, 2), (3, 6), (3, 10), (3, 14), (3, 18), (3, 22), (3, 26), -- Student Group read permissions
(4, 2), (4, 6), (4, 10), (4, 14), (4, 18), (4, 22), (4, 26); -- Faculty Group read permissions

-- Creating default admin user
INSERT INTO `users` (`username`, `password`, `role_id`) VALUES ('admin', '" . password_hash('nihita1981', PASSWORD_DEFAULT) . "', 1);

-- Associating admin user with Admin Group
INSERT INTO `user_permission_groups` (`user_id`, `group_id`) VALUES (1, 1);
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
    'menu' => ['menu_id', 'menu_name', 'page_id'],
    'pages' => ['page_id', 'page_name'],
    'permissions' => ['permission_id', 'permission_name'],
    'roles' => ['role_id', 'role_name'],
    'role_permissions' => ['role_id', 'permission_id'],
    'submenu' => ['submenu_id', 'submenu_name', 'menu_id', 'page_id'],
    'users' => ['user_id', 'username', 'password', 'role_id'],
    'permission_groups' => ['permission_group_id', 'group_name'],
    'permission_group_permissions' => ['group_id', 'permission_id'],
    'user_permission_groups' => ['user_id', 'group_id']
];

$foreignKeys = [
    'menu' => [
        'page_id' => ['table' => 'pages', 'key' => 'page_id', 'field' => 'page_name']
    ],
    'role_permissions' => [
        'role_id' => ['table' => 'roles', 'key' => 'role_id', 'field' => 'role_name'],
        'permission_id' => ['table' => 'permissions', 'key' => 'permission_id', 'field' => 'permission_name']
    ],
    'submenu' => [
        'menu_id' => ['table' => 'menu', 'key' => 'menu_id', 'field' => 'menu_name'],
        'page_id' => ['table' => 'pages', 'key' => 'page_id', 'field' => 'page_name']
    ],
    'users' => [
        'role_id' => ['table' => 'roles', 'key' => 'role_id', 'field' => 'role_name']
    ],
    'permission_group_permissions' => [
        'group_id' => ['table' => 'permission_groups', 'key' => 'permission_group_id', 'field' => 'group_name'],
        'permission_id' => ['table' => 'permissions', 'key' => 'permission_id', 'field' => 'permission_name']
    ],
    'user_permission_groups' => [
        'user_id' => ['table' => 'users', 'key' => 'user_id', 'field' => 'username'],
        'group_id' => ['table' => 'permission_groups', 'key' => 'permission_group_id', 'field' => 'group_name']
    ]
];

foreach ($tables as $table => $columns) {
    $generator = new CRUDGenerator($table, $columns, $foreignKeys[$table] ?? []);
    $generator->generateFiles();
}

echo "CRUD files generated successfully.\n";

$conn->close();
?>
