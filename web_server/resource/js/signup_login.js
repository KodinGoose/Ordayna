let url = window.location.protocol + "//" + window.location.host + "/";

async function refresh() {
  const response = await fetch(url + "token/refresh_refresh_token", { method: "GET" });
  if (response.ok === true) {
      location.href = 'profile.html';
  }
}

refresh();


async function signup() {
  let disp_name = document.getElementById("signup_display_name").value;
  let email = document.getElementById("signup_email").value;
  let tel = document.getElementById("signup_tel").value;
  let pass_1 = document.getElementById("signup_pass_1").value;
  let pass_2 = document.getElementById("signup_pass_2").value;

  let errored = false;
  if (disp_name.length === 0) {
    document.getElementById("signup_display_name_err").innerHTML = "Úres felhasználónév nem megengedett<br>";
    errored = true;
  } else if (disp_name.length > 200) {
    document.getElementById("signup_display_name_err").innerHTML = "A felhasználónév maximum hossza 200<br>";
    errored = true;
  } else {
    document.getElementById("signup_display_name_err").innerHTML = "";
  }
  if (email.length === 0) {
    document.getElementById("signup_email_err").innerHTML = "Úres email nem megengedett<br>";
    errored = true;
  } else if (email.length > 254) {
    document.getElementById("signup_email_err").innerHTML = "Az email maximum hossza 254<br>";
    errored = true;
  } else if (email.match(/^[^@]+[@]+[^@]+$/) === null) {
    document.getElementById("signup_email_err").innerHTML = "Nem valid email<br>";
    errored = true;
  } else {
    document.getElementById("signup_email_err").innerHTML = "";
  }
  if (tel.length > 15) {
    document.getElementById("signup_tel_err").innerHTML = "A telefonszám maximum hossza 15<br>";
    errored = true;
  } else if (tel.match(/^\d+$/) === null && tel.length !== 0) {
    document.getElementById("signup_tel_err").innerHTML = "A telefonszámnak numerikusnak kell lennie<br>";
    errored = true;
  } else {
    document.getElementById("signup_tel_err").innerHTML = "";
  }
  if (pass_1.length < 12) {
    document.getElementById("signup_pass_1_err").innerHTML = "A jelszó minimum hossza 12<br>";
    errored = true;
  } else {
    document.getElementById("signup_pass_1_err").innerHTML = "";
  }
  if (pass_2.length < 12) {
    document.getElementById("signup_pass_2_err").innerHTML = "A jelszó minimum hossza 12<br>";
    errored = true;
  } else {
    document.getElementById("signup_pass_2_err").innerHTML = "";
  }
  if (errored === true) return;

  if (pass_1 !== pass_2) {
    document.getElementById("signup_pass_2_err").innerHTML = "A jelszavaknak egyeznie kell<br>";
    return;
  }

  const response = await fetch(url + "user/create", {
    method: "POST",
    body: tel.length === 0 ? JSON.stringify({
      disp_name: disp_name,
      email: email,
      pass: pass_1,
    }) : JSON.stringify({
      disp_name: disp_name,
      email: email,
      pass: pass_1,
      phone_number: tel,
    })
  });
  if (response.ok === false) {
    const result = await response.text();
    if (result === "Already exists") {
      document.getElementById("signup_display_name_err").innerHTML = "User already exists with this email<br>";
    } else {
      document.getElementById("signup_display_name_err").innerHTML = result + "<br>";
    }
  } else {
    document.getElementById("signup_display_name_err").innerHTML = "";
    location.href = 'login.html';
  }
}

async function login() {
  let email = document.getElementById("login_email").value;
  let pass = document.getElementById("login_pass").value;

  let errored = false;
  if (email.length === 0) {
    document.getElementById("login_email_err").innerHTML = "Úres email nem megengedett<br>";
    errored = true;
  } else if (email.length > 254) {
    document.getElementById("login_email_err").innerHTML = "Az email maximum hossza 254<br>";
    errored = true;
  } else if (email.match(/^[^@]+[@]+[^@]+$/) === null) {
    document.getElementById("login_email_err").innerHTML = "Nem valid email<br>";
    errored = true;
  } else {
    document.getElementById("login_email_err").innerHTML = "";
  }
  if (pass.length < 12) {
    document.getElementById("login_pass_err").innerHTML = "A jelszó minimum hossza 12<br>";
    errored = true;
  } else {
    document.getElementById("login_pass_err").innerHTML = "";
  }
  if (errored === true) return;

  const response = await fetch(url + "token/get_refresh_token", {
    method: "POST",
    body: JSON.stringify({
      email: email,
      pass: pass,
    })
  });
  if (response.status === 403) {
    document.getElementById("login_email_err").innerHTML = "Username or password is incorrect<br>";
  } else {
    document.getElementById("login_email_err").innerHTML = "";
    location.href = 'profile.html';
  }
}
