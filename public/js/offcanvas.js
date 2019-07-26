// offcanvas

$(function () {
  'use strict'

  $('[data-toggle="offcanvas-left"]').on('click', function () {
    $('.offcanvas-collapse-from-left').toggleClass('open')
  })
  $('[data-toggle="offcanvas-right"]').on('click', function () {
    $('.offcanvas-collapse-from-right').toggleClass('open')
  })
})
