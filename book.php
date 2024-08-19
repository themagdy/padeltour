<?php
$filename = 'tournament_data.txt';
$configFile = 'config.txt';

// Read configuration
$config = parse_ini_file($configFile);
$maxTeams = $config['max_teams'];
$maxWaitingList = $config['max_waiting_list'];

// Read existing data
if (file_exists($filename)) {
    $data = json_decode(file_get_contents($filename), true);
} else {
    $data = [
        'teams' => [],
        'waitingList' => []
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $teamName = trim($_POST['teamName']);
    $player1 = trim($_POST['player1']);
    $player2 = trim($_POST['player2']);
    $phone = trim($_POST['phone']);

    // Check for empty fields
    if (empty($teamName) || empty($player1) || empty($player2) || empty($phone)) {
        header('Location: tour.php?error=All fields are required');
        exit;
    }

    // Check if the team is already booked
    $teamExists = false;
    foreach ($data['teams'] as $team) {
        if ($team['name'] === $teamName) {
            $teamExists = true;
            break;
        }
    }

    if ($teamExists) {
        header('Location: tour.php?error=This team is already booked');
        exit;
    }

    // Check the current number of teams and waiting list entries
    $currentTeamCount = count($data['teams']);
    $currentWaitingListCount = count($data['waitingList']);

    // Debugging output
    error_log("Current Teams: $currentTeamCount, Max Teams: $maxTeams");
    error_log("Current Waiting List: $currentWaitingListCount, Max Waiting List: $maxWaitingList");

    // Add the team to the bookings or waiting list
    if ($currentTeamCount < $maxTeams) {
        $data['teams'][] = [
            'name' => $teamName,
            'player1' => $player1,
            'player2' => $player2,
            'phone' => $phone,
            'status' => 'pending'
        ];
    } elseif ($currentWaitingListCount < $maxWaitingList) {
        $data['waitingList'][] = [
            'name' => $teamName,
            'player1' => $player1,
            'player2' => $player2,
            'phone' => $phone
        ];
    } else {
        header('Location: tour.php?error=The tournament is fully booked');
        exit;
    }

    // Save data
    file_put_contents($filename, json_encode($data));

    // Redirect with success message
    header('Location: tour.php?success=true');
    exit;
}
