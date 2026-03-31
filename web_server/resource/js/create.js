import { validateNumber, validateString } from "./validate.js";
import { url, getCookie } from "./cookie.js";

let intezmeny_id = getCookie("intezmeny_id");
if (intezmeny_id === null) location.replace("profile.html");

async function prepareCreate() {
  if (typeof document.getElementById("create_choice").options[document.getElementById("create_choice").selectedIndex] === 'undefined') {
    document.getElementById("create_choice").selectedIndex = 0;
  }
  const val = document.getElementById("create_choice").options[document.getElementById("create_choice").selectedIndex].getAttribute("value");
  if (val === "class") {
    document.getElementById("create_form").innerHTML = `
      <input id="class_name">
      <input id="class_count">
      <button onclick="createClass()">Osztály létrehozása</button>
      <div class="errors">
        <span class="err" id="class_name_err"></span>
        <span class="err" id="class_count_err"></span>
      </div>
    `;
  } else if (val === "group") {
    const response = await fetch(url + "intezmeny/get/classes", {
      method: "POST",
      body: JSON.stringify({
        intezmeny_id: intezmeny_id,
      })
    });
    if (response.ok !== true) {
      return;
    }
    let classes = await response.json();
    let html = `
      <input id="group_name">
      <input id="group_count">
      <select id="group_class">
        <option value="-1">-</option>
    `;
    for (let i = 0; i < classes.length; i++) {
      html += `<option value="${classes[i].id}">${classes[i].name}</option>`;
    }
    html += `
      </select>
      <button onclick="createGroup()">Csoport létrehozása</button>
      <div class="errors">
        <span class="err" id="group_name_err"></span>
        <span class="err" id="group_count_err"></span>
      </div>
    `;
    document.getElementById("create_form").innerHTML = html;
  } else if (val === "lesson") {
    document.getElementById("create_form").innerHTML = `
      <input id="lesson_name">
      <button onclick="createLesson()">Tanóra létrehozása</button>
      <div class="errors">
        <span class="err" id="lesson_name_err"></span>
      </div>
    `;
  } else if (val === "room") {
    document.getElementById("create_form").innerHTML = `
      <input id="room_name">
      <input id="room_type">
      <input id="room_space">
      <button onclick="createRoom()">Szoba létrehozása</button>
      <div class="errors">
        <span class="err" id="room_name_err"></span>
        <span class="err" id="room_type_err"></span>
        <span class="err" id="room_space_err"></span>
      </div>
    `;
  } else if (val === "teacher") {
    const response = await fetch(url + "intezmeny/user/get_all", {
      method: "POST",
      body: JSON.stringify({
        intezmeny_id: intezmeny_id,
      })
    });
    if (response.ok !== true) {
      return;
    }
    let users = await response.json();
    let html = `
      <input id="teacher_name">
      <input id="teacher_job">
      <select id="teacher_user" size="10">
        <option value="-1">-</option>
    `;
    for (let i = 0; i < users.length; i++) {
      if (users[i].role !== "student") continue;
      html += `<option value="${users[i].id}">${users[i].display_name}</option>`;
    }
    html += `
      </select>
      <button onclick="createTeacher()">Tanár létrehozása</button>
      <div class="errors">
        <span class="err" id="teacher_name_err"></span>
        <span class="err" id="teacher_job_err"></span>
      </div>
    `;
    document.getElementById("create_form").innerHTML = html;
  } else if (val === "user") {
    document.getElementById("create_form").innerHTML = `
      <input id="user_email">
      <button onclick="inviteUser()">Felhasználó meghívása</button>
      <div class="errors">
        <span class="err" id="user_email_err"></span>
      </div>
    `;
  }
}

async function createClass() {
  const class_name = validateString("class_name", "class_name_err", 200, 1, "név");
  const headcount = validateNumber("class_count", "class_count_err", Number.MAX_SAFE_INTEGER, 0, "létszám");
  if (class_name === false || headcount === false) return;

  const response = await fetch(url + "intezmeny/create/class", {
    method: "POST",
    body: JSON.stringify({
      intezmeny_id: intezmeny_id,
      name: class_name,
      headcount: headcount + "",
    })
  });
  if (response.ok !== true) {
    let result = await response.text();
    document.getElementById("class_name_err").innerHTML = result;
    return;
  }
  document.getElementById("class_name").value = "";
  document.getElementById("class_count").value = "";
}

