<?php

class CRUDGenerator {
    private $tableName;
    private $primaryKey;
    private $columns;
    private $foreignKeys;
    private $uniqueKeys;

    public function __construct($tableName, $columns, $foreignKeys = [], $uniqueKeys = []) {
        $this->tableName = $tableName;
        $this->primaryKey = $columns[0];  // Assuming the first column is always the primary key
        $this->columns = $columns;
        $this->foreignKeys = $foreignKeys;
        $this->uniqueKeys = $uniqueKeys;
    }

    public function generateFiles() {
        $this->generateManagePHP();
        $this->generateManageJS();
        $this->generateActionsPHP();
    }

    private function generateManagePHP() {
        $content = "<?php\n";
        $content .= "// Check permission to view manage {$this->tableName}\n";
        $content .= "if (!check_permission('read_manage_{$this->tableName}')) {\n";
        $content .= "    set_flash_message('danger', 'You do not have permission to view this page.');\n";
        $content .= "    header('Location: dashboard.php');\n";
        $content .= "    exit();\n";
        $content .= "}\n";
        $content .= "?>\n\n";
        $content .= "<div class='container mx-auto mt-8'>\n";
        $content .= "    <h1 class='text-3xl mb-6'>Manage " . ucfirst($this->tableName) . "</h1>\n\n";
        $content .= "    <div id='{$this->tableName}-form' class='hidden bg-white rounded-lg shadow-md p-6 mb-8'>\n";
        $content .= "        <h2 id='form-title' class='text-2xl mb-4'>Add " . ucfirst($this->tableName) . "</h2>\n";
        $content .= "        <form id='{$this->tableName}-form-element'>\n";
        $content .= "            <input type='hidden' id='{$this->primaryKey}' name='{$this->primaryKey}'>\n";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "            <div class='mb-4'>\n";
                $content .= "                <label for='{$column}' class='block text-gray-700'>" . ucfirst(str_replace('_', ' ', $column)) . "</label>\n";
                if (array_key_exists($column, $this->foreignKeys)) {
                    $content .= "                <select id='{$column}' name='{$column}' class='w-full border border-gray-300 p-2 rounded select2-dropdown' required>\n";
                    $content .= "                    <option value=''>Select " . ucfirst(str_replace('_', ' ', $column)) . "</option>\n";
                    $content .= "                </select>\n";
                } else {
                    $content .= "                <input type='text' id='{$column}' name='{$column}' class='w-full border border-gray-300 p-2 rounded' required>\n";
                }
                $content .= "            </div>\n";
            }
        }
        $content .= "            <div class='flex justify-between mt-4'>\n";
        $content .= "                <button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded'>Save</button>\n";
        $content .= "                <button type='button' id='cancel' class='bg-red-500 text-white px-4 py-2 rounded'>Cancel</button>\n";
        $content .= "            </div>\n";
        $content .= "        </form>\n";
        $content .= "    </div>\n\n";
        $content .= "    <div class='mb-4'>\n";
        $content .= "        <input type='text' id='search-box' class='w-full border border-gray-300 p-2 rounded' placeholder='Search " . ucfirst($this->tableName) . "...'>\n";
        $content .= "    </div>\n\n";
        $content .= "    <div id='{$this->tableName}-list'></div>\n\n";
        $content .= "    <?php if (check_permission('create_manage_{$this->tableName}')): ?>\n";
        $content .= "    <button id='add-{$this->tableName}' class='bg-green-500 text-white px-4 py-2 rounded mt-4'>Add " . ucfirst($this->tableName) . "</button>\n";
        $content .= "    <?php endif; ?>\n";
        $content .= "</div>\n\n";
        $content .= "<?php include('../includes/footer.php'); ?>\n";
        $content .= "<link href='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css' rel='stylesheet' />\n";
        $content .= "<style>\n";
        $content .= "    .select2-container--default .select2-selection--single {\n";
        $content .= "        height: 2.5rem;\n";
        $content .= "        border-color: #D1D5DB;\n";
        $content .= "        border-radius: 0.375rem;\n";
        $content .= "    }\n";
        $content .= "    .select2-container--default .select2-selection--single .select2-selection__rendered {\n";
        $content .= "        padding-left: 0.75rem;\n";
        $content .= "        line-height: 2.5rem;\n";
        $content .= "    }\n";
        $content .= "    .select2-container--default .select2-selection--single .select2-selection__arrow {\n";
        $content .= "        height: 2.5rem;\n";
        $content .= "    }\n";
        $content .= "</style>\n";
        $content .= "<script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>\n";
        $content .= "<script src='https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js'></script>\n";
        $content .= "<script src='../js/manage_{$this->tableName}.js'></script>\n";
        $content .= "</body>\n</html>\n";

        file_put_contents("../pages/manage_{$this->tableName}.php", $content);
    }

    
    private function generateManageJS() {
        $tableNameCamelCase = ucfirst($this->tableName);
        $content = "$(document).ready(function() {\n";
        $content .= "    function fetch{$tableNameCamelCase}(search = '') {\n";
        $content .= "        $.ajax({\n";
        $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
        $content .= "            type: 'GET',\n";
        $content .= "            data: { action: 'fetch', search: search },\n";
        $content .= "            success: function(response) {\n";
        $content .= "                const data = JSON.parse(response);\n";
        $content .= "                if (data.success) {\n";
        $content .= "                    let table = `<table class='w-full bg-white rounded shadow-md'>`;\n";
        $content .= "                    table += `<thead><tr>`;\n";
        foreach ($this->columns as $column) {
            $content .= "                    table += `<th class='border px-4 py-2'>" . ucfirst(str_replace('_', ' ', $column)) . "</th>`;\n";
        }
        $content .= "                    table += `<th class='border px-4 py-2'>Actions</th>`;\n";
        $content .= "                    table += `</tr></thead>`;\n";
        $content .= "                    table += `<tbody>`;\n";
        $content .= "                    data.data.forEach(function(item) {\n";
        $content .= "                        table += `<tr>`;\n";
        foreach ($this->columns as $column) {
            $content .= "                        table += `<td class='border px-4 py-2'>\${item.{$column}}</td>`;\n";
        }
        $content .= "                        table += `<td class='border px-4 py-2'>`;\n";
        $content .= "                        if (data.permissions.update) {\n";
        $content .= "                            table += `<button class='bg-blue-500 text-white px-2 py-1 rounded edit-{$this->tableName}' data-id='\${item.{$this->primaryKey}}'>Edit</button>`;\n";
        $content .= "                        }\n";
        $content .= "                        if (data.permissions.delete) {\n";
        $content .= "                            table += `<button class='bg-red-500 text-white px-2 py-1 rounded delete-{$this->tableName}' data-id='\${item.{$this->primaryKey}}'>Delete</button>`;\n";
        $content .= "                        }\n";
        $content .= "                        table += `</td>`;\n";
        $content .= "                        table += `</tr>`;\n";
        $content .= "                    });\n";
        $content .= "                    table += `</tbody>`;\n";
        $content .= "                    table += `</table>`;\n";
        $content .= "                    $('#{$this->tableName}-list').html(table);\n";
        $content .= "                } else {\n";
        $content .= "                    alert('Error fetching {$this->tableName}.');\n";
        $content .= "                }\n";
        $content .= "            },\n";
        $content .= "            error: function() {\n";
        $content .= "                alert('Error fetching {$this->tableName}.');\n";
        $content .= "            }\n";
        $content .= "        });\n";
        $content .= "    }\n\n";
        $content .= "    $('#add-{$this->tableName}').click(function() {\n";
        $content .= "        $('#{$this->tableName}-form-element')[0].reset();\n";
        $content .= "        $('#form-title').text('Add " . ucfirst($this->tableName) . "');\n";
        $content .= "        $('#{$this->primaryKey}').val('');\n";
        $content .= "        $('#{$this->tableName}-form').removeClass('hidden');\n";
        $content .= "    });\n\n";
        $content .= "    $('#cancel').click(function() {\n";
        $content .= "        $('#{$this->tableName}-form').addClass('hidden');\n";
        $content .= "    });\n\n";
        $content .= "    $('#{$this->tableName}-form-element').submit(function(e) {\n";
        $content .= "        e.preventDefault();\n";
        $content .= "        const formData = new FormData(this);\n";
        $content .= "        formData.append('action', 'save');\n";
        $content .= "        $.ajax({\n";
        $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
        $content .= "            type: 'POST',\n";
        $content .= "            data: formData,\n";
        $content .= "            processData: false,\n";
        $content .= "            contentType: false,\n";
        $content .= "            success: function(response) {\n";
        $content .= "                const data = JSON.parse(response);\n";
        $content .= "                if (data.success) {\n";
        $content .= "                    alert('" . ucfirst($this->tableName) . " saved successfully.');\n";
        $content .= "                    $('#{$this->tableName}-form').addClass('hidden');\n";
        $content .= "                    fetch{$tableNameCamelCase}();\n";
        $content .= "                } else {\n";
        $content .= "                    alert('Error saving {$this->tableName}: ' + data.message);\n";
        $content .= "                }\n";
        $content .= "            },\n";
        $content .= "            error: function() {\n";
        $content .= "                alert('Error saving {$this->tableName}.');\n";
        $content .= "            }\n";
        $content .= "        });\n";
        $content .= "    });\n\n";
        $content .= "    $(document).on('click', '.edit-{$this->tableName}', function() {\n";
        $content .= "        const id = $(this).data('id');\n";
        $content .= "        $.ajax({\n";
        $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
        $content .= "            type: 'GET',\n";
        $content .= "            data: { action: 'get', id: id },\n";
        $content .= "            success: function(response) {\n";
        $content .= "                const data = JSON.parse(response);\n";
        $content .= "                if (data.success) {\n";
        $content .= "                    const item = data.data;\n";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                if (array_key_exists($column, $this->foreignKeys)) {
                    $foreignField = str_replace('_id', '_name', $column);
                    $content .= "                    $('#{$column}').empty().append(new Option(item.{$foreignField}, item.{$column}, false, true)).trigger('change');\n";
                } else {
                    $content .= "                    $('#{$column}').val(item.{$column});\n";
                }
            }
        }
        $content .= "                    $('#{$this->primaryKey}').val(item.{$this->primaryKey});\n"; // Ensure primary key is set
        $content .= "                    $('#form-title').text('Edit " . ucfirst($this->tableName) . "');\n";
        $content .= "                    $('#{$this->tableName}-form').removeClass('hidden');\n";
        $content .= "                } else {\n";
        $content .= "                    alert('Error fetching {$this->tableName} details: ' + data.message);\n";
        $content .= "                }\n";
        $content .= "            },\n";
        $content .= "            error: function() {\n";
        $content .= "                alert('Error fetching {$this->tableName} details.');\n";
        $content .= "            }\n";
        $content .= "        });\n";
        $content .= "    });\n\n";
        $content .= "    $(document).on('click', '.delete-{$this->tableName}', function() {\n";
        $content .= "        if (!confirm('Are you sure you want to delete this " . ucfirst($this->tableName) . "?')) return;\n";
        $content .= "        const id = $(this).data('id');\n";
        $content .= "        $.ajax({\n";
        $content .= "            url: '../actions/actions_{$this->tableName}.php',\n";
        $content .= "            type: 'POST',\n";
        $content .= "            data: { action: 'delete', id: id },\n";
        $content .= "            success: function(response) {\n";
        $content .= "                const data = JSON.parse(response);\n";
        $content .= "                if (data.success) {\n";
        $content .= "                    alert('" . ucfirst($this->tableName) . " deleted successfully.');\n";
        $content .= "                    fetch{$tableNameCamelCase}();\n";
        $content .= "                } else {\n";
        $content .= "                    alert('Error deleting " . ucfirst($this->tableName) . ": ' + data.message);\n";
        $content .= "                }\n";
        $content .= "            },\n";
        $content .= "            error: function() {\n";
        $content .= "                alert('Error deleting {$this->tableName}.');\n";
        $content .= "            }\n";
        $content .= "        });\n";
        $content .= "    });\n\n";
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $content .= "    function fetch" . ucfirst($foreignTable['table']) . "() {\n";
            $content .= "        $('#{$column}').select2({\n";
            $content .= "            ajax: {\n";
            $content .= "                url: '../actions/actions_{$this->tableName}.php',\n";
            $content .= "                dataType: 'json',\n";
            $content .= "                delay: 250,\n";
            $content .= "                data: function(params) {\n";
            $content .= "                    return {\n";
            $content .= "                        action: 'search_{$foreignTable['table']}',\n";
            $content .= "                        search: params.term\n";
            $content .= "                    };\n";
            $content .= "                },\n";
            $content .= "                processResults: function(data) {\n";
            $content .= "                    return {\n";
            $content .= "                        results: data.items\n";
            $content .= "                    };\n";
            $content .= "                },\n";
            $content .= "                cache: true\n";
            $content .= "            },\n";
            $content .= "            minimumInputLength: 2,\n";
            $content .= "            placeholder: 'Select " . ucfirst($foreignTable['table']) . "',\n";
            $content .= "            allowClear: true,\n";
            $content .= "            theme: 'default'\n";
            $content .= "        }).on('select2:open', function() {\n";
            $content .= "            $('.select2-dropdown').css('z-index', 9999);\n";
            $content .= "        });\n";
            $content .= "    }\n\n";
            $content .= "    fetch" . ucfirst($foreignTable['table']) . "();\n\n";
        }
        $content .= "    $('#search-box').on('input', function() {\n";
        $content .= "        const search = $(this).val();\n";
        $content .= "        fetch{$tableNameCamelCase}(search);\n";
        $content .= "    });\n\n";
        $content .= "    fetch{$tableNameCamelCase}();\n";
        $content .= "});\n";
    
        file_put_contents("../js/manage_{$this->tableName}.js", $content);
    }
    
    private function generateActionsPHP() {
        $content = "<?php\n";
        $content .= "include('../includes/session.php');\n";
        $content .= "include('../includes/dbconfig.php');\n\n";
        $content .= "\$action = \$_REQUEST['action'];\n\n";
        $content .= "switch (\$action) {\n";
        $content .= "    case 'fetch':\n";
        $content .= "        if (!check_permission('read_manage_{$this->tableName}')) {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "            exit();\n";
        $content .= "        }\n\n";
        $content .= "        \$search = \$_GET['search'] ?? '';\n";
        $content .= "        \$sql = \"SELECT * FROM {$this->tableName} WHERE \";\n";
        $firstColumn = true;
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                if ($firstColumn) {
                    $firstColumn = false;
                } else {
                    $content .= "        \$sql .= \" OR \";\n";
                }
                $content .= "        \$sql .= \"{$column} LIKE '%\$search%'\";\n";
            }
        }
        $content .= "        \$sql .= \" ORDER BY {$this->primaryKey} DESC\";\n";
        $content .= "        \$result = \$conn->query(\$sql);\n";
        $content .= "        \$data = [];\n";
        $content .= "        while (\$row = \$result->fetch_assoc()) {\n";
        $content .= "            \$data[] = \$row;\n";
        $content .= "        }\n";
        $content .= "        \$permissions = [\n";
        $content .= "            'update' => check_permission('update_manage_{$this->tableName}'),\n";
        $content .= "            'delete' => check_permission('delete_manage_{$this->tableName}')\n";
        $content .= "        ];\n";
        $content .= "        echo json_encode(['success' => true, 'data' => \$data, 'permissions' => \$permissions]);\n";
        $content .= "        break;\n\n";
        $content .= "    case 'save':\n";
        $content .= "        if (!check_permission('create_manage_{$this->tableName}') && !check_permission('update_manage_{$this->tableName}')) {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "            exit();\n";
        $content .= "        }\n\n";
        $content .= "        \$id = \$_POST['{$this->primaryKey}'] ?? '';\n";
    
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "        \${$column} = \$_POST['{$column}'];\n";
            }
        }
    
        $content .= "\n        if (\$id) {\n";
        $content .= "            if (!check_permission('update_manage_{$this->tableName}')) {\n";
        $content .= "                echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "                exit();\n";
        $content .= "            }\n\n";
        $content .= "            // Update existing record\n";
        $content .= "            \$sql = \"UPDATE {$this->tableName} SET \";\n";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "            \$sql .= \"{$column} = ?, \";\n";
            }
        }
        $content = rtrim($content, ", \n") . "\n";
        $content .= "            \$sql .= \"updated_at = NOW() WHERE {$this->primaryKey} = ?\";\n";
        $content .= "            \$stmt = \$conn->prepare(\$sql);\n";
        $types = str_repeat('s', count($this->columns) - 1) . 'i';
        $content .= "            \$stmt->bind_param('{$types}', ";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "\${$column}, ";
            }
        }
        $content .= "\$id);\n";
        $content .= "        } else {\n";
        $content .= "            if (!check_permission('create_manage_{$this->tableName}')) {\n";
        $content .= "                echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "                exit();\n";
        $content .= "            }\n\n";
        if (!empty($this->uniqueKeys)) {
            // Check for duplicate record based on unique keys
            $content .= "            \$duplicateCheckSql = \"SELECT * FROM {$this->tableName} WHERE ";
            $uniqueCheckConditions = [];
            foreach ($this->uniqueKeys as $uniqueKey) {
                $uniqueCheckConditions[] = "{$uniqueKey} = ?";
            }
            $content .= implode(' OR ', $uniqueCheckConditions) . "\";\n";
            $content .= "            \$duplicateStmt = \$conn->prepare(\$duplicateCheckSql);\n";
            $uniqueKeyTypes = str_repeat('s', count($this->uniqueKeys));
            $uniqueKeyParams = implode(', ', array_map(fn($col) => "\${$col}", $this->uniqueKeys));
            $content .= "            \$duplicateStmt->bind_param('{$uniqueKeyTypes}', {$uniqueKeyParams});\n";
            $content .= "            \$duplicateStmt->execute();\n";
            $content .= "            \$duplicateResult = \$duplicateStmt->get_result();\n";
            $content .= "            if (\$duplicateResult->num_rows > 0) {\n";
            $content .= "                echo json_encode(['success' => false, 'message' => 'Record already exists']);\n";
            $content .= "                exit();\n";
            $content .= "            }\n\n";
        }
        $content .= "            // Insert new record\n";
        $content .= "            \$sql = \"INSERT INTO {$this->tableName} (";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "{$column}, ";
            }
        }
        $content = rtrim($content, ", ") . ", created_at, updated_at) VALUES (";
        $content .= str_repeat('?, ', count($this->columns) - 1) . "NOW(), NOW())\";\n";
        $content .= "            \$stmt = \$conn->prepare(\$sql);\n";
        $types = str_repeat('s', count($this->columns) - 1);
        $content .= "            \$stmt->bind_param('{$types}', ";
        foreach ($this->columns as $column) {
            if ($column !== $this->primaryKey) {
                $content .= "\${$column}, ";
            }
        }
        $content = rtrim($content, ", ") . ");\n";
        $content .= "        }\n\n";
        $content .= "        if (\$stmt->execute()) {\n";
        $content .= "            echo json_encode(['success' => true]);\n";
        $content .= "        } else {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => \$conn->error]);\n";
        $content .= "        }\n";
        $content .= "        break;\n\n";
        $content .= "    case 'get':\n";
        $content .= "        if (!check_permission('read_manage_{$this->tableName}')) {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "            exit();\n";
        $content .= "        }\n\n";
        $content .= "        \$id = \$_GET['id'];\n";
        
        // Build the join SQL query dynamically
        $selectColumns = [];
        $joinClauses = [];
        foreach ($this->columns as $column) {
            $selectColumns[] = "{$this->tableName}.{$column}";
            if (isset($this->foreignKeys[$column])) {
                $foreignTable = $this->foreignKeys[$column]['table'];
                $foreignField = $this->foreignKeys[$column]['field'];
                $selectColumns[] = "{$foreignTable}.{$foreignField} AS {$foreignField}";
                $joinClauses[] = "JOIN {$foreignTable} ON {$this->tableName}.{$column} = {$foreignTable}.{$this->foreignKeys[$column]['key']}";
            }
        }
        $selectColumns = implode(', ', $selectColumns);
        $joinClauses = implode(' ', $joinClauses);
        
        $content .= "        \$sql = \"SELECT {$selectColumns} FROM {$this->tableName} {$joinClauses} WHERE {$this->primaryKey} = ?\";\n";
        
        $content .= "        \$stmt = \$conn->prepare(\$sql);\n";
        $content .= "        \$stmt->bind_param('i', \$id);\n";
        $content .= "        \$stmt->execute();\n";
        $content .= "        \$result = \$stmt->get_result();\n";
        $content .= "        \$data = \$result->fetch_assoc();\n";
        $content .= "        echo json_encode(['success' => true, 'data' => \$data]);\n";
        $content .= "        break;\n\n";
        $content .= "    case 'delete':\n";
        $content .= "        if (!check_permission('delete_manage_{$this->tableName}')) {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
        $content .= "            exit();\n";
        $content .= "        }\n\n";
        $content .= "        \$id = \$_POST['id'];\n";
        $content .= "        \$sql = \"DELETE FROM {$this->tableName} WHERE {$this->primaryKey} = ?\";\n";
        $content .= "        \$stmt = \$conn->prepare(\$sql);\n";
        $content .= "        \$stmt->bind_param('i', \$id);\n";
        $content .= "        if (\$stmt->execute()) {\n";
        $content .= "            echo json_encode(['success' => true]);\n";
        $content .= "        } else {\n";
        $content .= "            echo json_encode(['success' => false, 'message' => \$conn->error]);\n";
        $content .= "        }\n";
        $content .= "        break;\n\n";
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $content .= "    case 'search_{$foreignTable['table']}':\n";
            $content .= "        if (!check_permission('read_manage_{$this->tableName}')) {\n";
            $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
            $content .= "            exit();\n";
            $content .= "        }\n\n";
            $content .= "        \$search = \$_GET['search'];\n";
            $content .= "        \$sql = \"SELECT {$foreignTable['key']} AS id, {$foreignTable['field']} AS text FROM {$foreignTable['table']} WHERE {$foreignTable['field']} LIKE ?\";\n";
            $content .= "        \$stmt = \$conn->prepare(\$sql);\n";
            $content .= "        \$search = \"%{\$search}%\";\n";
            $content .= "        \$stmt->bind_param('s', \$search);\n";
            $content .= "        \$stmt->execute();\n";
            $content .= "        \$result = \$stmt->get_result();\n";
            $content .= "        \$items = [];\n";
            $content .= "        while (\$row = \$result->fetch_assoc()) {\n";
            $content .= "            \$items[] = \$row;\n";
            $content .= "        }\n";
            $content .= "        echo json_encode(['items' => \$items]);\n";
            $content .= "        break;\n";
        }
    
        // Add cases based on $foreignKeys
        foreach ($this->foreignKeys as $column => $foreignTable) {
            $tableName = $foreignTable['table'];
            $primaryKey = $foreignTable['key'];
            $field = $foreignTable['field'];
            
            $content .= "    case 'get_{$tableName}':\n";
            $content .= "        if (!check_permission('read_manage_{$this->tableName}')) {\n";
            $content .= "            echo json_encode(['success' => false, 'message' => 'Unauthorized']);\n";
            $content .= "            exit();\n";
            $content .= "        }\n\n";
            $content .= "        \${$primaryKey} = \$_GET['id'];\n";
            $content .= "        \$sql = \"SELECT * FROM {$tableName} WHERE {$primaryKey} = ?\";\n";
            $content .= "        \$stmt = \$conn->prepare(\$sql);\n";
            $content .= "        \$stmt->bind_param('i', \${$primaryKey});\n";
            $content .= "        \$stmt->execute();\n";
            $content .= "        \$result = \$stmt->get_result();\n";
            $content .= "        \$data = \$result->fetch_assoc();\n";
            $content .= "        echo json_encode(['success' => true, 'data' => \$data]);\n";
            $content .= "        break;\n";
        }
    
        $content .= "    default:\n";
        $content .= "        echo json_encode(['success' => false, 'message' => 'Invalid action']);\n";
        $content .= "        break;\n";
        $content .= "}\n";
        $content .= "?>\n";
    
        file_put_contents("../actions/actions_{$this->tableName}.php", $content);
    }
    
}

?>
