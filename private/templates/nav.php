<?php
  session_start();
  require TEMPLATE_PATH . "/bootstrap_scripts.php";
  $protocol = isset($_SERVER["HTTPS"]) ? 'https://' : 'http://';
  $base_url = $protocol . $_SERVER['HTTP_HOST'] . '/mythical-pets/public';
?>
<div class="container-fluid mx-0 p-0 bg-dark">
  <div class="row mx-0">
    <div class="col-lg-1 mx-0 p-1">
      <img src = <?php echo $base_url . "/images/logo.png"; ?> class="mx-auto" style="min-height: 128px; max-height: 128px"> </img>
    </div>
    <div class="col-11 m-0 p-0">
      <div class="row mx-auto">
        <nav class="navbar navbar-dark bg-dark w-100">
          <ul class="nav">
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $base_url ?>">Home</a>
            </li>
            <?php if(!isset($_SESSION['username'])){ ?>
              <li class="nav-item">
                <a class="nav-link" data-toggle="modal" data-target="#regModal" href="#">Register</a>
              </li>
            <?php } ?>
            <li class="nav-item">
              <a class="nav-link" href="<?php echo $base_url . '/pages/user_profile.php?profileID=' . $_SESSION['userID']?>">My Account</a>
            </li>
            <?php
            if (isset($_SESSION['admin']) && $_SESSION['admin'] == 0) { ?>
              <li class="nav-item">
                <a name="my-items" class="nav-link" href="<?php echo $base_url ?>/pages/my_items.php">My Items</a>
              </li>
            <?php }  ?>
            <li class="nav-item">
              <a name="my-bids" class="nav-link" href="<?php echo $base_url ?>/pages/my_bids.php"> <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == "1"){echo "All Bids";} else {echo "My Bids";} ?></a>
            </li>
            <li class="nav-item">
              <a name="myPurchases" class="nav-link" href="<?php echo $base_url ?>/pages/my_purchases.php"><?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == "1"){echo "All Purchases";} else {echo "My Purchases";} ?></a>
            </li>
            <?php if(isset($_SESSION['admin'])){
              if($_SESSION['admin']=='1'){?>
                  <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url ?>/pages/admin_users_view.php">All Users</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_url ?>/pages/admin_items_view.php">All Items</a>
                  </li>
              <?php }
            }?>
            <?php if(isset($_SESSION['username'])){ ?>
              <li class="nav-item">
                <a class="nav-link" id="logoutlink" href="#" onclick="logout()">Log Out</a>
              </li>
            <?php } ?>
          </ul>

          <?php
            if(isset($_SESSION['username'])){ ?>
              <div class="navbar-brand">Hello,
                <?php echo $_SESSION['username'] ?>
              </div>
            <?php }  else{ ?>
              <form name="signInForm" class="form-inline" action="forms/sign_in.php" method="POST">
                <div class="form-group">
                  <input class="form-control mr-sm-2" type="text" name="email" placeholder="E-mail" name="email" required>
                </div>
                <div class="form-group">
                  <input class="form-control mr-sm-2" type="password" name="password" placeholder="Password" name="password" required>
                </div>
                <div class="form-group">
                  <button class="btn btn-outline-primary" type="submit" id="SignInSubmit" onclick="submitSignIn()">Sign In</button>
                </div>
              </form>
          <?php  } ?>
        </nav>
      </div>
      <div class="row mx-auto">
        <?php  require TEMPLATE_PATH . '/searchbar.php'; ?>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="regModal" tabindex="-1" role="dialog" aria-labelledby="regModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content bg-dark" id="reg-modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-light" id="reg-modal-title">Register with us</h5>
        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body bg-white">
        <form class="form" name= "regform" id="regForm" action="forms/register_user.php" method="POST">
          <label>Personal Details</label>
          <div class="row mb-2">
            <div class="col-md-6 pr-1">
              <input class="form-control" type="text" placeholder="First Name" name="firstName" id="r_firstName" required>
            </div>
            <div class="col-md-6 pl-1">
              <input class="form-control" type="text" placeholder="Last Name" name="lastName" id="r_lastName" required>
            </div>
          </div>
          <label>Account Details</label>
          <div class="row mb-2">
            <div class="col-md-12">
              <input class="form-control" type="text" placeholder="Email" name="email" id="r_email" required>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-6 pr-1">
              <input class="form-control" type="password" placeholder="Password" name="password" id="r_password"  required>
            </div>
            <div class="col-md-6 pl-1">
              <input class="form-control" type="password" placeholder="Confirm Password" name="confpassword" id="r_confpassword" required>
            </div>
          </div>
          <label>Address</label>
          <div class="row mb-2">
            <div class="col-md-12">
              <input class="form-control" type="text" placeholder="Address Line 1" name="addl1" id="r_addl1" required>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-12">
              <input class="form-control" type="text" placeholder="Address Line 2" name="addl2" id="r_addl2" required>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-12">
              <input class="form-control" type="text" placeholder="City" name="city" id="r_city" required>
            </div>
          </div>
          <div class="row mb-2">
            <div class="col-md-6 pr-1">
              <input class="form-control" type="text" placeholder="Country" name="country" id="r_country" required>
            </div>
            <div class="col-md-6 pl-1">
              <input class="form-control" type="text" placeholder="Postcode" name="postcode" id="r_postcode" required>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-between bg-light">
            <a href="#" data-toggle="modal" data-target="#signInModal" data-dismiss="modal">Already Registered?</a>
            <button class="btn btn-outline-success" type="submit" id="registerSubmit" onclick="submitRegister()">Register</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="signInModal" tabindex="-1" role="dialog" aria-labelledby="regModal" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content bg-dark" id="sign-in-modal-content">
      <div class="modal-header">
        <h5 class="modal-title text-light" id="sign-in-modal-title">Sign In</h5>
        <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body bg-white">
        <form class="form" id="signInModalForm" action="forms/sign_in.php" method="POST">
          <div class="form-group">
            <input class="form-control" type="text" placeholder="Email" name="email" required>
          </div>
          <div class="form-group">
            <input class="form-control" type="password" placeholder="Password" name="password" required>
          </div>
        </form>
      </div>
      <div class="modal-footer justify-content-between bg-light">
            <a href="#" data-toggle="modal" data-target="#regModal" data-dismiss="modal">New User?</a>
            <button class="btn btn-outline-success" type="submit" id="signInModalSubmit" onclick="submitSignInModal()">Sign In</button>
      </div>
    </div>
  </div>
