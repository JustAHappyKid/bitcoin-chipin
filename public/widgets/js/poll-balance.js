
function checkBalance() {
  $.ajax({
    url: checkProgressURI,
    type: 'GET',
    dataType: 'text',
    success: function (amountStr) {
      var progress = parseFloat(amountStr);
      if (progress > lastProgress) {
        //playSound('cha-ching');
        $('.status-bar-container .bar').animate({width: progress.toString() + "%"}, 2500).
          delay(1000);
        updateAmountRaised($('.raised-and-goal .raised .amount'), baseCurrency);
        updateAmountRaised($('.raised-and-goal .raised .alt-amount'), altCurrency);
      }
      lastProgress = progress;
      setTimeout('checkBalance()', 5000);
    },
    error: function (data) {
      console.error("It seems the balance-lookup failed: " + data);
    }
  });
}

function updateAmountRaised(elem, currency) {
  $.ajax({
    type: 'GET', url: amountRaisedURI + '?currency=' + currency,
    dataType: 'text',
    success: function (amountStr) {
      elem.fadeOut(1000, function() {
        elem.html(amountStr); elem.fadeIn(1000); });
    }
  });
}

function playSound(soundObj) {
  var sound = document.getElementById(soundObj);
  if (sound) sound.play();
}

jQuery(document).ready(function () {
  setTimeout('checkBalance()', 5000);
});
