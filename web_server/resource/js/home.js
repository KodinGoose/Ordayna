import { getCookie } from "./cookie.js";

let intezmeny_id = getCookie("intezmeny_id");
if (intezmeny_id === null) location.href = "profile.html";
let intezmeny_name = getCookie("intezmeny_name");
if (intezmeny_name === null) location.href = "profile.html";
let user_role = getCookie("user_role");
if (user_role === null) location.href = "profile.html";
document.getElementById("i-name").innerHTML = `${intezmeny_name} ${user_role}`;
