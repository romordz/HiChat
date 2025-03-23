console.log('Fetching header.php');
fetch('header.php')
    .then(response => response.text())
    .then(data => {
        console.log('header.php loaded');
        document.getElementById('header-placeholder').innerHTML = data;
        import('../js/dropdown.js').then(module => {
            module.initializeDropdown();
            console.log('dropdown.js loaded and initialized');
        });
    })
    .catch(error => console.error('Error loading header.php:', error));