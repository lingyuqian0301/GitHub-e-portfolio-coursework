
<?php
include  'header.php';
?>


<div class="container">
<p class="text-suceess">Registration successful.Please login</p>
<div class="container">
<form method='POST' action='loginprocess.php'>
    <fieldset>
        <legend>Login </legend>
      
      <div>
        <label for="exampleInputPassword1" class="form-label mt-4">User ID</label>
        <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Enter user id" autocomplete="off" required>
        </div>

        <div>
        <label for="exampleInputPassword1" class="form-label mt-4">Password</label>
        <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Enter Password" autocomplete="off" required>
        </div><br><br>

         

        <button type="submit" class="btn btn-primary">Login</button>

    </fieldset>
</form>
</div>

<?php
include  'footer.php';
?>