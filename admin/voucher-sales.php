<?php

include_once "pages/header.pages.php";

include_once "pages/navbar.pages.php";

?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <?php

        if (isset($_GET['error']) && $_GET['error'] != "") {
            echo "<div class='alert alert-danger'>" . str_replace('_', ' ', $_GET['error']) . "</div>";
        }

        ?>
        <h3 class="text-center">Sell Voucher</h3>
        <div class="card mt-2">
            <div class="card-body">
                <h5>Charges: </h5>
                <p>Fee per month with SMS charges: D1020</p>
                <p>Fee per month without SMS charges: D870</p>
                <br>
                <form action="preview-sales.php" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="type">Type of Voucher</label>
                        <select name="type" id="type" class="form-control" onchange="changeMin()">
                            <option value="MONTH">MONTH</option>
                            <option value="YEAR">YEAR</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="duration">Duration</label>
                        <input type="number" name="duration" id="duration" min="3" max="12" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="email">E-Mail Address</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" name="phone" id="phone" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="sms">Will use SMS? </label>
                        <input type="checkbox" name="sms" id="sms" class="form-check">
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Generate Invoice" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include_once "pages/footer.pages.php"; ?>

<script>
    const durationField = document.getElementById("duration");
    const typeField = document.getElementById('type');

    function changeMin() {
        if (typeField.value === "MONTH") {
            durationField.setAttribute("min", 3);
            durationField.setAttribute("value", 3);
        } else {
            durationField.setAttribute("min", 1);
            durationField.setAttribute("value", 1);
        }
    }
</script>