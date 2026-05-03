<?php
include 'dbconnect.php';
include 'header.php';
include 'csrf.php';

// Ensure session is available for flash messages
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container">
    <form method='POST' action='registerprocess.php'>
        <fieldset>
            <legend>Registration Form</legend>

            <?php echo csrf_input(); ?>

            <?php
            if (!empty($_SESSION['errors'])) {
                echo '<div class="alert alert-danger"><ul class="mb-0">';
                foreach ($_SESSION['errors'] as $err) {
                    echo '<li>' . htmlspecialchars($err, ENT_QUOTES, 'UTF-8') . '</li>';
                }
                echo '</ul></div>';
                unset($_SESSION['errors']);
            }

            if (isset($_GET['error']) && $_GET['error'] == 'passwordmismatch') {
                echo '<div class="alert alert-danger">Passwords do not match. Please try again.</div>';
            }
            ?>

            <div>
                <label for="inputName" class="form-label mt-4">Full name</label>
                <input type="text" name="fname" class="form-control" id="inputName" placeholder="Please enter your full official name" autocomplete="off" required>
            </div>

            <div>
                <label for="inputPassword1" class="form-label mt-4">Password</label>
                <input type="password" name="fpwd" class="form-control" id="inputPassword1" placeholder="Create strong password" autocomplete="off" required>
            </div>

            <div>
                <label for="inputPassword2" class="form-label mt-4">Confirm Password</label>
                <input type="password" name="fpwd_confirm" class="form-control" id="inputPassword2" placeholder="Retype your password" autocomplete="off" required>
            </div>

            <div>
                <label for="inputEmail" class="form-label mt-4">Email address</label>
                <input type="email" name="femail" class="form-control" id="inputEmail" aria-describedby="emailHelp" placeholder="Enter email">
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
            </div>

            <div>
                <label for="selectOperator" class="form-label mt-4">Select Operator</label>
                <select class="form-select" name="foperator" id="selectOperator">
                    <option>011</option>
                    <option>012</option>
                    <option>013</option>
                    <option>017</option>
                    <option>019</option>
                </select>
            </div>

            <div>
                <label for="inputPhone" class="form-label mt-4">Phone number</label>
                <input type="text" name="fphone" class="form-control" id="inputPhone" placeholder="e.g., 12345678 (without operator/dash)" autocomplete="off" required>
            </div>

            <div>
                <label for="selectGender" class="form-label mt-4">Gender</label>
                <select class="form-select" name="fgender" id="selectGender">
                    <option value="M">Male</option>
                    <option value="F">Female</option>
                </select>
            </div>

            <div>
                <label for="selectProg" class="form-label mt-4">Programme</label>
                <select class="form-select" name="fprog" id="selectProg">
                    <?php
                    $sqlProg = "SELECT * FROM `tb_programme`";
                    $resultProg = mysqli_query($con, $sqlProg);
                    while ($rowProg = mysqli_fetch_array($resultProg)) {
                        echo "<option value='" . $rowProg['p_id'] . "'>" . $rowProg['p_name'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div>
                <label for="selectCol" class="form-label mt-4">Residential College</label>
                <?php
                $sql = "SELECT * FROM `tb_residential`";
                $result = mysqli_query($con, $sql);
                echo "<select class='form-select' name='fcol' id='selectCol'>";
                while ($row = mysqli_fetch_array($result)) {
                    echo "<option value='" . $row['r_id'] . "'>" . $row['r_name'] . "</option>";
                }
                echo "</select>";
                ?>
            </div>
            <br><br>

            <button type="submit" class="btn btn-primary">Register</button>
            <button type="reset" class="btn btn-warning">Clear Input</button>
            <br><br><br>
        </fieldset>
    </form>
</div>

<?php
include 'footer.php';
?>