<?php
// mysql_functions.php

require_once 'config.php'; // Inclui as configurações do banco de dados

/**
 * Variável global para armazenar a conexão mysqli.
 * Usamos global para que a conexão possa ser acessada e reutilizada por outras funções
 * sem precisar ser passada como parâmetro repetidamente.
 */
$GLOBALS['conn'] = null;

/**
 * Verifica se a tabela 'entry' existe e a cria se não existir.
 *
 * @return bool True se a tabela existir ou foi criada com sucesso, false em caso de erro.
 */
function db_create_entry_table() {
    $conn = db_connect(); // Garante que a conexão está aberta
    if (!$conn) {
        error_log("Não foi possível conectar ao banco de dados para verificar/criar a tabela 'entry'.");
        return false;
    }

    $table_name = 'entry';

    // 1. Verificar se a tabela já existe
    $check_table_sql = "SHOW TABLES LIKE '$table_name'";
    $result = $conn->query($check_table_sql);

    if ($result === false) {
        error_log("Erro ao verificar a existência da tabela '$table_name': " . $conn->error);
        return false;
    }

    if ($result->num_rows == 0) {
        // A tabela não existe, então vamos criá-la
        $create_table_sql = "
            CREATE TABLE $table_name (
                id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                text BLOB,
                author VARCHAR(255),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        if ($conn->query($create_table_sql) === TRUE) {
            echo "Tabela '$table_name' criada com sucesso!<br>"; // Mensagem para o usuário
            return true;
        } else {
            error_log("Erro ao criar a tabela '$table_name': " . $conn->error);
            return false;
        }
    } else {
        // A tabela já existe
        // echo "Tabela '$table_name' já existe.<br>"; // Mensagem para o usuário (opcional)
        return true;
    }
}

/**
 * Abre uma nova conexão com o banco de dados MySQL usando mysqli.
 *
 * @return mysqli|false Objeto mysqli em caso de sucesso, ou false em caso de falha.
 */
function db_connect() {
    global $conn; // Acessa a variável global da conexão

    // Se a conexão já existe e está ativa, retorna-a
    if ($conn instanceof mysqli && $conn->ping()) {
        return $conn;
    }

    // Tenta estabelecer uma nova conexão
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Verifica se a conexão foi bem-sucedida
    if ($conn->connect_error) {
        die("Erro de Conexão com o Banco de Dados: " . $conn->connect_error);
        return false;
    }

    // Define o charset para UTF-8 para evitar problemas com acentuação
    $conn->set_charset("utf8");

    return $conn;
}

/**
 * Fecha a conexão com o banco de dados.
 *
 * @return bool True se a conexão foi fechada com sucesso, false caso contrário.
 */
function db_close() {
    global $conn;

    if ($conn instanceof mysqli) {
        return $conn->close();
    }
    return false;
}

/**
 * Executa uma consulta SELECT no banco de dados e retorna os resultados.
 *
 * @param string $sql A consulta SQL SELECT.
 * @param string $types String de tipos para os parâmetros (ex: 'isd' para int, string, double).
 * @param array $params Array de parâmetros para a consulta preparada.
 * @return array|false Um array de arrays associativos com os resultados, ou false em caso de erro.
 */
function db_select($sql, $types = '', $params = []) {
    $conn = db_connect(); // Garante que a conexão está aberta
    if (!$conn) {
        return false;
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Erro ao preparar a consulta SELECT: " . $conn->error);
        return false;
    }

    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    }

    if (!$stmt->execute()) {
        error_log("Erro ao executar a consulta SELECT: " . $stmt->error);
        $stmt->close();
        return false;
    }

    $result = $stmt->get_result();
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    $stmt->close();
    return $data;
}

/**
 * Executa uma consulta INSERT, UPDATE ou DELETE no banco de dados.
 *
 * @param string $sql A consulta SQL (INSERT, UPDATE ou DELETE).
 * @param string $types String de tipos para os parâmetros (ex: 'isd' para int, string, double).
 * @param array $params Array de parâmetros para a consulta preparada.
 * @return int|false O número de linhas afetadas pela consulta, ou false em caso de erro.
 */
