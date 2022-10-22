
function sendAjaxRequest(url, parameter, successCallback, errorCallback) {

    $.ajax(url, {
        type: 'GET',
        data: parameter,
        success: function (data, status, xhr) {
            successCallback(data, status, xhr);
        },
        error: function (jqXhr, textStatus, errorMessage) {
            errorCallback(jqXhr, textStatus, errorMessage);
        }
    });
}