</div>

<style>
  .form-group{margin-left: 5px;}
  .nav-link {color: #fff;}
  .nav-link:hover {color: #0275d8;}
  .nav-link.active{font-weight: bold;}
</style>

<script type="text/javascript">
  function submitRegister(){
    var fname = $("#r_firstName").val();
    var lname = $("#r_lastName").val();
    var email = $("#r_email").val();
    var password = $("#r_password").val();
    var confpassword = $("#r_confpassword").val();
    var addl1 = $("#r_addl1").val();
    var addl2 = $("#r_addl2").val();
    var city = $("#r_city").val();
    var country = $("#r_country").val();
    var postcode = $("#r_postcode").val();

    if(fname && lname && email && password && confpassword && addl1 && city && country && postcode){
      if(confpassword == password){
        setAction(regForm, 'register_user.php');
        regForm.submit();
      } else{
        alert("Please ensure password fields match before submission!");
      }
    } else{
      alert("Please complete all fields before submission!");
    }
  }
  function submitSignInModal(){
    setAction(signInModalForm, 'sign_in.php');
    signInModalForm.submit();
  }
  function submitSignIn(){
    setAction(signInForm, 'sign_in.php');
  }
  function logout(){
    if (document.location.pathname.endsWith('public/') || document.location.pathname.endsWith('index.php')){
      logoutlink.href = 'forms/log_out.php';
    } else{
      logoutlink.href = '../forms/log_out.php';
    }
  }
  function setAction(form, file){
    if (document.location.pathname.endsWith('public/') || document.location.pathname.endsWith('index.php')){
      form.action = 'forms/' + file;
    }else{
      form.action = '../forms/' + file;
    }
  }
</script>
