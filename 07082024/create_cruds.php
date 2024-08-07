<?php
require_once('CRUDGeneratorv10.php');
$tables = [
    'menu' => ['menu_id', 'menu_name'],
    'pages' => ['page_id', 'page_name'],
    'submenu' => ['submenu_id', 'submenu_name', 'menu_id', 'page_id'],
    'users' => ['user_id', 'username', 'password', 'role_id'],
    'permissions' => ['permission_id', 'permission_name'],
    'roles' => ['role_id', 'role_name'],
    'permission_groups' => ['permission_group_id', 'group_name'],
    'permission_group_permissions' => ['permission_group_permissions_id', 'group_id', 'permission_id'],
    'user_permission_groups' => ['user_permission_groups_id', 'user_id', 'group_id']
];

$foreignKeys = [
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

$uniqueKeys = [
    'users' => ['username'],
    'permission_group_permissions' => ['group_id', 'permission_id'],
    'user_permission_groups' => ['user_id', 'group_id']
];

// Generate CRUD files for each table
foreach ($tables as $table => $columns) {
    $foreignKeysForTable = $foreignKeys[$table] ?? [];
    $uniqueKeysForTable = $uniqueKeys[$table] ?? [];
    $generator = new CRUDGenerator($table, $columns, $foreignKeysForTable, $uniqueKeysForTable);
    $generator->generateFiles();
}
?>