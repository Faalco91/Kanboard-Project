<div id="create-project-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>{{ __('Créer un nouveau projet') }}</h2>
            <span class="close">&times;</span>
        </div>
        <form id="create-project-form" method="POST" action="{{ route('projects.store') }}">
            @csrf
            <div class="form-group">
                <label for="name">{{ __('Nom du projet') }}</label>
                <input type="text" id="name" name="name" required class="form-control">
            </div>
            <div class="form-group">
                <label for="description">{{ __('Description') }}</label>
                <textarea id="description" name="description" class="form-control"></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ __('Créer') }}</button>
                <button type="button" class="btn btn-secondary close-modal">{{ __('Annuler') }}</button>
            </div>
        </form>
    </div>
</div>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 8px;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.form-control {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.btn {
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
}

.btn-primary {
    background-color: #4CAF50;
    color: white;
    border: none;
}

.btn-secondary {
    background-color: #f1f1f1;
    border: 1px solid #ddd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('create-project-modal');
    const closeButtons = modal.querySelectorAll('.close, .close-modal');
    const form = document.getElementById('create-project-form');

    // Ouvrir le modal
    window.openCreateProjectModal = function() {
        modal.style.display = 'block';
    }

    // Fermer le modal
    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    });

    // Fermer le modal en cliquant en dehors
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Gérer la soumission du formulaire
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: form.name.value,
                    description: form.description.value
                })
            });

            if (response.ok) {
                window.location.reload();
            } else {
                const data = await response.json();
                alert(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        }
    });
});
</script> 