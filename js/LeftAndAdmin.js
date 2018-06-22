(function($){
    $.entwine('ss', function($){

        $('.cms').on('click', '#action_doDeleteRecord', function() {
            if (confirm("Are you sure you want to delete this record?")) {
                if (confirm("Ok if you hit delete again this is irreversible.")) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        });

        $('.cms').on('click', '.col-buttons a', function() {
            event.stopPropagation();
        });

        $('.cms-menu-list').on('click', 'li', function() {
            location.reload();
        });

    });


})(jQuery);