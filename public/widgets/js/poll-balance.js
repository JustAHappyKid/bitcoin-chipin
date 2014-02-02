
var pollingFrequency = 5000; // 5 seconds

function checkBalance() {
  $.ajax({
    url: checkBalanceURI,
    type: 'GET',
    dataType: 'text',
    success: function (balanceStr) {
      var balance = parseFloat(balanceStr);
      if (balance > lastBalance) {
        //playSound('cha-ching');
        updateProgressBar();
        updateAmountRaised($('.raised-and-goal .raised .amount'), baseCurrency);
        updateAmountRaised($('.raised-and-goal .raised .alt-amount'), altCurrency);
      }
      lastBalance = balance;
      setTimeout('checkBalance()', pollingFrequency);
    },
    error: function (data) {
      console.error("It seems the balance-lookup failed: " + data);
      setTimeout('checkBalance()', pollingFrequency * 2);
    }
  });
}

function updateProgressBar() {
  $.ajax({
    type: 'GET', url: checkProgressURI, dataType: 'text',
    success: function (progressStr) {
      $('.status-bar-container .bar').animate({width: progressStr + "%"}, 2500).
        delay(1000);
    }
  })
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
  setTimeout('checkBalance()', pollingFrequency);
});
