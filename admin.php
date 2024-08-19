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
    die('No data found.');
}

// Handle actions
if (isset($_GET['action'])) {
    $teamIndex = $_GET['index'];
    $listType = $_GET['list']; // 'teams' or 'waitingList'
    $action = $_GET['action']; // 'toggleConfirm', 'moveToWaitingList', 'moveToTeams', 'remove'

    if ($action === 'toggleConfirm' && $listType === 'teams') {
        if (isset($data['teams'][$teamIndex])) {
            $team = $data['teams'][$teamIndex];
            $team['status'] = $team['status'] === 'confirmed' ? 'pending' : 'confirmed';
            $data['teams'][$teamIndex] = $team;
            file_put_contents($filename, json_encode($data));
            header('Location: admin.php');
            exit();
        } else {
            echo 'Invalid team index.';
        }
    } elseif ($listType === 'waitingList' && $action === 'moveToTeams') {
        if (isset($data['waitingList'][$teamIndex])) {
            $team = $data['waitingList'][$teamIndex];
            if (count($data['teams']) < $maxTeams) {
                $team['status'] = 'pending';
                $data['teams'][] = $team;
                unset($data['waitingList'][$teamIndex]);
                $data['waitingList'] = array_values($data['waitingList']);
                file_put_contents($filename, json_encode($data));
                header('Location: admin.php');
                exit();
            } else {
                echo 'Cannot move this team to participant teams. Tournament is full.';
            }
        } else {
            echo 'Invalid team index.';
        }
    } elseif ($listType === 'teams' && $action === 'moveToWaitingList') {
        if (isset($data['teams'][$teamIndex])) {
            $team = $data['teams'][$teamIndex];
            if (count($data['waitingList']) < $maxWaitingList) {
                $team['status'] = 'pending';
                $data['waitingList'][] = $team;
                unset($data['teams'][$teamIndex]);
                $data['teams'] = array_values($data['teams']);
                file_put_contents($filename, json_encode($data));
                header('Location: admin.php');
                exit();
            } else {
                echo 'Cannot move this team to the waiting list. Waiting list is full.';
            }
        } else {
            echo 'Invalid team index.';
        }
    } elseif ($listType === 'waitingList' && $action === 'confirm') {
        if (isset($data['waitingList'][$teamIndex])) {
            $team = $data['waitingList'][$teamIndex];
            if (count($data['teams']) < $maxTeams) {
                $team['status'] = 'pending';
                $data['teams'][] = $team;
                unset($data['waitingList'][$teamIndex]);
                $data['waitingList'] = array_values($data['waitingList']);
                file_put_contents($filename, json_encode($data));
                header('Location: admin.php');
                exit();
            } else {
                echo 'Cannot confirm this team. Tournament is full.';
            }
        } else {
            echo 'Invalid team index.';
        }
    } elseif ($action === 'remove') {
        if ($listType === 'teams' && isset($data['teams'][$teamIndex])) {
            unset($data['teams'][$teamIndex]);
            $data['teams'] = array_values($data['teams']);
            file_put_contents($filename, json_encode($data));
            header('Location: admin.php');
            exit();
        } elseif ($listType === 'waitingList' && isset($data['waitingList'][$teamIndex])) {
            unset($data['waitingList'][$teamIndex]);
            $data['waitingList'] = array_values($data['waitingList']);
            file_put_contents($filename, json_encode($data));
            header('Location: admin.php');
            exit();
        } else {
            echo 'Invalid team index or list type.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Padel Tournament</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        button {
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
        }

        .confirm-button {
            background-color: #007bff;
            color: #fff;
        }

        .confirm-button:hover {
            background-color: #0056b3;
        }

        .confirmed-button {
            background-color: #28a745;
            color: #fff;
        }

        .confirmed-button:hover {
            background-color: #218838;
        }

        .move-button {
            background-color: #17a2b8;
            color: #fff;
        }

        .move-button:hover {
            background-color: #117a8b;
        }

        .remove-button {
            background-color: #dc3545;
            color: #fff;
        }

        .remove-button:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Admin Panel - Padel Tournament</h1>

        <h2>Teams</h2>
        <table>
            <tr>
                <th>Team Name</th>
                <th>Player 1</th>
                <th>Player 2</th>
                <th>Phone Number</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($data['teams'] as $index => $team): ?>
                <tr>
                    <td><?php echo htmlspecialchars($team['name']); ?></td>
                    <td><?php echo htmlspecialchars($team['player1']); ?></td>
                    <td><?php echo htmlspecialchars($team['player2']); ?></td>
                    <td><?php echo htmlspecialchars($team['phone']); ?></td>
                    <td>
                        <a href="admin.php?action=moveToWaitingList&index=<?php echo $index; ?>&list=teams"><button class="move-button">Move to Waiting List</button></a>
                        <a href="admin.php?action=toggleConfirm&index=<?php echo $index; ?>&list=teams">
                            <button class="<?php echo $team['status'] === 'confirmed' ? 'confirmed-button' : 'confirm-button'; ?>">
                                <?php echo $team['status'] === 'confirmed' ? 'Confirmed' : 'Confirm'; ?>
                            </button>
                        </a>
                        <a href="admin.php?action=remove&index=<?php echo $index; ?>&list=teams">
                            <button class="remove-button">Remove</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <h2>Waiting List</h2>
        <table>
            <tr>
                <th>Team Name</th>
                <th>Player 1</th>
                <th>Player 2</th>
                <th>Phone Number</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($data['waitingList'] as $index => $team): ?>
                <tr>
                    <td><?php echo htmlspecialchars($team['name']); ?></td>
                    <td><?php echo htmlspecialchars($team['player1']); ?></td>
                    <td><?php echo htmlspecialchars($team['player2']); ?></td>
                    <td><?php echo htmlspecialchars($team['phone']); ?></td>
                    <td>
                        <a href="admin.php?action=moveToTeams&index=<?php echo $index; ?>&list=waitingList"><button class="move-button">Move to Participant Teams</button></a>
                        <a href="admin.php?action=remove&index=<?php echo $index; ?>&list=waitingList">
                            <button class="remove-button">Remove</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>

</html>