function customMenuUpdateFields(type) {
    type = parseInt(type);
    switch (type) {
        case 1: //link inner
        case 2: //link external
            $('url').up('tr').show();
            $('url').addClassName('required-entry');
            $('default_category').up('tr').hide();
            $('default_category').removeClassName('required-entry');
            $('source_attribute').up('tr').hide();
            $('source_attribute').removeClassName('required-entry');
            $('cms_page_id').up('tr').hide();
            $('cms_page_id').removeClassName('required-entry');
            $('show_children').up('tr').hide();
            break;
        case 3: //category
            $('url').removeClassName('required-entry');
            $('default_category').up('tr').show();
            $('default_category').addClassName('required-entry');
            $('source_attribute').up('tr').hide();
            $('source_attribute').removeClassName('required-entry');
            $('cms_page_id').up('tr').hide();
            $('cms_page_id').removeClassName('required-entry');
            $('show_children').up('tr').show();
            break;
        case 4: //attribute values
            $('url').up('tr').hide();
            $('url').removeClassName('required-entry');
            $('cms_page_id').up('tr').hide();
            $('cms_page_id').removeClassName('required-entry');
            $('source_attribute').up('tr').show();
            $('source_attribute').addClassName('required-entry');
            $('show_children').up('tr').hide();
            break;
        case 5: //cms page
            $('url').removeClassName('required-entry');
            $('cms_page_id').up('tr').show();
            $('cms_page_id').addClassName('required-entry');
            $('default_category').up('tr').hide();
            $('default_category').removeClassName('required-entry');
            $('source_attribute').up('tr').hide();
            $('source_attribute').removeClassName('required-entry');
            $('show_children').up('tr').show();
            break;
    }
}

document.observe("dom:loaded", function() {
    var type = $('type');
    type.observe('change', function (evt){
        customMenuUpdateFields(type.value);
    });
    customMenuUpdateFields(type.value);
});