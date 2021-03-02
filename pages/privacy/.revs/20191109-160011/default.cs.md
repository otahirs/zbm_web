---
title: privacy
routable: 'no'
---

<div id="optout-form">
  <p>You may choose not to have a unique web analytics cookie identification number assigned to your computer to avoid the aggregation and analysis of data collected on this website.</p>
  <p>To make that choice, please click below to receive an opt-out cookie.</p>

  <p>
    <input type="checkbox" id="optout" />
    <label for="optout"><strong></strong></label>
  </p>
</div>
<script>
document.addEventListener("DOMContentLoaded", function(event) {
  function setOptOutText(element) {
    _paq.push([function() {
      element.checked = !this.isUserOptedOut();
      document.querySelector('label[for=optout] strong').innerText = this.isUserOptedOut()
        ? 'You are currently opted out. Click here to opt in.'
        : 'You are currently opted in. Click here to opt out.';
    }]);
  }

  var optOut = document.getElementById("optout");
  optOut.addEventListener("click", function() {
    if (this.checked) {
      _paq.push(['forgetUserOptOut']);
    } else {
      _paq.push(['optUserOut']);
    }
    setOptOutText(optOut);
  });
  setOptOutText(optOut);
});
</script>