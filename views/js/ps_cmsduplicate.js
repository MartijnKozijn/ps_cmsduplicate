document.addEventListener('DOMContentLoaded', function() {
    var dropdowns = document.querySelectorAll('.dropdown-toggle-dots');

    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('click', function() {
            setTimeout(function() {
                var menu = dropdown.nextElementSibling;
                if (menu) {
                    var duplicateButton = document.createElement('a');
                    duplicateButton.classList.add('dropdown-item');
                    duplicateButton.setAttribute('href', menu.closest('tr').querySelector('a.edit').href.replace('update', 'duplicate'));
                    duplicateButton.innerHTML = '<i class="icon-copy"></i> Duplicate';
                    
                    menu.appendChild(duplicateButton);
                }
            }, 100);
        });
    });
});
