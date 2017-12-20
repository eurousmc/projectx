<?php
session_start();
?>
<div style="padding:10px;">
  <div class="container">
    <div class="row">
      <div class="col-sm-6">
        <input
        type="text"
        class="form-control"
        id="text_message"
        >
      </div>
      <div class="col-sm-3">
        <button
        class="btn btn-default"
        id="btn_submit"
        ><span class="glyphicon">&#xe171;</span>&nbsp;ส่งข้อความ</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready(function(){

  $("#btn_submit").click(function() {
    call_submit();
  });

  // this for capture enter even
  $("#text_message").keypress(function(e) {
    if (e.which == 13) call_submit();
  });

  // Function for send data to ajax file
  function call_submit() {
    //get data from textbox
    var new_message = document.getElementById('text_message').value;

    // sending data
    $.ajax({
      type: 'post',
      url: 'add_message.php',
      data: { newmessage : new_message }
    });

    // clear text input
    $("#text_message").val('');
  }

});
</script>
