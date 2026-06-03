/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(document).on('change', '#preinscription_commune', function () {
  let $field = $(this)
  let $communeField = $('#preinscription_commune')
  let $form = $field.closest('form')
  let target = '#' + $field.attr('id').replace('commune', 'quartier')
  // Les données à envoyer en Ajax
  let data = {}
  data[$communeField.attr('name')] = $communeField.val()
  data[$field.attr('name')] = $field.val()
  // On soumet les données
  $.post($form.attr('action'), data).then(function (data) {
    // On récupère le nouveau <select>
    let $input = $(data).find(target)
    // On remplace notre <select> actuel
    $(target).replaceWith($input)
  })
})