



/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

var $departement2 = $('#cotiserdepartement_departement');
// When departement gets selected ...
$departement2.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  
  // Simulate form data, but only include the selected departement value.
  var data = {};
  data[$departement2.attr('name')] = $departement2.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#cotiserdepartement_cotisationdepartement').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#cotiserdepartement_cotisationdepartement')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});




