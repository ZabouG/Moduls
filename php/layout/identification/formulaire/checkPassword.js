function checkPassword() {

    console.log('checkPassword()');

    let password = document.getElementById('password').value;
    let password2 = document.getElementById('password2').value;
    let messageContainer = document.getElementById('password-message-container');
    let valideButton = document.getElementById('valide');

    // Vide la div avant d'ajouter les nouveaux messages
    messageContainer.innerHTML = '';

    let errors = []; // Tableau pour stocker les messages d'erreur

    // Vérification des conditions
    if (password.length > 0) {  // Vérifie que l'utilisateur a commencé à écrire
        if (password !== password2) {
            errors.push("Les mots de passe ne correspondent pas.");
        }
        if (password.length < 8) {
            errors.push("Le mot de passe doit contenir au moins 8 caractères.");
        }
        // Si des erreurs existent, les afficher et désactiver le bouton
        if (errors.length > 0) {
            document.getElementById('password').style.borderColor = 'red';
            document.getElementById('password2').style.borderColor = 'red';
            valideButton.setAttribute('disabled', 'disabled');

            // Ajouter un <p> pour chaque message d'erreur
            errors.forEach(error => {
                let p = document.createElement('p');
                p.className = 'text-sm mt-1';
                p.style.color = 'red';
                p.innerText = error;
                messageContainer.appendChild(p);
            });
        } else {
            // Si tout est bon, activer le bouton et afficher un message de validation
            document.getElementById('password').style.borderColor = 'green';
            document.getElementById('password2').style.borderColor = 'green';
            valideButton.removeAttribute('disabled');

            let successMessage = document.createElement('p');
            successMessage.className = 'text-sm mt-1';
            successMessage.style.color = 'green';
            successMessage.innerText = "Mot de passe valide.";
            messageContainer.appendChild(successMessage);
        }
    } else {
        // Réinitialiser les bordures si aucun texte n'est saisi
        document.getElementById('password').style.borderColor = '';
        document.getElementById('password2').style.borderColor = '';
        valideButton.setAttribute('disabled', 'disabled');
    }
}


// Ajout d'événements pour la validation en temps réel
document.getElementById('password').addEventListener('input', checkPassword);
document.getElementById('password2').addEventListener('input', checkPassword);

