/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



var $commune = $('#fidele_commune');
// When commune gets selected ...

$commune.change(function () {


    // ... retrieve the corresponding form.
    var $form = $(this).closest('form');
    // Simulate form data, but only include the selected commune value.
    var data = {};
    data[$commune.attr('name')] = $commune.val();
    // Submit data via AJAX to the form's action path.

    $.ajax({

        url: $form.attr('action'),
        type: $form.attr('method'),
        data: data,
        complete: function (html) {

            // Replace current quartier field ...
            $('#fidele_quartier').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html.responseText).find('#fidele_quartier')
                    );

            // Position field now displays the appropriate quartiers.
        }
    });
});

var $zone = $('#fidele_zone');
// When zone gets selected ...

$zone.change(function () {


    // ... retrieve the corresponding form.
    var $form = $(this).closest('form');
    // Simulate form data, but only include the selected zone value.
    var data = {};
    data[$zone.attr('name')] = $zone.val();
    // Submit data via AJAX to the form's action path.

    $.ajax({

        url: $form.attr('action'),
        type: $form.attr('method'),
        data: data,
        complete: function (html) {

            // Replace current cellule field ...
            $('#fidele_cellule').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html.responseText).find('#fidele_cellule')
                    );

            // Position field now displays the appropriate cellules.
        }
    });
});



var $commune = $('#validation_commune');
// When commune gets selected ...

$commune.change(function () {



    // ... retrieve the corresponding form.
    var $form = $(this).closest('form');
    // Simulate form data, but only include the selected commune value.
    var data = {};
    data[$commune.attr('name')] = $commune.val();
    // Submit data via AJAX to the form's action path.

    $.ajax({

        url: $form.attr('action'),
        type: $form.attr('method'),
        data: data,
        complete: function (html) {

            // Replace current quartier field ...
            $('#validation_quartier').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html.responseText).find('#validation_quartier')
                    );

            // Position field now displays the appropriate quartiers.
        }
    });
});

var $zone = $('#validation_zone');
// When zone gets selected ...

$zone.change(function () {


    // ... retrieve the corresponding form.
    var $form = $(this).closest('form');
    // Simulate form data, but only include the selected zone value.
    var data = {};
    data[$zone.attr('name')] = $zone.val();
    // Submit data via AJAX to the form's action path.

    $.ajax({

        url: $form.attr('action'),
        type: $form.attr('method'),
        data: data,
        complete: function (html) {

            // Replace current cellule field ...
            $('#validation_cellule').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html.responseText).find('#validation_cellule')
                    );

            // Position field now displays the appropriate cellules.
        }
    });
});
    
    
    
    var $zone = $('#enfant_zone');
// When zone gets selected ...

$zone.change(function () {


    // ... retrieve the corresponding form.
    var $form = $(this).closest('form');
    // Simulate form data, but only include the selected zone value.
    var data = {};
    data[$zone.attr('name')] = $zone.val();
    // Submit data via AJAX to the form's action path.

    $.ajax({

        url: $form.attr('action'),
        type: $form.attr('method'),
        data: data,
        complete: function (html) {

            // Replace current cellule field ...
            $('#enfant_cellule').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html.responseText).find('#enfant_cellule')
                    );

            // Position field now displays the appropriate cellules.
        }
    });
});
    