async function createGroup() {
  const group_name = validateString("group_name", "group_name_err", 200, 1, "név");
  const headcount = validateNumber("group_count", "group_count_err", Number.MAX_SAFE_INTEGER, 0, "létszám");
  if (typeof document.getElementById("group_class").options[document.getElementById("group_class").selectedIndex] === 'undefined') {
    document.getElementById("group_class").selectedIndex = 0;
  }
  const class_id = document.getElementById("group_class").options[document.getElementById("group_class").selectedIndex].getAttribute("value");
  if (group_name === false || headcount === false) return;

  const response = await fetch(url + "intezmeny/create/group", {
    method: "POST",
    body: class_id === "-1" ? JSON.stringify({
      intezmeny_id: intezmeny_id,
      name: group_name,
      headcount: headcount + "",
    }) : JSON.stringify({
      intezmeny_id: intezmeny_id,
      name: group_name,
      headcount: headcount + "",
      class_id: class_id,
    })
  });
  if (response.ok !== true) {
    let result = await response.text();
    document.getElementById("group_name_err").innerHTML = result;
    return;
  }
  document.getElementById("group_name").value = "";
  document.getElementById("group_count").value = "";
  document.getElementById("group_class").selectedIndex = 0;
}

async function createLesson() {
  const lesson_name = validateString("lesson_name", "lesson_name_err", 200, 1, "név");
  if (lesson_name === false) return;

  const response = await fetch(url + "intezmeny/create/lesson", {
    method: "POST",
    body: JSON.stringify({
      intezmeny_id: intezmeny_id,
      name: lesson_name,
    })
  });
  if (response.ok !== true) {
    let result = await response.text();
    document.getElementById("lesson_name_err").innerHTML = result;
    return;
  }
  document.getElementById("lesson_name").value = "";
}

async function createRoom() {
  const room_name = validateString("room_name", "room_name_err", 200, 1, "név");
  const room_type = validateString("room_type", "room_type_err", 200, 1, "tipus");
  const room_space = validateString("room_space", "room_space_err", 200, 1, "férőhely");
  if (room_name === false || room_type === false || room_space === false) return;

  const response = await fetch(url + "intezmeny/create/room", {
    method: "POST",
    body: JSON.stringify({
      intezmeny_id: intezmeny_id,
      name: room_name,
      type: room_type,
      space: room_space,
    })
  });
  if (response.ok !== true) {
    let result = await response.text();
    document.getElementById("room_name_err").innerHTML = result;
    return;
  }
  document.getElementById("room_name").value = "";
  document.getElementById("room_type").value = "";
  document.getElementById("room_space").value = "";
}

async function createTeacher() {
  const teacher_name = validateString("teacher_name", "teacher_name_err", 200, 1, "név");
  const teacher_job = validateString("teacher_job", "teacher_job_err", 200, 1, "szakma");
  if (typeof document.getElementById("teacher_user").options[document.getElementById("teacher_user").selectedIndex] === 'undefined') {
    document.getElementById("teacher_user").selectedIndex = 0;
  }
  const user_id = document.getElementById("teacher_user").options[document.getElementById("teacher_user").selectedIndex].getAttribute("value");
  if (teacher_name === false || teacher_job === false) return;

  const response = await fetch(url + "intezmeny/create/teacher", {
    method: "POST",
    body: user_id === "-1" ? JSON.stringify({
      intezmeny_id: intezmeny_id,
      name: teacher_name,
      job: teacher_job,
    }) : JSON.stringify({
      intezmeny_id: intezmeny_id,
      name: teacher_name,
      job: teacher_job,
      teacher_uid: user_id,
    })
  });
  if (response.ok !== true) {
    let result = await response.text();
    document.getElementById("teacher_name_err").innerHTML = result;
    return;
  }
  document.getElementById("teacher_name").value = "";
  document.getElementById("teacher_job").value = "";
  document.getElementById("teacher_user").selectedIndex = 0;
}

async function inviteUser() {
  const user_email = validateString("user_email", "user_email_err", 200, 1, "email");
  if (user_email === false) return;

  const response = await fetch(url + "intezmeny/user/invite", {
    method: "POST",
    body: JSON.stringify({
      intezmeny_id: intezmeny_id,
      email: user_email,
    })
  });
  if (response.ok !== true) {
    let result = await response.text();
    if (result === "Already exists") {
      document.getElementById("user_email_err").innerHTML = "Ez a felhasználó már meg van hívva";
    } else {
      document.getElementById("user_email_err").innerHTML = result;
    }
    return;
  }
  document.getElementById("user_email").value = "";
}

await prepareCreate();

window.prepareCreate = prepareCreate;
window.createClass = createClass;
window.createGroup = createGroup;
window.createLesson = createLesson;
window.createRoom = createRoom;
window.createTeacher = createTeacher;
window.inviteUser = inviteUser;
