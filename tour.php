<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padel Tournament Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
        }

        button,
        .toggle-button {
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            margin: 10px 0;
        }

        .toggle-button {
            background-color: #007bff;
            color: #fff;
        }

        .toggle-button:hover {
            background-color: #0056b3;
        }

        .form-container {
            display: none;
            margin-top: 20px;
        }

        .form-container.open {
            display: block;
        }

        .form-container form {
            display: grid;
            gap: 10px;
        }

        .form-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-row label {
            width: 150px;
            font-weight: bold;
        }

        .form-row input[type="text"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            flex: 1;
        }

        .form-row input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-row input[type="submit"]:hover {
            background-color: #218838;
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

        .message,
        .error {
            margin: 10px 0;
            font-weight: bold;
        }

        .message {
            color: #28a745;
        }

        .error {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Book Your Spot for the Padel Tournament</h1>
        <button class="toggle-button" onclick="toggleForm()">Book Your Spot</button>

        <div id="formContainer" class="form-container">
            <form action="book.php" method="post">
                <div class="form-row">
                    <label for="teamName">Team Name:</label>
                    <input type="text" id="teamName" name="teamName" required>
                </div>
                <div class="form-row">
                    <label for="player1">Player 1:</label>
                    <input type="text" id="player1" name="player1" required>
                </div>
                <div class="form-row">
                    <label for="player2">Player 2:</label>
                    <input type="text" id="player2" name="player2" required>
                </div>
                <div class="form-row">
                    <label for="phone">Phone Number:</label>
                    <input type="text" id="phone" name="phone" required>
                </div>
                <div class="form-row">
                    <input type="submit" value="Book Spot">
                </div>
            </form>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
            <div class="message">Your spot has been booked successfully!</div>
        <?php elseif (isset($_GET['error']) && $_GET['error']): ?>
            <div class="error"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <h2>Current Bookings</h2>

        <h3>Teams</h3>
        <?php if (empty($data['teams'])): ?>
            <p class="message">No teams have booked yet.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Team Name</th>
                    <th>Player 1</th>
                    <th>Player 2</th>
                    <th>Phone Number</th>
                </tr>
                <?php foreach ($data['teams'] as $team): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($team['name']); ?></td>
                        <td><?php echo htmlspecialchars($team['player1']); ?></td>
                        <td><?php echo htmlspecialchars($team['player2']); ?></td>
                        <td><?php echo htmlspecialchars($team['phone']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <h3>Waiting List</h3>
        <?php if (empty($data['waitingList'])): ?>
            <p class="message">The waiting list is currently empty.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Team Name</th>
                    <th>Player 1</th>
                    <th>Player 2</th>
                    <th>Phone Number</th>
                </tr>
                <?php foreach ($data['waitingList'] as $team): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($team['name']); ?></td>
                        <td><?php echo htmlspecialchars($team['player1']); ?></td>
                        <td><?php echo htmlspecialchars($team['player2']); ?></td>
                        <td><?php echo htmlspecialchars($team['phone']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <script>
        function toggleForm() {
            const formContainer = document.getElementById('formContainer');
            if (formContainer.classList.contains('open')) {
                formContainer.classList.remove('open');
            } else {
                formContainer.classList.add('open');
            }
        }
    </script>
</body>

</html>