/**
 * Displays an confirmation box before running a link object
 * This function is called while clicking links
 *
 * @param   object   the link
 * @param   object   the sql query to submit
 *
 * @return  boolean  whether to run the query or not
 */
function confirmLink(theLink, textItem)
{
    // browser is Opera (crappy js implementation)
    if (typeof(window.opera) != 'undefined') {
        return true;
    }

    var is_confirmed = confirm('Do you really want to' + ' ' + textItem);
    if (is_confirmed) {
        theLink.href += '&is_js_confirmed=1';
    }

    return is_confirmed;
} // end of the 'confirmLink()' function
