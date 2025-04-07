console.log('checkInscription.js');

// Ajout des événements pour la validation en temps réel
document.getElementById('password').addEventListener('input', checkPassword);
document.getElementById('password2').addEventListener('input', checkPassword);
        
document.getElementById('identifiant').addEventListener('input', checkPseudo); 

// Fonction pour vérifier les mots de passe
function checkPassword() {
    console.log('checkPassword()');

    let password = document.getElementById('password').value;
    let password2 = document.getElementById('password2').value;
    let messageContainer = document.getElementById('password-message-container');
    let messageContainer2 = document.getElementById('password-message-container2');
    let valideButton = document.getElementById('valide');

    // Vide les conteneurs de message avant d'ajouter de nouveaux messages
    messageContainer.innerHTML = '';
    messageContainer2.innerHTML = '';

    let errors = [];
    let errors2 = [];

    // Vérifications des conditions des mots de passe
    if (password.length > 0) {
        if (password !== password2 && password2.length > 0) {
            errors.push("Les mots de passe ne correspondent pas.");
        }
        if (password.length < 8) {
            errors2.push("Le mot de passe doit contenir au moins 8 caractères.");
        }
        if (!/[A-Z]/.test(password) || !/[a-z]/.test(password)) {
            errors2.push("Le mot de passe doit contenir au moins une majuscule et une minuscule.");
        }
        if (!/[0-9]/.test(password)) {
            errors2.push("Le mot de passe doit contenir au moins un chiffre.");
        }
        if (!/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
            errors2.push("Le mot de passe doit contenir au moins un caractère spécial.");
        }

        // Désactive le bouton si des erreurs existent
        if (errors.length > 0 || errors2.length > 0) {
            document.getElementById('password').style.borderColor = 'red';
            document.getElementById('password2').style.borderColor = 'red';
            valideButton.setAttribute('disabled', 'disabled');

            // Ajouter les erreurs de mots de passe
            errors.forEach(error => {
                let p = document.createElement('p');
                p.className = 'text-sm mt-1';
                p.style.color = 'red';
                p.innerText = error;
                messageContainer.appendChild(p);
            });

            // Ajouter les erreurs de sécurité
            errors2.forEach(error => {
                let p = document.createElement('p');
                p.className = 'text-sm mt-1';
                p.style.color = 'red';
                p.innerText = error;
                messageContainer2.appendChild(p);
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


// Fonction pour vérifier si l'identifiant est déjà pris
let timeout = null;

function checkPseudo() {
    console.log('checkPseudo()');

    let pseudo = document.getElementById('identifiant').value;
    let message = document.getElementById('pseudo-message');
    let identifiantInput = document.getElementById('identifiant');

    // Annule la requête précédente si l'utilisateur continue d'écrire
    clearTimeout(timeout);

    if (pseudo.length < 3) {
        message.innerText = "L'identifiant doit avoir au moins 3 caractères.";
        message.style.color = "red";
        identifiantInput.style.borderColor = 'red';
        return;
    } else {
        message.innerText = "";
        identifiantInput.style.borderColor = '';
    }

    // Délai avant l'envoi de la requête (500ms après la dernière saisie)
    timeout = setTimeout(() => {
        fetch("http://localhost:8181/identification/check_identifiant.php", {
            method: "POST",
            body: new URLSearchParams({ identifiant: pseudo }),
            headers: { "Content-Type": "application/x-www-form-urlencoded" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                message.innerText = "❌ identifiant déjà pris.";
                message.style.color = "red";
                identifiantInput.style.borderColor = 'red';
            } else {
                message.innerText = "✅ identifiant disponible.";
                message.style.color = "green";
                identifiantInput.style.borderColor = 'green';
            }
        })
        .catch(error => console.error("Erreur :", error));
    }, 500); // Attendre 500ms après la dernière frappe
}




