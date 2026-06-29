function toggleEditNegocio() {
    const form = document.getElementById('formNegocio');
    const btnEdit = document.getElementById('btnEditNegocio');
    
    if (form.classList.contains('is-editing')) {
        form.classList.remove('is-editing');
        btnEdit.style.display = 'inline-flex';
        // Reset form values to original
        form.reset();
    } else {
        form.classList.add('is-editing');
        btnEdit.style.display = 'none';
        // Focus first input
        setTimeout(() => form.querySelector('input').focus(), 100);
    }
}

async function guardarNegocio(e) {
    e.preventDefault();
    const form = e.target;
    const fd = new FormData(form);
    
    try {
        const BASE = document.getElementById('baseUrl').value;
        const res = await fetch(BASE + 'ajustes/negocio', {
            method: 'POST',
            body: fd
        });
        const data = await res.json();
        
        if (data.error === 0) {
            showSnack(data.msg, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showSnack(data.msg, 'error');
        }
    } catch (err) {
        showSnack('Error de conexión', 'error');
    }
}

function renovarLicencia() {
    customPrompt('Renovar Licencia', 'Ingrese la nueva clave de licencia:', '', async (val) => {
        if (!val || val.trim() === '') return;
        
        const fd = new FormData();
        fd.append('licencia', val.trim());
        
        try {
            const BASE = document.getElementById('baseUrl').value;
            const res = await fetch(BASE + 'ajustes/licencia', {
                method: 'POST',
                body: fd
            });
            const data = await res.json();
            
            if (data.error === 0) {
                showSnack(data.msg, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showSnack(data.msg, 'error');
            }
        } catch (err) {
            showSnack('Error de conexión', 'error');
        }
    });
}
