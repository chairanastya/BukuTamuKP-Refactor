export function initDropdown(dropdownId = 'dropdown') {
    const dropdown = document.getElementById(dropdownId);
    
    if (!dropdown) {
        console.warn(`[initDropdown] Element with ID "${dropdownId}" not found`);
        return;
    }
    
    window.toggleDropdown = function() {
        dropdown.classList.toggle('hidden');
    };
    
    document.addEventListener('click', function(event) {
        const button = event.target.closest('button');
        const toggleButton = button && button.getAttribute('onclick') === 'toggleDropdown()';
        const clickedInsideDropdown = dropdown.contains(event.target);
        
        if (!clickedInsideDropdown && !toggleButton) {
            dropdown.classList.add('hidden');
        }
    });
}

export default initDropdown;
