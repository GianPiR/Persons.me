<?php
// Auto-seeder para manter dados sempre atualizados
require_once __DIR__ . '/env-loader.php';

// Database configuration from .env
$dbPath = env('DB_DATABASE', 'database/database.sqlite');

function getDbConnection() {
    global $dbPath;
    try {
        $fullPath = __DIR__ . '/../' . $dbPath;
        $pdo = new PDO('sqlite:' . $fullPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        return null;
    }
}

function initializeTables() {
    $pdo = getDbConnection();
    if (!$pdo) return false;
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    // Create people table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS people (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            cpf TEXT UNIQUE NOT NULL,
            type TEXT CHECK(type IN ('individual', 'legal_entity')) NOT NULL,
            phone TEXT,
            email TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
    
    return true;
}

function seedUsers() {
    $pdo = getDbConnection();
    if (!$pdo) return false;
    
    // Verificar se já existem usuários
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() > 0) {
        return true; // Já tem dados
    }
    
    $users = [
        ['Administrador', 'admin@sistema.com', password_hash('123456', PASSWORD_DEFAULT)],
        ['Usuário Teste', 'teste@exemplo.com', password_hash('123456', PASSWORD_DEFAULT)],
        ['Maria Silva', 'maria@exemplo.com', password_hash('123456', PASSWORD_DEFAULT)],
        ['João Santos', 'joao@exemplo.com', password_hash('123456', PASSWORD_DEFAULT)],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    foreach ($users as $user) {
        $stmt->execute($user);
    }
    
    return true;
}

function seedPeople() {
    $pdo = getDbConnection();
    if (!$pdo) return false;
    
    // Verificar se já existem pessoas
    $stmt = $pdo->query("SELECT COUNT(*) FROM people");
    if ($stmt->fetchColumn() > 0) {
        return true; // Já tem dados
    }
    
    $people = [
        ['João Silva Santos', '123.456.789-00', 'individual', '(11) 99999-9999', 'joao.silva@email.com'],
        ['Maria Oliveira Costa', '987.654.321-00', 'individual', '(11) 88888-8888', 'maria.oliveira@email.com'],
        ['Ana Carolina Pereira', '456.789.123-45', 'individual', '(21) 77777-7777', 'ana.pereira@email.com'],
        ['Pedro Henrique Lima', '789.123.456-78', 'individual', '(41) 66666-6666', 'pedro.lima@email.com'],
        ['Carlos Eduardo Santos', '321.654.987-11', 'individual', '(31) 55555-5555', 'carlos.santos@email.com'],
        ['Empresa XYZ Ltda', '12.345.678/0001-90', 'legal_entity', '(11) 3333-3333', 'contato@empresaxyz.com'],
        ['Tech Solutions S.A.', '98.765.432/0001-10', 'legal_entity', '(11) 4444-4444', 'info@techsolutions.com'],
        ['Comércio ABC EIRELI', '11.222.333/0001-44', 'legal_entity', '(31) 5555-5555', 'comercio@abc.com'],
        ['Consultoria Beta ME', '22.333.444/0001-55', 'legal_entity', '(51) 2222-2222', 'beta@consultoria.com'],
        ['Inovação Digital LTDA', '33.444.555/0001-66', 'legal_entity', '(61) 1111-1111', 'contato@inovacaodigital.com'],
    ];
    
    $stmt = $pdo->prepare("INSERT INTO people (name, cpf, type, phone, email) VALUES (?, ?, ?, ?, ?)");
    foreach ($people as $person) {
        $stmt->execute($person);
    }
    
    return true;
}

// Auto-initialize database
function autoSeed() {
    if (!initializeTables()) {
        return false;
    }
    
    seedUsers();
    seedPeople();
    
    return true;
}

// Execute auto-seeding
autoSeed();
?>
