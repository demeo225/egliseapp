



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






/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

var $commune = $('#enfant_commune');

// When commune gets selected ...
$commune.change(function() {
 
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected commune value.
  var data = {};
  data[$commune.attr('name')] = $commune.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#enfant_quartier').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#enfant_quartier')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});


var $zone = $('#enfant_zone');
// When zone gets selected ...
$zone.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected zone value.
  var data = {};
  data[$zone.attr('name')] = $zone.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#enfant_cellule').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#enfant_cellule')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});






