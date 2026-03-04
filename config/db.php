<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$dbPath = __DIR__ . '/../data/medicare_plus.db';
$dsn = 'sqlite:' . $dbPath;

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
}

function initializeDatabase(PDO $pdo): void
{
    $pdo->exec('CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        full_name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT NOT NULL CHECK(role IN ("admin", "doctor", "patient")),
        specialization TEXT,
        experience_years INTEGER DEFAULT 0,
        qualifications TEXT,
        consultation_charge REAL DEFAULT 0,
        availability TEXT,
        location TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS services (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        category TEXT NOT NULL,
        name TEXT NOT NULL,
        description TEXT NOT NULL
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        patient_id INTEGER NOT NULL,
        doctor_id INTEGER NOT NULL,
        appointment_date TEXT NOT NULL,
        appointment_time TEXT NOT NULL,
        status TEXT DEFAULT "Pending",
        notes TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(patient_id) REFERENCES users(id),
        FOREIGN KEY(doctor_id) REFERENCES users(id)
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS medical_reports (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        patient_id INTEGER NOT NULL,
        doctor_id INTEGER NOT NULL,
        report_title TEXT NOT NULL,
        report_type TEXT NOT NULL,
        summary TEXT NOT NULL,
        file_link TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(patient_id) REFERENCES users(id),
        FOREIGN KEY(doctor_id) REFERENCES users(id)
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sender_id INTEGER NOT NULL,
        receiver_id INTEGER NOT NULL,
        subject TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(sender_id) REFERENCES users(id),
        FOREIGN KEY(receiver_id) REFERENCES users(id)
    )');

    $pdo->exec('CREATE TABLE IF NOT EXISTS feedback (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        patient_id INTEGER NOT NULL,
        doctor_id INTEGER NOT NULL,
        rating INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5),
        comment TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY(patient_id) REFERENCES users(id),
        FOREIGN KEY(doctor_id) REFERENCES users(id)
    )');

    seedDatabase($pdo);
}

function seedDatabase(PDO $pdo): void
{
    $userCount = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($userCount === 0) {
        $password = password_hash('Password@123', PASSWORD_DEFAULT);
        $users = [
            ['System Admin', 'admin@medicareplus.com', 'admin', null, 0, null, 0, null, 'Colombo'],
            ['Dr. Nethmi Perera', 'nethmi@medicareplus.com', 'doctor', 'Cardiology', 12, 'MBBS, MD Cardiology', 15000, 'Mon-Fri 9AM-3PM', 'Colombo'],
            ['Dr. Ravindu Silva', 'ravindu@medicareplus.com', 'doctor', 'Pediatrics', 9, 'MBBS, DCH', 12000, 'Mon-Sat 10AM-5PM', 'Galle'],
            ['Dr. Ishara Fernando', 'ishara@medicareplus.com', 'doctor', 'Radiology', 15, 'MBBS, FRCR', 18000, 'Tue-Sun 8AM-2PM', 'Kandy'],
            ['Ayesha Fernando', 'ayesha@gmail.com', 'patient', null, 0, null, 0, null, 'Matara']
        ];
        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password, role, specialization, experience_years, qualifications, consultation_charge, availability, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($users as $u) {
            $stmt->execute([$u[0], $u[1], $password, $u[2], $u[3], $u[4], $u[5], $u[6], $u[7], $u[8]]);
        }
    }

    $serviceCount = (int)$pdo->query('SELECT COUNT(*) FROM services')->fetchColumn();
    if ($serviceCount === 0) {
        $services = [
            ['Cardiology', 'Heart Health Clinic', 'Comprehensive diagnosis and treatment of cardiovascular conditions.'],
            ['Pediatrics', 'Child Wellness Services', 'Vaccinations, growth tracking, and pediatric consultations.'],
            ['Radiology', 'Advanced Imaging', 'MRI, CT, Ultrasound, and digital diagnostics for accurate reporting.'],
            ['Emergency Care', '24/7 Emergency Unit', 'Rapid response emergency support with critical care specialists.'],
            ['General Medicine', 'Family Medicine', 'Routine checkups and chronic disease management for all ages.']
        ];
        $stmt = $pdo->prepare('INSERT INTO services (category, name, description) VALUES (?, ?, ?)');
        foreach ($services as $service) {
            $stmt->execute($service);
        }
    }
}

initializeDatabase($pdo);
