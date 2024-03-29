(function ($) {
    /**
     * 切换指定表单内容
     */
    function toggle(e) {
        var $this = $(this);
        if (!$this.is(':checked')) return;
        var express;
        var $form = $this.closest('form');
        var $formGroup = $form
            .find('.form-group')
            .not($this.closest('.form-group')) // 排除类型
            .not('.submit-button'); // 排除按钮
        $formGroup.hide();
        switch ($this.val()) {
            case 'text':
                express = '.field-message-content';
                $formGroup
                    .filter(express)
                    .find('input[name=mediaType]')
                    .val('text');
                break;
            case 'image':
                $('#btn-type').attr('href','/wechat/media/pick?type=image');
                $('#message-mediaid').val('');
                express = '.field-message-mediaid';
                $formGroup
                    .filter(express)
                    .find('input[name=mediaType]')
                    .val('image');
                break;
            case 'voice':
                $('#btn-type').attr('href','/wechat/media/pick?type=voice');
                $('#message-mediaid').val('');
                express = '.field-message-mediaid';
                $formGroup
                    .filter(express)
                    .find('input[name=mediaType]')
                    .val('voice');
                break;
            case 'video':
                $('#btn-type').attr('href','/wechat/media/pick?type=video');
                $('#btn-thumbType').attr('href','/wechat/media/pick?type=thumb');
                $('#message-mediaid').val('');
                $('#message-thumbmediaid').val('');
                express = '.field-message-mediaid, .field-message-thumbmediaid, .field-message-title, .field-message-description';
                $formGroup
                    .filter(express)
                    .find('input[name=mediaType]')
                    .val('video');
                break;
            case 'music':
                $('#btn-thumbType').attr('href','/wechat/media/pick?type=thumb');
                $('#message-mediaid').val('');
                $('#message-thumbmediaid').val('');
                express = '.field-message-title, .field-message-description, .field-message-musicurl, .field-message-hqmusicurl, .field-message-thumbmediaid';
                $formGroup
                    .filter(express)
                    .find('input[name=mediaType]')
                    .val('music');
                break;
            case 'news':
                return alert('抱歉. 暂不支持News信息发送');
            default:
                return;
        }

        $formGroup.filter(express).show();
    }

    var Message = function(element) {
        this.form = $(element);
        this.form
            .find('[name="Message[msgType]"]')
            .on('click', toggle)
            .filter(":checked")
            .click();
    };

    function Plugin(option) {
        return this.each(function() {
            var $this = $(this);
            var data = $this.data('wechat.message');

            if (!data) $this.data('wechat.message', (data = new Message(this)));
            if (typeof option == 'string') data[option].call($this);
        });
    }

    $.fn.message = Plugin;
})(jQuery);