<?php
$total_quantity = 0;
include 'conn.php';

if (isset($_POST['Export'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Date validation
    if ($start_date > $end_date) {
        // You can just output the error here if you like, but no exit or page refresh.
        $error_message = "End date cannot be earlier than start date.";
    } else {
        $sql1 = "SELECT s_tbl.id, s_tbl.name, date, s_tbl.mobileno, s_tbl.email, s_tbl.password, 
                         product_tbl.product_name, product_tbl.product_quntity, c_tbl.c_id, c_tbl.c_name, 
                         c_tbl.c_email, c_tbl.password AS c_password, ad_tbl.address, ad_tbl.city
                  FROM s_tbl 
                  JOIN product_tbl ON s_tbl.id = product_tbl.id 
                  JOIN c_tbl ON s_tbl.id = c_tbl.c_id 
                  JOIN ad_tbl ON s_tbl.id = ad_tbl.id 
                  WHERE date BETWEEN '$start_date' AND '$end_date'";

        $result = mysqli_query($conn, $sql1);

        if (mysqli_num_rows($result) > 0) {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename="data_export.csv"');

            $output = fopen('php://output', 'w');
            fputcsv($output, ['total_quantity', 'ID', 'Name', 'Date', 'Mobileno', 'Email', 'Password', 'Product_Name', 'Product_quantity', 'c_id', 'C_Name', 'C_Email', 'Password', 'Address', 'City']);

            while ($row = mysqli_fetch_assoc($result)) {
                $total_quantity += $row['product_quntity'];
                fputcsv($output, $row);
            }
            echo 'Total Quantity: ' . $total_quantity;
            fclose($output);
            exit();
        } else {
            echo "No records found for the selected date range.";
        }
    }
}

if (isset($_POST['filter'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Date validation
    if ($start_date > $end_date) {
        // You can just output the error here if you like, but no exit or page refresh.
        $error_message = "End date cannot be earlier than start date.";
    } else {
        $sql = "SELECT s_tbl.id, s_tbl.name, date, s_tbl.mobileno, s_tbl.email, s_tbl.password, 
                        product_tbl.product_name, product_tbl.product_quntity, c_tbl.c_id, c_tbl.c_name, 
                        c_tbl.c_email, c_tbl.password, ad_tbl.address, ad_tbl.city
                FROM s_tbl 
                JOIN product_tbl ON s_tbl.id = product_tbl.id 
                JOIN c_tbl ON product_tbl.id = c_tbl.c_id 
                JOIN ad_tbl ON product_tbl.id = ad_tbl.id
                WHERE date BETWEEN '$start_date' AND '$end_date'";

        $result = mysqli_query($conn, $sql);
    }
} else {
    $sql = "SELECT s_tbl.id, s_tbl.name, date, s_tbl.mobileno, s_tbl.email, s_tbl.password, 
                    product_tbl.product_name, product_tbl.product_quntity, c_tbl.c_id, c_tbl.c_name, 
                    c_tbl.c_email, c_tbl.password, ad_tbl.address, ad_tbl.city
            FROM s_tbl 
            JOIN product_tbl ON s_tbl.id = product_tbl.id 
            JOIN c_tbl ON product_tbl.id = c_tbl.c_id 
            JOIN ad_tbl ON product_tbl.id = ad_tbl.id";

    $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- JavaScript to handle the date validation without page refresh -->
    <script>
        function validateDate() {
            var startDate = document.getElementById("start_date").value;
            var endDate = document.getElementById("end_date").value;
            var errorMessage = document.getElementById("error-message");
            
            // Check if end date is earlier than start date
            if (startDate && endDate && startDate > endDate) {
                errorMessage.textContent = "End date cannot be valid than start date.";
                errorMessage.style.color = "red";
                return false;
            } else {
                errorMessage.textContent = "";
                return true;
            }
        }
    </script>
</head>
<body>
    <form class="form-inline d-flex" method="POST" onsubmit="return validateDate()">
        <div class="date-part col-lg-6 border-0" style="margin-left:5%">
            <div class="col-lg-12">
                <div class="form-group">
                    Start Date: <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo isset($start_date) ? $start_date : ''; ?>" required>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group" style="margin-left:100%">
                    <input type="submit" name="Export" class="btn btn-primary" value="Export">
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group" style="margin-left:100%">
                    <input type="submit" name="filter" class="btn btn-primary mt-3" value="Filter">
                </div>
            </div>

            <div class="col-lg-12">
                <div class="form-group">
                    End Date: <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo isset($end_date) ? $end_date : ''; ?>" required>
                </div>
            </div>

            <!-- Display error message -->
            <div id="error-message"></div>
        </div>
    </form>

    <table class="table mydatatable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Name</th>
                <th>Mobileno</th>
                <th>Email</th>
                <th>Password</th>
                <th>Product Name</th>
                <th>Product Quantity</th>
                <th>c_id</th>
                <th>c_name</th>
                <th>c_email</th>
                <th>c_password</th>
                <th>Address</th>
                <th>City</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (isset($result) && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $total_quantity += $row['product_quntity'];
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['mobileno']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['password']; ?></td>
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['product_quntity']; ?></td>
                    <td><?php echo $row['product_quntity']; ?></td>
                    <td><?php echo $row['c_id']; ?></td>
                    <td><?php echo $row['c_name']; ?></td>
                    <td><?php echo $row['c_email']; ?></td>
                    <td><?php echo $row['password']; ?></td>
                    <td><?php echo $row['address']; ?></td>
                    <td><?php echo $row['city']; ?></td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='14'>No records found your selected date range.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</body>
</html>
