

var $departement = $('#groupefidele_departement');
// When zone gets selected ...
$departement.change(function() {
  // ... retrieve the corresponding form.
  var $form = $(this).closest('form');
  // Simulate form data, but only include the selected zone value.
  var data = {};
  data[$departement.attr('name')] = $departement.val();
  // Submit data via AJAX to the form's action path.
  $.ajax({
    url : $form.attr('action'),
    type: $form.attr('method'),
    data : data,
    complete: function(html) {
      // Replace current fidele field ...
      $('#groupefidele_groupe').replaceWith(
        // ... with the returned one from the AJAX response.
        $(html.responseText).find('#groupefidele_groupe')
      );
      // Position field now displays the appropriate fideles.
    }
  });
});

