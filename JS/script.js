// script.js — jQuery validation + photo preview + print/download// Runs on GitHub – No PHP needed.
// Displays formatted result using only JavaScript.

$(function(){

  $("#regForm").on("submit", function(e){
    e.preventDefault();

    // Collect values
    let data = {
      firstName: $("#firstName").val(),
      lastName: $("#lastName").val(),
      email: $("#email").val(),
      phone: $("#phone").val(),
      dob: $("#dob").val() || "N/A",
      gender: $("#gender").val() || "N/A",
      address: $("#address").val() || "N/A",
      course: $("#course").val()
    };

    // Simple validation
    if(data.firstName.trim() === "" || data.email.trim() === "" || data.phone.trim() === "" || data.course.trim() === ""){
      alert("Please fill all required fields.");
      return;
    }

    // Build result HTML
    let html = `
      <h2>Application Submitted Successfully</h2>
      <div class="result-row">
        <div class="result-label">Full Name</div>
        <div class="result-value">${data.firstName} ${data.lastName}</div>
      </div>
      <div class="result-row">
        <div class="result-label">Email</div>
        <div class="result-value">${data.email}</div>
      </div>
      <div class="result-row">
        <div class="result-label">Phone</div>
        <div class="result-value">${data.phone}</div>
      </div>
      <div class="result-row">
        <div class="result-label">Date of Birth</div>
        <div class="result-value">${data.dob}</div>
      </div>
      <div class="result-row">
        <div class="result-label">Gender</div>
        <div class="result-value">${data.gender}</div>
      </div>
      <div class="result-row">
        <div class="result-label">Address</div>
        <div class="result-value">${data.address.replace(/\n/g, "<br>")}</div>
      </div>
      <div class="result-row">
        <div class="result-label">Course Applied For</div>
        <div class="result-value">${data.course}</div>
      </div>
    `;

    // Hide form, show result
    $("#regForm").hide();
    $("#result").html(html).fadeIn();
  });

});

