document.getElementById('connectionForm').addEventListener('submit', async (event) => {
    event.preventDefault();

    const form = event.target;
    const button = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const btnProgress = document.getElementById('btnProgress');

    // Désactive visuellement et fonctionnellement le bouton
    button.disabled = true;
    btnText.classList.add('invisible');
    btnLoader.classList.remove('hidden');
    btnProgress.classList.add('w-full');

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('http://localhost:8181/identification/check-connection.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Échec de la connexion');
        }

        console.log('Connexion réussie:', result);
        localStorage.setItem('token', result.token); // Stocke le token dans le localStorage
        localStorage.setItem('user', JSON.stringify(result.user)); // Stocke les informations de l'utilisateur
        // Exemple : rediriger vers une autre page
        window.location.href = "../index.html";

    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur lors de la connexion : ' + error.message);
    } finally {
        // Réactive le bouton
        button.disabled = false;
        btnText.classList.remove('invisible');
        btnLoader.classList.add('hidden');
        btnProgress.classList.remove('w-full');
        alert(result.message || 'Erreur lors de la connexion');
    }
});
