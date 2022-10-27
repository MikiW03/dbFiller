<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="data_generator.js" type="module" defer></script>
    <style>
        body {
            background-color: darkgray;
            display: grid;
            height: 90vh;
            place-content: center;
        }

        .content {
            background-color: white;
            border-radius: 10px;
            padding: 2rem;

            display: grid;
        }

        input {
            border: solid black;
            border-width: 0 0 2px 0;

            padding-left: .5rem;
        }

        button {
            padding: .2rem .5rem;
            background-color: black;
            color: white;
            border: 0;
            cursor: pointer;
        }

        .warning {
            text-align: center;
            font-size: 14px;
        }

        .loading {
            background-color: chartreuse;
            width: fit-content;
            border-radius: 10px;
            padding: 1rem 2rem;
            margin: 0;

            justify-self: center;
        }

        .loading.process {
            background-color: yellow;
        }
    </style>
    <title>DB Filler</title>
</head>

<body>
    <div class="content">
        <form action="" method="POST">
            <label for="rows">How many rows do you want to insert?</label>
            <input type="number" name="rows" id="rows">
            <button>Insert</button>
            <div class="bar"></div>
            <p class="warning">Warning: page will refresh multiple times</p>
        </form>

        <?php
        define("FIELDS", [
            'admin' => 0,
            'bibliotekarz' => 1,
            'czytelnik' => 2,
        ]);

        if (!function_exists('str_starts_with')) {
            function str_starts_with($haystack, $needle)
            {
                return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
            }
        }

        $host = "localhost";
        $user = "root";
        $password = "";
        $db = "biblioteka";

        if (!empty($_POST['rows'])) {
            $_COOKIE['repeats'] = 0;
            setcookie("rows", $_POST['rows'], time() + 60 * 60 * 24);
            print("<meta http-equiv='refresh' content='0;URL=index.php'>");
        }

        if (isset($_COOKIE['rows'])) {
            $rows = $_COOKIE['rows'];
            if (!isset($_COOKIE['repeats'])) {
                setcookie("repeats", 1, time() + 60 * 60 * 24, "/");
                print("<meta http-equiv='refresh' content='1;URL=index.php'>");
            } else {
                if ($_COOKIE['repeats'] < $rows) {
                    setcookie("repeats", $_COOKIE['repeats'] + 1, time() + 60 * 60 * 24, "/");
                    print("<p class='loading process'>Filling out the db...</p>");
                } else {
                    print("<p class='loading'>Finished</p>");
                }
            }

            $counter = $_COOKIE['repeats'] ?? 1;
            if (isset($_COOKIE['repeats']) && $_COOKIE['repeats'] > 0) {
                $conn = mysqli_connect("localhost", "root", "", "biblioteka");
                $res = mysqli_query($conn, "SHOW TABLES");
                $tables = mysqli_fetch_all($res);

                foreach ($tables as $table) {
                    $tableName = reset($table);
                    $res = mysqli_query($conn, "SHOW columns FROM $tableName");
                    $columns = mysqli_fetch_all($res, MYSQLI_ASSOC);
                    $columnsToFill = array_filter($columns, function ($col) {
                        return $col["Key"] != "PRI";
                    });
                    $columnsNamesToFill = array_map(function ($col) {
                        return $col['Field'];
                    }, $columnsToFill);

                    $content = [];
                    foreach ($columnsNamesToFill as $col) {
                        if ($col == 'login' || $col == 'haslo') {
                            $item = json_decode($_COOKIE['data'], true)[$col][FIELDS[$tableName]];
                        } else if (str_starts_with($col, "id")) {
                            $item = rand(1, $counter);
                        } else {
                            $item = json_decode($_COOKIE['data'], true)[$col];
                        }
                        array_push($content, mysqli_real_escape_string($conn, $item));
                    };

                    mysqli_query(
                        $conn,
                        "INSERT INTO $tableName(" . implode(', ', $columnsNamesToFill) . ") 
                    VALUES ('" . implode("', '", $content) . "')"
                    );
                }
            }
            if (isset($_COOKIE['repeats']) && $_COOKIE['repeats'] < $rows) {
                print("<meta http-equiv='refresh' content='1;URL=index.php'>");
            }
        }
        ?>
    </div>
</body>

</html>