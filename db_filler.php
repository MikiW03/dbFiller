<?php
// How many rows are supposed to be inserted
$rows = 10;

if (!isset($_COOKIE['repeats'])) {
    print(1);
    setcookie("repeats", 1, time() + (86400 * 30), "/");
    print("<meta http-equiv='refresh' content='1;URL=db_filler.php'>");
} else {
    if ($_COOKIE['repeats'] < $rows) {
        print($_COOKIE['repeats'] + 1);
        setcookie("repeats", $_COOKIE['repeats'] + 1, time() + (86400 * 30), "/");
    } else {
        print("Finished");
    }
}
$counter = $_COOKIE['repeats'] ?? 1;

// print("<pre>");
// var_dump(json_decode($_COOKIE['data'], true) ?? "empty");
// print("</pre>");

if (isset($_COOKIE['repeats']) && $_COOKIE['repeats'] > 0) {

    $conn = mysqli_connect("localhost", "root", "", "testowa");
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

        print("<br>");
        print("<br>");
        $content = [];
        foreach ($columnsNamesToFill as $col) {
            if (str_starts_with($col, "id")) {
                $item = rand(1, $counter);
            } else {
                $item = json_decode($_COOKIE['data'], true)[$col];
                if (is_array($item)) {
                    $item = array_shift($item);
                }
            }

            array_push($content, mysqli_real_escape_string($conn, $item));
        };

        // var_dump("INSERT INTO $tableName(" . implode(', ', $columnsNamesToFill) . ")
        // VALUES ('" . implode("', '", $content) . "')");

        mysqli_query(
            $conn,
            "INSERT INTO $tableName(" . implode(', ', $columnsNamesToFill) . ") 
            VALUES ('" . implode("', '", $content) . "')"
        );
    }
?>

    <script type="module">
        import {
            faker
        } from 'https://cdn.skypack.dev/@faker-js/faker';

        function getData() {
            const data = {
                imie: faker.name.firstName(),
                nazwisko: faker.name.lastName(),
                login: [
                    faker.name.firstName() + Math.floor(Math.random() * 100),
                    faker.name.firstName() + Math.floor(Math.random() * 100),
                    faker.name.firstName() + Math.floor(Math.random() * 100)
                ],
                haslo: [
                    faker.internet.password(),
                    faker.internet.password(),
                    faker.internet.password(),
                ],
                adres: faker.address.street() + " " + Math.floor(Math.random() * 100),
                miasto: faker.address.city(),
                wojewodztwo: faker.address.state(),
                telefon: faker.phone.number('#########'),
                kod_pocztowy: faker.address.zipCode('##-###'),
                email: faker.internet.email(),
                nazwa: faker.lorem.words(1),
                isbn: faker.random.numeric(11),
                tytul: faker.random.words(),
                autor: faker.name.fullName(),
                stron: Math.floor(Math.random() * 1000) + 80,
                wydawnictwo: faker.random.words(),
                rok_wydania: Math.floor(Math.random() * 122) + 1900,
                opis: faker.lorem.sentences(),
                data_zamowienia: faker.date.between(subtractMonths(8), subtractMonths(6)),
                data_odbioru: faker.date.between(subtractMonths(5), subtractMonths(3)),
                data_zwrotu: faker.date.between(subtractMonths(2), subtractMonths(0))
            };

            return data
        }

        const data = getData()
        document.cookie = `data = ${JSON.stringify(data)}`

        function subtractMonths(numOfMonths, date = new Date()) {
            date.setMonth(date.getMonth() - numOfMonths);
            return date.toISOString();
        }
    </script>

<?php
}
if (isset($_COOKIE['repeats']) && $_COOKIE['repeats'] < $rows) {
    print("<meta http-equiv='refresh' content='1;URL=db_filler.php'>");
}
?>