let url = window.location.protocol + "//" + window.location.host + "/";

async function getAccessToken() {
  const response = await fetch(url + "token/get_access_token", { method: "GET" });
  if (response.ok !== true) {
    location.href = 'login.html';
  }
}

// Called every minute
let intervalId = setInterval(getAccessToken, 1000 * 60);

getAccessToken();

const user = {
  "display_name": "admin1",
  "email": "forTest@email.com",
  "tel": "06 20 666 1939",
  "img": '<img src="img\\img3.jpg" alt="pfp" >'
}


const display = document.getElementById("og_display");
const email = document.getElementById("mail_add");
const tel = document.getElementById("og_tel");
const img = document.getElementById("pfp");
const hide = document.getElementById("hide");


function loadUserData() {
  display.innerHTML = user.display_name;
  email.innerHTML = user.email;
  tel.innerHTML = user.tel;
  img.innerHTML = user.img;
}

function hide_show() {
  hide.style = "display:block"
}

function changePfp(a) {
  switch (a) {
    case 1:
      img.innerHTML = '<img src="img\\img' + a + '.jpg" alt="pfp" >'
      break;
    case 2:
      img.innerHTML = '<img src="img\\img' + a + '.jpg" alt="pfp" >'
      break;
    case 3:
      img.innerHTML = '<img src="img\\img' + a + '.jpg" alt="pfp" >'
      break;
    default:
      hide.style = "display:none"
      break;
  }
}

async function signout() {
  const response = await fetch(url + "user/logout", { method: "GET" });
  if (response.ok === true) {
    location.href = 'login.html';
  }
}

loadUserData();

