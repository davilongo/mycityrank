// Función de confirmación antes de eliminar el post
function confirmDelete(event) {
    event.preventDefault(); // Evitar que el formulario se envíe inmediatamente
    const confirmed = confirm('¿Estás seguro de que deseas eliminar este post?');
    if (confirmed) {
        // Si el usuario confirma, enviar el formulario
        event.target.closest('form').submit();
    }
}
