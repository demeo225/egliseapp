



/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */






/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

var $cellule = $('#cotisercellule_cellule');
// When cellule gets selected ...
$cellule.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected cellule value.
  var data = {};
  data[$cellule.attr('name')] = $cellule.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#cotisercellule_fidele').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#cotisercellule_fidele')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});


var $cellule = $('#cotisercellule_cellule');
// When cellule gets selected ...
$cellule.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected cellule value.
  var data = {};
  data[$cellule.attr('name')] = $cellule.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#cotisercellule_cotisationcellule').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#cotisercellule_cotisationcellule')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});



