jQuery('document').ready(function($){
  function readURL(input, img) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        img.attr('src', e.target.result);
      }
      reader.readAsDataURL(input.files[0]);
    }
  }
  $(".user_avatar__uploader > input").change(function() {
    readURL(this, $('.user_avatar__avatar > img'));
  });
});