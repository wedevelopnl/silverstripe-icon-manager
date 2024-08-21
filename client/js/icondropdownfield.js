(function($) {
  $(document).ready(function() {
    $('.icondropdown').on('change', function(e) {
      e.stopPropagation();

      var selectedIcon = $(this).val();
      var literalPreviewField = $('.icon-preview-holder');

      literalPreviewField.html('Loading preview...');

      $.ajax({
        url: `${e.currentTarget.dataset.iconPreviewEndpoint}?icon=${selectedIcon}`,
        method: 'GET',
        success: function(response) {
          var innerHTML = response;

          literalPreviewField.html(response);
        }
      });
    });
  });
})(jQuery);
