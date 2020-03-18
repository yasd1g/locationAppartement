$('#add-image').click(function () {

    //Je récupère le numéro d'index des futurs champs que je vais créer ( le plus devant c est pour reccuperer un nombre
    const index = +$('#widgets-counter').val();

    //Je récupère le prototype des entrées
    const tmpl = $('#ad_images').data('prototype').replace(/__name__/g, index);

    //J'injecte ce code au sein de la div et je fais +1 sur le counter
    $('#ad_images').append(tmpl);
    $('#widgets-counter').val(index + 1);

    //Je gére le button supprimer
    handleDeleteButtons();


});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function () {
        const target = this.dataset.target;
        $(target).remove();
    })
}

function updateCounter(){
    const count = +$('#ad_images div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();
handleDeleteButtons();
