document.getElementById('connectionForm').addEventListener('submit', async (event) => {
    event.preventDefault();

    const form = event.target;
    const button = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');
    const btnProgress = document.getElementById('btnProgress');
    const messageText = document.getElementById('message');

    button.disabled = true;
    btnText.classList.add('invisible');
    btnLoader.classList.remove('hidden');
    btnProgress.classList.add('w-full');

    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    console.log('Payload envoyé :', data);

    try {
        const response = await fetch('http://localhost:8181/identification/check-connection.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });

        const text = await response.text();
        console.log("Réponse brute du serveur :", text);

        const result = JSON.parse(text);

       if (!result.success || !result.user || !result.user.token) {
            throw new Error(result.message || 'Échec de la connexion');
        }

        // ✅ Connexion OK
        messageText.textContent = result.message;
        messageText.classList.remove('text-red-600');
        messageText.classList.add('text-green-600', 'opacity-100');
        
        setTimeout(() => {
            messageText.classList.remove('opacity-100');
            messageText.classList.add('opacity-0');
        }, 5000);
        
        localStorage.setItem('token', result.user.token);
        localStorage.setItem('user', JSON.stringify(result.user));

        window.location.href = "../index.html";

    } catch (error) {
        console.error('Erreur:', error);
        messageText.textContent = 'Erreur lors de la connexion : ' + error.message;
        messageText.classList.remove('text-green-600');
        messageText.classList.add('text-red-600', 'opacity-100');

        setTimeout(() => {
            messageText.classList.remove('opacity-100');
            messageText.classList.add('opacity-0');
        }, 5000);


        // Réinitialisation du champ mot de passe
        const passwordInput = form.querySelector('[name="password"]');
        if (passwordInput) passwordInput.value = '';
    } finally {
        button.disabled = false;
        btnText.classList.remove('invisible');
        btnLoader.classList.add('hidden');
        btnProgress.classList.remove('w-full');
    }
});
