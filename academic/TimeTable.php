<?php
session_start();
require_once 'config.php';
require_once 'header_admin.php';

class TimeTable {
    private $entries = [];

    public function __construct($entries) {
        $this->entries = $entries;
    }

    public function generateHTML() {
        $html = '<!DOCTYPE html>
                <html lang="en">
                <head>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <title>Class Timetable</title>
                    <style>
                        table {
                            width: 100%;
                            border-collapse: collapse;
                        }
                        th, td {
                            padding: 8px;
                            text-align: left;
                            border-bottom: 1px solid #ddd;
                        }
                        th {
                            background-color: #f2f2f2;
                        }
                    </style>
                </head>
                <body>
                    <h1>Class Timetable</h1>
                    <table>
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Classroom</th>
                            <th>Subject</th>
                        </tr>';

        foreach ($this->entries as $entry) {
            $html .= '<tr>
                        <td>' . htmlspecialchars($entry['day']) . '</td>
                        <td>' . htmlspecialchars($entry['time']) . '</td>
                        <td>' . htmlspecialchars($entry['ClassName']) . ' - ' . htmlspecialchars($entry['ClassRoomNumber']) . '</td>
                        <td>' . htmlspecialchars($entry['SubjectName']) . '</td>
                    </tr>';
        }

        $html .= '</table>
                </body>
                </html>';

        return $html;
    }
}

try {
    $stmt = $pdo->query('SELECT t.day, t.time, c.ClassName, c.ClassRoomNumber, s.SubjectName 
                         FROM timetable t
                         JOIN classes c ON t.class_id = c.ClassID
                         JOIN subjects s ON t.subject_id = s.SubjectID');
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $timeTable = new TimeTable($entries);
    echo $timeTable->generateHTML();
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
