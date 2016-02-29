var $notice = $('div.notice'),
    $dynFieldType = $('select#dyn_field_type'),
    $choiceAddBtn = $('button#dynForm_choiceAdd'),
    $choiceList = $('div#dynForm_choiceList'),
    choiceList = [],
    $choice = '<div class="form-group"><input class="dynForm_choice form-control" type="text"></div>',
    $dataField = $('input#dyn_field_data');


$notice.click(function() {
    $(this).fadeOut();
});

$dynFieldType.on('change', function() {
    if($(this).val() === 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType') {
        $choiceAddBtn.show();
        $choiceList.show();
    } else {
        $choiceAddBtn.hide();
        $choiceList.hide();
        $dataField.val('');
    }
});

$choiceAddBtn.on('click', function (e) {
    e.preventDefault();
    $choiceList.append($choice);
});

$('body').on('keyup', 'input.dynForm_choice', function () {
    choiceList = [];
    $('input.dynForm_choice').each(function() {
        var choice = $(this).val();
        choiceList.push(choice);
    });
    $dataField.val(choiceList.join(';'));
    //console.log($dataField.val());
});