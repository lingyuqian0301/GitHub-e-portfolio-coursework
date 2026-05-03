
<?php
include  'header.php';
?>


<div class="container">
<form method='POST' action='loginprocess.php'>
    <fieldset>
        <legend>Login </legend>
      
      <div>
        <label for="inputEmail" class="form-label mt-4">Email</label>
        <input type="email" name="femail" class="form-control" id="inputEmail" placeholder="Enter email address" autocomplete="off" required>
        </div>

        <div>
        <label for="exampleInputPassword1" class="form-label mt-4">Password</label>
        <input type="password" name="fpwd" class="form-control" id="exampleInputPassword1" placeholder="Enter Password" autocomplete="off" required>
        </div><br><br>

         

        <button type="submit" class="btn btn-primary">Login</button>

    </fieldset>
</form>
</div>

<?php
include  'footer.php';
?>