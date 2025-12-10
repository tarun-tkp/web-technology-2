// script.js — jQuery validation + photo preview + print/download
$(function(){

  // Photo preview
  $('#photo').on('change', function(e){
    const file = this.files && this.files[0];
    const preview = $('#photoPreview');
    preview.empty();
    if (!file) return;
    if (!file.type.startsWith('image/')) {
      preview.text('Please select an image file.');
      return;
    }
    if (file.size > 1_000_000) {
      preview.text('Image too large (max 1MB).');
      return;
    }
    const reader = new FileReader();
    reader.onload = function(ev){
      $('<img>').attr('src', ev.target.result).appendTo(preview);
    };
    reader.readAsDataURL(file);
  });

  // Client-side validation before submit
  $('#regForm').on('submit', function(e){
    $('.field-error').remove();
    let valid = true;

    const first = $('#firstName').val().trim();
    const email = $('#email').val().trim();
    const phone = $('#phone').val().trim();
    const course = $('#course').val().trim();

    if (!first) { showErr('#firstName','First name is required'); valid = false; }
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showErr('#email','Valid email is required'); valid = false; }
    const digits = phone.replace(/\D/g,'');
    if (!phone || digits.length < 7 || digits.length > 15) { showErr('#phone','Phone must be 7–15 digits'); valid = false; }
    if (!course) { showErr('#course','Course is required'); valid = false; }

    if (!valid) {
      e.preventDefault();
      $('html,body').animate({scrollTop:($('.field-error').first().offset().top - 80)}, 300);
    }
  });

  function showErr(selector, text){
    $('<div class="field-error" style="color:#e02424;margin-top:6px">'+text+'</div>').insertAfter(selector);
  }

  // Print button (on result page)
  $('#printBtn').on('click', function(){
    window.print();
  });

  // Download page as PNG (simple approach: use html2canvas if you want nicer export)
  $('#downloadBtn').on('click', function(){
    alert('Use Print → Save as PDF in the print dialog, or take a screenshot. For automatic PNG export add html2canvas.');
  });

});
