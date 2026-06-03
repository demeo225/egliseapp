



/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */



var $zone2 = $('#cotiserzone_zone');
// When zone gets selected ...
$zone2.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected zone value.
  var data = {};
  data[$zone2.attr('name')] = $zone2.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#cotiserzone_zonecotisation').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#cotiserzone_zonecotisation')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});


var $zone2 = $('#cotiserzone_zone');
// When zone gets selected ...
$zone2.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected zone value.
  var data = {};
  data[$zone2.attr('name')] = $zone2.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#cotiserzone_fidele').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#cotiserzone_fidele')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});




