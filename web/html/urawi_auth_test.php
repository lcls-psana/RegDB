<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Test URAWI Authentication</title>
<style>
body {
    margin:     0;
    padding:    0;

    font-family:    'Segoe UI',Tahoma,Helvetica,Arial,Verdana,sans-serif;
    font-size:      13px;
}
div.visible {
    display:    block;
}
div.hidden {
    display:    none;
}
#auth {
    padding:    20px;
    position:   absolute;
    left:   35%;
    top:    30%;
}
#auth > h1 {
    margin-bottom:  25px;
}
#auth .name {
    width:          72px;
    margin-left:    72px;
    padding-top:    4px;
    font-weight:    bold;
}
#auth .value > input {
    width:      156px;
    padding:    4px;
}
#auth .name ,
#auth .value {
    margin-bottom:  5px;
}
#auth .last {
    margin-top:     15px;
    margin-bottom:  0;
}

#body {
    padding:    20px;
}

/*************************************************
 * Overload default styles for JQuery UI buttons *
 *************************************************/

button.control-button,
label.control-label {
    font-size:  10px !important;
    color:      black !important;
}
button.control-button-important {
    color:      red !important;
}
button.control-button-small {
    font-size:  9px !important;    
}

button {
    background: rgba(240, 248, 255, 0.39) !important;
    border-radius: 2px !important;
}

</style>
<link type="text/css" href="/jquery/css/custom-theme-1.9.1/jquery-ui.custom.css" rel="Stylesheet" />

<script type="text/javascript" src="/jquery/js/jquery.min.js"></script>
<script type="text/javascript" src="/jquery/js/jquery-ui-1.9.1.custom.min.js"></script>

<script>

function WebService_POST (url, params, on_success, on_failure) {
    var jqXHR  = $.post (
        url ,
        params ,
        function(data) {
            var result = eval(data) ;
            if (result.status === 'success') {
                on_success(result) ;
            } else {
                on_failure(result.message) ;
            }
        } ,
        'JSON'
    ).error(function () {
        on_failure('operation failed because of: '+jqXHR.statusText) ;
    }) ;
}

var is_authenticated = false ,
    personId = 0 ;

$(function () {
    var auth           = $('#auth') ,
        login_button   = auth.find('button#login').button() ,
        username_input = auth.find('input#username') ,
        password_input = auth.find('input#password') ,
        body           = $('#body') ;

    login_button  .prop("disabled", false) ;
    username_input.prop("disabled", false) ;
    password_input.prop("disabled", false) ;

    login_button.click(function () {
        
        login_button  .prop("disabled", true) ;
        username_input.prop("disabled", true) ;
        password_input.prop("disabled", true) ;

        WebService_POST (
            'ws/urawi_auth.php' ,
            {   username: username_input.val() ,
                password: password_input.val()
            } ,
            function (data) {
                is_authenticated = true ;
                personId = data.personId ;
                auth.removeClass('visible').addClass('hidden') ;
                body.removeClass('hidden') .addClass('visible') ;
            } ,
            function (errmsg) {
                alert(errmsg) ;
                login_button  .prop("disabled", false) ;
                username_input.prop("disabled", false) ;
                password_input.prop("disabled", false) ;
            }
        ) ;
    }) ;
}) ;
</script>

</head>
<body>

    <div id="auth" class="visible" >

        <h1>Login with URAWI credentials</h1>
        <div class="name"  style="float:left;" >Account</div>
        <div class="value" style="float:left;" ><input id="username" type="text"     size="16" /></div>
        <div style="clear:both;"></div>
        
        <div class="name"  style="float:left;" >Password</div>
        <div class="value" style="float:left;" ><input id="password" type="password" size="16" /></div>
        <div style="clear:both;" ></div>

        <div class="name  last" style="float:left;" >&nbsp;</div>
        <div class="value last" style="float:left;" ><button id="login" class="control-button" >LOGIN</button></div>
        <div style="clear:both;" ></div>
    </div>

    <div id="body" class="hidden" >
        Here be the body of the application which should only be see
        by authenticated users.
    </div>

</body>
</html>
