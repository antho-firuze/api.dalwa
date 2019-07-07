<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>SIMPIPRO :: Activation Form</title>
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'>
  <link rel="stylesheet" href="<?=BASE_URL.'__application/views/activation/';?>css/style.css" media="screen" type="text/css" />
</head>
<body>
  <div class="login-card result" style="display:none;"><h1 id="message"></h1></div>
  <div class="login-card mainform">
    <h1>Activation</h1><br>
    <form id="activationform">
      <input type="text" name="email" require placeholder="Email">
      <input type="password" name="password" require placeholder="Password">
      <input type="submit" class="login login-submit" value="Activate">
    </form>
  </div>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/URI.js/1.19.1/URI.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/validator/10.8.0/validator.min.js'></script>
  <script>
    var lang = 'us';
    var agent = 'web';
    var jsonrpc_url = '<?=API_URL;?>';
    var uri = URI(location.href);
    var appcode = URI.parseQuery(uri.query()).appcode;
    var token = URI.encode(URI.parseQuery(uri.query()).token);
    
    $(document).on('submit', '#activationform', function(e) {
      e.preventDefault();
      
      // console.log(token); return false;

      // var params = JSON.stringify({ 
      //     "id":Math.floor(Math.random() * 1000),
      //     "agent":"web", 
      //     "appcode":appcode, 
      //     "lang":lang,
      //     "method":"auth.login", 
      //     "params":{"token":token,"email":$(this).find('[name="email"]').val(),"password":$(this).find('[name="password"]').val()}
      //   });
      
      // console.log(params); return false;
      // console.log("a:"+JSON.stringify(params)); return false;

      var email = $(this).find('[name="email"]').val();
      var password = $(this).find('[name="password"]').val();
      if (!validator.isEmail(email) || validator.isEmpty(password)) {
        alert("Incorrect Email & Password !");
        return false;        
      }

      $.ajax({ url:jsonrpc_url, method:"POST", async:true, dataType:'json',
        data: JSON.stringify({ 
          "id":Math.floor(Math.random() * 1000),
          "agent":agent, 
          "appcode":appcode, 
          "lang":lang,
          "method":"auth.activation", 
          "params":{"token":token,"email":email,"password":password}
        }),
        beforeSend: function(xhr) { $(this).find('[type="submit"]').attr("disabled", "disabled"); },
        success: function(data) {
          // console.log(data);
          if (data.status) {
            $(".mainform").hide();
            $("#message").html(data.message);
            $(".result").show();
            // db_store('session', JSON.stringify(data.result));
            // var url_to = uri.path('backend').search({
            //   "lang":lang, 
            //   "state":"client", 
            //   "page":"dashboard",
            //   "token":data.result.user.token
            // });
            // window.location = url_to;
          } else {
            alert(data.message);
          }
          setTimeout(function(){ $(this).find('[type="submit"]').removeAttr("disabled"); },1000);
        },
        error: function(data, status, errThrown) {
          if (data.status >= 500){
            var message = data.statusText;
          } else {
            var error = JSON.parse(data.responseText);
            var message = error.message;
          }
          alert(message);
          setTimeout(function(){ $(this).find('[type="submit"]').removeAttr("disabled"); },1000);
        }
      });
    }); 
  </script>
</body>
</html>