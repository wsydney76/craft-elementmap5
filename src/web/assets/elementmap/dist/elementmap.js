var $btn = $('#map-btn');

$btn.on('click', function() {

    url = elementMapAjaxBaseUrl.replace('%s', elementMapElementId);

    $.get(url)
        .done(function(data) {
            const hud = new Garnish.HUD($btn, data, {
                orientations: ['top', 'bottom', 'right', 'left'],
                hudClass: 'hud guide-hud',
            });
        })
        .fail(function() {
            alert("Error, see developer tools -> network -> Fetch/XHR for details.");
            console.error("Element Map: Failed to load map data from " + url);
        });
});

setTimeout(() => {
    Craft.cp.$primaryForm.data('elementEditor').on('createProvisionalDraft', function() {
        elementMapElementId = Craft.cp.$primaryForm.data('elementEditor').settings.elementId;
    });
}, 500)

