<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Sensitive Data</title>
    <style>
        table,
        th,
        td {
            border: 2px solid black;
            border-radius: 5px;
            width: fit-content;
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;

        }

        th,
        td {
            padding: 5px;
        }

        th {
            text-align: left;
        }

        table {
            border-spacing: 5px;
        }
    </style>
</head>

<body>
    <table>
        <thead>
            <tr>
                <th>School ID: </th>
                <th><?= $schoolId ?></th>
            </tr>
            <tr>
                <th>Current Academic Year ID: </th>
                <th><?= $academicYear ?></th>
            </tr>
            <tr>
                <th>Class IDs</th>
                <th>
                    <?php

                    if ($classData) {
                        foreach ($classData as $class) {
                            echo "<tr>";
                            echo "<th>Class name: {$class[0]}</th>";
                            echo "<th>ID: {$class[1]}</th>";
                            echo "</tr>";
                        }
                    }

                    ?>
                </th>
            </tr>
            <tr>
                <th></th>
                <th></th>
            </tr>
        </thead>
    </table>
</body>

</html>