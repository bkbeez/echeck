<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../app/autoload.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    exit(json_encode(['ok'=>false,'error'=>'Invalid JSON']));
}

$data = [
    'student_id'   => trim($input['student_id'] ?? ''),
    'prefix'       => trim($input['prefix'] ?? ''),
    'first_name'   => trim($input['first_name'] ?? ''),
    'last_name'    => trim($input['last_name'] ?? ''),
    'event_id'     => trim($input['event_id'] ?? ''),
    'organization' => trim($input['organization'] ?? ''),
    'email'        => trim($input['email'] ?? '')
];

// validate
foreach ($data as $key => $value) {
    if ($value === '') {
        exit(json_encode(['ok'=>false,'error'=>"missing $key"]));
    }
}

if (!is_numeric($data['event_id'])) {
    exit(json_encode(['ok'=>false,'error'=>'invalid event_id']));
}

if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    exit(json_encode(['ok'=>false,'error'=>'invalid email']));
}

try {
    // ป้องกันเช็คอินซ้ำ
    $exists = DB::one(
        "SELECT id FROM checkin 
            WHERE student_id = :student_id 
            AND prefix = :prefix
            AND firstname = :firstname
            AND lastname = :lastname
            AND event_id = :event_id
            AND organization = :organization
            AND email = :email
            LIMIT 1",
        [
            'student_id' => $data['student_id'],
            'prefix' => $data['prefix'],
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'event_id'   => $data['event_id'],
            'organization' => $data['organization'],
            'email' => $data['email']
        ]
    );

    if ($exists) {
        exit(json_encode(['ok'=>false,'error'=>'already checked in']));
    }

    // บันทึก
    DB::create(
        "INSERT INTO checkin
        (student_id, prefix, first_name, last_name, event_id, organization, email, checkin_at)
        VALUES
        (:student_id, :prefix, :first_name, :last_name, :event_id, :organization, :email, NOW())",
        $data
    );

    echo json_encode([
        'ok' => true,
        'student_id' => $data['student_id'],
        'name' => $data['prefix'].' '.$data['first_name'].' '.$data['last_name']
    ]);

} catch (Exception $e) {
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()]);
}
