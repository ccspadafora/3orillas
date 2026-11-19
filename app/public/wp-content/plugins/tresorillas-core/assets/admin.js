/* Selector de medios para los campos del proyecto. */
jQuery(function ($) {
  $(document).on('click', '.tor-media', function (e) {
    e.preventDefault();
    var b = $(this), id = b.data('for'), multiple = b.data('multiple') === 1, tipo = b.data('type');
    var frame = wp.media({ title: 'Elegir', multiple: multiple ? 'add' : false, library: { type: tipo } });
    frame.on('open', function () {
      var sel = frame.state().get('selection');
      String($('#' + id).val()).split(',').filter(Boolean).forEach(function (n) { var a = wp.media.attachment(n); a.fetch(); sel.add(a); });
    });
    frame.on('select', function () {
      var items = frame.state().get('selection').toJSON(), prev = $('.tor-previa[data-for="' + id + '"]').empty();
      $('#' + id).val(items.map(function (a) { return a.id; }).join(','));
      items.forEach(function (a) {
        if (tipo === 'video') { prev.append($('<span class="tor-video">').text(a.filename)); }
        else { var u = (a.sizes && a.sizes.thumbnail) ? a.sizes.thumbnail.url : a.url; prev.append($('<img alt="">').attr('src', u)); }
      });
    });
    frame.open();
  });
  $(document).on('click', '.tor-quitar', function (e) {
    e.preventDefault(); var id = $(this).data('for'); $('#' + id).val(''); $('.tor-previa[data-for="' + id + '"]').empty();
  });
});
