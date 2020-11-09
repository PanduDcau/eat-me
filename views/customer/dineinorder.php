<?php

// if(!isset($_SERVER['HTTP_REFERER'])){
//   //header('Location: /online/login');
// }
// if(!isset($_SESSION['user_phone'])){
//   header('Location: /online/login');
// }

require_once './controllers/customer/DineinOrderController.php';
//Initiate an instance of controller
$DineinOrderController = new DineinOrderController();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="../../img/favicon.png" />
  <!-- Global Styles -->
  <link rel="stylesheet" href="../../css/style.css" />
  <!-- Local Styles -->
  <link rel="stylesheet" href="../../css/dineinorderstyles.css">
  <title>Your Order</title>
</head>

<body>

  <div class="navbar">
    <div class="columns group">
      <div class="column is-2">
        <img src="../../img/logo.png" height=56 width="224" />
      </div>
      <div class="column is-10 has-text-right nav-logout">
        <i class="fa fa-user" aria-hidden="true"></i>
        <?php
        $DineinOrderController->renderNavBar($_SESSION['user_phone']);
        ?>
        <form class="d-inline" action="/online" method="POST">
          <button class="button is-primary" name="logout">Logout</button>
        </form>
      </div>
    </div>
  </div>



  <section>
    <div class="columns group">
      <div class="column is-2"></div>
      <div class="column is-8">
        <div class="card pl-2 pr-2">
          <h1 class="orange-color mt-0 mb-1">My Order</h1>
          <img class="status-image" src="../../img/preparing.gif" alt="">
          <h5 class="title mb-0">Your order is preparing...</h5>
          <h6 class="title mt-0" id="order-id">#4323</h6>
          <div class="content has-text-left">
            <h5 class=" ml-0 mb-0 title">Order Items</h5>
            <p class="menu-items">Chicken Ramen x1, Dosai x20, Faluda x2</p>
            <h5 class=" ml-0 mb-0 title">Payment</h5>
            <div class="mt-1 payment-buttons">
              <button class="pl-0 payment-button" type="submit" name="place-order"><img class="payment-option" src="../../img/payhere.png" alt=""></button>
              <button class="payment-button" type="submit" name="place-order"><img class="payment-option" src="../../img/paycash.png" alt=""></button>
            </div>
          </div>
        </div>
      </div>
      <div class="column is-2"></div>
    </div>
  </section>


  <div class="popout" onclick="stopEvent();">
    <div class="popout-btn" id="popout-btn">
      <i class="icon fab fa-tripadvisor"></i>
    </div>
    <div class="panel" id="popout-panel">
      <div class="panel-header">
        <center>
          <h5 class="title header-title mt-0 mb-0">Take a moment to review us!</h5>
        </center>
      </div>
      <div class="panel-body">
        <h5 class="title">How would you like to rate your EatMe<sup>TM</sup> experience?</h5>

        <div class="rating">
          <input type="radio" name="rating" id="rating-5">
          <label for="rating-5" onclick="getRating(5);"></label>
          <input type="radio" name="rating" id="rating-4">
          <label for="rating-4" onclick="getRating(4);"></label>
          <input type="radio" name="rating" id="rating-3">
          <label for="rating-3" onclick="getRating(3);"></label>
          <input type="radio" name="rating" id="rating-2">
          <label for="rating-2" onclick="getRating(2);"></label>
          <input type="radio" name="rating" id="rating-1">
          <label for="rating-1" onclick="getRating(1);"></label>
          <div class="emoji-wrapper">
            <div class="emoji">
              <img src="../../img/r0.svg" class="rating-0" alt="">
              <img src="../../img/r1.svg" class="rating-1" alt="">
              <img src="../../img/r2.svg" class="rating-2" alt="">
              <img src="../../img/r3.svg" class="rating-3" alt="">
              <img src="../../img/r4.svg" class="rating-4" alt="">
              <img src="../../img/r5.svg" class="rating-5" alt="">
            </div>
          </div>
        </div>
        <div class="review-box">
          <p class="subtitle mb-0 mt-2 pl-0 ml-0">Tell us more!</p>
          <div class="notification-holder" id="notification-holder" style="display: none">
            <div class="artemis-notification notification-success" id="notification-type">
              <p id="notification-message">Hello</p>
            </div>
          </div>
          <textarea type="text" class="review-input mt-1" id="review-text" rows="3"></textarea>
        </div>
        <button class="button is-primary" onclick="submitReview();">Submit</button>
        <div class="row has-text-right">
          Find More at Trip Advisor <i class="icon fab fa-tripadvisor"></i>
        </div>
      </div>
    </div>
  </div>

  <script>
    let reviewStatus = false;
    let rating = 0;

    if (!reviewStatus) {
      setTimeout(triggerReview, 5000);
    }

    function triggerReview() {
      document.getElementById('popout-btn').click();
    }

    function getRating(value) {
      rating = value;
    }

    function stopEvent() {
      //override the default close behaviour
      event.stopPropagation();
    }
    document.getElementById('popout-btn').addEventListener('click', function() {
      document.getElementById('popout-btn').classList.add("active");
      document.getElementById('popout-panel').classList.add("active");
      event.stopPropagation();
    })
    document.body.addEventListener("click", function(e) {
      document.getElementById('popout-btn').classList.remove("active");
      document.getElementById('popout-panel').classList.remove("active");
      event.stopPropagation();
    });

    async function submitReview() {
      let review = document.getElementById('review-text').value;
      let orderId = document.getElementById('order-id').innerHTML.replace(/#\b/g, "");
      let data = {
        "rating": rating,
        "review": review,
        "id": orderId
      }
      try {
        const response = await fetch('/api/v1/review', {
          method: 'POST',
          body: JSON.stringify(data)
        });

        let dataJson = JSON.parse(await response.text());
        document.getElementById("notification-holder").style.display = "block";
        document.getElementById("notification-type").className = "artemis-notification notification-success";
        document.getElementById("notification-message").innerHTML = "Successfully Submitted"
        document.getElementById('review-text').value = "";
      } catch (err) {
        document.getElementById("notification-holder").style.display = "block";
        document.getElementById("notification-type").className = "artemis-notification notification-danger";
        document.getElementById("notification-message").innerHTML = "Something went wrong!"
      }
    }
  </script>
</body>

</html>