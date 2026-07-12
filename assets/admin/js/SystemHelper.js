var SystemHelper = {
    version: "1.0",

    initEditor: function (selector) {
        ClassicEditor.create(document.querySelector(selector)).catch(
            (error) => {
                console.error(error);
            }
        );
    },

};




$(document).ready(function() {
    function renderFormControls()
    {
        $(document).find('.form-control[required]').each(function() {
            var $input = $(this);
            var $label = $input.closest('.form-group, .mb-3, .form-floating').find('label').first();
            
            if ($label.length && !$label.hasClass('required-marked')) {
                $label.html($label.html() + '<strong class="ms-1" style="color: red">*</strong> ');
                $label.addClass('required-marked'); 
            }
        });
    };

    window.renderFormControls = renderFormControls;
    
    renderFormControls();
});