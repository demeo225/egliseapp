



/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

var $famille = $('#cotiserfamille_famille');
// When famille gets selected ...
$famille.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected famille value.
  var data = {};
  data[$famille.attr('name')] = $famille.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#cotiserfamille_fidele').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#cotiserfamille_fidele')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});


var $famille = $('#cotiserfamille_famille');
// When famille gets selected ...
$famille.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected famille value.
  var data = {};
  data[$famille.attr('name')] = $famille.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#cotiserfamille_cotisationfamille').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#cotiserfamille_cotisationfamille')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});

