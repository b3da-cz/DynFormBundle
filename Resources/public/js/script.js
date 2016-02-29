var $notice = $('div.notice'),
    $dynFieldType = $('select#dyn_field_type'),
    $choiceAddBtn = $('button#dynForm_choiceAdd'),
    $rowChoiceAddBtn = $('div#dynForm_rowChoiceAdd'),
    $choiceList = $('div#dynForm_choiceList'),
    choiceList = [],
    $choice = '<div class="form-group"><label class="col-sm-2 label"> &nbsp; </label><div class="col-sm-10"><input class="dynForm_choice form-control" type="text"></div></div>',
    $dataField = $('input#dyn_field_data');


$notice.click(function() {
    $(this).fadeOut();
});

var switchFieldType = function (chosenType) {
    if(chosenType === 'Symfony\\Component\\Form\\Extension\\Core\\Type\\ChoiceType') {
        $rowChoiceAddBtn.show();
        $choiceList.show();
    } else {
        $rowChoiceAddBtn.hide();
        $choiceList.hide();
        $dataField.val('');
    }
};

$dynFieldType.on('change', function() {
    switchFieldType($(this).val());
});

switchFieldType($dynFieldType.val());
if($dataField.val() > '') {
    var choices = $dataField.val().split(';');
    choices.forEach(function (val) {
        $choiceList.append('<div class="form-group"><label class="col-sm-2 label"> &nbsp; </label><div class="col-sm-10"><input class="dynForm_choice form-control" type="text" value="'+val+'"></div></div>');
    });
}

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
});