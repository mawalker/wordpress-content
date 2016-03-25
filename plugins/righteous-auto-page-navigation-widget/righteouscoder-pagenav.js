/*
 * Allow Arrow Keys to navigate forwards and backwards.
 */
jQuery(document).ready(function () {
        jQuery(document).keydown(function(e) {
            var url = false;
            if (e.which == 37) {  // Left arrow key code
                var url = document.getElementsByClassName('nav-previous')[0];
            }
            else if (e.which == 39) {  // Right arrow key code
                var url = document.getElementsByClassName('nav-next')[0];
            }
            if (url) { window.location = url;   }
        });
    });
