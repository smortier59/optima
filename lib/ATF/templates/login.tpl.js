var seed = hex_md5("{$smarty.now}");

$(function() {
  $("#schema").on("keyup", function(event) {
    __submitLoginKeyPress(event);
  });

  $("#login").on("keyup", function(event) {
    __submitLoginKeyPress(event);
  });

  $("#password").on("keyup", function(event) {
    __submitLoginKeyPress(event);
  });

  $("#go").on("click", function() {
    __submitLogin();
  });

  $("#goRecovery").on("click", function() {
    __recovery();
  });

  $("#generateNewPassword").on("click", function() {
    __generateNewPassword(event);
  });
});

function __submitLoginKeyPress(event) {
  if (
    event.keyCode == 13 &&
    $("#schema").val() &&
    $("#login").val() &&
    $("#password").val()
  ) {
    __submitLogin();
  }
}

function __submitLogin() {
  $("#submitButton").hide();
  $("#loadingAnim").show();
  $("#login", "#schema", "#password").each(function(s) {
    s.disabled = true;
  });

  $.ajax({
    url: "usr,login.ajax",
    type: "POST",
    data: {
      "schema": encodeURI($("#schema").val()).toLowerCase(),
      "store": ($("#store").checked ? 1 : 0),
      "login": encodeURI($("#login").val()),
      "password": $("#password").val(),
      "url": encodeURI($("#url").val())
    },
    timeout: 120000,
    success: function(response, textStatus, jqXHR) {
      if (response.result) {
        ATF.ajax_refresh(jQuery.extend(response));

        if (response.location) {
          ATF.__toLocation(response.location);
        } else {
          ATF.__toLocation("accueil.html");
        }
      } else {
        __wrongPassword(jqXHR);
      }
    }
  });
}

function __wrongPassword(obj, req) {
  $("#loadingAnim").hide();
  $("#submitButton").show();
  $("#login", "#schema", "#password").each(function(s) {
    s.disabled = false;
  });
  if (req && req.getResponseHeader("x-error-reason")) {
    Modalbox.show(req.getResponseHeader("x-error-reason"), {
      title: "Mot de passe expiré",
      method: "post"
    });
  } else {
    Modalbox.show("wrongPassword.dialog", {
      title: "Mot de passe erroné",
      method: "post"
    });
  }
}

function __recovery() {
  var mail = $("#email").val();
  var schema = $("#schema").val();
  ["schema", "login", "password","email","goRecovery","go"].forEach(function(id) { if (document.getElementById(id)) { document.getElementById(id).disabled = true; } });

  $.ajax({
    url: "usr,recovery.ajax",
    type: "POST",
    data: {
      "schema": encodeURI(schema).toLowerCase(),
      "email": encodeURI(mail).toLowerCase()
    },
    complete: function(jqXHR, textStatus) {
      Modalbox.show(
        'Si "' +
          mail +
          '" est associé à un compte utilisateur du domaine "' +
          schema +
          '", un email vous a été envoyé. Suivez les instructions pour réinitialiser votre mot de passe.',
        { title: "Récupération de mot de passe", method: "post" }
      );
      ["schema", "login", "password","email","goRecovery","go"].forEach(function(id) { if (document.getElementById(id)) { document.getElementById(id).disabled = false; } });
      $(".emailRecovery").hide();
      $("#email").val("");
      $("#loadingAnim").hide();
      $("#submitButton").show();
    }
  });
}

function __generateNewPassword(e) {

  if ($("#schema").val() && $("#new_password").val() && $("#new_password_cnf").val()) {
    $("#errorUpdatePassword").html("");


    if($("#new_password").val() == $("#new_password_cnf").val()){


      ["schema", "new_password", "new_password_cnf","generateNewPassword"].forEach(function(id) { if (document.getElementById(id)) { document.getElementById(id).disabled = true; } });


      var params = "k=" + encodeURI($("#k").val()).toLowerCase() + "&schema=" + encodeURI($("#schema").val()).toLowerCase() + "&new_password=" + encodeURI($("#new_password").val());

      $.ajax({
        url: "usr,update_password.ajax",
        type: "POST",
        data: {
          "k": encodeURI($("#k").val()).toLowerCase(),
          "schema": encodeURI($("#schema").val()).toLowerCase(),
          "new_password": encodeURI($("#new_password").val())
        },
        complete: function(response) {
          if (response.error && response.error.length) {
            Modalbox.show(ATF.usr.trans(response.error[0].msg.text), { title:'Erreur' });
            ["schema", "new_password", "new_password_cnf","generateNewPassword"].forEach(function(id) { if (document.getElementById(id)) { document.getElementById(id).disabled = false; } });
          } else {
            $('#success-change-password').show();
          }
        },
        error: function (obj) {
          console.log('ERROR', obj)
        }
      });
    }else{
      ["schema", "new_password", "new_password_cnf"].forEach(function(id) { if (document.getElementById(id)) {

        document.getElementById(id).disabled = false;
      } });

      $("#errorUpdatePassword").html("Les mots de passe ne sont pas identique");
    }

  }
}
