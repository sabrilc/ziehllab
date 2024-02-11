yii.confirm = function (message, okCallback, cancelCallback) {
    swal({
        text: message,
        icon: 'warning',
        buttons : {
            cancel : {
                text : "Cancelar",
                value : null,
                visible : true,
                className : "",
                closeModal : true
            },
            confirm : {
                text : "Confirmar",
                value : true,
                visible : true,
                className : "",
                closeModal : true
            }
        },
        closeOnClickOutside: true
    }).then( selection => { if(selection){okCallback();}else{cancelCallback();} });
}