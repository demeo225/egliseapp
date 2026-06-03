



/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

var $groupe2 = $('#cotisergroupe_groupe');
// When groupe gets selected ...
$groupe2.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected groupe value.
  var data = {};
  data[$groupe2.attr('name')] = $groupe2.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#cotisergroupe_cotisationgroupe').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#cotisergroupe_cotisationgroupe')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});