function db_execute($sql, $types, $params) {
    $conn = db_connect(); // Garante que a conexão está aberta
    if (!$conn) {
        return false;
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Erro ao preparar a consulta (INSERT/UPDATE/DELETE): " . $conn->error);
        return false;
    }

    if (!empty($params) && !empty($types)) {
        $stmt->bind_param($types, ...$params);
    } else {
         error_log("db_execute: Parâmetros e tipos são obrigatórios para INSERT/UPDATE/DELETE. SQL: " . $sql);
         $stmt->close();
         return false;
    }


    if (!$stmt->execute()) {
        error_log("Erro ao executar a consulta (INSERT/UPDATE/DELETE): " . $stmt->error);
        $stmt->close();
        return false;
    }

    $affected_rows = $stmt->affected_rows;
    $stmt->close();

    return $affected_rows;
}

/**
 * Insere um novo registro no banco de dados.
 *
 * @param string $table O nome da tabela.
 * @param array $data Um array associativo com os nomes das colunas e seus valores.
 * @return int|false O ID da última inserção (last_insert_id) em caso de sucesso, ou false em caso de erro.
 */
function db_insert($table, $data) {
    $conn = db_connect();
    if (!$conn) {
        return false;
    }

    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";

    $types = '';
    $params = [];
    foreach ($data as $value) {
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $params[] = $value;
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Erro ao preparar a consulta INSERT: " . $conn->error);
        return false;
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        error_log("Erro ao executar a consulta INSERT: " . $stmt->error);
        $stmt->close();
        return false;
    }

    $last_id = $conn->insert_id;
    $stmt->close();
    return $last_id;
}

/**
 * Atualiza registros no banco de dados.
 *
 * @param string $table O nome da tabela.
 * @param array $data Um array associativo com os nomes das colunas e seus novos valores.
 * @param string $where A cláusula WHERE da consulta (ex: "id = ?").
 * @param string $where_types String de tipos para os parâmetros da cláusula WHERE.
 * @param array $where_params Array de parâmetros para a cláusula WHERE.
 * @return int|false O número de linhas afetadas pela atualização, ou false em caso de erro.
 */
function db_update($table, $data, $where, $where_types, $where_params) {
    $conn = db_connect();
    if (!$conn) {
        return false;
    }

    $set_clauses = [];
    $params = [];
    $types = '';

    foreach ($data as $column => $value) {
        $set_clauses[] = "$column = ?";
        if (is_int($value)) {
            $types .= 'i';
        } elseif (is_float($value)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
        $params[] = $value;
    }

    $sql = "UPDATE $table SET " . implode(', ', $set_clauses) . " WHERE $where";

    // Adiciona os tipos e parâmetros da cláusula WHERE aos tipos e parâmetros totais
    $types .= $where_types;
    $params = array_merge($params, $where_params);

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Erro ao preparar a consulta UPDATE: " . $conn->error);
        return false;
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        error_log("Erro ao executar a consulta UPDATE: " . $stmt->error);
        $stmt->close();
        return false;
    }

    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}

/**
 * Deleta registros do banco de dados.
 *
 * @param string $table O nome da tabela.
 * @param string $where A cláusula WHERE da consulta (ex: "id = ?").
 * @param string $types String de tipos para os parâmetros da cláusula WHERE.
 * @param array $params Array de parâmetros para a cláusula WHERE.
 * @return int|false O número de linhas afetadas pela exclusão, ou false em caso de erro.
 */
function db_delete($table, $where, $types, $params) {
    $conn = db_connect();
    if (!$conn) {
        return false;
    }

    $sql = "DELETE FROM $table WHERE $where";

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Erro ao preparar a consulta DELETE: " . $conn->error);
        return false;
    }

    $stmt->bind_param($types, ...$params);

    if (!$stmt->execute()) {
        error_log("Erro ao executar a consulta DELETE: " . $stmt->error);
        $stmt->close();
        return false;
    }

    $affected_rows = $stmt->affected_rows;
    $stmt->close();
    return $affected_rows;
}

// Opcional: Auto-fechar a conexão ao final do script (use com cautela se você tiver muitos scripts pequenos)
// register_shutdown_function('db_close');